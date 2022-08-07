<?php
/**
 * Kintone_Form_Post_Kintone
 *
 * @package Kintone_Form
 */

/**
 * Kintone_Form_Post_Kintone
 */
class Kintone_Form_Post_Kintone {
	/**
	 * Constructor
	 */
	public function __construct() {

		// 現在ログインしているユーザー情報を取得しkintoneに保存するための設定.
		if ( is_user_logged_in() ) {
			add_filter( 'wpcf7_verify_nonce', '__return_true' );
		}

		/**
		 * Wpcf7_submit フックの起動時にuploadsフォルダーの添付画像が削除されるので、
		 * submit の前のメール送信後のhookのwpcf7_mail_sentを使用する.
		 */
		add_action( 'wpcf7_mail_sent', array( $this, 'kintone_form_send' ) );
		add_filter( 'wpcf7_form_tag', array( $this, 'kintone_form_set_post_title' ) );

		// contact form 7 multi step module が有効なら実行する.
		if ( function_exists( 'cf7msm_fs' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'kintone_form_enqueue_scripts_save_cf7msm_checkbox_to_kintone' ) );
		}
	}

	public function kintone_form_enqueue_scripts_save_cf7msm_checkbox_to_kintone() {
		wp_enqueue_script(
			'kintone-form-save-cf7msm-checkbox-to-kintone',
			KINTONE_FORM_URL . '/asset/js/save_cf7msm_checkbox_to_kintone.js',
			array( 'jquery' ),
			gmdate(
				'YmdGis',
				filemtime( KINTONE_FORM_PATH . '/asset/js/save_cf7msm_checkbox_to_kintone.js' )
			),
			true
		);

	}


	/**
	 * フォームの情報をkintoneに登録する.
	 *
	 * @param WPCF7_ContactForm $contact_form .
	 */
	public function kintone_form_send( $contact_form ) {

		if ( $contact_form->is_true( 'demo_mode' ) || $contact_form->is_true( 'do_not_store' ) ) {
			return;
		}

		$submission = WPCF7_Submission::get_instance();
		if ( empty( $submission ) ) {
			return;
		}

		$cf7_send_data = $submission->get_posted_data();
		$cf7_send_data = apply_filters( 'kintone_form_cf7_posted_data_before_post_to_kintone', $cf7_send_data );

		$kintone_setting_data = $contact_form->prop( 'kintone_setting_data' );

		if ( empty( $kintone_setting_data ) ) {
			return;
		}

		if ( ! isset( $kintone_setting_data['app_datas'] ) ) {
			return;
		}
		if ( empty( $kintone_setting_data['app_datas'] ) ) {
			return;
		}

		$kintone_post_data = array();
		$app_count         = 0;

		$e = new WP_Error();

		// kintoneアプリのマルチ設定考慮して、appdataごとにループ.
		foreach ( $kintone_setting_data['app_datas'] as $appdata ) {

			$kintone_post_data[ $app_count ]['appid'] = $appdata['appid'];
			$kintone_post_data[ $app_count ]['token'] = $appdata['token'];
			$kintone_post_data[ $app_count ]['datas'] = array();

			// CFf7の設定画面で紐づけされたデータ.
			$kintone_fields_and_cf7_mailtag_relate_data = self::get_data_for_post( $appdata );

			// 設定があれば処理する.
			if ( isset( $kintone_fields_and_cf7_mailtag_relate_data['setting'] ) ) {

				// kintoneに設定されている全フィールドをループ.

				foreach ( $appdata['formdata']['properties'] as $kintone_form_properties_data ) {

					if ( isset( $kintone_form_properties_data['code'] ) ) {
						if ( 'SUBTABLE' === $kintone_form_properties_data['type'] ) {
							/**
							 * SUBTABLEの処理.
							 */
							$subtable_records = array();
							$subtable_records = apply_filters(
								'kintone_form_subtable',
								$subtable_records,
								$appdata,
								$kintone_form_properties_data,
								$kintone_setting_data,
								$cf7_send_data,
								$kintone_fields_and_cf7_mailtag_relate_data,
								$e
							);

							if ( ! empty( $subtable_records ) ) {
								$kintone_post_data[ $app_count ]['datas'][ $kintone_form_properties_data['code'] ] = $subtable_records;
							}
						} else {
							// 通常処理.
							$post_data = $this->generate_format_kintone_data( $kintone_setting_data, $appdata, $kintone_fields_and_cf7_mailtag_relate_data, $kintone_form_properties_data, $cf7_send_data, $e );
							if ( isset( $post_data['value'] ) ) {
								$kintone_post_data[ $app_count ]['datas'][ $kintone_form_properties_data['code'] ] = $post_data;
							}
						}
					}
				}
			}

			$app_count ++;

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

					$url = Kintone_Form_Utility::get_kintone_url( $kintone_setting_data, 'record' );

					$this->save_data_to_kintone(
						$url,
						$data['token'],
						$data['appid'],
						$kintone_setting_data['kintone_basic_authentication_id'],
						Kintone_Form_Utility::decode( $kintone_setting_data['kintone_basic_authentication_password'] ),
						$data['datas'],
						$kintone_setting_data['email_address_to_send_kintone_registration_error'],
						$unique_key,
						$update_key
					);
				}
			}
		}
	}

	// CF7の設定画面で関連付けされたデーターとマッチングさせる.
	private function generate_format_kintone_data( $kintone_setting_data, $appdata, $kintone_fields_and_cf7_mailtag_relate_data, $kintone_form_field_properties, $cf7_send_data, $e, $subtable_flag = false ) {

		$formated_kintone_value = array();

		// CF7の設定画面で関連付けされたデーターをベースにループ.
		foreach ( $kintone_fields_and_cf7_mailtag_relate_data['setting'] as $related_kintone_fieldcode => $related_cf7_mail_tag ) {

			// メールタグが設定されているものだけ kintoneのフィールドにあわせる.
			if ( ! empty( $related_cf7_mail_tag ) ) {

				if ( $kintone_form_field_properties['code'] === $related_kintone_fieldcode ) {

					$formated_kintone_value = self::get_formated_data_for_kintone( $kintone_setting_data, $appdata, $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

					// 一致するのがあり、値の取得ができたのでループを抜ける.
					break;

				}
			}
		}

		return $formated_kintone_value;
	}

	public static function get_formated_data_for_kintone( $kintone_setting_data, $appdata, $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e ) {

		switch ( $kintone_form_field_properties['type'] ) {
			case 'SINGLE_LINE_TEXT':
				$post_data = KintoneFormText::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'NUMBER':
				$post_data = KintoneFormNumber::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'RADIO_BUTTON':
				$post_data = KintoneFormRadio::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'CHECK_BOX':
				$post_data = KintoneFormCheckbox::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'MULTI_SELECT':
				$post_data = KintoneForm_multi_select::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'DROP_DOWN':
				$post_data = KintoneFormDropdown::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'DATE':
				$post_data = KintoneFormDate::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'TIME':
				$post_data = KintoneFormTime::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'DATETIME':
				$post_data = KintoneFormDatetime::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'LINK':
				$post_data = KintoneFormLink::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'RICH_TEXT':
				$post_data = KintoneFormRichText::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'MULTI_LINE_TEXT':
				$post_data = KintoneFormMultiLineText::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'ORGANIZATION_SELECT':
				$post_data = KintoneFormOrganization::format_to_kintone_data( $kintone_form_field_properties, $cf7_send_data, $related_cf7_mail_tag, $e );

				return $post_data;
			case 'MODIFIER':
			case 'CREATOR':
			case 'UPDATED_TIME':
			case 'CREATED_TIME':
			case 'CALC':
			case 'USER_SELECT':
			case 'REFERENCE_TABLE':
			case 'GROUP':
			case 'SUBTABLE':
			case 'STATUS':
			case 'STATUS_ASSIGNEE':
			case 'CATEGORY':
			case 'RECORD_NUMBER':
				break;
			case 'FILE':
				// todo $kintone_setting_data, $appdata 必要?.
				$post_data = apply_filters_deprecated( 'kintone_form_attachments_data', array( $kintone_setting_data, $appdata, $cf7_send_data, $kintone_form_field_properties, $related_cf7_mail_tag, $e ), '2.24.0', 'kintone_form_file_data' );

				return apply_filters( 'kintone_form_file_data', $post_data, $kintone_setting_data, $appdata, $cf7_send_data, $kintone_form_field_properties, $related_cf7_mail_tag, $e );
		}
	}

	/**
	 * CF7MailTagにpost_title存在する場合は記事のタイトルを設定する.
	 *
	 * @param array $tag .
	 *
	 * @return array .
	 */
	public function kintone_form_set_post_title( $tag ) {

		global $post;

		if ( ! is_array( $tag ) ) {
			return $tag;
		}

		$name = $tag['name'];
		if ( 'post_title' === $name ) {
			$post_title    = get_the_title( $post );
			$tag['values'] = (array) $post_title;
		}

		return $tag;
	}

	/**
	 * Kintoneに保存するためにkintoneとCF7のメールタグの関連データを取得する
	 * CF7のkintoneタブではオリジナルテキスト設定とCF7メールタグから選択する方法があり、それぞれ違う配列に保存されるので、
	 * それを合体させている.
	 *
	 * @param array $appdata .
	 *
	 * @return array
	 */
	public static function get_data_for_post( $appdata ) {

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

	/**
	 * エラーメール送信する
	 *
	 * @param WP_Error $e .
	 * @param string   $email_address_to_send_kintone_registration_error .
	 */
	private function erro_mail( $e, $email_address_to_send_kintone_registration_error ) {

		$contact_form             = WPCF7_ContactForm::get_current();
		$cf7_name                 = $contact_form->name();
		$cf7_name_after_urldecode = urldecode( $cf7_name );

		$cf7_id = $contact_form->id();

		$error_msg  = '';
		$error_msg .= $cf7_name_after_urldecode . '(ID:' . $cf7_id . ')' . "\r\n";
		$error_msg .= '-----------------------' . "\r\n";
		$error_msg .= implode( "\r\n", $e->get_error_messages() ) . "\r\n";
		$error_data = $e->get_error_data();

		if ( ! empty( $error_data ) ) {
			$error_data = var_export( $error_data, true );
			$error_msg .= $error_data;
		}

		if ( $email_address_to_send_kintone_registration_error ) {
			$to = $email_address_to_send_kintone_registration_error;
		} else {
			$to = get_option( 'admin_email' );
		}

		$subject = 'kintone form post error';
		$body    = $error_msg;
		wp_mail( $to, $subject, $body );

	}

	/**
	 * Kintoneへデータを保存する.
	 *
	 * @param string $url .
	 * @param string $token .
	 * @param string $appid .
	 * @param string $basic_auth_user .
	 * @param string $basic_auth_pass .
	 * @param array  $datas .
	 * @param string $email_address_to_send_kintone_registration_error .
	 * @param string $unique_key .
	 * @param string $update_key .
	 *
	 * @return boolean|WP_Error
	 */
	private function save_data_to_kintone( $url, $token, $appid, $basic_auth_user, $basic_auth_pass, $datas, $email_address_to_send_kintone_registration_error, $unique_key, $update_key ) {

		$headers = array_merge(
			Kintone_Form_Utility::get_auth_header( $token ),
			Kintone_Form_Utility::get_basic_auth_header( $basic_auth_user, $basic_auth_pass ),
			array( 'Content-Type' => 'application/json' )
		);

		$headers = apply_filters(
			'form_data_to_kintone_post_header',
			$headers,
			Kintone_Form_Utility::get_auth_header( $token ),
			Kintone_Form_Utility::get_basic_auth_header( $basic_auth_user, $basic_auth_pass ),
			array( 'Content-Type' => 'application/json' )
		);
		$datas   = apply_filters( 'form_data_to_kintone_post_datas', $datas, $appid, $unique_key );
		$datas   = $this->stripslashes_deep( $datas );

		$body = array(
			'app'    => $appid,
			'record' => $datas,
		);

		$update_key = apply_filters( 'form_data_to_kintone_get_update_key_for_add_on_enable_update_key', $update_key, $body );

		$tmp_options = apply_filters(
			'form_data_to_kintone_before_wp_remoto_post',
			array(
				'method' => 'POST',
				'body'   => $body,
			),
			$update_key
		);

		if ( ! empty( $tmp_options ) ) {

			$options = array(
				'method'  => $tmp_options['method'],
				'headers' => $headers,
				'body'    => json_encode( $tmp_options['body'] ),
			);

			// kintoneにフォームデータを追加/更新する.
			$url = apply_filters( 'form_data_to_kintone_kintone_post_url', $url, $tmp_options );
			$res = wp_remote_post(
				$url,
				$options
			);

			$res = apply_filters( 'form_data_to_kintone_saved', $res, $tmp_options['body'] );

			if ( is_wp_error( $res ) ) {
				$this->erro_mail( $res, $email_address_to_send_kintone_registration_error );

				return $res;
			} elseif ( 200 !== $res['response']['code'] ) {

				$retry = false;
				$retry = apply_filters( 'form_data_to_kintone_retry_save', $retry, $res );
				if ( $retry ) {

					$reset_data = array(
						'url'             => $url,
						'token'           => $token,
						'appid'           => $appid,
						'basic_auth_user' => $basic_auth_user,
						'basic_auth_pass' => $basic_auth_pass,
						'datas'           => $datas,
						'email_address_to_send_kintone_registration_error' => $email_address_to_send_kintone_registration_error,
						'unique_key'      => $unique_key,
						'update_key'      => $update_key,
					);

					$reset_data = apply_filters( 'form_data_to_kintone_reset_data', $reset_data, $res );

					$this->save_data_to_kintone(
						$reset_data['url'],
						$reset_data['token'],
						$reset_data['appid'],
						$reset_data['basic_auth_user'],
						$reset_data['basic_auth_pass'],
						$reset_data['datas'],
						$reset_data['email_address_to_send_kintone_registration_error'],
						$reset_data['unique_key'],
						$reset_data['update_key']
					);
				} else {
					$message = json_decode( $res['body'], true );
					$e       = new WP_Error();

					$errors   = array();
					$errors[] = 'code: ' . $res['response']['code'];
					$errors[] = 'message: ' . $res['response']['message'];

					if ( isset( $message['errors'] ) ) {
						$errors[] = $message['errors'];
					}
					$e->add( 'validation-error', $message['message'], $errors );
					$this->erro_mail( $e, $email_address_to_send_kintone_registration_error );

					return $e;
				}
			} else {
				do_action( 'form_data_to_kintone_completed_saving', $res, $tmp_options['body'] );
				return true;
			}
		}

	}

	private function stripslashes_deep( $value ) {
		$value = is_array( $value ) ? array_map( 'stripslashes_deep', $value ) : stripslashes( $value );

		return $value;
	}
}

