<?php 

class KintoneForm_radio
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
			$instance = new KintoneForm_radio();
		}
		return $instance;
	}

	public static function format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e ) {

		$return_data = array();
		$value = $cf7_send_data[$cf7_mail_tag][0];

		if( $kintone_form_data['required'] == 'true' && empty($value) ){
			$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : Required fields');
		}


		if( !empty( $value ) ){

			$match_flg = false;
			foreach ($kintone_form_data['options'] as $option_key => $option_value) {
				

				if( $value == $option_value ){
					$match_flg = true;
				}

			}
			if(!$match_flg){
				$e->add('Error', $cf7_mail_tag .'->'. $kintone_form_data['code'].' : not match');
			}

		}
			
		$return_data['value'] = $value;

		return $return_data;

	}



}


