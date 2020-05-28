<?php
/**
 * Class Api
 *
 * EDD connection for downloading Elementor Pro
 *
 * @package downloader-for-elementor
 */

namespace WPBRO\Downloader_For_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Api {

	const PRODUCT_NAME = 'Elementor Pro';

	const STORE_URL = 'https://my.elementor.com/api/v1/licenses/';

	/**
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	private static function remote_post( $body_args = [] ) {
		$body_args = wp_parse_args(
			$body_args,
			[
				'api_version' => '',
				'item_name'   => self::PRODUCT_NAME,
				'site_lang'   => get_bloginfo( 'language' ),
				'url'         => home_url(),
			]
		);

		$response = wp_remote_post( self::STORE_URL, [
			'timeout' => 40,
			'body'    => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data;
	}

	public static function get_plugin_package_url( $version, $license ) {
		$url = 'https://my.elementor.com/api/v1/pro-downloads/';

		$body_args = [
			'item_name' => self::PRODUCT_NAME,
			'version'   => $version,
			'license'   => $license,
			'url'       => home_url(),
		];

		$response = wp_remote_post( $url, [
			'timeout' => 40,
			'body'    => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$data          = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 401 === $response_code ) {
			return new \WP_Error( $response_code, $data['message'] );
		}

		if ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data['package_url'];
	}

	public static function get_latest_version() {
		$url = 'https://my.elementor.com/api/v1/pro-downloads/';

		$body_args = [
			'version' => '',
			'license' => '',
			'url'     => home_url(),
		];

		$response = wp_remote_get( $url, [
			'timeout' => 40,
			'body'    => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$data          = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 401 === $response_code ) {
			return new \WP_Error( $response_code, $data['message'] );
		}

		if ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data['versions'][0];
	}
}
