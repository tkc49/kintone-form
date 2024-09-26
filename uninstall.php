<?php
/**
 * プラグインをアンインストールしたときに実行されるスクリプト.
 *
 * @package kintone-form
 */

// 直接アクセスを防ぐ.
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Contact Form 7の全フォームから kintone_setting_data を削除.
if ( class_exists( 'WPCF7_ContactForm' ) ) {
	$forms = WPCF7_ContactForm::find();
	foreach ( $forms as $form ) {
		$properties = $form->get_properties();
		if ( isset( $properties['kintone_setting_data'] ) ) {
			$properties['kintone_setting_data'] = array();
			$form->set_properties( $properties );
			$form->save();
		}
	}
}
delete_post_meta_by_key( '_kintone_setting_data' );
