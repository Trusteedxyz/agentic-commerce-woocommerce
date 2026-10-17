<?php

declare(strict_types=1);

/**
 * El plugin comprueba el cobro con el PSP antes de dar un pedido por pagado
 * (C-A3): lo que Trusteed afirme no basta.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-payment-verifier.php';
require_once __DIR__ . '/../../includes/class-stripe-sync.php';

final class PaymentVerifierTest extends TestCase
{
    // Clave ficticia construida por partes para no dejar un literal con forma de secreto.
    private const KEY = 'sk_' . 'test_' . 'AbCdEf0123456789AbCdEf01';

    /** @var array<int,array{url:string,args:array}> */
    private array $calls = [];
    /** @var array<string,array{status:int,body:string}> */
    private array $responses = [];
    private bool $reused = false;
    private ?string $key = self::KEY;
    private ?array $paypal = ['client_id' => 'cid', 'client_secret' => 'sec', 'mode' => 'production'];

    private function verifier(): Trusteed_Payment_Verifier
    {
        return new Trusteed_Payment_Verifier(
            function (string $url, array $args): array {
                $this->calls[] = ['url' => $url, 'args' => $args];
                return $this->responses[$url] ?? ['status' => 0, 'body' => ''];
            },
            fn(): string => (string) $this->key,
            fn(): ?array => $this->paypal,
            fn(string $ref, int $order_id): bool => $this->reused
        );
    }

    private function stripe(array $pi, int $status = 200): void
    {
        $this->responses['https://api.stripe.com/v1/payment_intents/pi_X'] = ['status' => $status, 'body' => (string) json_encode($pi)];
    }

    public function test_stripe_succeeded_same_amount_currency_is_verified(): void
    {
        $this->stripe(['status' => 'succeeded', 'amount_received' => 4999, 'currency' => 'eur']);
        $this->assertSame(['ok' => true, 'reason' => 'verified'], $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5));
        $this->assertSame('https://api.stripe.com/v1/payment_intents/pi_X', $this->calls[0]['url']);
        $this->assertSame('Bearer ' . self::KEY, $this->calls[0]['args']['headers']['Authorization']);
    }

    /** C-A2: con una clave restringida rk_ (lo único que el comercio entrega) el verificador sigue funcionando. */
    public function test_restricted_rk_key_from_read_credentials_verifies(): void
    {
        $rk = 'rk_' . 'live_' . 'AbCdEf0123456789AbCdEf01';
        $verifier = new Trusteed_Payment_Verifier(
            function (string $url, array $args): array {
                $this->calls[] = ['url' => $url, 'args' => $args];
                return $this->responses[$url] ?? ['status' => 0, 'body' => ''];
            },
            function () use ($rk): string {
                $creds = Trusteed_Stripe_Sync::read_credentials(['testmode' => 'no', 'secret_key' => $rk]);
                return null === $creds ? '' : $creds['secret_key'];
            },
            fn(): ?array => null,
            fn(string $ref, int $order_id): bool => false
        );
        $this->stripe(['status' => 'succeeded', 'amount_received' => 4999, 'currency' => 'eur']);
        $this->assertSame(['ok' => true, 'reason' => 'verified'], $verifier->verify('', 'pi_X', 4999, 'EUR', 5));
        $this->assertSame('Bearer ' . $rk, $this->calls[0]['args']['headers']['Authorization']);
    }

    public function test_stripe_requires_capture_is_not_succeeded(): void
    {
        $this->stripe(['status' => 'requires_capture', 'amount_received' => 0, 'currency' => 'eur']);
        $this->assertSame('not_succeeded', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
    }

    public function test_stripe_amount_received_differs_is_amount_mismatch(): void
    {
        $this->stripe(['status' => 'succeeded', 'amount_received' => 4998, 'currency' => 'eur']);
        $r = $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5);
        $this->assertFalse($r['ok']);
        $this->assertSame('amount_mismatch', $r['reason']);
    }

    public function test_stripe_currency_differs_is_currency_mismatch(): void
    {
        $this->stripe(['status' => 'succeeded', 'amount_received' => 4999, 'currency' => 'usd']);
        $this->assertSame('currency_mismatch', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
    }

    public function test_reference_used_by_other_order_is_reused(): void
    {
        $this->reused = true;
        $this->stripe(['status' => 'succeeded', 'amount_received' => 4999, 'currency' => 'eur']);
        $this->assertSame('reference_reused', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
        $this->assertSame([], $this->calls);
    }

    public function test_stripe_500_or_timeout_is_psp_unreachable(): void
    {
        $this->stripe([], 500);
        $this->assertSame('psp_unreachable', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
        $this->responses = []; // transporte caído: status 0
        $this->assertSame('psp_unreachable', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
    }

    public function test_stripe_body_not_json_is_psp_unreachable(): void
    {
        $this->responses['https://api.stripe.com/v1/payment_intents/pi_X'] = ['status' => 200, 'body' => '<html>'];
        $this->assertSame('psp_unreachable', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
    }

    public function test_stripe_401_is_psp_rejected(): void
    {
        $this->stripe(['error' => ['type' => 'authentication_error']], 401);
        $this->assertSame('psp_rejected', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
    }

    public function test_no_stripe_key_is_no_credentials(): void
    {
        $this->key = '';
        $this->assertSame('no_credentials', $this->verifier()->verify('', 'pi_X', 4999, 'EUR', 5)['reason']);
        $this->assertSame([], $this->calls);
    }

    public function test_reference_with_path_characters_is_rejected_without_request(): void
    {
        $this->assertSame('psp_rejected', $this->verifier()->verify('', 'pi_X/../charges', 4999, 'EUR', 5)['reason']);
        $this->assertSame([], $this->calls);
    }

    private function paypal(string $status, string $value = '49.99', string $cur = 'EUR'): void
    {
        $this->responses['https://api-m.paypal.com/v1/oauth2/token'] = ['status' => 200, 'body' => '{"access_token":"tok"}'];
        $this->responses['https://api-m.paypal.com/v2/payments/captures/8TW16685UK546374C'] = [
            'status' => 200,
            'body'   => (string) json_encode(['status' => $status, 'amount' => ['value' => $value, 'currency_code' => $cur]]),
        ];
    }

    public function test_paypal_completed_capture_is_verified(): void
    {
        $this->paypal('COMPLETED');
        $this->assertSame(['ok' => true, 'reason' => 'verified'], $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5));
        $this->assertSame('POST', $this->calls[0]['args']['method']);
        $this->assertSame('Bearer tok', $this->calls[1]['args']['headers']['Authorization']);
    }

    public function test_paypal_sandbox_credentials_use_the_sandbox_host(): void
    {
        $this->paypal('COMPLETED');
        $this->paypal = ['client_id' => 'cid', 'client_secret' => 'sec', 'mode' => 'sandbox'];
        $this->responses['https://api-m.sandbox.paypal.com/v1/oauth2/token'] = ['status' => 200, 'body' => '{"access_token":"tok"}'];
        $this->responses['https://api-m.sandbox.paypal.com/v2/payments/captures/8TW16685UK546374C'] = $this->responses['https://api-m.paypal.com/v2/payments/captures/8TW16685UK546374C'];
        $this->assertTrue($this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['ok']);
        $this->assertStringContainsString('sandbox', $this->calls[0]['url']);
    }

    public function test_paypal_pending_capture_is_not_succeeded(): void
    {
        $this->paypal('PENDING');
        $this->assertSame('not_succeeded', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
    }

    public function test_paypal_amount_and_currency_mismatches(): void
    {
        $this->paypal('COMPLETED', '49.98');
        $this->assertSame('amount_mismatch', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
        $this->paypal('COMPLETED', '49.99', 'USD');
        $this->assertSame('currency_mismatch', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
    }

    public function test_paypal_value_with_too_many_decimals_is_amount_mismatch(): void
    {
        $this->paypal('COMPLETED', '49.9900001');
        $this->assertSame('amount_mismatch', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
    }

    public function test_paypal_without_credentials_or_token_failure(): void
    {
        $this->paypal('COMPLETED');
        $this->paypal = null;
        $this->assertSame('no_credentials', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
        $this->paypal = ['client_id' => 'cid', 'client_secret' => 'sec', 'mode' => 'production'];
        $this->responses['https://api-m.paypal.com/v1/oauth2/token'] = ['status' => 401, 'body' => '{}'];
        $this->assertSame('psp_rejected', $this->verifier()->verify('paypal', '8TW16685UK546374C', 4999, 'EUR', 5)['reason']);
    }

    public function test_unknown_method_is_rejected(): void
    {
        $this->assertSame('psp_rejected', $this->verifier()->verify('bitcoin', 'pi_X', 4999, 'EUR', 5)['reason']);
    }
}
