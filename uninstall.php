<?php
if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_post_meta_by_key( '_kintone_setting_data' );

