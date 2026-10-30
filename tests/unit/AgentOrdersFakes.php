<?php

declare(strict_types=1);

/**
 * Dobles de prueba de los endpoints de pedidos de agente (plan 2026-10-05, Fase A).
 *
 * Sin WordPress: una petición REST mínima, una respuesta REST mínima y un
 * «puerto Woo» en memoria. El puerto real (Trusteed_Agent_Woo_Adapter) habla
 * con WooCommerce y NO se puede ejecutar aquí; lo que se prueba en este fichero
 * es la decisión que toma el endpoint ENCIMA del puerto.
 */

if (!class_exists('WP_REST_Response')) {
    class WP_REST_Response
    {
        /** @var mixed */
        private $data;
        private int $status;

        public function __construct($data = null, int $status = 200)
        {
            $this->data   = $data;
            $this->status = $status;
        }

        public function get_data()
        {
            return $this->data;
        }

        public function get_status(): int
        {
            return $this->status;
        }
    }
}

final class Trusteed_Test_Rest_Request
{
    /** @var array<string,string> */
    private array $headers;
    private string $body;
    /** @var array<string,mixed> */
    private array $params;

    /**
     * @param array<string,string> $headers
     * @param array<string,mixed>  $params
     */
    public function __construct(string $body, array $headers = [], array $params = [])
    {
        $this->body    = $body;
        $this->headers = $headers;
        $this->params  = $params;
    }

    public function get_body(): string
    {
        return $this->body;
    }

    public function get_header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function get_param(string $name)
    {
        return $this->params[$name] ?? null;
    }
}

/** Puerto Woo en memoria: registra las llamadas y devuelve lo que el test dicte. */
final class Trusteed_Test_Woo_Port
{
    /** @var array<int,array<string,mixed>> */
    public array $orders = [];
    /** @var array<int,string> */
    public array $calls = [];
    public int $next_id = 100;
    /** @var array<string,mixed>|\WP_Error */
    public $quote_result;
    public string $rules_decision = 'ALLOW';
    /** @var array<string,string>|null */
    public ?array $rules_agent_reason = null;
    public ?string $rules_agent_did = null;
    public ?string $rules_identity_level = null;
    /** Enlace de pago que el adaptador real pondria en un pedido pending; null = ninguno. */
    public ?string $payment_url = null;

    public function __construct()
    {
        $this->quote_result = self::default_quote();
    }

    /** @return array<string,mixed> */
    public static function default_quote(): array
    {
        return [
            'currency'       => 'EUR',
            'subtotal_cents' => 5000,
            'discount_cents' => 0,
            'shipping_cents' => 500,
            'tax_cents'      => 1155,
            'total_cents'    => 6655,
            'lines'          => [['product_id' => 12, 'variation_id' => 0, 'qty' => 2, 'line_total_cents' => 5000]],
            'shipping'       => [
                'chosen' => ['id' => 'flat_rate:1', 'label' => 'Envio', 'cost_cents' => 500],
                'rates'  => [['id' => 'flat_rate:1', 'label' => 'Envio', 'cost_cents' => 500]],
            ],
        ];
    }

    public function quote(array $input)
    {
        $this->calls[] = 'quote';
        return $this->quote_result;
    }

    public function find_order_by_trusteed_id(string $trusteed_order_id): ?array
    {
        $this->calls[] = 'find';
        foreach ($this->orders as $o) {
            if (($o['trusteed_order_id'] ?? null) === $trusteed_order_id) {
                return $o;
            }
        }
        return null;
    }

    public function get_order(int $id): ?array
    {
        return $this->orders[$id] ?? null;
    }

    public function create_order(array $input)
    {
        $this->calls[] = 'create';
        $id = $this->next_id++;
        $this->orders[$id] = [
            'id'                => $id,
            'number'            => (string) $id,
            'status'            => 'pending',
            'total_cents'       => (int) ($this->quote_result['total_cents'] ?? 0),
            'currency'          => 'EUR',
            'trusteed_order_id' => (string) $input['trusteed_order_id'],
            'is_paid'           => false,
            'transaction_id'    => '',
        ];
        if (null !== $this->payment_url) {
            $this->orders[$id]['payment_url'] = $this->payment_url;
        }
        return $this->orders[$id];
    }

    public function evaluate_rules(int $order_id, ?string $agent_did, ?string $identity_level = null): string
    {
        $this->calls[] = 'rules';
        $this->rules_agent_did = $agent_did;
        $this->rules_identity_level = $identity_level;
        return $this->rules_decision;
    }

    public function last_agent_reason(): ?array
    {
        return $this->rules_agent_reason;
    }

    public function delete_order(int $order_id): void
    {
        $this->calls[] = 'delete';
        unset($this->orders[$order_id]);
    }

    public function complete_payment(int $order_id, string $payment_reference, string $payment_method = ''): void
    {
        $this->calls[] = 'complete';
        $this->orders[$order_id]['payment_method'] = $payment_method;
        $this->orders[$order_id]['is_paid']        = true;
        $this->orders[$order_id]['status']         = 'processing';
        $this->orders[$order_id]['transaction_id'] = $payment_reference;
    }

    public function hold_for_review(int $order_id, string $note): void
    {
        $this->calls[] = 'hold';
        $this->orders[$order_id]['status'] = 'on-hold';
        $this->orders[$order_id]['note']   = $note;
    }
}
