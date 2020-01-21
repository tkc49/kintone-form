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
		/**
		 * Wpcf7_submit フックの起動時にuploadsフォルダーの添付画像が削除されるので、
		 * submit の前のメール送信後のhookのwpcf7_mail_sentを使用する.
		 */
		add_action( 'wpcf7_mail_sent', array( $this, 'kintone_form_send' ) );
		add_filter( 'wpcf7_form_tag', array( $this, 'kintone_form_set_post_title' ) );
	}


	/**
	 * フォームの情報をkintoneに登録する.
	 *
	 * @param WPCF7_ContactForm $contact_form .
	 */
	public function kintone_form_send( $contact_form ) {

		$submission = WPCF7_Submission::get_instance();
		if ( empty( $submission ) ) {
			return;
		}
		$cf7_send_data = $submission->get_posted_data();

		// Contact Form 7 add confirm.
		if ( isset( $cf7_send_data['_wpcf7c'] ) && 'step1' === $cf7_send_data['_wpcf7c'] ) {
			return;
		}

		$kintone_setting_data = $contact_form->prop( 'kintone_setting_data' );
		if ( empty( $kintone_setting_data ) ) {
			return;
		}

		$kintone_post_data = array();
		$post_data_count   = 0;

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
								if ( $kintone_fieldcode === $kintone_form_data['code'] ) {

									switch ( $kintone_form_data['type'] ) {
										case 'SINGLE_LINE_TEXT':
											$post_data = KintoneFormText::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'NUMBER':
											$post_data = KintoneFormNumber::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RADIO_BUTTON':
											$post_data = KintoneFormRadio::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'CHECK_BOX':
											$post_data = KintoneFormCheckbox::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && is_array( $post_data['value'] ) ) {
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
											if ( isset( $post_data['value'] ) && is_array( $post_data['value'] ) ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DROP_DOWN':
											$post_data = KintoneFormDropdown::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DATE':
											$post_data = KintoneFormDate::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'TIME':
											$post_data = KintoneFormTime::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'DATETIME':
											$post_data = KintoneFormDatetime::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'LINK':
											$post_data = KintoneFormLink::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RICH_TEXT':
											$post_data = KintoneFormRichText::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'MULTI_LINE_TEXT':
											$post_data = KintoneFormMultiLineText::format_to_kintone_data(
												$kintone_form_data,
												$cf7_send_data,
												$cf7_mail_tag,
												$e
											);
											if ( isset( $post_data['value'] ) && '' !== $post_data['value'] ) {
												$kintone_post_data[ $post_data_count ]['datas'][ $kintone_form_data['code'] ] = $post_data;
											}
											break;
										case 'RECORD_NUMBER':
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
											if ( isset( $post_data['value'] ) && is_array( $post_data['value'] ) ) {
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
		$error_msg  .= $cf7_name_after_urldecode . '(ID:' . $cf7_id . ')' . "\r\n";
		$error_msg  .= '-----------------------' . "\r\n";
		$error_msg  .= implode( "\r\n", $e->get_error_messages() ) . "\r\n";
		$error_data = $e->get_error_data();

		if ( ! empty( $error_data ) ) {
			$error_data = var_export( $error_data, true );
			$error_msg  .= $error_data;
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

		$body = array(
			'app'    => $appid,
			'record' => $datas,
		);

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
			$res = wp_remote_post(
				$url,
				$options
			);

			if ( is_wp_error( $res ) ) {
				$this->erro_mail( $res, $email_address_to_send_kintone_registration_error );

				return $res;
			} elseif ( 200 !== $res['response']['code'] ) {

				$message = json_decode( $res['body'], true );
				$e       = new WP_Error();

				$errors = array();
				if ( isset( $message['errors'] ) ) {
					$errors = $message['errors'];
				}
				$e->add( 'validation-error', $message['message'], $errors );
				$this->erro_mail( $e, $email_address_to_send_kintone_registration_error );

				return $e;
			} else {
				return true;
			}
		}

	}
}

