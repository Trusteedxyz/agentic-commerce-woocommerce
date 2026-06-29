<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap for WooCommerce plugin unit tests.
 *
 * Defines the ABSPATH constant required by all plugin includes and loads
 * the classes under test. No WordPress runtime needed.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Canonical WP function stubs shared across all test files. Loaded FIRST so
// these deterministic definitions win over the per-file `function_exists`
// guards, eliminating order-dependent cross-file stub collisions.
require_once __DIR__ . '/wp-stubs.php';

require_once __DIR__ . '/../../includes/class-token-verifier.php';
require_once __DIR__ . '/../../includes/class-snapshot-client-woo.php';
