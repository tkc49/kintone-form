<?php
/**
 * Date
 *
 * @package kintone-form
 */

/**
 * KintoneFormDate
 */
class KintoneFormDate {

	/**
	 * Get instance.
	 */
	public static function get_instance() {
		/**
		 * A variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneFormDate();
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
		if ( '_date' === $cf7_mail_tag ) {
			$value = date_i18n( 'Y-m-d' );
		} else {

			if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
				$value = $cf7_send_data[ $cf7_mail_tag ];
			}
		}

		$value = apply_filters( 'kintone_form_date_customize_mailtag', $value, $cf7_send_data, $cf7_mail_tag );

		if ( ! empty( $value ) ) {

			if ( strtotime( $value ) === false ) {
				$return_data['value'] = '';

				return $return_data;
			}

			$value = date( 'Y-m-d', strtotime( $value ) );
		}

		$return_data['value'] = $value;

		return $return_data;

	}

}


