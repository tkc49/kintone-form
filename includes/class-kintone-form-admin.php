<?php
/**
 * Admin
 *
 * @package Kintone_Form
 */

/**
 * Admin
 */
class Kintone_Form_Admin {

	/**
	 * CF7のメールタグとkintoneのフィールドコードの関連付け.
	 *
	 * @var $consistency
	 */
	private $consistency = array(
		'text'       => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
			'DROP_DOWN',
		),
		'email'      => array(
			'SINGLE_LINE_TEXT',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'url'        => array(
			'SINGLE_LINE_TEXT',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'tel'        => array(
			'SINGLE_LINE_TEXT',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'number'     => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'range'      => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'date'       => array(
			'SINGLE_LINE_TEXT',
			'DATE',
			'DATETIME',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'textarea'   => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'select'     => array(
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
			'MULTI_LINE_TEXT',
		),
		'checkbox'   => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'radio'      => array(
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
			'MULTI_LINE_TEXT',
		),
		'acceptance' => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'RADIO_BUTTON',
			'CHECK_BOX',
			'MULTI_SELECT',
			'DROP_DOWN',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),
		'quiz'       => array(
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
			'MULTI_LINE_TEXT',
		),
		'file'       => array(
			'FILE',
		),
		'hidden'     => array(
			'SINGLE_LINE_TEXT',
			'NUMBER',
			'DATE',
			'TIME',
			'DATETIME',
			'LINK',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
			'DROP_DOWN',
		),
		'time'       => array(
			'SINGLE_LINE_TEXT',
			'TIME',
			'RICH_TEXT',
			'MULTI_LINE_TEXT',
		),


	);

	/**
	 * ???.
	 *
	 * @var $kintone_fieldcode_supported_list
	 */
	private $kintone_fieldcode_supported_list = array(
		'SINGLE_LINE_TEXT' => 'text',
		'NUMBER'           => 'number',
		'RADIO_BUTTON'     => 'radio',
		'CHECK_BOX'        => 'checkbox',
		'MULTI_SELECT'     => 'select',
		'DROP_DOWN'        => 'select',
		'DATE'             => 'date',
		'TIME'             => 'time',
		'DATETIME'         => '',
		'LINK'             => 'url',
		'RICH_TEXT'        => 'textarea',
		'MULTI_LINE_TEXT'  => 'textarea',
	);

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'wpcf7_contact_form_properties' ), 10, 2 );

