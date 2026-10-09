<?php
/**
 * Signed rule-snapshot payload — decoding AFTER the signature verified
 * (plan 04, Task 24).
 *
 * The rules service compresses with raw DEFLATE (RFC 1951) every snapshot
 * whose JSON exceeds 50 KB and marks it `zip: "DEF"` in the protected header.
 * The signature covers the COMPRESSED bytes: verify first, then call this.
 * Nothing is ever inflated before the signature verified.
 *
 * Contract (section `compressedSnapshot` of
 * packages/shared/src/enforcement/__fixtures__/platform-flow-conformance.json):
 * no `zip` ⇒ the JSON as is; `zip` === 'DEF' exactly ⇒ inflate with a cap of
 * MAX_INFLATED_BYTES; any other `zip`, a corrupt/truncated/empty stream, going
 * over the cap, or an output that is not a JSON object ⇒ not usable.
 *
 * Anti-bomb: inflate_init(ZLIB_ENCODING_RAW) + inflate_add() fed
 * INPUT_CHUNK_BYTES at a time. DEFLATE expands at most ~1032:1, so each step
 * yields at most ~66 KB and the accumulated output is compared with the cap
 * after every step: memory never goes past the cap plus one step, whatever
 * size the bomb declares. (gzinflate( $d, $max ) is not used: its limit is
 * approximate — it returns one byte over the cap — and it cannot tell a bomb
 * from a corrupt stream.)
 *
 * @package Trusteed
 * @since   2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Snapshot_Payload_Decoder
 */
final class Trusteed_Snapshot_Payload_Decoder {

	const ZIP_DEFLATE        = 'DEF';
	const MAX_INFLATED_BYTES = 2097152;
	const INPUT_CHUNK_BYTES  = 64;

	const OK               = 'ok';
	const ZIP_UNSUPPORTED  = 'zip_unsupported';
	const INFLATE_FAILED   = 'inflate_failed';
	const INFLATE_OVER_CAP = 'inflate_over_cap';
	const PAYLOAD_NOT_JSON = 'payload_not_json';

	/**
	 * Does the header declare any `zip`? (With it, every failure is "not usable".)
	 *
	 * @param array $header Parsed protected header.
	 * @return bool
	 */
	public static function declares_zip( array $header ): bool {
		return array_key_exists( 'zip', $header );
	}

	/**
	 * @param array  $header        Parsed protected header.
	 * @param string $payload_bytes Payload segment after base64url (the signed bytes).
	 * @return array{0: string, 1: ?array} [OK or reason, payload or null]
	 */
	public static function decode( array $header, string $payload_bytes ): array {
		$json = $payload_bytes;
		if ( self::declares_zip( $header ) ) {
			if ( self::ZIP_DEFLATE !== $header['zip'] ) {
				return array( self::ZIP_UNSUPPORTED, null );
			}
			list( $status, $inflated ) = self::inflate_capped( $payload_bytes );
			if ( self::OK !== $status ) {
				return array( $status, null );
			}
			$json = (string) $inflated;
		}

		// A JSON object (not a list nor a scalar): it starts with '{'.
		$trimmed = ltrim( $json );
		$payload = json_decode( $json, true );
		if ( ! is_array( $payload ) || '' === $trimmed || '{' !== $trimmed[0] ) {
			return array( self::PAYLOAD_NOT_JSON, null );
		}
		return array( self::OK, $payload );
	}

	/**
	 * @param string $bytes Raw DEFLATE stream.
	 * @return array{0: string, 1: ?string}
	 */
	private static function inflate_capped( string $bytes ): array {
		if ( '' === $bytes || ! function_exists( 'inflate_init' ) ) {
			return array( self::INFLATE_FAILED, null );
		}
		$ctx = @inflate_init( ZLIB_ENCODING_RAW ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( false === $ctx ) {
			return array( self::INFLATE_FAILED, null );
		}
		$out = '';
		$len = strlen( $bytes );
		for ( $off = 0; $off < $len; $off += self::INPUT_CHUNK_BYTES ) {
			$chunk = @inflate_add( $ctx, substr( $bytes, $off, self::INPUT_CHUNK_BYTES ), ZLIB_SYNC_FLUSH ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
			if ( false === $chunk ) {
				return array( self::INFLATE_FAILED, null );
			}
			$out .= $chunk;
			if ( strlen( $out ) > self::MAX_INFLATED_BYTES ) {
				return array( self::INFLATE_OVER_CAP, null );
			}
			if ( ZLIB_STREAM_END === inflate_get_status( $ctx ) ) {
				break;
			}
		}
		if ( ZLIB_STREAM_END !== inflate_get_status( $ctx ) ) {
			return array( self::INFLATE_FAILED, null );
		}
		return array( self::OK, $out );
	}
}
