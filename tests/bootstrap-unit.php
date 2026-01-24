<?php
/**
 * PHPUnit bootstrap file for unit tests (no WordPress dependency).
 *
 * @package Kintone_Form
 */

// Define constants needed by the plugin.
if ( ! defined( 'AUTH_SALT' ) ) {
	define( 'AUTH_SALT', 'test-auth-salt-value-for-unit-tests' );
}

if ( ! defined( 'KINTONE_FORM_PATH' ) ) {
	define( 'KINTONE_FORM_PATH', dirname( __DIR__ ) );
}

// Mock WordPress functions used by the utility class.
if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * Mock esc_attr function.
	 *
	 * @param string $text Text to escape.
	 * @return string Escaped text.
	 */
	function esc_attr( $text ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * Mock WP_Error class.
	 */
	class WP_Error {
		/**
		 * Errors.
		 *
		 * @var array
		 */
		private $errors = array();

		/**
		 * Constructor.
		 *
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 */
		public function __construct( $code = '', $message = '' ) {
			if ( $code ) {
				$this->add( $code, $message );
			}
		}

		/**
		 * Add error.
		 *
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 */
		public function add( $code, $message ) {
			$this->errors[ $code ][] = $message;
		}

		/**
		 * Get error messages.
		 *
		 * @param string $code Error code.
		 * @return array Error messages.
		 */
		public function get_error_messages( $code = '' ) {
			if ( $code ) {
				return isset( $this->errors[ $code ] ) ? $this->errors[ $code ] : array();
			}
			$all_messages = array();
			foreach ( $this->errors as $messages ) {
				$all_messages = array_merge( $all_messages, $messages );
			}
			return $all_messages;
		}

		/**
		 * Check if there are errors.
		 *
		 * @return bool True if there are errors.
		 */
		public function has_errors() {
			return ! empty( $this->errors );
		}
	}
}

// Load Composer autoloader.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Load the utility class.
require_once KINTONE_FORM_PATH . '/includes/class-kintone-form-KintoneFormUtility.php';