		add_action( 'wpcf7_admin_init', array( $this, 'kintone_form_add_tag_generator_text' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );

		add_action( 'wpcf7_save_contact_form', array( $this, 'wpcf7_save_contact_form' ), 10, 3 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * 'kintone_setting_data' をプロパティーに設定
	 *
	 * @param array             $properties .
	 * @param WPCF7_ContactForm $contact_form .
	 *
	 * @return array .
	 */
	public function wpcf7_contact_form_properties( $properties, $contact_form ) {

		$properties = wp_parse_args(
			$properties,
			array(
				'kintone_setting_data' => array(),
			)
		);

		return $properties;
	}


	/**
	 * Kintone設定パネルをCF7のタブに追加.
	 *
	 * @param array $panels .
	 *
	 * @return array .
	 */
	public function wpcf7_editor_panels( $panels ) {

		$panels['form-kintone-panel'] = array(
			'title'    => 'kintone',
			'callback' => array( $this, 'form_kintone_panel_form' ),
		);

		return $panels;
	}

	/**
	 * Kintoneパネル画面作成.
	 *
	 * @param WPCF7_ContactForm $post .
	 */
	public function form_kintone_panel_form( $post ) {

		$cf7                  = WPCF7_ContactForm::get_current();
		$kintone_setting_data = $cf7->prop( 'kintone_setting_data' );

		$kintone_setting_data = wp_parse_args(
			$kintone_setting_data,
			array(
				'domain'                                           => '',
				'email_address_to_send_kintone_registration_error' => get_option( 'admin_email' ),
				'app_datas'                                        => array(),
			)
		);

		$domain                                           = $kintone_setting_data['domain'];
		$email_address_to_send_kintone_registration_error = $kintone_setting_data['email_address_to_send_kintone_registration_error'];

		$kintone_basic_authentication_id = '';
		if ( isset( $kintone_setting_data['kintone_basic_authentication_id'] ) ) {
			$kintone_basic_authentication_id = $kintone_setting_data['kintone_basic_authentication_id'];
		}

		$kintone_basic_authentication_password = '';
		if ( isset( $kintone_setting_data['kintone_basic_authentication_password'] ) ) {
			$kintone_basic_authentication_password = Kintone_Form_Utility::decode(
				$kintone_setting_data['kintone_basic_authentication_password']
			);
		}

		$mailtags = $post->collect_mail_tags();
		$tags     = $post->scan_form_tags();

		$this->kintone_fieldcode_supported_list = apply_filters(
			'kintone_fieldcode_supported_list',
			$this->kintone_fieldcode_supported_list
		);

		?>
		<h2><?php esc_html_e( 'Setting kintone', 'kintone-form' ); ?></h2>
		<fieldset>

			<p class="description">

			<table>
				<tr>
					<th><?php esc_html_e( 'kintone domain:', 'kintone-form' ); ?></th>
					<td>
						<input
							type="text"
							id="kintone-form-domain"
							placeholder="xxxx.cybozu.com"
							name="kintone_setting_data[domain]"
							class=""
							size="70"
							value="<?php echo esc_attr( $domain ); ?>"
						/>
					</td>
				</tr>
				<tr>
					<th>
						<?php
						esc_html_e(
							'E-mail address to send kintone registration error:',
							'kintone-form'
						);
						?>
					</th>
					<td>
						<input
							type="text"
							id="email-address-to-send-kintone-registration-error"
							name="kintone_setting_data[email_address_to_send_kintone_registration_error]"
							class="" size="70"
							value="<?php echo esc_attr( $email_address_to_send_kintone_registration_error ); ?>"
						/>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Basic Authentication:', 'kintone-form' ); ?></th>
					<td>
						ID：
						<input
							type="text"
							id="kintone-basic-authentication-id"
							name="kintone_setting_data[kintone_basic_authentication_id]"
							class=""
							size="30"
							value="<?php echo esc_attr( $kintone_basic_authentication_id ); ?>"
						/>
						/ Password：
						<input
							type="password"
							id="kintone-basic-authentication-password"
							name="kintone_setting_data[kintone_basic_authentication_password]"
							class=""
							size="30"
							value="<?php echo esc_attr( $kintone_basic_authentication_password ); ?>"
						/>
					</td>
				</tr>
			</table>
			</p>

			<p class="description">
			<div class="repeat">
				<div id="kintone_form_setting" class="wrapper" style="border-collapse: collapse;">

					<div class="container">

						<?php if ( isset( $kintone_setting_data['app_datas'] ) && ! empty( $kintone_setting_data['app_datas'] ) ) : ?>

							<?php $i = 0; ?>
							<?php foreach ( $kintone_setting_data['app_datas'] as $app_data ) : ?>

								<table class="row" style="margin-bottom: 30px; border-top: 6px solid #ccc; width: 100%;">
									<tr>
										<td valign="top" style="padding: 10px 0px;">
											APP
											ID:<input
												type="text"
												id="kintone-form-appid"
												name="kintone_setting_data[app_datas][<?php echo esc_attr(
													$i
												); ?>][appid]"
												class="small-text"
												size="70"
												value="<?php echo esc_attr( $app_data['appid'] ); ?>"
											/>
											Api
											Token:<input
												type="text"
												id="kintone-form-token"
												name="kintone_setting_data[app_datas][<?php echo esc_attr(
													$i
												); ?>][token]"
												class="regular-text"
												size="70"
												value="<?php echo esc_attr( $app_data['token'] ); ?>"
											/>
											<input type="submit" class="button-primary" name="get-kintone-data" value="GET">
										</td>
										<td></td>
										<td><span class="remove button">Remove</span></td>
									</tr>
									<tr>
										<td colspan="2">
											<table style="width: 100%;">

												<tr>
													<th style="text-align: left; padding: 5px 10px;">
														<?php
														esc_html_e(
															'kintone Label(fieldcode)',
															'kintone-form'
														);
														?>
													</th>
													<th></th>
													<th style="text-align: left; padding: 5px 10px;">Contact form 7 mail
														tag
													</th>
													<th style="text-align: left; padding: 5px 10px;"><?php _e(
															'Example Contact Form 7\'s Shortcode<br>※ Change <span style="color:red">your-cf7-tag-name</span> to original name ( your-name or your-email or etc )',
															'kintone-form'
														); ?></th>
												</tr>

												<?php if ( isset( $app_data['formdata']['properties'] ) ) : ?>
													<?php foreach ( $app_data['formdata']['properties'] as $form_data ) : ?>
														<?php

														if ( isset( $form_data['code'] ) ) :
															$select_option = '';

															if ( isset( $app_data['setting'][ $form_data['code'] ] ) && ! empty( $app_data['setting'][ $form_data['code'] ] ) ) {
																$select_option = $app_data['setting'][ $form_data['code'] ];
															}

															$original_cf7tag_name = '';
															$selectbox_readonly   = '';
															if ( isset( $app_data['setting_original_cf7tag_name'][ $form_data['code'] ] ) && ! empty( $app_data['setting_original_cf7tag_name'][ $form_data['code'] ] ) ) {
																$original_cf7tag_name = $app_data['setting_original_cf7tag_name'][ $form_data['code'] ];
																$selectbox_readonly   = 'disabled="disabled"';
															}

															$error_msg = $this->check_consistency(
																$tags,
																$select_option,
																$form_data
															);
															?>

															<?php $hash = hash( 'md5', $form_data['code'] ); ?>

															<tr>
																<td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
																	<?php
																	echo esc_html(
																		     ( isset( $form_data['label'] ) ) ? $form_data['label'] : ""
																	     ) . '(' . esc_html(
																		     $form_data['code']
																	     ) . ')';
																	?>
																</td>
																<td><-</td>
																<td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
																	<?php if ( array_key_exists(
																		$form_data['type'],
																		$this->kintone_fieldcode_supported_list
																	) ): ?>

																		<select id="cf7-mailtag-<?php echo $hash; ?>" <?php echo $selectbox_readonly; ?> name="kintone_setting_data[app_datas][<?php echo $i; ?>][setting][<?php echo esc_attr(
																			$form_data['code']
																		) ?>]">
																			<option value=""></option>
																			<?php foreach ( $mailtags as $value ): ?>
																				<option <?php selected(
																					$value,
																					$select_option
																				); ?> value="<?php echo esc_attr(
																					$value
																				); ?>">
																					[<?php echo esc_attr( $value ); ?>]
																				</option>
																			<?php endforeach; ?>
																		</select> or
																		<input type="text" id="<?php echo $hash; ?>" class="your-cf7-tag-name" placeholder="your-cf7-tag-name" name="kintone_setting_data[app_datas][<?php echo $i; ?>][setting_original_cf7tag_name][<?php echo esc_attr(
																			$form_data['code']
																		) ?>]" value="<?php echo $original_cf7tag_name; ?>"/>
																		<?php if ( $error_msg ): ?>
																			<div style="color:red; font-weight:bold;"><?php echo $error_msg; ?></div>
																		<?php endif; ?>
																	<?php else: ?>
																		<?php if ( $form_data['type'] == 'FILE' ): ?>
																			<a href="<?php echo admin_url(
																				'admin.php?page=form-data-to-kintone-setting'
																			); ?>" title="">Add-Ons</a>
																		<?php else: ?>
																			Not Support
																		<?php endif; ?>
																	<?php endif; ?>
																</td>
																<td style="padding: 5px 10px; border-bottom: 1px solid #e2e2e2;">
																	<?php if ( array_key_exists(
																		$form_data['type'],
																		$this->kintone_fieldcode_supported_list
																	) ): ?>
																		<?php echo $this->create_sample_shortcode(
																			$form_data,
																			$select_option,
																			$original_cf7tag_name,
																			$hash
																		); ?>
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

								<?php $i ++; ?>

							<?php endforeach; ?>

						<?php else : ?>

							<table class="row" style="margin-bottom: 30px; border-top: 6px solid #ccc; width: 100%;">
								<tr>
									<td valign="top" style="padding: 10px 0px;">
										APP
										ID:<input type="text" id="kintone-form-appid" name="kintone_setting_data[app_datas][0][appid]" class="small-text" size="70" value=""/>
										Api
										Token:<input type="text" id="kintone-form-token" name="kintone_setting_data[app_datas][0][token]" class="regular-text" size="70" value=""/>
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
							<span class="add button">追加</span> ←
							<a
								href="<?php echo admin_url(
									'admin.php?page=form-data-to-kintone-setting'
								); ?>" title="">Add-Ons</a>
						</td>
					</tr>
					</tfoot>
				</div>
			</div>
			</p>
		</fieldset>
		<?php
	}

	/**
	 * サンプルのショートコードタグを作成する.
	 *
	 * @param array  $form_data .
	 * @param string $select_option .
	 * @param string $original_cf7tag_name .
	 * @param string $hash .
	 *
	 * @return string .
	 */
	private function create_sample_shortcode( $form_data, $select_option, $original_cf7tag_name, $hash ) {

		if ( $original_cf7tag_name ) {
			$tag_name = $original_cf7tag_name;
		} elseif ( $select_option ) {
			$tag_name = $select_option;
		} else {
			$tag_name = 'your-cf7-tag-name';
		}

		$cf7_mailtag_name = '<span id="short-code-' . $hash . '" style="color:red">' . $tag_name . '</span>';

		$shortcode = '';

		if ( ! empty( $this->kintone_fieldcode_supported_list[ $form_data['type'] ] ) ) {

			$shortcode .= '[';

			if ( 'RADIO_BUTTON' === $form_data['type'] || 'CHECK_BOX' === $form_data['type'] || 'MULTI_SELECT' === $form_data['type'] || 'DROP_DOWN' === $form_data['type'] ) {
				$options = '"' . implode( '" "', $form_data['options'] ) . '"';
				if ( 'MULTI_SELECT' === $form_data['type'] ) {
					$shortcode .= $this->kintone_fieldcode_supported_list[ $form_data['type'] ] . ' ' . $cf7_mailtag_name . ' multiple ' . $options;
				} else {
					$shortcode .= $this->kintone_fieldcode_supported_list[ $form_data['type'] ] . ' ' . $cf7_mailtag_name . ' ' . $options;
				}
			} else {

				$shortcode .= $this->kintone_fieldcode_supported_list[ $form_data['type'] ] . ' ' . $cf7_mailtag_name;

			}
			$shortcode .= ']';
		}

		return $shortcode;

	}

	/**
	 * 型のチェック.
	 *
	 * @param array  $tags .
	 * @param string $setting_cf7_mail_tag .
	 * @param array  $kintone_field .
	 *
	 * @return string .
	 */
	private function check_consistency( $tags, $setting_cf7_mail_tag, $kintone_field ) {

		if ( empty( $setting_cf7_mail_tag ) ) {
			return '';
		}

		foreach ( (array) $tags as $tag ) {
			$type = $tag->basetype;

			if ( empty( $type ) ) {
				continue;
			} else {

				$tag_name = $tag->name;
				if ( $setting_cf7_mail_tag === $tag_name ) {

					if ( isset( $kintone_field['type'] ) && isset( $this->consistency[ $type ] ) ) {
						if ( in_array( $kintone_field['type'], $this->consistency[ $type ], true ) ) {
							return '';
						} else {
							return 'This setting is error.';
						}
					} else {
						return '';
					}
				}
			}
		}

	}

	/**
	 * CF7のフォームタブにCF7のショートコードをコピペできるブタンを追加.
	 */
	public function kintone_form_add_tag_generator_text() {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add(
			'kintone',
			__( 'kintone', 'kintone-form' ),
			array( $this, 'kintone_form_tag_generator' )
		);
	}

	/**
	 * タグを生成する.
	 *
	 * @param WPCF7_ContactForm $contact_form .
	 * @param string            $args .
	 */
	public function kintone_form_tag_generator( $contact_form, $args = '' ) {

		$args = wp_parse_args( $args, array() );
		$type = $args['id'];

		if ( ! in_array( $type, array( 'email', 'url', 'tel' ), true ) ) {
			$type = 'text';
		}

		$description          = 'Please copy & paste the code below.';
		$properties           = $contact_form->get_properties();
		$kintone_setting_data = $properties['kintone_setting_data'];

		$insert_code = '';

		if ( isset( $kintone_setting_data['app_datas'] ) ) {
			foreach ( $kintone_setting_data['app_datas'] as $appdata ) {

				if ( isset( $appdata['formdata']['properties'] ) ) {
					foreach ( $appdata['formdata']['properties'] as $form_data ) {

						if ( isset( $form_data['code'] ) ) {

							$select_option = '';
							if ( isset( $appdata['setting'][ $form_data['code'] ] ) && ! empty( $appdata['setting'][ $form_data['code'] ] ) ) {
								$select_option = $appdata['setting'][ $form_data['code'] ];
							}

							$original_cf7tag_name = '';
							$selectbox_readonly   = '';
							if ( isset( $appdata['setting_original_cf7tag_name'][ $form_data['code'] ] ) && ! empty( $appdata['setting_original_cf7tag_name'][ $form_data['code'] ] ) ) {
								$original_cf7tag_name = $appdata['setting_original_cf7tag_name'][ $form_data['code'] ];
							}

							$code = wp_strip_all_tags(
								$this->create_sample_shortcode(
									$form_data,
									$select_option,
									$original_cf7tag_name,
									''
								)
							);
							if ( $code ) {
								$insert_code .= '<label> ' . $form_data['label'] . "\n    " . $code . "</label>\n\n";
							}
						}
					}
				}
			}
		}

		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo esc_html( $description ); ?></legend>
				<textarea class="tag code" name="" id="" rows="18" style="width:100%" readonly="readonly">
<?php echo esc_textarea( $insert_code ); ?>
				</textarea>
			</fieldset>
		</div>

		<div class="insert-box">

			<div class="submitbox">
				<input
					type="button"
					class="button button-primary kintone-form-insert-tag"
					value="<?php echo esc_attr( __( 'Insert Tag', 'kintone-form' ) ); ?>"
				/>
			</div>

		</div>
		<?php
	}

	/**
	 * Js & Css 読み込み.
	 */
	public function register_assets() {

		// styles.
		wp_enqueue_style(
			'kintone-form',
			KINTONE_FORM_URL . '/asset/css/kintone-form.css',
			array(),
			date(
				'YmdGis',
				filemtime( KINTONE_FORM_PATH . '/asset/css/kintone-form.css' )
			)
		);

		wp_enqueue_script( 'jquery' );

		wp_register_script(
			'my_loadmore',
			KINTONE_FORM_URL . '/asset/js/myloadmore.js',
			array( 'jquery' ),
			date(
				'YmdGis',
				filemtime( KINTONE_FORM_PATH . '/asset/js/myloadmore.js' )
			),
			true
		);

		global $wp_query;
		wp_localize_script(
			'my_loadmore',
			'misha_loadmore_params',
			array(
				'ajaxurl'      => site_url() . '/wp-admin/admin-ajax.php',
				'posts'        => 'hosoya',
				'current_page' => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
				'max_page'     => $wp_query->max_num_pages,
			)
		);

		wp_enqueue_script( 'my_loadmore' );

	}

	/**
	 * Save.
	 *
	 * @param WPCF7_ContactForm $contact_form .
	 * @param array             $args .
	 * @param string            $context .
	 */
	public function wpcf7_save_contact_form( $contact_form, $args, $context ) {

		$properties = array();

		$i = 0;
		if ( isset( $args['kintone_setting_data']['app_datas'] ) ) {
			foreach ( $args['kintone_setting_data']['app_datas'] as $app_data ) {

				if ( ! empty( $app_data['appid'] ) && ! empty( $app_data['token'] ) && ! empty( $args['kintone_setting_data']['domain'] ) ) {

					$url               = 'https://' . $args['kintone_setting_data']['domain'] . '/k/v1/form.json?app=' . $app_data['appid'];
					$kintone_form_data = $this->kintone_api(
						$url,
						$app_data['token'],
						$args['kintone_setting_data']['kintone_basic_authentication_id'],
						$args['kintone_setting_data']['kintone_basic_authentication_password']
					);

					if ( $args['kintone_setting_data']['kintone_basic_authentication_password'] ) {
						$args['kintone_setting_data']['kintone_basic_authentication_password'] = self::encode(
							$args['kintone_setting_data']['kintone_basic_authentication_password']
						);
					}

					if ( ! is_wp_error( $kintone_form_data ) ) {
						$app_data['formdata'] = $kintone_form_data;
					}

					$args['kintone_setting_data']['app_datas'][ $i ] = $app_data;

				}

				$i ++;

			}
			$properties['kintone_setting_data'] = $args['kintone_setting_data'];
			$contact_form->set_properties( $properties );
		}
	}

	/**
	 * パスワードをエンコードする
	 *
	 * @param string $value .
	 *
	 * @return string.
	 */
	public static function encode( $value ) {
		return base64_encode( md5( AUTH_SALT ) . $value . md5( md5( AUTH_SALT ) ) );
	}

	/**
	 * 管理画面のヘッダー部分に通知を出す.
	 */
	public function admin_notices() {

		$message = get_transient( 'my-custom-admin-errors' )
		?>

		<?php if ( $message ) : ?>
			<div class="error">
				<ul>
					<li><?php echo esc_html( $message ); ?></li>
				</ul>
			</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * kintoneからデータを取得する.
	 *
	 * @param string  $request_url .
	 * @param string  $kintone_token .
	 * @param string  $basic_auth_user .
	 * @param string  $basic_auth_pass .
	 * @param boolean $file .
	 *
	 * @return array|WP_Error
	 */
	public function kintone_api( $request_url, $kintone_token, $basic_auth_user = null, $basic_auth_pass = null, $file = false ) {

		if ( $request_url ) {

			$headers = array_merge(
				Kintone_Form_Utility::get_auth_header( $kintone_token ),
				Kintone_Form_Utility::get_basic_auth_header( $basic_auth_user, $basic_auth_pass )
			);

			$res = wp_remote_get(
				$request_url,
				array(
					'headers' => $headers,
				)
			);

			if ( is_wp_error( $res ) ) {

				return $res;

			} else {

				if ( 200 !== $res['response']['code'] ) {

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


