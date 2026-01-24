<?php
/**
 * Unit tests for kintone form modules.
 *
 * These tests focus on the data transformation logic in isolation,
 * using mocks for WordPress and Contact Form 7 dependencies.
 *
 * @package Kintone_Form
 */

namespace KintoneForm\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WP_Error;

/**
 * Test class for kintone form modules.
 */
class ModulesTest extends TestCase {

	/**
	 * Whether modules have been loaded.
	 *
	 * @var bool
	 */
	private static $modulesLoaded = false;

	/**
	 * Set up test class.
	 */
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		if ( self::$modulesLoaded ) {
			return;
		}

		// Load mocks first.
		require_once __DIR__ . '/helpers/wp-mocks.php';
		require_once __DIR__ . '/helpers/kintone-form-mock.php';

		// Now load plugin files.
		require_once KINTONE_FORM_PATH . '/includes/check-acceptance.php';
		require_once KINTONE_FORM_PATH . '/modules/text.php';
		require_once KINTONE_FORM_PATH . '/modules/number.php';
		require_once KINTONE_FORM_PATH . '/modules/checkbox.php';
		require_once KINTONE_FORM_PATH . '/modules/multi_select.php';
		require_once KINTONE_FORM_PATH . '/modules/dropdown.php';
		require_once KINTONE_FORM_PATH . '/modules/radio.php';

