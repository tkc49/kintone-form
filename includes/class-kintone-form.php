<?php
/**
 * Kintone_Form
 *
 * @package Kintone_Form
 */

/**
 * Kintone_Form
 */
class Kintone_Form {


	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Register
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 1 );
	}

	/**
	 * Plugins Loaded
	 */
	public function plugins_loaded() {

		require_once KINTONE_FORM_PATH . '/includes/class-utility.php';

		if ( is_admin() ) {
			require_once KINTONE_FORM_PATH . '/includes/class-admin.php';
			new Admin();
		} else {
			require_once KINTONE_FORM_PATH . '/includes/class-post-kintone.php';
			new Post_Kintone();
		}

	}
}
