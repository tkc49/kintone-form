<?php

class Kintone_Form_Utility {
	public static function decode( $encoded ) {
		return preg_match( '/^[a-f0-9]{32}$/', $encoded ) ? $encoded : str_replace( array( md5( AUTH_SALT ), md5( md5( AUTH_SALT ) ) ), '', base64_decode( $encoded ) );
	}

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org.
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

	/**
	 * Kintoneへ接続するURLを返す
	 * ゲストスペース
	 * https://(サブドメイン名).cybozu.com/k/guest/(スペースのID)/v1/record.json
	 *
	 * @param array $kintone_setting_data
	 *
	 * @return string
	 */
	public static function get_kintone_url( $kintone_setting_data, $type ) {

		if ( isset( $kintone_setting_data['kintone_guest_space_id'] ) && $kintone_setting_data['kintone_guest_space_id'] ) {
			$url = 'https://' . $kintone_setting_data['domain'] . '/k/guest/' . esc_attr( $kintone_setting_data['kintone_guest_space_id'] ) . '/v1/' . $type . '.json';
		} else {
			$url = 'https://' . $kintone_setting_data['domain'] . '/k/v1/' . $type . '.json';
		}

		return $url;

	}

}
