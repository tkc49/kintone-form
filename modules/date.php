<?php 

class KintoneForm_date
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
			$instance = new KintoneForm_date();
		}
		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();
		$value = $cf7_send_data[$cf7_mail_tag];

		if( $kintone_form_data['required'] == 'true' && empty($value) ){
			// エラー
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Required fields');
		}

		if(!empty($value)){

			if(strtotime($value) === false){
				//$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Date format error');
				return $return_data['value'] = '';
			}

			$value = date('Y-m-d',strtotime($value));
		}

		$return_data['value'] = $value;

		return $return_data;

	}



}


