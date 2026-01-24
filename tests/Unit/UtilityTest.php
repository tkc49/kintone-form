<?php
/**
 * Unit tests for Kintone_Form_Utility class.
 *
 * @package Kintone_Form
 */

namespace KintoneForm\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kintone_Form_Utility;
use WP_Error;

/**
 * Test class for Kintone_Form_Utility.
 */
class UtilityTest extends TestCase {

	/**
	 * Test token encoding and decoding.
	 */
	public function test_encode_and_decode_token(): void {
		$original_token = 'test-api-token-12345';

		$encoded = Kintone_Form_Utility::encode_token( $original_token );
		$decoded = Kintone_Form_Utility::decode_token( $encoded );

		$this->assertSame( $original_token, $decoded );
	}

	/**
	 * Test encode_token with empty string.
	 */
	public function test_encode_token_empty(): void {
		$this->assertSame( '', Kintone_Form_Utility::encode_token( '' ) );
	}

	/**
	 * Test decode_token with empty string.
	 */
	public function test_decode_token_empty(): void {
		$this->assertSame( '', Kintone_Form_Utility::decode_token( '' ) );
	}

	/**
	 * Test decode_token with raw (unencoded) token.
	 */
	public function test_decode_token_raw(): void {
		$raw_token = 'raw-token';
		$this->assertSame( $raw_token, Kintone_Form_Utility::decode_token( $raw_token ) );
	}

	/**
	 * Test mask_token.
	 */
	public function test_mask_token(): void {
		$token  = 'abcdefghij1234567890';
		$masked = Kintone_Form_Utility::mask_token( $token );

		// Should show last 6 characters only.
		$this->assertStringEndsWith( '567890', $masked );
		$this->assertStringStartsWith( '●', $masked );
	}

	/**
	 * Test mask_token with short token.
	 */
	public function test_mask_token_short(): void {
		$token  = 'abc';
		$masked = Kintone_Form_Utility::mask_token( $token );

		// Token <= 6 chars should be fully masked.
		$this->assertSame( '●●●', $masked );
	}

	/**
	 * Test mask_token with empty token.
	 */
	public function test_mask_token_empty(): void {
		$this->assertSame( '', Kintone_Form_Utility::mask_token( '' ) );
	}

	/**
	 * Test tokens_to_string with array.
	 */
	public function test_tokens_to_string_array(): void {
		$tokens = array( 'token1', 'token2', 'token3' );
		$result = Kintone_Form_Utility::tokens_to_string( $tokens );

		$this->assertSame( 'token1,token2,token3', $result );
	}

	/**
	 * Test tokens_to_string with single string.
	 */
	public function test_tokens_to_string_single(): void {
		$token  = 'single-token';
		$result = Kintone_Form_Utility::tokens_to_string( $token );

		$this->assertSame( $token, $result );
	}

	/**
	 * Test normalize_tokens with comma-separated string.
	 */
	public function test_normalize_tokens_string(): void {
		$tokens = 'token1, token2, token3';
		$result = Kintone_Form_Utility::normalize_tokens( $tokens );

		$this->assertSame( array( 'token1', 'token2', 'token3' ), $result );
	}

	/**
	 * Test normalize_tokens with array.
	 */
	public function test_normalize_tokens_array(): void {
		$tokens = array( 'token1', '', 'token2' );
		$result = Kintone_Form_Utility::normalize_tokens( $tokens );

		$this->assertSame( array( 'token1', 'token2' ), array_values( $result ) );
	}

	/**
	 * Test normalize_tokens with empty string.
	 */
	public function test_normalize_tokens_empty(): void {
		$this->assertSame( array(), Kintone_Form_Utility::normalize_tokens( '' ) );
	}

	/**
	 * Test get_auth_header with valid token.
	 */
	public function test_get_auth_header_valid(): void {
		$token  = 'valid-api-token';
		$result = Kintone_Form_Utility::get_auth_header( $token );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'X-Cybozu-API-Token', $result );
		$this->assertSame( $token, $result['X-Cybozu-API-Token'] );
	}

	/**
	 * Test get_auth_header with array of tokens.
	 */
	public function test_get_auth_header_array(): void {
		$tokens = array( 'token1', 'token2' );
		$result = Kintone_Form_Utility::get_auth_header( $tokens );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'X-Cybozu-API-Token', $result );
		$this->assertSame( 'token1,token2', $result['X-Cybozu-API-Token'] );
	}

	/**
	 * Test get_auth_header with empty token.
	 */
	public function test_get_auth_header_empty(): void {
		$result = Kintone_Form_Utility::get_auth_header( '' );

		$this->assertInstanceOf( WP_Error::class, $result );
	}

	/**
	 * Test get_basic_auth_header.
	 */
	public function test_get_basic_auth_header(): void {
		$result = Kintone_Form_Utility::get_basic_auth_header( 'user', 'pass' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'Authorization', $result );
		$this->assertStringStartsWith( 'Basic ', $result['Authorization'] );

		// Verify base64 encoding.
		$encoded = substr( $result['Authorization'], 6 );
		$this->assertSame( 'user:pass', base64_decode( $encoded ) );
	}

	/**
	 * Test get_basic_auth_header with missing credentials.
	 */
	public function test_get_basic_auth_header_empty(): void {
		$result = Kintone_Form_Utility::get_basic_auth_header( null, null );
		$this->assertSame( array(), $result );

		$result = Kintone_Form_Utility::get_basic_auth_header( 'user', null );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test get_kintone_url for normal space.
	 */
	public function test_get_kintone_url_normal(): void {
		$settings = array(
			'domain' => 'example.cybozu.com',
		);

		$result = Kintone_Form_Utility::get_kintone_url( $settings, 'record' );

		$this->assertSame( 'https://example.cybozu.com/k/v1/record.json', $result );
	}

	/**
	 * Test get_kintone_url for guest space.
	 */
	public function test_get_kintone_url_guest_space(): void {
		$settings = array(
			'domain'                 => 'example.cybozu.com',
			'kintone_guest_space_id' => '123',
		);

		$result = Kintone_Form_Utility::get_kintone_url( $settings, 'record' );

		$this->assertSame( 'https://example.cybozu.com/k/guest/123/v1/record.json', $result );
	}

	/**
	 * Test get_kintone_url with different types.
	 */
	public function test_get_kintone_url_types(): void {
		$settings = array(
			'domain' => 'example.cybozu.com',
		);

		$this->assertSame(
			'https://example.cybozu.com/k/v1/records.json',
			Kintone_Form_Utility::get_kintone_url( $settings, 'records' )
		);

		$this->assertSame(
			'https://example.cybozu.com/k/v1/file.json',
			Kintone_Form_Utility::get_kintone_url( $settings, 'file' )
		);
	}

	/**
	 * Test decode (legacy method).
	 */
	public function test_decode_legacy(): void {
		// Test with raw token (32 char hex like MD5).
		$md5_like = 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6';
		$this->assertSame( $md5_like, Kintone_Form_Utility::decode( $md5_like ) );

		// Test with encoded value.
		$original = 'test-value';
		$encoded  = base64_encode( md5( AUTH_SALT ) . $original . md5( md5( AUTH_SALT ) ) );
		$this->assertSame( $original, Kintone_Form_Utility::decode( $encoded ) );
	}
}
