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
			'ORGANIZATION_SELECT',
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
		'SINGLE_LINE_TEXT'    => 'text',
		'NUMBER'              => 'number',
		'RADIO_BUTTON'        => 'radio',
		'CHECK_BOX'           => 'checkbox',
		'MULTI_SELECT'        => 'select',
		'DROP_DOWN'           => 'select',
		'DATE'                => 'date',
		'TIME'                => 'time',
		'DATETIME'            => '',
		'LINK'                => 'url',
		'RICH_TEXT'           => 'textarea',
		'MULTI_LINE_TEXT'     => 'textarea',
		'ORGANIZATION_SELECT' => 'text',
		'FILE'                => 'file',
	);

	/**
	 * フィールドタイプのグループ定義.
	 *
	 * @var array
	 */
	private $field_type_groups = array(
		'text'          => array(
			'label' => 'テキストフィールド',
			'icon'  => '📝',
			'types' => array( 'SINGLE_LINE_TEXT', 'MULTI_LINE_TEXT', 'RICH_TEXT', 'LINK' ),
		),
		'selection'     => array(
			'label' => '選択フィールド',
			'icon'  => '📋',
			'types' => array( 'RADIO_BUTTON', 'CHECK_BOX', 'MULTI_SELECT', 'DROP_DOWN' ),
		),
		'datetime'      => array(
			'label' => '日付・時刻フィールド',
			'icon'  => '📅',
			'types' => array( 'DATE', 'TIME', 'DATETIME' ),
		),
		'number'        => array(
			'label' => '数値フィールド',
			'icon'  => '🔢',
			'types' => array( 'NUMBER' ),
		),
		'user_org'      => array(
			'label' => 'ユーザー・組織',
			'icon'  => '👥',
			'types' => array( 'ORGANIZATION_SELECT' ),
		),
		'file'          => array(
			'label' => 'ファイル',
			'icon'  => '📎',
			'types' => array( 'FILE' ),
		),
		'table'         => array(
			'label' => 'テーブル',
			'icon'  => '📊',
			'types' => array( 'SUBTABLE' ),
		),
		'not_supported' => array(
			'label' => 'Not Supported',
			'icon'  => '⚠',
			'types' => array(), // 動的に設定.
		),
	);


	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'wpcf7_contact_form_properties' ), 10, 2 );
		if ( defined( 'WPCF7_VERSION' ) ) {
			if ( version_compare( WPCF7_VERSION, '5.5.3', '>=' ) ) {
				add_filter( 'wpcf7_pre_construct_contact_form_properties', array( $this, 'wpcf7_contact_form_properties' ), 10, 2 );
			} else {
				add_filter( 'wpcf7_contact_form_properties', array( $this, 'wpcf7_contact_form_properties' ), 10, 2 );
			}
		}

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
				'domain'    => '',
				'email_address_to_send_kintone_registration_error' => get_option( 'admin_email' ),
				'app_datas' => array(),
			)
		);

		$domain = $kintone_setting_data['domain'];
		$email_address_to_send_kintone_registration_error = $kintone_setting_data['email_address_to_send_kintone_registration_error'];

		$kintone_basic_authentication_id = '';
		if ( isset( $kintone_setting_data['kintone_basic_authentication_id'] ) ) {
			$kintone_basic_authentication_id = $kintone_setting_data['kintone_basic_authentication_id'];
		}

		$kintone_basic_authentication_password = '';
		if ( isset( $kintone_setting_data['kintone_basic_authentication_password'] ) && $kintone_setting_data['kintone_basic_authentication_password'] ) {
			$kintone_basic_authentication_password = Kintone_Form_Utility::decode(
				$kintone_setting_data['kintone_basic_authentication_password']
			);
		}

		$kintone_guest_space_id = '';
		if ( isset( $kintone_setting_data['kintone_guest_space_id'] ) ) {
			$kintone_guest_space_id = $kintone_setting_data['kintone_guest_space_id'];
		}

		$mailtags = $post->collect_mail_tags();
		$tags     = $post->scan_form_tags();

		$this->kintone_fieldcode_supported_list = apply_filters(
			'kintone_fieldcode_supported_list',
			$this->kintone_fieldcode_supported_list
		);

		?>
		<h2><?php esc_html_e( 'Setting kintone', 'kintone-form' ); ?></h2>

		<?php
		// 基本設定セクション.
		$this->render_basic_settings(
			$domain,
			$email_address_to_send_kintone_registration_error,
			$kintone_basic_authentication_id,
			$kintone_basic_authentication_password,
			$kintone_guest_space_id
		);
		?>

		<div class="repeat">
			<div id="kintone_form_setting" class="wrapper">
				<div class="container">

					<?php if ( isset( $kintone_setting_data['app_datas'] ) && ! empty( $kintone_setting_data['app_datas'] ) ) : ?>

						<?php $multi_kintone_app_count = 0; ?>
						<?php foreach ( $kintone_setting_data['app_datas'] as $app_data ) : ?>

							<?php
							$this->render_app_section(
								$app_data,
								$multi_kintone_app_count,
								$tags,
								$mailtags
							);
							?>

							<?php ++$multi_kintone_app_count; ?>

						<?php endforeach; ?>

					<?php else : ?>

						<?php
						$this->render_app_section(
							array(
								'appid'    => '',
								'tokens'   => array(),
								'formdata' => array(),
							),
							0,
							$tags,
							$mailtags
						);
						?>

					<?php endif; ?>

					<?php do_action( 'kintone_form_setting_panel_after' ); ?>

				</div>

				<div class="kf-add-app-section">
					<span class="add button"><?php esc_html_e( '追加', 'kintone-form' ); ?></span>
					<span style="margin-left: 8px; color: #646970;">← Add-Ons</span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * 基本設定セクションをレンダリング.
	 *
	 * @param string $domain ドメイン.
	 * @param string $error_email エラー通知メール.
	 * @param string $basic_auth_id Basic認証ID.
	 * @param string $basic_auth_password Basic認証パスワード.
	 * @param string $guest_space_id ゲストスペースID.
	 */
	private function render_basic_settings( $domain, $error_email, $basic_auth_id, $basic_auth_password, $guest_space_id ) {
		?>
		<div class="kf-settings-section">
			<div class="kf-settings-section-header">
				<span class="dashicons dashicons-admin-generic"></span>
				<h3><?php esc_html_e( '基本設定', 'kintone-form' ); ?></h3>
			</div>
			<div class="kf-settings-section-content">
				<div class="kf-form-row">
					<label class="kf-form-label">
						<?php esc_html_e( 'kintone ドメイン', 'kintone-form' ); ?>
						<span class="required">*</span>
					</label>
					<div class="kf-form-field">
						<div class="kf-form-field-with-prefix">
							<span class="kf-form-prefix">https://</span>
							<input
								type="text"
								id="kintone-form-domain"
								placeholder="xxxx.cybozu.com"
								name="kintone_setting_data[domain]"
								value="<?php echo esc_attr( $domain ); ?>"
							/>
						</div>
					</div>
				</div>

				<div class="kf-form-row">
					<label class="kf-form-label">
						<?php esc_html_e( 'エラー通知メール', 'kintone-form' ); ?>
					</label>
					<div class="kf-form-field">
						<input
							type="email"
							multiple
							id="email-address-to-send-kintone-registration-error"
							name="kintone_setting_data[email_address_to_send_kintone_registration_error]"
							value="<?php echo esc_attr( $error_email ); ?>"
						/>
						<p class="kf-form-hint">
							<?php esc_html_e( 'kintone への登録エラー時に通知するメールアドレス', 'kintone-form' ); ?>
						</p>
					</div>
				</div>

				<div class="kf-form-row">
					<label class="kf-form-label">
						<?php esc_html_e( 'Basic 認証', 'kintone-form' ); ?>
					</label>
					<div class="kf-form-field">
						<div class="kf-form-field-inline">
							<input
								type="text"
								id="kintone-basic-authentication-id"
								name="kintone_setting_data[kintone_basic_authentication_id]"
								placeholder="<?php esc_attr_e( 'ユーザー名', 'kintone-form' ); ?>"
								value="<?php echo esc_attr( $basic_auth_id ); ?>"
							/>
							<span class="separator">/</span>
							<input
								type="password"
								id="kintone-basic-authentication-password"
								name="kintone_setting_data[kintone_basic_authentication_password]"
								placeholder="<?php esc_attr_e( 'パスワード', 'kintone-form' ); ?>"
								value="<?php echo esc_attr( $basic_auth_password ); ?>"
							/>
						</div>
						<p class="kf-form-hint">
							<?php esc_html_e( '※ Basic認証が有効な場合のみ必要', 'kintone-form' ); ?>
						</p>
					</div>
				</div>

				<div class="kf-form-row">
					<label class="kf-form-label">
						<?php esc_html_e( 'ゲストスペースID', 'kintone-form' ); ?>
					</label>
					<div class="kf-form-field">
						<input
							type="text"
							id="kintone-guest-space-id"
							name="kintone_setting_data[kintone_guest_space_id]"
							value="<?php echo esc_attr( $guest_space_id ); ?>"
							style="max-width: 120px;"
						/>
						<p class="kf-form-hint">
							<?php esc_html_e( '※ ゲストスペースアプリの場合のみ必要', 'kintone-form' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * アプリ設定セクションをレンダリング.
	 *
	 * @param array $app_data アプリデータ.
	 * @param int   $app_index アプリのインデックス.
	 * @param array $tags CF7タグ.
	 * @param array $mailtags CF7メールタグ.
	 */
	private function render_app_section( $app_data, $app_index, $tags, $mailtags ) {
		$appid  = isset( $app_data['appid'] ) ? $app_data['appid'] : '';
		$tokens = isset( $app_data['tokens'] ) ? $app_data['tokens'] : ( isset( $app_data['token'] ) ? $app_data['token'] : '' );
		?>
		<div class="kf-app-section row" data-app-index="<?php echo esc_attr( $app_index ); ?>">
			<!-- アプリヘッダー -->
			<div class="kf-app-header">
				<div class="kf-app-header-field">
					<label for="kintone-form-appid-<?php echo esc_attr( $app_index ); ?>">APP ID:</label>
					<input
						type="text"
						id="kintone-form-appid-<?php echo esc_attr( $app_index ); ?>"
						name="kintone_setting_data[app_datas][<?php echo esc_attr( $app_index ); ?>][appid]"
						class="small-text"
						size="10"
						value="<?php echo esc_attr( $appid ); ?>"
					/>
					<input type="submit" class="button-primary" name="get-kintone-data" value="GET">
				</div>
				<div class="kf-app-header-actions">
					<span class="remove button"><?php esc_html_e( 'Remove', 'kintone-form' ); ?></span>
				</div>
			</div>

			<!-- トークンセクション -->
			<div class="kf-app-token-section">
				<div style="display: flex; align-items: flex-start; gap: 12px;">
					<label style="font-weight: 500; padding-top: 6px;">API Token:</label>
					<?php echo $this->render_token_fields( $tokens, $app_index ); ?>
				</div>
			</div>

			<?php if ( isset( $app_data['formdata']['properties'] ) && ! empty( $app_data['formdata']['properties'] ) ) : ?>
				<?php $grouped_fields = $this->group_fields_by_type( $app_data['formdata']['properties'] ); ?>

				<!-- 検索ボックス -->
				<div class="kf-search-box">
					<div class="kf-search-wrapper">
						<span class="dashicons dashicons-search"></span>
						<input
							type="text"
							class="kf-search-input"
							placeholder="<?php esc_attr_e( 'フィールドを検索...', 'kintone-form' ); ?>"
							data-app-index="<?php echo esc_attr( $app_index ); ?>"
						/>
						<button type="button" class="kf-search-clear" title="<?php esc_attr_e( 'クリア', 'kintone-form' ); ?>">
							<span class="dashicons dashicons-no-alt"></span>
						</button>
					</div>
					<div class="kf-search-results-info" style="display: none;"></div>
				</div>

				<!-- フィールドアコーディオン -->
				<div class="kf-accordion-container">
					<?php
					$this->render_field_accordions(
						$grouped_fields,
						$app_data,
						$app_index,
						$tags,
						$mailtags
					);
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * フィールドをタイプごとにグループ化.
	 *
	 * @param array $properties kintoneフィールドプロパティ.
	 * @return array グループ化されたフィールド.
	 */
	private function group_fields_by_type( $properties ) {
		$grouped = array();

		// サポートされているすべてのタイプを収集.
		$supported_types = array();
		foreach ( $this->field_type_groups as $group_key => $group ) {
			if ( 'not_supported' !== $group_key ) {
				$supported_types = array_merge( $supported_types, $group['types'] );
			}
		}

		// 各グループを初期化.
		foreach ( $this->field_type_groups as $group_key => $group ) {
			$grouped[ $group_key ] = array(
				'label'  => $group['label'],
				'icon'   => $group['icon'],
				'fields' => array(),
			);
		}

		// フィールドを適切なグループに振り分け.
		foreach ( $properties as $field ) {
			if ( ! isset( $field['code'] ) ) {
				continue;
			}

			$field_type = isset( $field['type'] ) ? $field['type'] : '';
			$assigned   = false;

			foreach ( $this->field_type_groups as $group_key => $group ) {
				if ( 'not_supported' !== $group_key && in_array( $field_type, $group['types'], true ) ) {
					$grouped[ $group_key ]['fields'][] = $field;
					$assigned                          = true;
					break;
				}
			}

			// どのグループにも属さない場合は not_supported へ.
			if ( ! $assigned ) {
				$grouped['not_supported']['fields'][] = $field;
			}
		}

		// 空のグループを除去.
		foreach ( $grouped as $group_key => $group ) {
			if ( empty( $group['fields'] ) ) {
				unset( $grouped[ $group_key ] );
			}
		}

		return $grouped;
	}

	/**
	 * フィールドアコーディオンをレンダリング.
	 *
	 * @param array $grouped_fields グループ化されたフィールド.
	 * @param array $app_data アプリデータ.
	 * @param int   $app_index アプリのインデックス.
	 * @param array $tags CF7タグ.
	 * @param array $mailtags CF7メールタグ.
	 */
	private function render_field_accordions( $grouped_fields, $app_data, $app_index, $tags, $mailtags ) {
		foreach ( $grouped_fields as $group_key => $group ) {
			$is_not_supported = ( 'not_supported' === $group_key );
			$is_expanded      = ! $is_not_supported; // Not Supported以外は初期展開.
			$accordion_id     = 'kf-accordion-' . $app_index . '-' . $group_key;
			$content_id       = 'kf-accordion-content-' . $app_index . '-' . $group_key;
			$field_count      = count( $group['fields'] );
			?>
			<div class="kf-accordion-group <?php echo $is_not_supported ? 'kf-accordion-group--not-supported' : ''; ?>" data-group="<?php echo esc_attr( $group_key ); ?>">
				<button
					type="button"
					class="kf-accordion-header"
					id="<?php echo esc_attr( $accordion_id ); ?>"
					aria-expanded="<?php echo $is_expanded ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $content_id ); ?>"
				>
					<span class="dashicons dashicons-arrow-right-alt2 kf-accordion-toggle"></span>
					<span class="kf-accordion-icon"><?php echo esc_html( $group['icon'] ); ?></span>
					<span class="kf-accordion-title"><?php echo esc_html( $group['label'] ); ?></span>
					<span class="kf-accordion-count" data-original-count="<?php echo esc_attr( $field_count ); ?>"><?php echo esc_html( $field_count ); ?></span>
				</button>
				<div
					class="kf-accordion-content"
					id="<?php echo esc_attr( $content_id ); ?>"
					role="region"
					aria-labelledby="<?php echo esc_attr( $accordion_id ); ?>"
					<?php echo $is_expanded ? 'style="display: block;"' : ''; ?>
				>
					<?php if ( $is_not_supported ) : ?>
						<?php $this->render_unsupported_fields_list( $group['fields'] ); ?>
					<?php else : ?>
						<?php
						$this->render_field_table(
							$group['fields'],
							$app_data,
							$app_index,
							$tags,
							$mailtags
						);
						?>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * フィールドテーブルをレンダリング.
	 *
	 * @param array $fields フィールド配列.
	 * @param array $app_data アプリデータ.
	 * @param int   $app_index アプリのインデックス.
	 * @param array $tags CF7タグ.
	 * @param array $mailtags CF7メールタグ.
	 */
	private function render_field_table( $fields, $app_data, $app_index, $tags, $mailtags ) {
		?>
		<table class="kf-field-table">
			<thead>
				<tr>
					<th>Update Key</th>
					<th class="kf-field-table-kintone"><?php esc_html_e( 'kintone Field', 'kintone-form' ); ?></th>
					<th class="kf-field-table-arrow"></th>
					<th class="kf-field-table-cf7"><?php esc_html_e( 'CF7 Mail Tag', 'kintone-form' ); ?></th>
					<th><?php esc_html_e( 'Shortcode Example', 'kintone-form' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $fields as $field ) : ?>
					<?php $this->render_field_row( $field, $app_data, $app_index, $tags, $mailtags ); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * フィールド行をレンダリング.
	 *
	 * @param array $field フィールドデータ.
	 * @param array $app_data アプリデータ.
	 * @param int   $app_index アプリのインデックス.
	 * @param array $tags CF7タグ.
	 * @param array $mailtags CF7メールタグ.
	 */
	private function render_field_row( $field, $app_data, $app_index, $tags, $mailtags ) {
		$label      = isset( $field['label'] ) ? $field['label'] : '';
		$code       = isset( $field['code'] ) ? $field['code'] : '';
		$field_type = isset( $field['type'] ) ? $field['type'] : '';
		?>
		<tr class="kf-field-row" data-field-label="<?php echo esc_attr( strtolower( $label ) ); ?>" data-field-code="<?php echo esc_attr( strtolower( $code ) ); ?>">
			<td>
				<?php if ( $this->is_update_key_kintone_field( $field ) ) : ?>
					<?php $checkbox_for_kintone_update_key = '<input type="checkbox" disabled="disabled" name="" value="">'; ?>
					<?php $checkbox_for_kintone_update_key = apply_filters( 'form_data_to_kintone_setting_checkbox_for_kintone_update_key', $checkbox_for_kintone_update_key, $app_data, $app_index, $field ); ?>
					<?php echo $checkbox_for_kintone_update_key; ?>
				<?php endif; ?>
			</td>
			<td>
				<div>
					<strong><?php echo esc_html( $label ); ?></strong>
				</div>
				<div class="kf-field-code"><?php echo esc_html( $code ); ?></div>
			</td>
			<td class="kf-field-arrow">←</td>
			<td>
				<?php if ( 'SUBTABLE' === $field_type ) : ?>
					<?php $this->render_subtable_fields( $field, $app_data, $app_index, $tags, $mailtags ); ?>
				<?php elseif ( array_key_exists( $field_type, $this->kintone_fieldcode_supported_list ) ) : ?>
					<?php echo $this->create_html_for_setting_cf7_mailtag( $tags, $mailtags, $app_data, $field, $app_index ); ?>
				<?php elseif ( 'FILE' === $field_type ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=form-data-to-kintone-setting' ) ); ?>">Add-Ons</a>
				<?php else : ?>
					<span class="description">Not Support</span>
				<?php endif; ?>
			</td>
			<td>
				<?php if ( 'SUBTABLE' !== $field_type && array_key_exists( $field_type, $this->kintone_fieldcode_supported_list ) ) : ?>
					<div class="kf-shortcode-preview">
						<?php echo $this->create_sample_shortcode( $field, $app_data ); ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * サブテーブルフィールドをレンダリング.
	 *
	 * @param array $field サブテーブルフィールド.
	 * @param array $app_data アプリデータ.
	 * @param int   $app_index アプリのインデックス.
	 * @param array $tags CF7タグ.
	 * @param array $mailtags CF7メールタグ.
	 */
	private function render_subtable_fields( $field, $app_data, $app_index, $tags, $mailtags ) {
		if ( ! isset( $field['fields'] ) || empty( $field['fields'] ) ) {
			return;
		}
		?>
		<div class="kf-subtable-wrapper">
			<table class="kf-subtable-fields">
				<?php foreach ( $field['fields'] as $subfield ) : ?>
					<tr>
						<td style="width: 40%;">
							<strong><?php echo esc_html( isset( $subfield['label'] ) ? $subfield['label'] : '' ); ?></strong>
							<span class="kf-field-code">(<?php echo esc_html( $subfield['code'] ); ?>)</span>
						</td>
						<td style="width: 20px; text-align: center;">←</td>
						<td>
							<?php if ( array_key_exists( $subfield['type'], $this->kintone_fieldcode_supported_list ) ) : ?>
								<?php echo $this->create_html_for_setting_cf7_mailtag( $tags, $mailtags, $app_data, $subfield, $app_index ); ?>
							<?php elseif ( 'FILE' === $subfield['type'] ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=form-data-to-kintone-setting' ) ); ?>">Add-Ons</a>
							<?php else : ?>
								<span class="description">Not Support</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php
	}

	/**
	 * Not Supportedフィールドリストをレンダリング.
	 *
	 * @param array $fields Not Supportedフィールド配列.
	 */
	private function render_unsupported_fields_list( $fields ) {
		?>
		<div class="kf-not-supported-list">
			<?php foreach ( $fields as $field ) : ?>
				<div class="kf-not-supported-item">
					<span class="dashicons dashicons-warning"></span>
					<span class="kf-not-supported-label">
						<?php echo esc_html( isset( $field['label'] ) ? $field['label'] : '' ); ?>
						(<?php echo esc_html( $field['code'] ); ?>)
					</span>
					<span class="kf-not-supported-type"><?php echo esc_html( $field['type'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * サンプルのショートコードタグを作成する.
	 *
	 * @param array $form_data .
	 * @param array $app_data .
	 *
	 * @return string .
	 */
	private function create_sample_shortcode( $form_data, $app_data ) {

		// selectボックスで既に設定されている cf7のメールタグを設定
		$select_option = '';
		if ( isset( $app_data['setting'][ $form_data['code'] ] ) && ! empty( $app_data['setting'][ $form_data['code'] ] ) ) {
			$select_option = $app_data['setting'][ $form_data['code'] ];
		}

		// テキストフォーム（オリジナルタグ名:setting_original_cf7tag_name）にデータが設定されている場合、selectbox は disabled に設定し、選択ができないようにする
		$original_cf7tag_name = '';
		if ( isset( $app_data['setting_original_cf7tag_name'][ $form_data['code'] ] ) && ! empty( $app_data['setting_original_cf7tag_name'][ $form_data['code'] ] ) ) {
			$original_cf7tag_name = $app_data['setting_original_cf7tag_name'][ $form_data['code'] ];
		}

		$hash = hash( 'md5', $form_data['code'] );

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
	 * Js & Css 読み込み.
	 */
	public function register_assets() {

		// Select2 library.
		wp_enqueue_style(
			'select2',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
			array(),
			'4.1.0-rc.0'
		);

		wp_enqueue_script(
			'select2',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
			array( 'jquery' ),
			'4.1.0-rc.0',
			true
		);

		// styles.
		wp_enqueue_style(
			'kintone-form',
			KINTONE_FORM_URL . '/asset/css/kintone-form.css',
			array( 'select2' ),
			date(
				'YmdGis',
				filemtime( KINTONE_FORM_PATH . '/asset/css/kintone-form.css' )
			)
		);

		wp_enqueue_script( 'jquery' );

		wp_register_script(
			'my_loadmore',
			KINTONE_FORM_URL . '/asset/js/myloadmore.js',
			array( 'jquery', 'select2' ),
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
		$i          = 0;
		$app_datas  = array();
		if ( isset( $args['kintone_setting_data']['app_datas'] ) ) {
			foreach ( $args['kintone_setting_data']['app_datas'] as $app_data ) {

				// トークンの処理: 新形式(tokens)と既存値(tokens_existing)をマージ
				$processed_tokens   = $this->process_tokens( $app_data );
				$app_data['tokens'] = $processed_tokens;

				// 後方互換性のため、tokenキーにはカンマ区切り文字列を設定
				$app_data['token'] = Kintone_Form_Utility::tokens_to_string( $processed_tokens );

				// tokens_existingは保存不要
				unset( $app_data['tokens_existing'] );

				// トークンが存在するかチェック
				$has_valid_token = ! empty( $app_data['token'] );

				if ( ! empty( $app_data['appid'] ) && $has_valid_token && ! empty( $args['kintone_setting_data']['domain'] ) ) {

					$url               = Kintone_Form_Utility::get_kintone_url( $args['kintone_setting_data'], 'form' );
					$url               = $url . '?app=' . $app_data['appid'];
					$kintone_form_data = $this->kintone_api(
						$url,
						$app_data['tokens'],
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
					} else {
						// エラーの場合は保存前のデータをセットする.
						$kintone_setting_data = $contact_form->prop( 'kintone_setting_data' );
						if ( isset( $kintone_setting_data['app_datas'] ) ) {
							foreach ( $kintone_setting_data['app_datas'] as $before_saved_app_data ) {
								if ( $app_data['appid'] === $before_saved_app_data['appid'] ) {
									$app_data['formdata'] = $before_saved_app_data['formdata'];
								}
							}
						}
					}

					$app_datas [ $i ] = $app_data;

				}

				++$i;

			}
			$args['kintone_setting_data']['app_datas'] = $app_datas;

			$properties['kintone_setting_data'] = $args['kintone_setting_data'];
			$contact_form->set_properties( $properties );
		}
	}

	/**
	 * kintone の仕様: 1アプリあたり最大9トークン.
	 */
	const MAX_TOKENS_PER_APP = 9;

	/**
	 * トークンの処理: 新規入力と既存値をマージしてエンコード.
	 *
	 * @param array $app_data アプリデータ.
	 * @return array エンコードされたトークン配列.
	 */
	private function process_tokens( $app_data ) {
		$new_tokens      = isset( $app_data['tokens'] ) ? $app_data['tokens'] : array();
		$existing_tokens = isset( $app_data['tokens_existing'] ) ? $app_data['tokens_existing'] : array();

		$result = array();

		// 各インデックスについて処理
		$max_index = max( count( $new_tokens ), count( $existing_tokens ) );
		for ( $j = 0; $j < $max_index; $j++ ) {
			// 最大数に達したら終了.
			if ( count( $result ) >= self::MAX_TOKENS_PER_APP ) {
				break;
			}

			$new_value      = isset( $new_tokens[ $j ] ) ? trim( $new_tokens[ $j ] ) : '';
			$existing_value = isset( $existing_tokens[ $j ] ) ? $existing_tokens[ $j ] : '';

			if ( ! empty( $new_value ) ) {
				// 新しい値が入力された場合: エンコードして保存
				$result[] = Kintone_Form_Utility::encode_token( $new_value );
			} elseif ( ! empty( $existing_value ) ) {
				// 空欄で既存値がある場合: 既存値を維持
				$result[] = $existing_value;
			}
			// 両方空の場合は追加しない
		}

		return array_values( array_filter( $result ) );
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
	 * Kintoneからデータを取得する.
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

				} elseif ( $file ) {

						$return_value = $res['body'];
				} else {
					$return_value = json_decode( $res['body'], true );
				}

				return $return_value;
			}
		} else {
			echo '<div class="error fade"><p><strong>URL is required</strong></p></div>';

			return new WP_Error( 'Error', 'URL is required' );
		}
	}

	/**
	 * Contact form 7 のメールタグを設定するためのセレクトボックスを作成する.
	 *
	 * @param string  $tags .
	 * @param array   $mailtags .
	 * @param array   $app_data .
	 * @param array   $kintone_filed .
	 * @param integer $multi_kintone_app_count .
	 *
	 * @return string html.
	 */
	private function create_html_for_setting_cf7_mailtag( $tags, $mailtags, $app_data, $kintone_filed, $multi_kintone_app_count ) {

		// selectボックスで既に設定されている cf7のメールタグを設定
		$selected_cf7_mailtag = '';
		if ( isset( $app_data['setting'][ $kintone_filed['code'] ] ) && ! empty( $app_data['setting'][ $kintone_filed['code'] ] ) ) {
			$selected_cf7_mailtag = $app_data['setting'][ $kintone_filed['code'] ];
		}

		// テキストフォーム（オリジナルタグ名:setting_original_cf7tag_name）にデータが設定されている場合、selectbox は disabled に設定し、選択ができないようにする
		$selectbox_readonly   = '';
		$original_cf7tag_name = '';
		if ( isset( $app_data['setting_original_cf7tag_name'][ $kintone_filed['code'] ] ) && ! empty( $app_data['setting_original_cf7tag_name'][ $kintone_filed['code'] ] ) ) {
			$selectbox_readonly   = 'disabled="disabled"';
			$original_cf7tag_name = $app_data['setting_original_cf7tag_name'][ $kintone_filed['code'] ];
		}

		$hash = hash( 'md5', $kintone_filed['code'] );
		ob_start();
		?>
		<!-- Create selectbox-->
		<select id="cf7-mailtag-<?php echo esc_attr( $hash ); ?>" class="kf-cf7-mailtag-select" <?php echo esc_attr( $selectbox_readonly ); ?>name="kintone_setting_data[app_datas][<?php echo esc_attr( $multi_kintone_app_count ); ?>][setting][<?php echo esc_attr( $kintone_filed['code'] ); ?>]">
			<option value=""><?php esc_html_e( '-- 選択 --', 'kintone-form' ); ?></option>

			<?php foreach ( $mailtags as $value ) : ?>
				<option <?php selected( $value, $selected_cf7_mailtag, true ); ?> value="<?php echo esc_attr( $value ); ?>">[<?php echo esc_attr( $value ); ?>]</option>
			<?php endforeach; ?>
			<?php foreach ( Kintone_Form::get_cf7_special_tags() as $cf7_specail_tag ) : ?>
				<option <?php selected( $cf7_specail_tag, $selected_cf7_mailtag ); ?> value="<?php echo esc_attr( $cf7_specail_tag ); ?>">[<?php echo esc_attr( $cf7_specail_tag ); ?>]</option>
			<?php endforeach; ?>
			<?php do_action( 'kintone_form_add_original_cf7_mail_tag_for_kintone_form', $selected_cf7_mailtag ); ?>

		</select>

		<!-- Create text for original's name.-->
		or
		<input type="text" id="<?php echo esc_attr( $hash ); ?>" class="your-cf7-tag-name" placeholder="your-cf7-tag-name" name="kintone_setting_data[app_datas][<?php echo esc_attr( $multi_kintone_app_count ); ?>][setting_original_cf7tag_name][<?php echo esc_attr( $kintone_filed['code'] ); ?>]" value="<?php echo esc_attr( $original_cf7tag_name ); ?>"/>

		<!-- Show error message-->
		<?php $error_msg = $this->check_consistency( $tags, $selected_cf7_mailtag, $kintone_filed ); ?>
		<?php if ( $error_msg ) : ?>
			<div style="color:red; font-weight:bold;"><?php echo esc_textarea( $error_msg ); ?></div>
		<?php endif; ?>

		<?php
		$html = ob_get_contents(); // 記録結果を変数に代入
		ob_end_clean(); // 記録

		return $html;
	}

	private function is_update_key_kintone_field( $form_data ) {

		if ( 'SINGLE_LINE_TEXT' === $form_data['type'] && ! isset( $form_data['relatedApp'] ) && isset( $form_data['unique'] ) && 'true' === $form_data['unique'] ) {
			return true;
		}

		if ( 'NUMBER' === $form_data['type'] && 'true' === $form_data['unique'] ) {
			return true;

		}

		return false;
	}

	/**
	 * APIトークンフィールドのHTMLを生成する.
	 *
	 * @param array $tokens トークン配列（エンコード済み）.
	 * @param int   $app_index アプリのインデックス.
	 * @return string HTML.
	 */
	private function render_token_fields( $tokens, $app_index ) {
		// 後方互換性: カンマ区切り文字列を配列に変換
		$tokens = Kintone_Form_Utility::normalize_tokens( $tokens );

		// 空の場合は1つの空フィールドを表示
		if ( empty( $tokens ) ) {
			$tokens = array( '' );
		}

		ob_start();
		?>
		<div class="kintone-token-fields" data-app-index="<?php echo esc_attr( $app_index ); ?>">
			<?php foreach ( $tokens as $token_index => $token ) : ?>
				<?php $masked = Kintone_Form_Utility::mask_token( $token ); ?>
				<div class="kintone-token-row" style="margin-bottom: 5px;">
					<?php if ( ! empty( $masked ) ) : ?>
						<span class="kintone-token-masked" style="display: inline-block; min-width: 200px; padding: 2px 5px; background: #f0f0f0; border-radius: 3px; font-family: monospace; margin-right: 10px;">
							<?php echo esc_html( $masked ); ?>
						</span>
					<?php endif; ?>
					<input
						type="password"
						name="kintone_setting_data[app_datas][<?php echo esc_attr( $app_index ); ?>][tokens][<?php echo esc_attr( $token_index ); ?>]"
						class="regular-text kintone-token-input"
						size="50"
						value=""
						placeholder="<?php echo empty( $masked ) ? esc_attr__( 'API Token を入力', 'kintone-form' ) : esc_attr__( '変更する場合のみ入力', 'kintone-form' ); ?>"
						autocomplete="new-password"
					/>
					<!-- 既存トークンを保持するhiddenフィールド -->
					<input
						type="hidden"
						name="kintone_setting_data[app_datas][<?php echo esc_attr( $app_index ); ?>][tokens_existing][<?php echo esc_attr( $token_index ); ?>]"
						value="<?php echo esc_attr( $token ); ?>"
					/>
					<button type="button" class="button kintone-token-remove" title="<?php esc_attr_e( '削除', 'kintone-form' ); ?>" <?php echo count( $tokens ) <= 1 ? 'style="display:none;"' : ''; ?>>×</button>
				</div>
			<?php endforeach; ?>
			<button type="button" class="button kintone-token-add" style="margin-top: 5px;">
				<?php esc_html_e( '+ トークン追加', 'kintone-form' ); ?>
			</button>
		</div>
		<?php
		return ob_get_clean();
	}
}
