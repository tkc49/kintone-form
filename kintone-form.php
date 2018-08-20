<?php
/**
 * Plugin Name: Form data to kintone
 * Plugin URI:
 * Description: This plugin is an addon for "Contact form 7".
 * Version:	 1.10.2
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

require KINTONE_FORM_PATH . '/includes/check-acceptance.php';




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
			'MULTI_LINE_TEXT',
			'DROP_DOWN'
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
		),
		'hidden' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
			'DROP_DOWN'
		),
		'time' => array(
			'SINGLE_LINE_TEXT',
			'TIME',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		

	);

	private $kintone_fieldcode_supported_list = array(
	    'SINGLE_LINE_TEXT' => 'text',
	    'NUMBER' => 'number',
	    'RADIO_BUTTON' => 'radio',
	    'CHECK_BOX' => 'checkbox',
	    'MULTI_SELECT' => 'select',
	    'DROP_DOWN' => 'select',
	    'DATE' => 'date',
	    'TIME' => 'time',
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

		add_action( 'wpcf7_admin_init', array( $this, 'kintone_form_add_tag_generator_text' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );


	}

	public function kintone_form_add_tag_generator_text() {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'kintone', __( 'kintone', 'kintone-form' ),
			array( $this, 'kintone_form_tag_generator') );
	}

	public function kintone_form_tag_generator( $contact_form, $args = '' ) {

		global $post;

		$args = wp_parse_args( $args, array() );
		$type = $args['id'];
	
		if ( ! in_array( $type, array( 'email', 'url', 'tel' ) ) ) {
			$type = 'text';
		}
	
		$description = 'Please copy & paste the code below.';
		$properties = $contact_form->get_properties();
		$kintone_setting_data = $properties['kintone_setting_data'];

		$insert_code = '';

		foreach ( $kintone_setting_data['app_datas'] as $appdata) {
			
			if( isset($appdata['formdata']['properties']) ){
				foreach ($appdata['formdata']['properties'] as $form_data){

					if(isset($form_data['code'])){

						$select_option = '';
						if(isset($appdata['setting'][$form_data['code']]) && !empty($appdata['setting'][$form_data['code']]) ){
							$select_option = $appdata['setting'][$form_data['code']];
						}

						$original_cf7tag_name = '';
						$selectbox_readonly = '';
						if(isset($appdata['setting_original_cf7tag_name'][$form_data['code']]) && !empty($appdata['setting_original_cf7tag_name'][$form_data['code']]) ){
							$original_cf7tag_name = $appdata['setting_original_cf7tag_name'][$form_data['code']];
						}

						$code = wp_strip_all_tags( $this->create_sample_shortcode($form_data, $select_option, $original_cf7tag_name, ''));
						if($code){
							$insert_code .= '<label> '.$form_data['label']."\n    ".$code."</label>\n\n";
						}
								
					}
				}
			}

		}
		
	?>
		<div class="control-box">
		<fieldset>
			<legend><?php echo esc_html( $description ); ?></legend>
			<textarea class="tag code" name="" id="" rows="18" style="width:100%" readonly="readonly"><?php echo $insert_code; ?></textarea>
		</fieldset>
		</div>
		
		<div class="insert-box">
		
			<div class="submitbox">
			<input type="button" class="button button-primary kintone-form-insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>
		
		</div>
	<?php
	}
		


	public function register_assets() {

		// styles
		wp_enqueue_style('kintone-form', plugin_dir_url( __FILE__ ).'asset/css/kintone-form.css', array());

		global $wp_query;

		wp_enqueue_script('jquery');
		// register our main script but do not enqueue it yet
		wp_register_script( 'my_loadmore', plugin_dir_url( __FILE__ ).'asset/js/myloadmore.js', array('jquery'),'',true );

		wp_localize_script( 'my_loadmore', 'misha_loadmore_params', array(
			'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
			'posts' => 'hosoya', // everything about your loop is here
			'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
			'max_page' => $wp_query->max_num_pages
		) );

		wp_enqueue_script( 'my_loadmore' );


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
			'email_address_to_send_kintone_registration_error' => get_option('admin_email'),
			'app_datas' => array()
		) );

		$domain = $kintone_setting_data['domain'];
		$email_address_to_send_kintone_registration_error = $kintone_setting_data['email_address_to_send_kintone_registration_error'];

		$kintone_basic_authentication_id = "";
		if( isset($kintone_setting_data['kintone_basic_authentication_id']) ){
			$kintone_basic_authentication_id = $kintone_setting_data['kintone_basic_authentication_id'];	
		}

		$kintone_basic_authentication_password = "";
		if( isset($kintone_setting_data['kintone_basic_authentication_password']) ){
			$kintone_basic_authentication_password = self::decode($kintone_setting_data['kintone_basic_authentication_password']);
		}

		$mailtags = $post->collect_mail_tags();
		$tags = $post->scan_form_tags();

		$this->kintone_fieldcode_supported_list = apply_filters( 'kintone_fieldcode_supported_list', $this->kintone_fieldcode_supported_list );



	?>
		<h2><?php echo esc_html( __( 'Setting kintone', 'form-kintone' ) ); ?></h2>
		<fieldset>

		<p class="description">

			<table>
				<tr>
					<th>kintone domain:</th>
					<td><input type="text" id="kintone-form-domain" placeholder="xxxx.cybozu.com" name="kintone_setting_data[domain]" class="" size="70" value="<?php echo esc_attr( $domain ); ?>" /></td>
				</tr>
				<tr>
					<th>E-mail address to send kintone registration error:</th>
					<td><input type="text" id="email-address-to-send-kintone-registration-error" name="kintone_setting_data[email_address_to_send_kintone_registration_error]" class="" size="70" value="<?php echo esc_attr( $email_address_to_send_kintone_registration_error ); ?>" /></td>
				</tr>
				<tr>
					<th>Basic Authentication:</th>
					<td>
						ID： <input type="text" id="kintone-basic-authentication-id" name="kintone_setting_data[kintone_basic_authentication_id]" class="" size="30" value="<?php echo esc_attr( $kintone_basic_authentication_id ); ?>" /> / Password： <input type="password" id="kintone-basic-authentication-password" name="kintone_setting_data[kintone_basic_authentication_password]" class="" size="30" value="<?php echo esc_attr( $kintone_basic_authentication_password ); ?>" />
					</td>
				</tr>	
			</table>
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
											<input type="submit" class="button-primary" name="get-kintone-data" value="GET">
										</td>
										<td></td>
										<td><span class="remove button">Remove</span></td>
									</tr>
									<tr>
	                                    <td colspan="2">
	                                        <table style="width: 100%;">

	                                        <tr>
	                                        	<th style="text-align: left; padding: 5px 10px;">kintone Label(fieldcode)</th>
	                                        	<th></th>
	                                        	<th style="text-align: left; padding: 5px 10px;">Contact form 7 mail tag</th>
	                                        	<th style="text-align: left; padding: 5px 10px;">Example Contact Form 7's Shortcode<br>※ Change <span style="color:red">your-cf7-tag-name</span> to original name ( your-name or your-email or etc )</th>
	                                        </tr>
											
	                                        <?php if(isset($app_data['formdata']['properties'])): ?>
		                                        <?php foreach ($app_data['formdata']['properties'] as $form_data): ?>
		                                        	<?php

		                                        	if(isset($form_data['code'])):
														$select_option = '';
														
			                                        	if(isset($app_data['setting'][$form_data['code']]) && !empty($app_data['setting'][$form_data['code']]) ){
															$select_option = $app_data['setting'][$form_data['code']];
														}

														$original_cf7tag_name = '';
														$selectbox_readonly = '';
			                                        	if(isset($app_data['setting_original_cf7tag_name'][$form_data['code']]) && !empty($app_data['setting_original_cf7tag_name'][$form_data['code']]) ){
															$original_cf7tag_name = $app_data['setting_original_cf7tag_name'][$form_data['code']];
															$selectbox_readonly = 'disabled="disabled"';
														}
														
			                                        	$error_msg = $this->check_consistency( $tags, $select_option, $form_data );
			                                        	?>

														<?php $hash = hash('md5', $form_data['code']) ?>

				                                        <tr>
				                                            <td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;"><?php echo esc_html( ( isset( $form_data['label'] ) ) ? $form_data['label'] : "" ).'('. esc_html( $form_data['code'] ).')'; ?></td>
				                                            <td><-</td>
				                                            <td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
				                                            	<?php if( array_key_exists( $form_data['type'] ,$this->kintone_fieldcode_supported_list ) ): ?>

					                                                <select id="cf7-mailtag-<?php echo $hash; ?>" <?php echo $selectbox_readonly; ?> name="kintone_setting_data[app_datas][<?php echo $i; ?>][setting][<?php echo esc_attr( $form_data['code'] ) ?>]">
					                                                	<option value=""></option>
					                                                	<?php foreach ($mailtags as $value): ?>
					                                                		<option <?php selected( $value, $select_option ); ?> value="<?php echo esc_attr($value); ?>">[<?php echo esc_attr($value); ?>]</option>
					                                                	<?php endforeach; ?>
					                                                </select> or <input type="text" id="<?php echo $hash; ?>" class="your-cf7-tag-name" placeholder="your-cf7-tag-name" name="kintone_setting_data[app_datas][<?php echo $i; ?>][setting_original_cf7tag_name][<?php echo esc_attr( $form_data['code'] ) ?>]" value="<?php echo $original_cf7tag_name; ?>" />
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
				                                            		<?php echo $this->create_sample_shortcode( $form_data, $select_option, $original_cf7tag_name, $hash ); ?>
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
										<input type="submit" class="button-primary" name="get-kintone-data" value="GET">
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

	private function create_sample_shortcode( $form_data, $select_option, $original_cf7tag_name, $hash ){

		$tag_name = '';
		if( $original_cf7tag_name ){
			$tag_name = $original_cf7tag_name;
		}elseif( $select_option ){
			$tag_name = $select_option;
		}else{
			$tag_name = 'your-cf7-tag-name';
		}

		$cf7_mailtag_name = '<span id="short-code-' . $hash . '" style="color:red">'.$tag_name.'</span>';

		$shortcode = '';

	   if(!empty($this->kintone_fieldcode_supported_list[$form_data['type']]) ){

		   $shortcode .= '[';

			if( $form_data['type'] == 'RADIO_BUTTON' ||
				$form_data['type'] == 'CHECK_BOX' ||
				$form_data['type'] == 'MULTI_SELECT' ||
				$form_data['type'] == 'DROP_DOWN' )
			{
				$options = '"'.implode('" "',$form_data['options']).'"';
				if( $form_data['type'] == 'MULTI_SELECT' ){
					$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $cf7_mailtag_name . ' multiple '.$options;
				}else{
					$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $cf7_mailtag_name . ' '.$options;
				}

			}else{

				$shortcode .= $this->kintone_fieldcode_supported_list[$form_data['type']] . ' ' . $cf7_mailtag_name;

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


	public static function encode( $value ){
		return base64_encode(md5(AUTH_SALT) . $value . md5(md5(AUTH_SALT)));
	}

	public static function decode( $encoded ){
		return preg_match('/^[a-f0-9]{32}$/', $encoded) ? $encoded : str_replace(array(md5(AUTH_SALT), md5(md5(AUTH_SALT))), '', base64_decode($encoded));
	}

	public function wpcf7_save_contact_form( $contact_form, $args, $context ){

		$properties = array();

		$i = 0;
		if(isset($args['kintone_setting_data']['app_datas'])){
			foreach ($args['kintone_setting_data']['app_datas'] as $app_data) {

				if( !empty($app_data['appid']) && !empty($app_data['token']) && !empty($args['kintone_setting_data']['domain']) ){

					$url = 'https://'.$args['kintone_setting_data']['domain'].'/k/v1/form.json?app='.$app_data['appid'];
					$kintone_form_data = $this->kintone_api( $url, $app_data['token'], $args['kintone_setting_data']['kintone_basic_authentication_id'], $args['kintone_setting_data']['kintone_basic_authentication_password'] );
										
					if( $args['kintone_setting_data']['kintone_basic_authentication_password'] ){
						$args['kintone_setting_data']['kintone_basic_authentication_password'] = self::encode( $args['kintone_setting_data']['kintone_basic_authentication_password'] );
					}
					
					if( !is_wp_error($kintone_form_data) ){
						$app_data['formdata'] = $kintone_form_data;
					}

					$args['kintone_setting_data']['app_datas'][$i] = $app_data;

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

			$kintone_data_for_post = $this->get_data_for_post($appdata);

			if(isset($kintone_data_for_post['setting'])){

				foreach ($kintone_data_for_post['setting'] as $kintone_fieldcode => $cf7_mail_tag) {

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

			$this->erro_mail( $e, $kintone_setting_data['email_address_to_send_kintone_registration_error']);


		}else{

			foreach ($kintone_post_data as $data) {

				if( !empty($kintone_setting_data['domain']) && !empty($data['token']) && !empty($data['appid']) ){
					$url = 'https://'.$kintone_setting_data['domain'].'/k/v1/record.json';
					$this->save_data( $url, $data['token'], $data['appid'], $kintone_setting_data['kintone_basic_authentication_id'], self::decode($kintone_setting_data['kintone_basic_authentication_password']), $data['datas'], $kintone_setting_data['email_address_to_send_kintone_registration_error'] );
				}

			}
		}
	}

	private function get_data_for_post( $appdata ){

		$data['setting'] = array();

		if( isset($appdata['setting_original_cf7tag_name']) && !empty($appdata['setting_original_cf7tag_name']) ){

			foreach ($appdata['setting_original_cf7tag_name'] as $key => $value) {
				if( $value ){
					$data['setting'][$key] = $value;
				}else{
					if(isset($appdata['setting'][$key])){
						$data['setting'][$key] = $appdata['setting'][$key];
					}	
				}
			}

		}else{
			if(isset($appdata['setting'])){
				return $appdata['setting'];
			}
		}

		return $data;
	}

	private function erro_mail( $e, $email_address_to_send_kintone_registration_error ){

		$error_msg = "";
		$error_msg .= implode("\r\n", $e->get_error_messages())."\r\n";
		$error_msg .= var_export($e->get_error_data(), true);
		

		if( $email_address_to_send_kintone_registration_error ){
			$to = $email_address_to_send_kintone_registration_error;
		}else{
			$to = get_option( 'admin_email' );
		}
		
		$subject = 'kintone form post error';
		$body = $error_msg;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $to, $subject, $body, $headers );


	}

	private function save_data( $url, $token, $appid, $basic_auth_user, $basic_auth_pass, $datas, $email_address_to_send_kintone_registration_error )
	{

		$headers = array_merge(
			self::get_auth_header( $token ),
			self::get_basic_auth_header( $basic_auth_user, $basic_auth_pass ),
			array( 'Content-Type' => 'application/json' )
		);


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
	    	$this->erro_mail( $res, $email_address_to_send_kintone_registration_error );
	        return $res;
	    } elseif (  $res["response"]["code"] !== 200 ) {
			
			$message = json_decode( $res["body"], true );
	        $e = new WP_Error();
	        $e->add( "validation-error", $message["message"], $message["errors"] );
	        $this->erro_mail( $e, $email_address_to_send_kintone_registration_error );
	        return $e;
	    } else {
	        return true;
	    }
	}

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org
	public static function get_basic_auth_header( $basic_auth_user = null, $basic_auth_pass = null )
	{
		if ( $basic_auth_user && $basic_auth_pass ) {
			$auth = base64_encode( $basic_auth_user.':'.$basic_auth_pass );
			return array( 'Authorization' => 'Basic '.$auth );
		} else {
			return array();
		}
	}

	//  form data to kintone WordPress Plugin incorporates code from WP to kintone WordPress Plugin, Copyright 2016 WordPress.org	
	public static function get_auth_header( $token )
	{
		if ( $token ) {
			return array( 'X-Cybozu-API-Token' => $token );
		} else {
			return new WP_Error( 'kintone', 'API Token is required' );
		}
	}


	public function kintone_api( $request_url, $kintone_token, $basic_auth_user = null, $basic_auth_pass = null ,$file = false ){

		if( $request_url ){

			$headers = array_merge(
				self::get_auth_header( $kintone_token ), self::get_basic_auth_header( $basic_auth_user, $basic_auth_pass )
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

				if( $res['response']['code'] != 200 ){

					set_transient( 'my-custom-admin-errors', 'kintone Error: ' . $res['response']['message'].'(Code='.$res['response']['code'].')' , 10 );
					return new WP_Error( $res['response']['code'], $res['response']['message'] );

				}else{

					if( $file ){
						$return_value = $res['body'];
					}else{
						$return_value = json_decode( $res['body'], true );
					}	
				}

				return $return_value;
			}
		}else{
			echo '<div class="error fade"><p><strong>URL is required</strong></p></div>';
			return new WP_Error( 'Error', 'URL is required' );
		}

	}
	public function admin_notices(){
?>

    <?php if ( $message = get_transient( 'my-custom-admin-errors' ) ): ?>

    <div class="error">
        <ul>
			<li><?php echo esc_html($message); ?></li>
        </ul>
    </div>
    <?php endif; ?>

<?php
	}

}
