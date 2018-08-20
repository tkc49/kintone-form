<?php 

class KintoneForm_multi_line_text
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
			$instance = new KintoneForm_multi_line_text();
		}
		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();
		$value = $cf7_send_data[$cf7_mail_tag];
		// 
		// Check Acceptance
		// 
		$value = check_acceptance( $value, $cf7_mail_tag );
		

		if( is_array($value) ){
			$value = implode("\n", $value);
		}		


		if( $kintone_form_data['required'] == 'true' && empty($value) ){
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Required fields');
		}


		$return_data['value'] = $value;

		return $return_data;

	}



}


