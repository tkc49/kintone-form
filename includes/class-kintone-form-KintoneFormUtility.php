<?php

class Kintone_Form_Utility {
	public static function decode( $encoded ) {
		return preg_match( '/^[a-f0-9]{32}$/', $encoded ) ? $encoded : str_replace(
			array(
				md5( AUTH_SALT ),
				md5( md5( AUTH_SALT ) )
			),
			'',
			base64_decode( $encoded )
		);
	}

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org
	public static function get_auth_header( $token ) {
		if ( $token ) {
			return array( 'X-Cybozu-API-Token' => $token );
		} else {
			return new WP_Error( 'kintone', 'API Token is required' );
		}
	}

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org
	public static function get_basic_auth_header( $basic_auth_user = null, $basic_auth_pass = null ) {
		if ( $basic_auth_user && $basic_auth_pass ) {
			$auth = base64_encode( $basic_auth_user . ':' . $basic_auth_pass );

			return array( 'Authorization' => 'Basic ' . $auth );
		} else {
			return array();
		}
	}


}
