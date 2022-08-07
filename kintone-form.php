<?php
/**
 * Plugin Name: Form data to kintone
 * Plugin URI:
 * Description: This plugin is an addon for "Contact Form 7".
 * Version:     2.26.0
 * Author:      Takashi Hosoya
 * Author URI:  http://ht79.info/
 * License:     GPLv2
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

define( 'KINTONE_FORM_URL', plugins_url( '', __FILE__ ) );
define( 'KINTONE_FORM_PATH', dirname( __FILE__ ) );

$data = get_file_data(
	__FILE__,
	array(
		'ver'   => 'Version',
		'langs' => 'Domain Path',
	)
);

define( 'KITONE_FORM_VERSION', $data['ver'] );
define( 'KITONE_FORM_LANGS', $data['langs'] );
load_plugin_textdomain(
	'kintone-form',
	false,
	dirname( plugin_basename( __FILE__ ) ) . KITONE_FORM_LANGS
);


require KINTONE_FORM_PATH . '/modules/text.php';
require KINTONE_FORM_PATH . '/modules/number.php';
require KINTONE_FORM_PATH . '/modules/radio.php';
require KINTONE_FORM_PATH . '/modules/checkbox.php';
require KINTONE_FORM_PATH . '/modules/date.php';
require KINTONE_FORM_PATH . '/modules/datetime.php';
require KINTONE_FORM_PATH . '/modules/dropdown.php';
require KINTONE_FORM_PATH . '/modules/link.php';
require KINTONE_FORM_PATH . '/modules/richText.php';
require KINTONE_FORM_PATH . '/modules/multiLineText.php';
require KINTONE_FORM_PATH . '/modules/multi_select.php';
require KINTONE_FORM_PATH . '/modules/time.php';
require KINTONE_FORM_PATH . '/includes/check-acceptance.php';
require KINTONE_FORM_PATH . '/modules/organization.php';
require KINTONE_FORM_PATH . '/modules/file.php';

require_once KINTONE_FORM_PATH . '/includes/class-kintone-form.php';

$kintone_form = new Kintone_Form();
$kintone_form->register();


