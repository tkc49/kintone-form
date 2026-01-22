<?php

class Kintone_Form_Utility {
	public static function decode( $encoded ) {
		return preg_match( '/^[a-f0-9]{32}$/', $encoded ) ? $encoded : str_replace( array( md5( AUTH_SALT ), md5( md5( AUTH_SALT ) ) ), '', base64_decode( $encoded ) );
	}

	/**
	 * APIトークンをエンコードする.
	 *
	 * @param string $token トークン.
	 * @return string エンコードされたトークン.
	 */
	public static function encode_token( $token ) {
		if ( empty( $token ) ) {
			return '';
		}
		return base64_encode( md5( AUTH_SALT ) . $token . md5( md5( AUTH_SALT ) ) );
	}

	/**
	 * エンコードされたAPIトークンをデコードする.
	 *
	 * @param string $encoded エンコードされたトークン.
	 * @return string デコードされたトークン.
	 */
	public static function decode_token( $encoded ) {
		if ( empty( $encoded ) ) {
			return '';
		}
		// 生のトークン（未エンコード）の場合はそのまま返す
		if ( ! preg_match( '/^[A-Za-z0-9+\/=]+$/', $encoded ) || strlen( $encoded ) < 64 ) {
			return $encoded;
		}
		$decoded = base64_decode( $encoded );
		if ( false === $decoded ) {
			return $encoded;
		}
		$prefix = md5( AUTH_SALT );
		$suffix = md5( md5( AUTH_SALT ) );
		if ( strpos( $decoded, $prefix ) === 0 && substr( $decoded, -32 ) === $suffix ) {
			return str_replace( array( $prefix, $suffix ), '', $decoded );
		}
		return $encoded;
	}

	/**
	 * APIトークンをマスク表示用に変換する.
	 *
	 * @param string $token トークン.
	 * @return string マスクされたトークン（末尾6文字のみ表示）.
	 */
	public static function mask_token( $token ) {
		if ( empty( $token ) ) {
			return '';
		}
		$token = self::decode_token( $token );
		$len   = strlen( $token );
		if ( $len <= 6 ) {
			return str_repeat( '●', $len );
		}
		return str_repeat( '●', $len - 6 ) . substr( $token, -6 );
	}

	/**
	 * トークン配列をカンマ区切り文字列に変換する.
	 *
	 * @param array|string $tokens トークン配列またはカンマ区切り文字列.
	 * @return string カンマ区切りのトークン文字列.
	 */
	public static function tokens_to_string( $tokens ) {
		if ( is_array( $tokens ) ) {
			$decoded_tokens = array();
			foreach ( $tokens as $token ) {
				$decoded = self::decode_token( $token );
				if ( ! empty( $decoded ) ) {
					$decoded_tokens[] = $decoded;
				}
			}
			return implode( ',', $decoded_tokens );
		}
		return self::decode_token( $tokens );
	}

	/**
	 * カンマ区切り文字列またはトークンを配列に変換する（後方互換性用）.
	 *
	 * @param array|string $tokens トークン.
	 * @return array トークン配列.
	 */
	public static function normalize_tokens( $tokens ) {
		if ( is_array( $tokens ) ) {
			return array_filter(
				$tokens,
				function ( $t ) {
					return ! empty( $t );
				}
			);
		}
		if ( empty( $tokens ) ) {
			return array();
		}
		// カンマ区切り文字列を配列に分割
		$token_array = array_map( 'trim', explode( ',', $tokens ) );
		return array_filter(
			$token_array,
			function ( $t ) {
				return ! empty( $t );
			}
		);
	}

	// form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org.
	public static function get_auth_header( $token ) {
		// 配列の場合はカンマ区切りに変換
		if ( is_array( $token ) ) {
			$token = self::tokens_to_string( $token );
		} else {
			$token = self::decode_token( $token );
		}
		if ( $token ) {
			return array( 'X-Cybozu-API-Token' => $token );
		} else {
			return new WP_Error( 'kintone', 'API Token is required' );
		}
	}

	// form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org
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
