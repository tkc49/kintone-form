<?php

/**
 * KintoneFormFile Class
 */
class KintoneFormFile {

	/**
	 * construct
	 */
	public function __construct() {
		add_filter( 'kintone_form_file_data', array( $this, 'get_kintone_file_key' ), 9999, 7 );
	}

	/**
	 * Get the file key uploaded to kintone.
	 *
	 * @param $kintone_setting_data
	 * @param $appdata
	 * @param $cf7_send_data
	 * @param $kintone_form_data
	 * @param $cf7_mail_tag
	 * @param $e
	 *
	 * @return array|mixed|WP_Error
	 */
	public function get_kintone_file_key( $post_data, $kintone_setting_data, $appdata, $cf7_send_data, $kintone_form_data, $cf7_mail_tag, $e ) {

		// 既に設定されている場合は何も処理をしない。
		if ( isset( $post_data['value'] ) ) {
			return $post_data;
		}

		$return_data = array();

		$value = $cf7_send_data[ $cf7_mail_tag ];

		if ( 'true' === $kintone_form_data['required'] && empty( $value ) ) {
			$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : Required fields' );
		}

		$submission = WPCF7_Submission::get_instance();
		if ( empty( $submission ) ) {
			return $return_data;
		}

		$uploaded_files = $submission->uploaded_files();
		if ( ! isset( $uploaded_files[ $cf7_mail_tag ] ) ) {
			return $return_data;
		}

		$file_path = $uploaded_files[ $cf7_mail_tag ];
		if ( empty( $file_path ) ) {
			return $return_data;
		}

		$file_name = mb_convert_encoding( mb_substr( $file_path[0], mb_strrpos( $file_path[0], DIRECTORY_SEPARATOR ) + 1 ), 'UTF-8', 'auto' );

		$file_info = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $file_info, $file_path[0] );
		$file_data = file_get_contents( $file_path[0] );
		finfo_close( $file_info );

		$request_url = Kintone_Form_Utility::get_kintone_url( $kintone_setting_data, 'file' );

		$boundary = '----' . microtime( true );
		$body     = '--' . $boundary . "\r\n" . 'Content-Disposition: form-data; name="file"; filename="' . $file_name . '"' . "\r\n" . 'Content-Type: ' . $mime_type . "\r\n\r\n" . $file_data . "\r\n" . '--' . $boundary . '--';

		$res = wp_remote_post(
			$request_url,
			array(
				'headers' => array(
					'Content-Type'       => "multipart/form-data; boundary={$boundary}",
					'X-Cybozu-API-Token' => $appdata['token'],
					'Content-Length'     => strlen( $body ),
				),
				'body'    => $body,
			)
		);

		if ( is_wp_error( $res ) ) {
			return $res;
		} else {
			$return_value           = json_decode( $res['body'], true );
			$return_data['value'][] = $return_value;
			if ( isset( $return_value['message'] ) && isset( $return_value['code'] ) ) {
				$e->add( 'Error', $cf7_mail_tag . '->' . $kintone_form_data['code'] . ' : ' . $return_value['message'] . '(' . $return_value['code'] . ')' );
				return $e;
			}

			return $return_data;
		}

	}

}
new KintoneFormFile();
