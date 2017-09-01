<?php 

class KintoneForm_link
{

	/*
	 * get instance
	 */
	public static function getInstance()
	{
		/**
		* a variable that keeps the sole instance.
		*/
		static $instance;

		if ( !isset( $instance ) ) {
			$instance = new KintoneForm_link();
		}
		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();
		$value = $cf7_send_data[$cf7_mail_tag];


		if( $kintone_form_data['required'] == 'true' && empty($value) ){
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Required fields');
		}

		if( !empty($kintone_form_data['minLength']) ){

			if( $kintone_form_data['minLength'] > mb_strlen( $value ) ){
				$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Minimum value error');
			}

		}

		if( !empty($kintone_form_data['maxLength']) ){

			if( $kintone_form_data['maxLength'] < mb_strlen( $value ) ){
				$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Maximum value error');
			}
			
		}

		$return_data['value'] = $value;

		return $return_data;

	}



}


