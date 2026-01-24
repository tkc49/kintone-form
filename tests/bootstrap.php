<?php
/**
 * PHPUnit bootstrap file.
 *
 * Determines which bootstrap to use based on test suite.
 * - Unit tests: Use minimal mocks, no WordPress.
 * - Integration tests: Use full WordPress testing environment.
 *
 * @package Kintone_Form
 */

// Check which test suite is being run.
$is_unit_test = false;

// Check command line arguments for --testsuite Unit.
if ( isset( $_SERVER['argv'] ) ) {
	$argv = $_SERVER['argv'];
	foreach ( $argv as $i => $arg ) {
		if ( '--testsuite' === $arg && isset( $argv[ $i + 1 ] ) && 'Unit' === $argv[ $i + 1 ] ) {
			$is_unit_test = true;
			break;
		}
		if ( str_starts_with( $arg, '--testsuite=Unit' ) ) {
			$is_unit_test = true;
			break;
		}
	}
}

// Also check if we're running from the Unit directory.
if ( ! $is_unit_test ) {
	$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );
	foreach ( $backtrace as $trace ) {
		if ( isset( $trace['file'] ) && str_contains( $trace['file'], '/tests/Unit/' ) ) {
			$is_unit_test = true;
			break;
		}
	}
}

if ( $is_unit_test ) {
	// Load unit test bootstrap (no WordPress).
	require_once __DIR__ . '/bootstrap-unit.php';
} else {
	// Load integration test bootstrap (with WordPress).
	$_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $_tests_dir ) {
		$_tests_dir = '/tmp/wordpress-tests-lib';
	}

	// Give access to tests_add_filter() function.
	require_once $_tests_dir . '/includes/functions.php';

	/**
	 * Manually load the plugin being tested.
	 */
	function _manually_load_plugin() {
		require dirname( __DIR__ ) . '/kintone-form.php';
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	// Start up the WP testing environment.
	require $_tests_dir . '/includes/bootstrap.php';
}
