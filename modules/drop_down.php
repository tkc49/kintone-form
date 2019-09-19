<?php

class KintoneForm_drop_down {

	/*
	 * get instance.
	 */
	public static function getInstance() {
		/**
		 * a variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneForm_drop_down();
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
		if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
			$value = $cf7_send_data[ $cf7_mail_tag ];
		}


		//
		// Check Acceptance.
		//
		$value = kintone_form_check_acceptance( $value, $cf7_mail_tag );

		if ( is_array( $value ) ) {
			$value = $value[0];
		}

		if ( 'true' === $kintone_form_data['required'] && empty( $value ) ) {

			$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Required fields' );

		}

		$return_data['value'] = $value;

		return $return_data;

	}


}


