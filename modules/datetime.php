<?php
/**
 * DateTime
 *
 * @package kintone-form
 */

/**
 * KintoneFormDatetime
 */
class KintoneFormDatetime {

	/**
	 * Get instance
	 */
	public static function get_instance() {
		/**
		 * A variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneFormDatetime();
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
		if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
			$value = $cf7_send_data[ $cf7_mail_tag ];
		}

		if ( strtotime( $value ) === false ) {
			$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Datetime format error' );
		}

		$value = date( 'Y-m-dTH:i:s+09:00', strtotime( $value ) );

		$return_data['value'] = $value;

		return $return_data;

	}
}


