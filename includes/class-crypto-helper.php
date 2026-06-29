<?php
/**
 * Crypto Helper — symmetric encryption for sensitive WP options.
 *
 * S039-SEC-004: sensitive secrets stored in wp_options (embed S2S secret,
 * Trusteed API key) must not live in plaintext. This helper encrypts/decrypts
 * using sodium_crypto_secretbox (XSalsa20-Poly1305) with a 32-byte key.
 *
 * The key is resolved automatically (see get_key()): it prefers an explicit
 * TRUSTEED_EMBED_SECRET_KEY constant, and otherwise derives a stable key from
 * this install's WordPress salts. At-rest encryption is therefore active out of
 * the box with no operator step. For the strongest guarantee (key shared across
 * a fleet, or sourced from a KMS) define the constant in wp-config.php:
 *   define( 'TRUSTEED_EMBED_SECRET_KEY', base64_encode( random_bytes( 32 ) ) );
 *
 * Existing plaintext values (legacy installs) are returned as-is by decrypt()
 * and re-encrypted on the next save. Only if no key material exists at all
 * (no salts, no constant) do methods fall back to plaintext + a logged notice.
 *
 * @package Trusteed
 * @since   1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Amcp_Crypto_Helper {

	/**
	 * Prefix prepended to every ciphertext stored in the DB.
	 * Used to distinguish encrypted values from plaintext legacy values.
	 */
	private const CIPHERTEXT_PREFIX = 'amcp_enc:';

	/**
	 * Expected byte length of the symmetric key (libsodium secretbox).
	 */
	private const KEY_BYTES = SODIUM_CRYPTO_SECRETBOX_KEYBYTES; // 32

	/**
	 * Encrypt a plaintext string using sodium_crypto_secretbox.
	 *
	 * Returns the encrypted value prefixed with `amcp_enc:` so decrypt() can
	 * distinguish ciphertext from legacy plaintext values.
	 * Returns the original $plaintext unchanged if no valid key is configured.
	 *
	 * @since 1.2.0
	 *
	 * @param string $plaintext Value to encrypt.
	 * @return string Encrypted (base64) string or original plaintext on error.
	 */
	public static function encrypt( string $plaintext ): string {
		$key = self::get_key();
		if ( null === $key ) {
			self::log_key_warning();
			return $plaintext;
		}

		$nonce      = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = sodium_crypto_secretbox( $plaintext, $nonce, $key );
		sodium_memzero( $key );

		return self::CIPHERTEXT_PREFIX . base64_encode( $nonce . $ciphertext );
	}

	/**
	 * Decrypt a value previously encrypted by encrypt().
	 *
	 * Handles three cases:
	 *  1. Ciphertext (starts with `amcp_enc:`) + valid key → decrypted plaintext.
	 *  2. Ciphertext + no/invalid key → empty string (cannot decrypt).
	 *  3. Plaintext (legacy or no key on encrypt) → returned as-is.
	 *
	 * @since 1.2.0
	 *
	 * @param string $stored Value retrieved from the DB.
	 * @return string Decrypted value, or empty string on decryption failure.
	 */
	public static function decrypt( string $stored ): string {
		if ( ! str_starts_with( $stored, self::CIPHERTEXT_PREFIX ) ) {
			// Legacy plaintext — return unchanged.
			return $stored;
		}

		$key = self::get_key();
		if ( null === $key ) {
			self::log_key_warning();
			return ''; // Cannot decrypt without key.
		}

		$raw = base64_decode( substr( $stored, strlen( self::CIPHERTEXT_PREFIX ) ), true );
		if ( false === $raw || strlen( $raw ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
			sodium_memzero( $key );
			return ''; // Corrupted ciphertext.
		}

		$nonce      = substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$plaintext  = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );
		sodium_memzero( $key );

		return false === $plaintext ? '' : $plaintext;
	}

	/**
	 * Resolve the 32-byte symmetric encryption key.
	 *
	 * Resolution order:
	 *  1. TRUSTEED_EMBED_SECRET_KEY constant (explicit operator key — strongest:
	 *     lives outside the DB and can be shared across a multisite/staging
	 *     fleet or sourced from an external KMS).
	 *  2. Zero-config fallback: a key deterministically derived from this
	 *     install's WordPress salts via HKDF. On standard installs the salts are
	 *     defined as constants in wp-config.php, so the derived key never touches
	 *     the database. This makes at-rest encryption active out of the box with
	 *     no operator step. The derivation is stable, so the same install always
	 *     reproduces the same key (required to decrypt previously stored values).
	 *     Note: rotating the WordPress salts invalidates existing ciphertext and
	 *     requires re-connecting the store.
	 *
	 * @since 1.2.0
	 *
	 * @return string|null 32-byte raw key, or null if no key material exists.
	 */
	private static function get_key(): ?string {
		if ( defined( 'TRUSTEED_EMBED_SECRET_KEY' ) ) {
			$decoded = base64_decode( TRUSTEED_EMBED_SECRET_KEY, true );
			if ( false !== $decoded && strlen( $decoded ) === self::KEY_BYTES ) {
				return $decoded;
			}
		}

		if ( function_exists( 'wp_salt' ) ) {
			$salt = (string) wp_salt( 'secure_auth' );
			if ( '' !== $salt ) {
				return hash_hkdf( 'sha256', $salt, self::KEY_BYTES, 'trusteed-embed-secret-v1' );
			}
		}

		return null;
	}

	/**
	 * Log a notice when no key material is available at all.
	 *
	 * With salt-derived keys this should never fire on a real WordPress install;
	 * it remains as a defensive guard for environments where wp_salt() yields no
	 * material (e.g. a heavily stubbed bootstrap).
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	private static function log_key_warning(): void {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions
		error_log(
			'[trusteed] S039-SEC-004: no encryption key material available (no WordPress salts and no ' .
			'TRUSTEED_EMBED_SECRET_KEY constant). Secrets are stored as plaintext. ' .
			'Define a key: define("TRUSTEED_EMBED_SECRET_KEY", base64_encode(random_bytes(32)));'
		);
	}
}
