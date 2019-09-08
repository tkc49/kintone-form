<?php
/**
 * Kintone_Form
 *
 * @package Kintone_Form
 */


class Kintone_Form {

	private $nonce = 'kintone_form_';

	private $kintone_form_data = array();


	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Register
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 1 );
	}

	/**
	 * Plugins Loaded
	 */
	public function plugins_loaded() {

		/**
		 * Wpcf7_submit フックの起動時にuploadsフォルダーの添付画像が削除されるので、
		 * wpcf7_mail_sent フックに変更.
		 */
		add_action( 'wpcf7_mail_sent', array( $this, 'kintone_form_send' ) );

	}


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


	public function kintone_form_send( $wpcf7_data ) {

		// Contact Form 7 add confirm
		if ( isset( $_POST["_wpcf7c"] ) && $_POST["_wpcf7c"] == "step1" ) {
			return;
		}


		$kintone_setting_data = $wpcf7_data->prop( 'kintone_setting_data' );
		if ( empty( $kintone_setting_data ) ) {
			return;
		}

		$submission = WPCF7_Submission::get_instance();
		if ( empty( $submission ) ) {
			return;
		}

		$cf7_send_data = $submission->get_posted_data();

		$kintone_post_data = array();


		$post_data_count = 0;

		$e = new WP_Error();

		foreach ( $kintone_setting_data['app_datas'] as $appdata ) {

			$kintone_post_data[ $post_data_count ]['appid'] = $appdata['appid'];
			$kintone_post_data[ $post_data_count ]['token'] = $appdata['token'];
			$kintone_post_data[ $post_data_count ]['datas'] = array();

			$kintone_data_for_post = $this->get_data_for_post( $appdata );

			if ( isset( $kintone_data_for_post['setting'] ) ) {

				foreach ( $kintone_data_for_post['setting'] as $kintone_fieldcode => $cf7_mail_tag ) {

					if ( ! empty( $cf7_mail_tag ) ) {

						foreach ( $appdata['formdata']['properties'] as $kintone_form_data ) {

							if ( isset( $kintone_form_data['code'] ) ) {
								if ( $kintone_fieldcode == $kintone_form_data['code'] ) {

									switch ( $kintone_form_data['type'] ) {
										case 'SINGLE_LINE_TEXT':
											$post_data = KintoneForm_text::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'NUMBER':
											$post_data = KintoneForm_number::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RADIO_BUTTON':
											$post_data = KintoneForm_radio::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'CHECK_BOX':
											$post_data = KintoneForm_checkbox::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'MULTI_SELECT':
											$post_data = KintoneForm_multi_select::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DROP_DOWN':
											$post_data = KintoneForm_drop_down::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DATE':
											$post_data = KintoneForm_date::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'TIME':
											$post_data = KintoneForm_time::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DATETIME':
											$post_data = KintoneForm_datetime::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'LINK':
											$post_data = KintoneForm_link::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RICH_TEXT':
											$post_data = KintoneForm_rich_text::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'MULTI_LINE_TEXT':
											$post_data = KintoneForm_multi_line_text::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RECORD_NUMBER':
											break;
										case 'MODIFIER':
											break;
										case 'CREATOR':
											break;
										case 'UPDATED_TIME':
											break;
										case 'CREATED_TIME':
											break;
										case 'CALC':
											break;
										case 'USER_SELECT':
											break;
										case 'REFERENCE_TABLE':
											break;
										case 'GROUP':
											break;
										case 'SUBTABLE':
											break;
										case 'STATUS':
											break;
										case 'STATUS_ASSIGNEE':
											break;
										case 'CATEGORY':
											break;
										case 'FILE':
											$post_data = apply_filters(
												'kintone_form_attachments_data',
												$kintone_setting_data,
												$appdata,
												$cf7_send_data,
												$kintone_form_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && ! empty( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
									}

								}
							}

						}

					}
				}
			}

			$post_data_count ++;

		}

		if ( $e->get_error_code() ) {

			$this->erro_mail( $e, $kintone_setting_data['email_address_to_send_kintone_registration_error'] );


		} else {

			// 1フォームで複数アプリを登録する時に紐付けるキーに利用
			$unique_key = '';
			$unique_key = apply_filters( 'form_data_to_kintone_get_unique_key', $unique_key );

			$update_key = '';
			$update_key = apply_filters( 'form_data_to_kintone_get_update_key', $update_key, $cf7_send_data );

			foreach ( $kintone_post_data as $data ) {

				if ( ! empty( $kintone_setting_data['domain'] ) && ! empty( $data['token'] ) && ! empty( $data['appid'] ) ) {
					$url = 'https://' . $kintone_setting_data['domain'] . '/k/v1/record.json';
					$this->save_data(
						$url,
						$data['token'],
						$data['appid'],
						$kintone_setting_data['kintone_basic_authentication_id'],
						self::decode( $kintone_setting_data['kintone_basic_authentication_password'] ),
						$data['datas'],
						$kintone_setting_data['email_address_to_send_kintone_registration_error'],
						$unique_key,
						$update_key
					);
				}

			}
		}
	}

	private function get_data_for_post( $appdata ) {

		$data['setting'] = array();

		if ( isset( $appdata['setting_original_cf7tag_name'] ) && ! empty( $appdata['setting_original_cf7tag_name'] ) ) {

			foreach ( $appdata['setting_original_cf7tag_name'] as $key => $value ) {
				if ( $value ) {
					$data['setting'][ $key ] = $value;
				} else {
					if ( isset( $appdata['setting'][ $key ] ) ) {
						$data['setting'][ $key ] = $appdata['setting'][ $key ];
					}
				}
			}

		} else {
			if ( isset( $appdata['setting'] ) ) {
				return $appdata['setting'];
			}
		}

		return $data;
	}

	private function erro_mail( $e, $email_address_to_send_kintone_registration_error ) {

		$error_msg  = "";
		$error_msg  .= implode( "\r\n", $e->get_error_messages() ) . "\r\n";
		$error_data = $e->get_error_data();
		$error_data = var_export( $error_data, true );

		$error_msg .= $error_data;


		if ( $email_address_to_send_kintone_registration_error ) {
			$to = $email_address_to_send_kintone_registration_error;
		} else {
			$to = get_option( 'admin_email' );
		}

		$subject = 'kintone form post error';
		$body    = $error_msg;
		wp_mail( $to, $subject, $body );


	}

	private function save_data( $url, $token, $appid, $basic_auth_user, $basic_auth_pass, $datas, $email_address_to_send_kintone_registration_error, $unique_key, $update_key ) {


		$headers = array_merge(
			self::get_auth_header( $token ),
			self::get_basic_auth_header( $basic_auth_user, $basic_auth_pass ),
			array( 'Content-Type' => 'application/json' )
		);

		$headers = apply_filters(
			'form_data_to_kintone_post_header',
			$headers,
			self::get_auth_header( $token ),
			self::get_basic_auth_header( $basic_auth_user, $basic_auth_pass ),
			array( 'Content-Type' => 'application/json' )
		);
		$datas   = apply_filters( 'form_data_to_kintone_post_datas', $datas, $appid, $unique_key );


		$body = array(
			"app"    => $appid,
			"record" => $datas
		);

		$tmp_options = apply_filters(
			'form_data_to_kintone_before_wp_remoto_post',
			array( "method" => "POST", "body" => $body ),
			$update_key
		);

		if ( ! empty( $tmp_options ) ) {

			$options = array(
				"method"  => $tmp_options["method"],
				"headers" => $headers,
				"body"    => json_encode( $tmp_options["body"] )
			);

			// kintoneにフォームデータを追加/更新する
			$res = wp_remote_post(
				$url,
				$options
			);

			if ( is_wp_error( $res ) ) {
				$this->erro_mail( $res, $email_address_to_send_kintone_registration_error );

				return $res;
			} elseif ( $res["response"]["code"] !== 200 ) {

				$message = json_decode( $res["body"], true );
				$e       = new WP_Error();
				$e->add( "validation-error", $message["message"], $message["errors"] );
				$this->erro_mail( $e, $email_address_to_send_kintone_registration_error );

				return $e;
			} else {
				return true;
			}

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

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org
	public static function get_auth_header( $token ) {
		if ( $token ) {
			return array( 'X-Cybozu-API-Token' => $token );
		} else {
			return new WP_Error( 'kintone', 'API Token is required' );
		}
	}


	public function kintone_api( $request_url, $kintone_token, $basic_auth_user = null, $basic_auth_pass = null, $file = false ) {

		if ( $request_url ) {

			$headers = array_merge(
				self::get_auth_header( $kintone_token ),
				self::get_basic_auth_header( $basic_auth_user, $basic_auth_pass )
			);

			$res = wp_remote_get(
				$request_url,
				array(
					'headers' => $headers
				)
			);

			if ( is_wp_error( $res ) ) {

				return $res;

			} else {

				if ( $res['response']['code'] != 200 ) {

					set_transient(
						'my-custom-admin-errors',
						'kintone Error: ' . $res['response']['message'] . '(Code=' . $res['response']['code'] . ')',
						10
					);

					return new WP_Error( $res['response']['code'], $res['response']['message'] );

				} else {

					if ( $file ) {
						$return_value = $res['body'];
					} else {
						$return_value = json_decode( $res['body'], true );
					}
				}

				return $return_value;
			}
		} else {
			echo '<div class="error fade"><p><strong>URL is required</strong></p></div>';

			return new WP_Error( 'Error', 'URL is required' );
		}

	}


}
