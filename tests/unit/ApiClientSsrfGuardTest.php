<?php

declare(strict_types=1);

/**
 * SSRF / credential-egress policy of Trusteed_Api_Client::require_secure_base().
 *
 * Every request() carries the `X-AgenticMCP-Key` credential, so the API base
 * host decides where that secret travels. README.md advertises "an exact host
 * allowlist and RFC1918 / IPv6 ULA / cloud IMDS blocklists"; these tests pin
 * that contract:
 *
 *   - Production (no dev opt-in): ONLY https + an exact ALLOWED_API_HOSTS match.
 *     Loopback and RFC1918 are rejected, because in production a private-range
 *     base URL means the credential is being redirected at internal infra.
 *   - Dev opt-in (`TRUSTEED_ALLOW_LOCAL_API_BASE` or environment type `local`):
 *     loopback + RFC1918 tolerated over plain HTTP so the local API keeps working.
 *   - Never, in ANY mode: cloud instance-metadata endpoints (169.254.0.0/16,
 *     Alibaba 100.100.100.200, metadata.google.internal) or IPv6 unique-local
 *     fc00::/7 and link-local fe80::/10. These are pure SSRF pivots with no
 *     legitimate use as a Trusteed API base.
 *
 * Why process isolation: tests/unit/CatalogSyncErrorHandlingTest.php declares a
 * *stub* `Trusteed_Api_Client`, so the real class cannot be required at file
 * scope without one of the two files losing (PHPUnit loads every test file into
 * one process). Requiring the real class inside setUp() of an isolated process
 * keeps both suites honest.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ApiClientSsrfGuardTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TRUSTEED_API_BASE')) {
            define('TRUSTEED_API_BASE', 'https://api.trusteed.xyz');
        }

        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url)
            {
                return (string) $url;
            }
        }
        if (!function_exists('untrailingslashit')) {
            function untrailingslashit($s)
            {
                return rtrim((string) $s, '/');
            }
        }
        if (!function_exists('wp_parse_url')) {
            function wp_parse_url($url, $component = -1)
            {
                return parse_url((string) $url, $component);
            }
        }
        if (!function_exists('__')) {
            function __($text, $domain = null)
            {
                return $text;
            }
        }
        // WP_Error itself is declared in tests/unit/wp-stubs.php (loaded by
        // bootstrap.php in both the parent and the isolated child process).
        if (!function_exists('wp_get_environment_type')) {
            function wp_get_environment_type(): string
            {
                return $GLOBALS['__trusteed_env_type'] ?? 'production';
            }
        }

        $GLOBALS['__trusteed_env_type'] = 'production';

        require_once __DIR__ . '/../../includes/class-api-client.php';
    }

    /**
     * Invoke the private policy method with a given API base.
     *
     * @return true|WP_Error
     */
    private function guard(string $apiBase)
    {
        $client = new Trusteed_Api_Client('test-key', $apiBase, 15);
        $method = new ReflectionMethod(Trusteed_Api_Client::class, 'require_secure_base');
        $method->setAccessible(true);

        return $method->invoke($client);
    }

    private function assertAllowed(string $apiBase): void
    {
        $result = $this->guard($apiBase);
        $code   = $result instanceof WP_Error ? $result->get_error_code() : '';
        $this->assertTrue($result, "expected {$apiBase} to be allowed, got WP_Error({$code})");
    }

    private function assertRejected(string $apiBase, string $expectedCode = ''): void
    {
        $result = $this->guard($apiBase);
        $this->assertInstanceOf(
            WP_Error::class,
            $result,
            "expected {$apiBase} to be REJECTED, but the guard allowed it"
        );
        if ('' !== $expectedCode) {
            $this->assertSame($expectedCode, $result->get_error_code());
        }
    }

    // -------------------------------------------------------------------------
    // Production allowlist (pre-existing behaviour — regression pins)
    // -------------------------------------------------------------------------

    public function testAllowsHttpsTrusteedOwnedHost(): void
    {
        $this->assertAllowed('https://api.trusteed.xyz');
        $this->assertAllowed('https://staging-api.trusteed.xyz');
    }

    public function testRejectsSuffixLookalikeHost(): void
    {
        $this->assertRejected('https://trusteed.xyz.evil.com', 'agenticmcp_untrusted_api_base');
    }

    public function testRejectsPlainHttpEvenForAllowlistedHost(): void
    {
        $this->assertRejected('http://api.trusteed.xyz', 'agenticmcp_insecure_api_base');
    }

    // -------------------------------------------------------------------------
    // Production must NOT tolerate loopback / RFC1918 without a dev opt-in
    // -------------------------------------------------------------------------

    public function testRejectsRfc1918InProduction(): void
    {
        $this->assertRejected('http://10.0.0.5:3000');
        $this->assertRejected('http://172.16.4.1:3000');
        $this->assertRejected('http://192.168.1.10:3000');
        $this->assertRejected('https://10.0.0.5');
    }

    public function testRejectsLoopbackInProduction(): void
    {
        $this->assertRejected('http://localhost:3000');
        $this->assertRejected('http://127.0.0.1:3000');
        $this->assertRejected('http://[::1]:3000');
        $this->assertRejected('http://host.docker.internal:3000');
    }

    // -------------------------------------------------------------------------
    // Blocklist — rejected in production AND under the dev opt-in
    // -------------------------------------------------------------------------

    public function testRejectsCloudImdsInProduction(): void
    {
        $this->assertRejected('http://169.254.169.254', 'trusteed_blocked_api_base');
        $this->assertRejected('https://169.254.170.2', 'trusteed_blocked_api_base');
        $this->assertRejected('http://100.100.100.200', 'trusteed_blocked_api_base');
        $this->assertRejected('http://metadata.google.internal', 'trusteed_blocked_api_base');
    }

    public function testRejectsCloudImdsEvenWhenLocalDevIsEnabled(): void
    {
        $GLOBALS['__trusteed_env_type'] = 'local';

        $this->assertRejected('http://169.254.169.254', 'trusteed_blocked_api_base');
        $this->assertRejected('http://100.100.100.200', 'trusteed_blocked_api_base');
        $this->assertRejected('http://metadata.google.internal', 'trusteed_blocked_api_base');
    }

    public function testRejectsIpv6UniqueLocalAndLinkLocalEvenWhenLocalDevIsEnabled(): void
    {
        $GLOBALS['__trusteed_env_type'] = 'local';

        // fc00::/7 unique-local (covers fc00::/8 and fd00::/8).
        $this->assertRejected('http://[fd00::1]:3000', 'trusteed_blocked_api_base');
        $this->assertRejected('https://[fdab:1234::9]', 'trusteed_blocked_api_base');
        $this->assertRejected('http://[fc00::1]', 'trusteed_blocked_api_base');
        // fe80::/10 link-local (the IPv6 face of IMDS).
        $this->assertRejected('http://[fe80::1]', 'trusteed_blocked_api_base');
        $this->assertRejected('http://[febf::1]', 'trusteed_blocked_api_base');
    }

    /**
     * IPv4-mapped IPv6 (`::ffff:169.254.169.254`) is not caught by the blocklist
     * regexes, so pin that it is still refused — by falling off the end of the
     * allowlist rather than by name. This documents the guard as fail-closed:
     * an address form nobody enumerated is rejected, not allowed.
     */
    public function testIpv4MappedIpv6MetadataIsStillRefusedUnderLocalDev(): void
    {
        $GLOBALS['__trusteed_env_type'] = 'local';

        $this->assertRejected('http://[::ffff:169.254.169.254]');
        $this->assertRejected('http://[::ffff:10.0.0.5]');
    }

    // -------------------------------------------------------------------------
    // Dev opt-in paths
    // -------------------------------------------------------------------------

    public function testEnvironmentTypeLocalReenablesLoopbackAndRfc1918(): void
    {
        $GLOBALS['__trusteed_env_type'] = 'local';

        $this->assertAllowed('http://localhost:3000');
        $this->assertAllowed('http://127.0.0.1:3000');
        $this->assertAllowed('http://[::1]:3000');
        $this->assertAllowed('http://host.docker.internal:3000');
        $this->assertAllowed('http://10.0.0.5:3000');
        $this->assertAllowed('http://192.168.1.10:3000');
    }

    public function testExplicitConstantReenablesLoopbackInNonLocalEnvironment(): void
    {
        define('TRUSTEED_ALLOW_LOCAL_API_BASE', true);
        $GLOBALS['__trusteed_env_type'] = 'production';

        $this->assertAllowed('http://localhost:3000');
        $this->assertAllowed('http://10.0.0.5:3000');
        // The blocklist still wins over the explicit opt-in.
        $this->assertRejected('http://169.254.169.254', 'trusteed_blocked_api_base');
    }

    public function testExplicitConstantSetToFalseOverridesLocalEnvironment(): void
    {
        define('TRUSTEED_ALLOW_LOCAL_API_BASE', false);
        $GLOBALS['__trusteed_env_type'] = 'local';

        $this->assertRejected('http://localhost:3000');
        $this->assertRejected('http://10.0.0.5:3000');
    }
}
