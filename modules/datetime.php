<?php 

class KintoneForm_datetime
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
			$instance = new KintoneForm_datetime();
		}
		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();
		$value = $cf7_send_data[$cf7_mail_tag];

		if( $kintone_form_data['required'] == 'true' && empty($value) ){
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Required fields');
		}

		if(strtotime($value) === false){
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Datetime format error');
		}

		$value = date('Y-m-dTH:i:s+09:00',strtotime($value));

		$return_data['value'] = $value;

		return $return_data;

	}

}


