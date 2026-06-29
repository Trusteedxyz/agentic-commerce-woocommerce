<?php

declare(strict_types=1);

/**
 * F6.PHP2 / M3 — Amcp_Crypto_Helper idempotency + legacy-plaintext contract.
 *
 * Verifies:
 *   1. encrypt(x) emits a value prefixed with `amcp_enc:` (versioned format).
 *   2. decrypt(encrypt(x)) === x (round-trip).
 *   3. decrypt(legacy plaintext, i.e. no prefix) returns the value as-is.
 *   4. decrypt('') === ''.
 *   5. decrypt('amcp_enc:<corrupted-base64>') returns '' (no throw).
 *   6. The ciphertext prefix is the documented `amcp_enc:` constant.
 *
 * No WordPress runtime is loaded; the helper only touches PHP core
 * (sodium + base64), so the bootstrap stays minimal.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Define a 32-byte sodium key once so encrypt/decrypt round-trips work.
// Must match the constant the helper resolves (TRUSTEED_EMBED_SECRET_KEY).
if (!defined('TRUSTEED_EMBED_SECRET_KEY')) {
    define('TRUSTEED_EMBED_SECRET_KEY', base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES)));
}

require_once __DIR__ . '/../../includes/class-crypto-helper.php';

use PHPUnit\Framework\TestCase;

final class CryptoHelperTest extends TestCase
{
    /** Path of the per-test captured error_log stream. */
    private string $error_log_file;
    /** Original error_log ini value, restored in tearDown. */
    private string $original_error_log;

    protected function setUp(): void
    {
        $this->original_error_log = (string) ini_get('error_log');
        $tmp = tempnam(sys_get_temp_dir(), 'amcp_crypto_log_');
        if (false === $tmp) {
            $this->fail('Unable to allocate temp file for error_log capture.');
        }
        $this->error_log_file = $tmp;
        ini_set('error_log', $this->error_log_file);
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->original_error_log);
        if (file_exists($this->error_log_file)) {
            @unlink($this->error_log_file);
        }
    }

    private function captured_log(): string
    {
        clearstatcache(true, $this->error_log_file);
        return file_exists($this->error_log_file)
            ? (string) file_get_contents($this->error_log_file)
            : '';
    }

    public function test_encrypt_includes_versioned_prefix(): void
    {
        $cipher = Amcp_Crypto_Helper::encrypt('hello-world');
        $this->assertStringStartsWith('amcp_enc:', $cipher);
        $this->assertNotSame('hello-world', $cipher);
    }

    public function test_decrypt_round_trip(): void
    {
        $plain  = 'super-secret-value-123';
        $cipher = Amcp_Crypto_Helper::encrypt($plain);
        $back   = Amcp_Crypto_Helper::decrypt($cipher);

        $this->assertSame($plain, $back);
    }

    public function test_decrypt_legacy_plaintext_returned_as_is(): void
    {
        $legacy = 'not-encrypted-string';
        $this->assertSame($legacy, Amcp_Crypto_Helper::decrypt($legacy));
    }

    public function test_decrypt_empty_returns_empty(): void
    {
        $this->assertSame('', Amcp_Crypto_Helper::decrypt(''));
    }

    public function test_decrypt_corrupted_ciphertext_returns_empty(): void
    {
        // Has the prefix but the body is not valid base64 + nonce + box.
        $result = Amcp_Crypto_Helper::decrypt('amcp_enc:!!!not-base64!!!');
        $this->assertSame('', $result);
    }

    public function test_decrypt_truncated_ciphertext_returns_empty(): void
    {
        // base64 decodes fine but shorter than nonce length → empty (no throw).
        $tiny = 'amcp_enc:' . base64_encode('short');
        $this->assertSame('', Amcp_Crypto_Helper::decrypt($tiny));
    }

    public function test_decrypt_does_not_leak_key_in_log(): void
    {
        Amcp_Crypto_Helper::decrypt('amcp_enc:' . base64_encode('short'));
        $log = $this->captured_log();
        // Whatever the helper logs, it MUST NOT contain the raw key.
        $this->assertStringNotContainsString(TRUSTEED_EMBED_SECRET_KEY, $log);
    }
}
