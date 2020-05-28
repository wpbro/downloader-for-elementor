<?php
/**
 * Downloader for Elementor
 *
 * @link              https://wordpress.org/plugins/downloader-for-elementor/
 * @package           downloader-for-elementor
 *
 * @wordpress-plugin
 * Plugin Name:       Downloader for Elementor
 * Plugin URI:        https://wordpress.org/plugins/downloader-for-elementor/
 * Description:       Downloader for Elementor is plugin for Elementor Pro downloading via License key from the dashboard
 * Version:           1.0.0
 * Author:            WPBRO - Dima Minka
 * Author URI:        https://wpbro.ru
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       downloader-for-elementor
 * Domain Path:       /languages
 */

namespace WPBRO\Downloader_For_Elementor;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_VERSION', '1.0.0' );
define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG', 'Downloader_For_Elementor' );
define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_FILE', __FILE__ );
define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_BASE', plugin_basename( __FILE__ ) );
define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_DIR', trailingslashit( __DIR__ ) );
define( 'WPBRO_DOWNLOADER_FOR_ELEMENTOR_URL', plugin_dir_url( WPBRO_DOWNLOADER_FOR_ELEMENTOR_FILE ) );

/**
 * Load gettext translate for our text domain.
 *
 * @return void
 * @since 1.0.0
 *
 */
function WPBRO_Downloader_For_Elementor() {

	load_plugin_textdomain( 'downloader-for-elementor' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\WPBRO_Downloader_For_Elementor_fail_load' );

		return;
	}

	require_once __DIR__ . '/includes/class-admin.php';
	require_once __DIR__ . '/includes/class-downloader.php';
	require_once __DIR__ . '/includes/class-api.php';
	require_once __DIR__ . '/includes/class-download.php';

	$admin   = new Admin();
	$pro     = new Downloader();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\WPBRO_Downloader_For_Elementor' );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @return void
 * @since 1.0.0
 *
 */
function WPBRO_Downloader_For_Elementor_fail_load() {
	$message = sprintf(
	/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'downloader-for-elementor' ),
		'<strong>' . esc_html__( 'Downloader for Elementor', 'downloader-for-elementor' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'downloader-for-elementor' ) . '</strong>'
	);

	echo '<div class="error"><p>' . $message . '</p></div>';
}

// eol.
