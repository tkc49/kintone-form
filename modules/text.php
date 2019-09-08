<?php

class KintoneForm_text {

	/*
	 * get instance
	 */
	public static function getInstance() {
		/**
		 * a variable that keeps the sole instance.
		 */
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new KintoneForm_text();
		}

		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		global $post;

		$return_data = array();

		$value = '';
		if ( isset( $cf7_send_data[ $cf7_mail_tag ] ) ) {
			$value = $cf7_send_data[ $cf7_mail_tag ];
		}

		// post_title 対応.
		if ( 'post_title' === $cf7_mail_tag ) {
			$value = get_the_title( $post->ID );
		}

		//
		// Check Acceptance.
		//
		$value = check_acceptance( $value, $cf7_mail_tag );


		$value = apply_filters( 'kintone_form_text_customize_mailtag', $value, $cf7_send_data, $cf7_mail_tag );


		if ( is_array( $value ) ) {
			$value = implode( ",", $value );
		}


		if ( $kintone_form_data['required'] == 'true' && empty( $value ) ) {
			// エラー
			$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Required fields' );
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


