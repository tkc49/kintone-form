<?php


//
// Check Acceptance
//
function kintone_form_check_acceptance( $value, $cf7_mail_tag ) {

	$mail_tag = new WPCF7_MailTag( '[' . $cf7_mail_tag . ']', $cf7_mail_tag, "" );
	$form_tag = $mail_tag->corresponding_form_tag();
	if ( isset( $form_tag->type ) && $form_tag->type == 'acceptance' ) {
		$value = $form_tag->content;
	}

	return $value;
}
