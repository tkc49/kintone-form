<?php
/**
 * Link
 *
 * @package kintone-form
 */

/**
 * KintoneFormLink
 */
class KintoneFormLink {

	/**
	 * Get instance
	 */
	public static function get_instance() {
		/**
		 * A variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneFormLink();
		}

		return $instance;
	}

	/**
	 * Format for kintone data.
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
		if ( in_array( $cf7_mail_tag, Kintone_Form::CF7_SPECAIL_TAGS, true ) ) {
			$value = apply_filters( 'wpcf7_special_mail_tags', '', $cf7_mail_tag, false );
		} else {
			if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
				$value = $cf7_send_data[ $cf7_mail_tag ];
			}
		}


		if ( ! empty( $kintone_form_data['minLength'] ) ) {

			if ( $kintone_form_data['minLength'] > mb_strlen( $value ) ) {
				$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Minimum value error' );
			}
		}

		if ( ! empty( $kintone_form_data['maxLength'] ) ) {

			if ( $kintone_form_data['maxLength'] < mb_strlen( $value ) ) {
				$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Maximum value error' );
			}
		}

		$return_data['value'] = $value;

		return $return_data;

	}
}