		self::$modulesLoaded = true;
	}

	/**
	 * Test KintoneFormText with simple text value.
	 */
	public function test_text_module_simple_value(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-text' => 'Hello World' );
		$cf7_mail_tag      = 'your-text';
		$e                 = new WP_Error();

		$result = \KintoneFormText::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertArrayHasKey( 'value', $result );
		$this->assertSame( 'Hello World', $result['value'] );
		$this->assertFalse( $e->has_errors() );
	}

	/**
	 * Test KintoneFormText with array value (joins with comma).
	 */
	public function test_text_module_array_value(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-text' => array( 'Value1', 'Value2' ) );
		$cf7_mail_tag      = 'your-text';
		$e                 = new WP_Error();

		$result = \KintoneFormText::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertSame( 'Value1,Value2', $result['value'] );
	}

	/**
	 * Test KintoneFormText with minLength validation.
	 */
	public function test_text_module_min_length_error(): void {
		$kintone_form_data = array(
			'code'      => 'field_code',
			'minLength' => 10,
		);
		$cf7_send_data     = array( 'your-text' => 'Short' );
		$cf7_mail_tag      = 'your-text';
		$e                 = new WP_Error();

		$result = \KintoneFormText::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertTrue( $e->has_errors() );
		$messages = $e->get_error_messages( 'Error' );
		$this->assertStringContainsString( 'Minimum value error', $messages[0] );
	}

	/**
	 * Test KintoneFormText with maxLength validation.
	 */
	public function test_text_module_max_length_error(): void {
		$kintone_form_data = array(
			'code'      => 'field_code',
			'maxLength' => 5,
		);
		$cf7_send_data     = array( 'your-text' => 'Too Long Text' );
		$cf7_mail_tag      = 'your-text';
		$e                 = new WP_Error();

		$result = \KintoneFormText::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertTrue( $e->has_errors() );
		$messages = $e->get_error_messages( 'Error' );
		$this->assertStringContainsString( 'Maximum value error', $messages[0] );
	}

	/**
	 * Test KintoneFormText with missing field (returns empty).
	 */
	public function test_text_module_missing_field(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array();
		$cf7_mail_tag      = 'nonexistent-field';
		$e                 = new WP_Error();

		$result = \KintoneFormText::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertSame( '', $result['value'] );
	}

	/**
	 * Test KintoneFormNumber with valid number.
	 */
	public function test_number_module_valid(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-number' => '12345' );
		$cf7_mail_tag      = 'your-number';
		$e                 = new WP_Error();

		$result = \KintoneFormNumber::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertSame( '12345', $result['value'] );
		$this->assertFalse( $e->has_errors() );
	}

	/**
	 * Test KintoneFormNumber with full-width numbers (converts to half-width).
	 */
	public function test_number_module_fullwidth(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-number' => '１２３４５' );
		$cf7_mail_tag      = 'your-number';
		$e                 = new WP_Error();

		$result = \KintoneFormNumber::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		// Full-width numbers should be converted to half-width.
		$this->assertSame( '12345', $result['value'] );
	}

	/**
	 * Test KintoneFormNumber with non-numeric value.
	 */
	public function test_number_module_non_numeric_error(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-number' => 'not a number' );
		$cf7_mail_tag      = 'your-number';
		$e                 = new WP_Error();

		$result = \KintoneFormNumber::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertTrue( $e->has_errors() );
		$messages = $e->get_error_messages( 'Error' );
		$this->assertStringContainsString( 'Numeric format error', $messages[0] );
	}

	/**
	 * Test KintoneFormNumber with min value validation.
	 */
	public function test_number_module_min_value_error(): void {
		$kintone_form_data = array(
			'code'     => 'field_code',
			'minValue' => 100,
		);
		$cf7_send_data     = array( 'your-number' => '50' );
		$cf7_mail_tag      = 'your-number';
		$e                 = new WP_Error();

		$result = \KintoneFormNumber::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertTrue( $e->has_errors() );
	}

	/**
	 * Test KintoneFormNumber with empty value (no error).
	 */
	public function test_number_module_empty(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-number' => '' );
		$cf7_mail_tag      = 'your-number';
		$e                 = new WP_Error();

		$result = \KintoneFormNumber::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertSame( '', $result['value'] );
		$this->assertFalse( $e->has_errors() );
	}

	/**
	 * Test KintoneFormCheckbox with array value.
	 */
	public function test_checkbox_module_array(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-checkbox' => array( 'Option1', 'Option2' ) );
		$cf7_mail_tag      = 'your-checkbox';
		$e                 = new WP_Error();

		$result = \KintoneFormCheckbox::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertIsArray( $result['value'] );
		$this->assertSame( array( 'Option1', 'Option2' ), $result['value'] );
	}

	/**
	 * Test KintoneFormCheckbox with empty selection (returns empty array).
	 */
	public function test_checkbox_module_empty(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-checkbox' => '' );
		$cf7_mail_tag      = 'your-checkbox';
		$e                 = new WP_Error();

		$result = \KintoneFormCheckbox::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertIsArray( $result['value'] );
		$this->assertEmpty( $result['value'] );
	}

	/**
	 * Test KintoneForm_multi_select with array value.
	 */
	public function test_multi_select_module_array(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-multi' => array( 'Item1', 'Item2', 'Item3' ) );
		$cf7_mail_tag      = 'your-multi';
		$e                 = new WP_Error();

		$result = \KintoneForm_multi_select::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertIsArray( $result['value'] );
		$this->assertSame( array( 'Item1', 'Item2', 'Item3' ), $result['value'] );
	}

	/**
	 * Test KintoneForm_multi_select with empty selection (returns empty array).
	 * This tests the fix for the empty multi-select bug.
	 */
	public function test_multi_select_module_empty(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array( 'your-multi' => '' );
		$cf7_mail_tag      = 'your-multi';
		$e                 = new WP_Error();

		$result = \KintoneForm_multi_select::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertIsArray( $result['value'] );
		$this->assertEmpty( $result['value'] );
	}

	/**
	 * Test KintoneForm_multi_select with missing field.
	 */
	public function test_multi_select_module_missing_field(): void {
		$kintone_form_data = array( 'code' => 'field_code' );
		$cf7_send_data     = array();
		$cf7_mail_tag      = 'nonexistent-field';
		$e                 = new WP_Error();

		$result = \KintoneForm_multi_select::format_to_kintone_data(
			$kintone_form_data,
			$cf7_send_data,
			$cf7_mail_tag,
			$e
		);

		$this->assertIsArray( $result['value'] );
		$this->assertEmpty( $result['value'] );
	}

	/**
	 * Test module singleton instances.
	 */
	public function test_module_singleton_instances(): void {
		$text1 = \KintoneFormText::get_instance();
		$text2 = \KintoneFormText::get_instance();
		$this->assertSame( $text1, $text2 );

		$number1 = \KintoneFormNumber::get_instance();
		$number2 = \KintoneFormNumber::get_instance();
		$this->assertSame( $number1, $number2 );
	}
}
