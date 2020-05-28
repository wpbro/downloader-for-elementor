<?php
/**
 * Class Downloader
 *
 * Downloader tab and options.
 *
 * @package downloader-for-elementor
 */

namespace WPBRO\Downloader_For_Elementor;

use Elementor\Settings;

class Downloader {

	/**
	 * Options constructor.
	 */
	public function __construct() {
		add_action( 'elementor/admin/after_create_settings/elementor', [ $this, 'register_admin_tools_fields' ] );
		add_action( 'admin_post_elementor_pro_download', [ $this, 'post_elementor_pro_download' ] );
	}


	/**
	 * Downloader for Elementor "Settings" page in WordPress Dashboard.
	 *
	 * @param Settings $settings
	 *
	 * @since 1.0.0
	 */
	public function register_admin_tools_fields( Settings $settings ) {
		# Downloader Tab register.
		$settings->add_tab(
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG,
			[
				'label' => sprintf(
					'<span class="dashicons dashicons-download" style="font-size: 17px; margin-top: 4px"></span>%s',
					__( 'Downloader for Elementor', 'downloader-for-elementor' )
				)
			]
		);

		# Elementor Pro Fields.
		$settings->add_fields(
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG,
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_elementor_pro', [
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_set_elementor_pro_license' => [
				'label'      => __( 'Elementor Pro', 'downloader-for-elementor' ),
				'field_args' => [
					'type' => 'raw_html',
					'html' => sprintf(
						'<input id="dfe_elementor_pro_key" type="password"/>
						<script>
							function dfeGetKey() {
							  const elementorProKey = document.getElementById("dfe_elementor_pro_key").value
							  const elementorProUrl = document.getElementById("dfe_key_pro").href;
							  document.getElementById("dfe_key_pro").href=elementorProUrl.replace("KEY", elementorProKey);
							}
						</script>'
					),
					'desc' => __( 'Please enter purchased license key here', 'downloader-for-elementor' ),
				],
			],
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_get_elementor_pro'         => [
				'field_args' => [
					'type' => 'raw_html',
					'html' => sprintf(
						'<a id="dfe_key_pro" href="%s" class="elementor-button-success elementor-button" onclick="dfeGetKey()">%s</a>',
						wp_nonce_url( admin_url( 'admin-post.php?action=elementor_pro_download&license=KEY' ), 'elementor_pro_download' ),
						__( 'Install and Activate', 'downloader-for-elementor' )
					),
					'desc' => sprintf(
						'<br><a href="https://my.elementor.com" target="_blank">%s</a>',
						__( 'Copy the key from Elementor account page', 'downloader-for-elementor' )
					),
				],
			],
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_elementor_pro_separator'   => [
				'field_args' => [
					'type' => 'raw_html',
					'html' => '<hr>',
				],
			],
		] );

		# ACF Pro Fields - In progress.
		$settings->add_fields(
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG,
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_acf_pro', [
			WPBRO_DOWNLOADER_FOR_ELEMENTOR_SLUG . '_set_acf_license' => [
				'label'      => __( 'ACF Pro', 'downloader-for-elementor' ),
				'field_args' => [
					'type' => 'raw_html',
					'html' => sprintf(
						'<input class="regular-text"  disabled/>'
					),
					'desc' => __( 'Coming soon...', 'downloader-for-elementor' ),
				],
			],
		] );
	}


	/**
	 * Elementor Pro Download.
	 *
	 * Fired by `admin_post_elementor_pro_download` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function post_elementor_pro_download() {
		check_admin_referer( 'elementor_pro_download' );

		$version = Api::get_latest_version();
		$license = $_GET['license'];

		if ( empty( $license ) || empty( $version ) ) {
			wp_die( __( 'ERROR: The license not exist or wrong version, back and try again.', 'downloader-for-elementor' ) );
		}

		$package_url = Api::get_plugin_package_url( $version, $license );
		if ( is_wp_error( $package_url ) ) {
			wp_die( $package_url );
		}

		$download = new Download( [
			'version'     => $version,
			'plugin_name' => 'Elementor Pro',
			'plugin_slug' => 'elementor-pro',
			'package_url' => $package_url,
		] );

		$download->run();

		wp_die( '', __( 'Dwonload Elementor Pro', 'downloader-for-elementor' ), [ 'response' => 200 ] );
	}
}

// eol.
