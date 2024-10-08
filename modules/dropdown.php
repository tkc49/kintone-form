<?php
/**
 * Drop Down
 *
 * @package kintone-form
 */

/**
 * KintoneFormDate
 */
class KintoneFormDropdown {

	/**
	 * Get instance.
	 */
	public static function get_instance() {
		/**
		 * A variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneFormDropdown();
		}

		return $instance;
	}

	/**
	 * Format_to_kintone_data
	 *
	 * @param array    $kintone_form_data .
	 * @param array    $cf7_send_data .
	 * @param string   $cf7_mail_tag .
	 * @param WP_Error $e .
	 *
	 * @return array An array of image URLs.
	 */
	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();

		$value = '';
		if ( in_array( $cf7_mail_tag, Kintone_Form::get_cf7_special_tags(), true ) ) {
			$mail_tag = new WPCF7_MailTag(
				sprintf( '[%s]', $cf7_mail_tag ), $cf7_mail_tag, ''
			);
			$value    = apply_filters( 'wpcf7_special_mail_tags', null, $cf7_mail_tag, false, $mail_tag );
		} else {
			if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
				$value = $cf7_send_data[ $cf7_mail_tag ];
			}
		}

		//
		// Check Acceptance.
		//
		$value = kintone_form_check_acceptance( $value, $cf7_mail_tag );

		if ( is_array( $value ) ) {
			if ( isset( $value[0] ) ) {
				$value = $value[0];
			} else {
				$value = '';
			}
		}

		$return_data['value'] = $value;

		return $return_data;

	}
}


