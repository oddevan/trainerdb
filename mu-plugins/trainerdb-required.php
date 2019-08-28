<?php // @codingStandardsIgnoreLine: Filename ok.
/**
 * Master include list of required plugins.
 *
 * @since  0.1
 */

require WPMU_PLUGIN_DIR . '/wds-required-plugins/wds-required-plugins.php';

/**
 * Add required plugins to WDS_Required_Plugins
 *
 * @since  0.1
 * @author Evan Hildreth
 *
 * @param  array $required Array of required plugins in `plugin_dir/plugin_file.php` form.
 * @return array           Modified array of required plugins.
 */
function wds_required_plugins_add( $required ) {

	$required = array_merge( $required, array(
		'advanced-custom-fields/acf.php',
		'trainerdb/trainerdb.php',
	) );

	return $required;
}
add_filter( 'wds_network_required_plugins', 'wds_required_plugins_add' );

/**
 * Include the composer autoloader
 *
 * @since 0.1
 */
require_once WP_CONTENT_DIR . '/vendor/autoload.php';
