<?php
/**
 * Plugin Name: Form data to kintone
 * Plugin URI:
 * Description: This plugin is an addon for "Contact form 7".
 * Version:	 1.0.14
 * Author:	  Takashi Hosoya
 * Author URI:  http://ht79.info/
 * License:	 GPLv2
 * Text Domain: kintone-form
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2017 Takashi Hosoya ( http://ht79.info/ )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'KINTONE_FORM_URL',  plugins_url( '', __FILE__ ) );
define( 'KINTONE_FORM_PATH', dirname( __FILE__ ) );
define( 'GUMROAD_KINTONE_FORM_ATTACHMENTS_SLUG', 'jIewp' );
define( 'GUMROAD_KINTONE_FORM_MULTI_KINTONE_APP_SLUG', 'Uqikv' );

$kintone_form = new KintoneForm();
$kintone_form->register();

require KINTONE_FORM_PATH . '/modules/text.php';
require KINTONE_FORM_PATH . '/modules/number.php';
require KINTONE_FORM_PATH . '/modules/radio.php';
require KINTONE_FORM_PATH . '/modules/checkbox.php';
require KINTONE_FORM_PATH . '/modules/date.php';
require KINTONE_FORM_PATH . '/modules/datetime.php';
require KINTONE_FORM_PATH . '/modules/drop_down.php';
require KINTONE_FORM_PATH . '/modules/link.php';
require KINTONE_FORM_PATH . '/modules/rich_text.php';
require KINTONE_FORM_PATH . '/modules/multi_line_text.php';
require KINTONE_FORM_PATH . '/modules/multi_select.php';
require KINTONE_FORM_PATH . '/modules/time.php';




class KintoneForm {

	private $version = '';
	private $langs   = '';
	private $nonce   = 'kintone_form_';

	private $kintone_form_data = array();

	private $consistency = array(
		'text' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'email' => array(
			'SINGLE_LINE_TEXT',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'url' => array(
			'SINGLE_LINE_TEXT',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'tel' => array(
			'SINGLE_LINE_TEXT',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'number' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'range' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'date' => array(
			'SINGLE_LINE_TEXT',
			'DATE',
			'DATETIME',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'textarea' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'select' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RADIO_BUTTON',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DROP_DOWN',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'checkbox' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'radio' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RADIO_BUTTON',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DROP_DOWN',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'acceptance' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RADIO_BUTTON',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DROP_DOWN',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'quiz' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RADIO_BUTTON',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DROP_DOWN',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT'
		),
		'file' => array(
			'FILE',
		)

	);

	private $kintone_fieldcode_supported_list = array(
	    'SINGLE_LINE_TEXT' => 'text',
	    'NUMBER' => 'number',
	    'RADIO_BUTTON' => 'radio',
	    'CHECK_BOX' => 'checkbox',
	    'MULTI_SELECT' => 'select',
	    'DROP_DOWN' => 'select',
	    'DATE' => 'date',
	    'TIME' => '',
	    'DATETIME' => '',
	    'LINK' => 'url',
	    'RICH_TEXT' => 'textarea',
	    'MULTI_LINE_TEXT' => 'textarea'
	);




	function __construct()
	{
		$data = get_file_data(
			__FILE__,
			array( 'ver' => 'Version', 'langs' => 'Domain Path' )
		);
		$this->version = $data['ver'];
		$this->langs   = $data['langs'];

	}

	public function register()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 1 );
	}

	public function plugins_loaded()
	{
		load_plugin_textdomain(
			'kintone-form',
			false,
			dirname( plugin_basename( __FILE__ ) ).$this->langs
		);

		add_action('admin_menu', array( $this, 'admin_menu' ) );


		// Add Setting Panel
		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );
		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'wpcf7_contact_form_properties' ), 10, 2 );
		add_action( 'wpcf7_save_contact_form', array( $this, 'wpcf7_save_contact_form' ), 10, 3 );
		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );

		add_action( 'wpcf7_before_send_mail', array( $this, 'kintone_form_send' ),1);

		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );

	}

	public function register_assets() {

		// styles
		wp_enqueue_style('kintone-form', plugin_dir_url( __FILE__ ).'asset/css/kintone-form.css', array());

	}

	public function form_data_to_kintone_setting(){

		$license_result_attachment = "";
		$license_result_multiple_kintone_app = "";

		if(! empty( $_POST ) &&  check_admin_referer($this->nonce)){

			if( isset($_POST['kintone_to_wp_license_key_attachment']) &&  !empty($_POST['kintone_to_wp_license_key_attachment']) ){

				$kintone_to_wp_license_key_attachment = sanitize_text_field($_POST['kintone_to_wp_license_key_attachment']);

				update_option('_kintone_to_wp_license_key_attachment', base64_encode($kintone_to_wp_license_key_attachment));

				$license_result_attachment = $this->gumroad_verify_license($kintone_to_wp_license_key_attachment, GUMROAD_KINTONE_FORM_ATTACHMENTS_SLUG);

			}else{
				delete_option('_kintone_to_wp_license_key_attachment');
			}

			if( isset($_POST['kintone_to_wp_license_key_multiple_kintone_app']) &&  !empty($_POST['kintone_to_wp_license_key_multiple_kintone_app']) ){

				$kintone_to_wp_license_key_multiple_kintone_app = sanitize_text_field($_POST['kintone_to_wp_license_key_multiple_kintone_app']);
				update_option('_kintone_to_wp_license_key_multiple_kintone_app', base64_encode($kintone_to_wp_license_key_multiple_kintone_app));

				$license_result_multiple_kintone_app = $this->gumroad_verify_license($kintone_to_wp_license_key_multiple_kintone_app, GUMROAD_KINTONE_FORM_MULTI_KINTONE_APP_SLUG);

			}else{
				delete_option('_kintone_to_wp_license_key_multiple_kintone_app');
			}

			echo '<div class="updated notice is-dismissible"><p><strong>Success</strong></p></div>';


		}

		$wp_n = wp_nonce_field($this->nonce);
		$kintone_to_wp_license_key_attachment = base64_decode(get_option( '_kintone_to_wp_license_key_attachment' ));
		$kintone_to_wp_license_key_multiple_kintone_app = base64_decode(get_option( '_kintone_to_wp_license_key_multiple_kintone_app' ));



	?>


		<div class="wrap">

			<h1>Form data to kintone add-ons:UPDATE</h1>
			<p>Coming Soon... :)</p>
			<div class="form-data-to-kintone-setting-block">

				<div class="title">
					<h3>License Information</h3>
				</div>
				<div class="inner">

					<form method="post" action="">
						<?php echo $wp_n; ?>
						<table class="form-table">
				        	<tr valign="top">
				        		<th scope="row"><label for="add_text">License key : Add-on Attachment</label></th>
				        		<td>
				        			<input name="kintone_to_wp_license_key_attachment" type="text" id="kintone_to_wp_license_key_attachment" value="<?php echo ($kintone_to_wp_license_key_attachment == "" ? "" : esc_textarea($kintone_to_wp_license_key_attachment)); ?>" class="regular-text" />
				        			<?php if(is_wp_error($license_result_attachment)): ?>
				        				<br><span style="color:red;"><?php echo $license_result_attachment->get_error_message(); ?></span>
				        			<?php endif; ?>
				        		</td>
				        	</tr>
				        	<tr valign="top">
				        		<th scope="row"><label for="add_text">License key : Add-on Multiple kintone app</label></th>
				        		<td>
				        			<input name="kintone_to_wp_license_key_multiple_kintone_app" type="text" id="kintone_to_wp_license_key_multiple_kintone_app" value="<?php echo ($kintone_to_wp_license_key_multiple_kintone_app == "" ? "" : esc_textarea($kintone_to_wp_license_key_multiple_kintone_app)); ?>" class="regular-text" />
				        			<?php if(is_wp_error($license_result_multiple_kintone_app)): ?>
				        				<br><span style="color:red;"><?php echo $license_result_multiple_kintone_app->get_error_message(); ?></span>
				        			<?php endif; ?>
				        		</td>
				        	</tr>

			        	</table>

				        <p class="submit"><input type="submit" class="button-primary" value="Activate License" /></p>

					</form>
				</div>
			</div>
		</div>




<?php

	}


	public function gumroad_verify_license( $license, $guid ) {
	    $ch = curl_init( 'https://api.gumroad.com/v2/licenses/verify' );
	    curl_setopt_array( $ch, [
	        CURLOPT_CONNECTTIMEOUT => 10,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_POST           => true,
	        CURLOPT_POSTFIELDS     => "product_permalink={$guid}&license_key={$license}",
	    ] );
	    $result = curl_exec( $ch );
	    curl_close( $ch );
	    if ( ! $result ) {
	        return false;
	    }
	    if ( ( $json = json_decode( $result ) ) && $json->success ) {
	        return $json;
	    } else {
	        return new WP_Error( 'invalid_license', '無効なライセンスです。', [ 'status' => 403 ] );
	    }
	}

	public function admin_menu(){
		add_menu_page( 'Form data to kintone', 'Form data to kintone', 'manage_options', 'form-data-to-kintone-setting', array( $this,'form_data_to_kintone_setting' ) );

	}


	public function wpcf7_editor_panels( $panels ){


		$panels['form-kintone-panel'] = array(
				'title' => 'kintone',
				'callback' => array( $this, 'form_kintone_panel_form')
			);

		return $panels;

	}

	public function form_kintone_panel_form( $post ) {

		$kintone_setting_data = get_post_meta( $post->id(), '_kintone_setting_data', true );

		$kintone_setting_data = wp_parse_args( $kintone_setting_data, array(
			'domain' => '',
			'app_datas' => array()
		) );

		$domain = $kintone_setting_data['domain'];
		$mailtags = $post->collect_mail_tags();
		$tags = $post->scan_form_tags();

		$this->kintone_fieldcode_supported_list = apply_filters( 'kintone_fieldcode_supported_list', $this->kintone_fieldcode_supported_list );



	?>
		<h2><?php echo esc_html( __( 'Setting kintone', 'form-kintone' ) ); ?></h2>
		<fieldset>

		<p class="description">
			<label for="kintone-form-domain">kintone domain:
				<input type="text" id="kintone-form-domain" name="kintone_setting_data[domain]" class="" size="70" value="<?php echo esc_attr( $domain ); ?>" />
			</label>
		</p>

		<p class="description">
			<div class="repeat">
				<div id="kintone_form_setting" class="wrapper" style="border-collapse: collapse;">

					<div class="container">

						<?php if( isset($kintone_setting_data['app_datas']) && !empty($kintone_setting_data['app_datas']) ): ?>

							<?php $i = 0; ?>
							<?php foreach ($kintone_setting_data['app_datas'] as $app_data): ?>

								<table class="row" style="margin-bottom: 30px; border-top: 6px solid #ccc; width: 100%;">
									<tr>
										<td valign="top" style="padding: 10px 0px;">
											APP ID:<input type="text" id="kintone-form-appid" name="kintone_setting_data[app_datas][<?php echo $i; ?>][appid]" class="small-text" size="70" value="<?php echo esc_attr( $app_data['appid'] ); ?>" />
											Api Token:<input type="text" id="kintone-form-token" name="kintone_setting_data[app_datas][<?php echo $i; ?>][token]" class="regular-text" size="70" value="<?php echo esc_attr( $app_data['token'] ); ?>" />
										</td>
										<td><span class="remove button">Remove</span></td>
									</tr>
									<tr>
	                                    <td colspan="2">
	                                        <table style="width: 100%;">

	                                        <tr>
	                                        	<th style="text-align: left; padding: 5px 10px;">kintone Label(fieldcode)</th>
	                                        	<th></th>
	                                        	<th style="text-align: left; padding: 5px 10px;">Contact form 7 mail tag</th>
	                                        	<th style="text-align: left; padding: 5px 10px;">Example Shortcode</th>
	                                        </tr>

	                                        <?php if(isset($app_data['formdata']['properties'])): ?>
		                                        <?php foreach ($app_data['formdata']['properties'] as $form_data): ?>
		                                        	<?php

		                                        	if(isset($form_data['code'])):
			                                        	$select_option = '';
			                                        	if(isset($app_data['setting'][$form_data['code']]) && !empty($app_data['setting'][$form_data['code']]) ){
			                                        		$select_option = $app_data['setting'][$form_data['code']];
			                                        	}

			                                        	$error_msg = $this->check_consistency( $tags, $select_option, $form_data );

			                                        	?>

				                                        <tr>
				                                            <td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;"><?php echo esc_html( $form_data['label'] ).'('. esc_html( $form_data['code'] ).')'; ?></td>
				                                            <td><-</td>
				                                            <td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
				                                            	<?php if( array_key_exists( $form_data['type'] ,$this->kintone_fieldcode_supported_list ) ): ?>

					                                                <select name="kintone_setting_data[app_datas][<?php echo $i; ?>][setting][<?php echo esc_attr( $form_data['code'] ) ?>]">
					                                                	<option value=""></option>
					                                                	<?php foreach ($mailtags as $value): ?>
					                                                		<option <?php selected( $value, $select_option ); ?> value="<?php echo esc_attr($value); ?>">[<?php echo esc_attr($value); ?>]</option>
					                                                	<?php endforeach; ?>
					                                                </select>
					                                                <?php if($error_msg): ?>
					                                                	<div style="color:red; font-weight:bold;"><?php echo $error_msg; ?></div>
					                                                <?php endif; ?>
					                                            <?php else: ?>
					                                            	<?php if( $form_data['type'] == 'FILE' ): ?>
					                                            		<a href="<?php echo admin_url('admin.php?page=form-data-to-kintone-setting'); ?>" title="">Add-Ons</a>
					                                            	<?php else: ?>
					                                            		Not Support
					                                            	<?php endif; ?>
				                                            	<?php endif; ?>
				                                            </td>
				                                            <td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
				                                            	<?php if( array_key_exists( $form_data['type'] ,$this->kintone_fieldcode_supported_list ) ): ?>
				                                            		<?php echo $this->create_sample_shortcode( $form_data, $select_option ); ?>
				                                            	<?php endif; ?>
				                                            </td>

				                                        </tr>
				                                    <?php endif; ?>

		                                        <?php endforeach; ?>
		                                    <?php endif; ?>
	                                        </table>
	                                    </td>
	                                </tr>

								</table>

								<?php $i++ ?>

							<?php endforeach; ?>

						<?php else: ?>

							<table class="row" style="margin-bottom: 30px; border-top: 6px solid #ccc; width: 100%;">
								<tr>
									<td valign="top" style="padding: 10px 0px;">
										APP ID:<input type="text" id="kintone-form-appid" name="kintone_setting_data[app_datas][0][appid]" class="small-text" size="70" value="" />
										Api Token:<input type="text" id="kintone-form-token" name="kintone_setting_data[app_datas][0][token]" class="regular-text" size="70" value="" />
									</td>
								</tr>
							</table>

						<?php endif; ?>

						<?php do_action( 'kintone_form_setting_panel_after' ); ?>

					</div>
					<tfoot>
						<tr>
							<td colspan="2">
								<span class="add button">追加</span> ← <a href="<?php echo admin_url('admin.php?page=form-data-to-kintone-setting'); ?>" title="">Add-Ons</a>
							</td>
						</tr>
					</tfoot>
				</div>
			</div>
		</p>
		</fieldset>
	<?php
	}

	private function create_sample_shortcode( $form_data, $select_option ){

	   $shortcode = '';

	   if(!empty($this->kintone_fieldcode_supported_list[$form_data['type']]) && !empty($select_option)  ){

		   $shortcode .= '[';

			if( $form_data['type'] == 'RADIO_BUTTON' ||
				$form_data['type'] == 'CHECK_BOX' ||
				$form_data['type'] == 'MULTI_SELECT' ||
				$form_data['type'] == 'DROP_DOWN' )
			{
				$options = '"'.implode('" "',$form_data['options']).'"';
				if( $form_data['type'] == 'MULTI_SELECT' ){
					$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $select_option . ' multiple '.$options;
				}else{
					$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $select_option . ' '.$options;
				}



			}else{

				$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $select_option;

			}
			$shortcode .= ']';
		}


		return $shortcode;

	}

	private function check_consistency( $tags, $setting_cf7_mail_tag, $kintone_field ){

		if(empty($setting_cf7_mail_tag)){
			return "";
		}

		foreach ( (array) $tags as $tag ) {
			$type = $tag->basetype;

			if ( empty( $type ) ) {
				continue;
			}else{

				$tag_name = $tag->name;
				if( $setting_cf7_mail_tag == $tag_name ){

					if (in_array($kintone_field['type'], $this->consistency[$type])) {
						return "";
					}else{
						return "This setting is error.";
					}
				}
			}
		}

	}

	public function wpcf7_contact_form_properties( $properties, $contact_form){

		$properties = wp_parse_args( $properties, array(
			'kintone_setting_data' => array()
		) );

		return $properties;
	}

	public function wpcf7_save_contact_form( $contact_form, $args, $context ){

		$properties = array();

		$i = 0;
		if(isset($args['kintone_setting_data']['app_datas'])){
			foreach ($args['kintone_setting_data']['app_datas'] as $app_data) {

				if( !empty($app_data['appid']) && !empty($app_data['token']) && !empty($args['kintone_setting_data']['domain']) ){

					$url = 'https://'.$args['kintone_setting_data']['domain'].'/k/v1/form.json?app='.$app_data['appid'];
					$kintone_form_data = $this->kintone_api( $url, $app_data['token'] );


					if( !is_wp_error($kintone_form_data) ){

						$app_data['formdata'] = $kintone_form_data;
						$args['kintone_setting_data']['app_datas'][$i] = $app_data;
					}

				}

				$i++;

			}
		}
		$properties['kintone_setting_data'] = $args['kintone_setting_data'];
		$contact_form->set_properties( $properties );

	}

	public function kintone_form_send( $wpcf7_data ){

		$kintone_setting_data = $wpcf7_data->prop('kintone_setting_data');
		if(empty($kintone_setting_data)){
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

		foreach ($kintone_setting_data['app_datas'] as $appdata) {

			$kintone_post_data[$post_data_count]['appid'] = $appdata['appid'];
			$kintone_post_data[$post_data_count]['token'] = $appdata['token'];
			$kintone_post_data[$post_data_count]['datas'] = array();

			if(isset($appdata['setting'])){

				foreach ($appdata['setting'] as $kintone_fieldcode => $cf7_mail_tag) {

					if(!empty($cf7_mail_tag)){

						foreach ($appdata['formdata']['properties'] as $kintone_form_data) {

							if(isset($kintone_form_data['code'])){
								if( $kintone_fieldcode == $kintone_form_data['code'] ){

									switch ( $kintone_form_data['type'] ) {
									    case 'SINGLE_LINE_TEXT':
									        $post_data = KintoneForm_text::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'NUMBER':
									    	$post_data = KintoneForm_number::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'RADIO_BUTTON':
									    	$post_data = KintoneForm_radio::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'CHECK_BOX':
									    	$post_data = KintoneForm_checkbox::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'MULTI_SELECT':
									    	$post_data = KintoneForm_multi_select::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'DROP_DOWN':
									    	$post_data = KintoneForm_drop_down::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'DATE':
									    	$post_data = KintoneForm_date::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'TIME':
									    	$post_data = KintoneForm_time::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'DATETIME':
									    	$post_data = KintoneForm_datetime::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'LINK':
									        $post_data = KintoneForm_link::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									    	break;
									    case 'RICH_TEXT':
									        $post_data = KintoneForm_rich_text::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									        break;
									    case 'MULTI_LINE_TEXT':
									        $post_data = KintoneForm_multi_line_text::format_to_kintone_data( $kintone_form_data, $cf7_send_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
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
									    	$post_data = apply_filters( 'kintone_form_attachments_data', $kintone_setting_data, $appdata, $cf7_send_data, $kintone_form_data, $cf7_mail_tag, $e );
									        if( isset($post_data['value']) && !empty($post_data['value'])){
									        	$kintone_post_data[$post_data_count]['datas'][$kintone_form_data['code']] = $post_data;
									        }
									    	break;
									}

								}
							}

						}

					}
				}
			}

			$post_data_count++;

		}

		if ($e->get_error_code()) {

			$this->erro_mail($e);


		}else{

			foreach ($kintone_post_data as $data) {

				if( !empty($kintone_setting_data['domain']) && !empty($data['token']) && !empty($data['appid']) ){
					$url = 'https://'.$kintone_setting_data['domain'].'/k/v1/record.json';
					$this->save_data( $url, $data['token'], $data['appid'], $data['datas'] );
				}

			}
		}
	}

	private function erro_mail( $e ){

		$error_msg = "";
		$error_msg = implode("\r\n", $e->get_error_messages());

		$to = get_option( 'admin_email' );
		$subject = 'kintone form post error';
		$body = $error_msg;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $to, $subject, $body, $headers );


	}

	private function save_data( $url, $token, $appid, $datas )
	{

	    $headers["X-Cybozu-API-Token"] = $token;
	    $headers["Content-Type"] = "application/json";

	    $body = array(
	        "app"	=> $appid,
	        "record" => $datas
	    );

	    $res = wp_remote_post(
	        $url,
	        array(
	            "method"  => "POST",
	            "headers" => $headers,
	            "body"	=> json_encode( $body )
	        )
	    );

	    if ( is_wp_error( $res ) ) {
	    	$this->erro_mail( $res );
	        return $res;
	    } elseif (  $res["response"]["code"] !== 200 ) {
	        $message = json_decode( $res["body"], true );
	        $e = new WP_Error();
	        $e->add( "validation-error", $message["message"], $message );
	        $this->erro_mail( $e );
	        return $e;
	    } else {
	        return true;
	    }
	}


	public function kintone_api( $request_url, $kintone_token, $file = false ){

		if( $request_url ){

			$headers = array( 'X-Cybozu-API-Token' =>  $kintone_token );

			$res = wp_remote_get(
				$request_url,
				array(
					'headers' => $headers
				)
			);

			if ( is_wp_error( $res ) ) {

				return $res;

			} else {

				if( $file ){
					$return_value = $res['body'];
				}else{
					$return_value = json_decode( $res['body'], true );
				}

				if ( isset( $return_value['message'] ) && isset( $return_value['code'] ) ) {

					echo '<div class="error fade"><p><strong>'.$return_value['message'].'</strong></p></div>';
					return new WP_Error( $return_value['code'], $return_value['message'] );
				}

				return $return_value;
			}
		}else{
			echo '<div class="error fade"><p><strong>URL is required</strong></p></div>';
			return new WP_Error( 'Error', 'URL is required' );
		}

	}

}
