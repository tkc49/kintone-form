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

	const CF7_SPECAIL_TAGS = array(
		'_remote_ip',
		'_user_agent',
		'_date',
		'_time',
		'_url',
		'_post_id',
		'_post_name',
		'_post_title',
		'_post_url',
		'_post_author',
		'_post_author_email',
		'_site_title',
		'_site_description',
		'_site_url',
		'_site_admin_email',
		'_user_login',
		'_user_email',
		'_user_url',
		'_user_first_name',
		'_user_last_name',
		'_user_nickname',
		'_user_display_name',
	);


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

		require_once KINTONE_FORM_PATH . '/includes/class-kintone-form-KintoneFormUtility.php';

		require_once KINTONE_FORM_PATH . '/includes/class-kintone-form-admin.php';
		new Kintone_Form_Admin();

		require_once KINTONE_FORM_PATH . '/includes/class-kintone-form-post-kintone.php';
		new Kintone_Form_Post_Kintone();

	}
}
