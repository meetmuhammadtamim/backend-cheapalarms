<?php
/**
 * Cloudflare Turnstile helpers for the contact form.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array{site_key:string,secret_key:string}
 */
function site_blocks_turnstile_config(): array {
	static $config = null;
	if ( is_array( $config ) ) {
		return $config;
	}

	$config = array(
		'site_key'   => '',
		'secret_key' => '',
	);

	$path = SITE_BLOCKS_DIR . 'config/turnstile.php';
	if ( ! is_readable( $path ) ) {
		return $config;
	}

	$loaded = include $path;
	if ( ! is_array( $loaded ) ) {
		return $config;
	}

	$config['site_key']   = isset( $loaded['site_key'] ) ? (string) $loaded['site_key'] : '';
	$config['secret_key'] = isset( $loaded['secret_key'] ) ? (string) $loaded['secret_key'] : '';

	return $config;
}

function site_blocks_turnstile_enabled(): bool {
	$config = site_blocks_turnstile_config();

	return $config['site_key'] !== '' && $config['secret_key'] !== '';
}

function site_blocks_turnstile_site_key(): string {
	return site_blocks_turnstile_config()['site_key'];
}

/**
 * Verify a Turnstile token with Cloudflare.
 */
function site_blocks_verify_turnstile( string $token ): bool {
	if ( $token === '' || ! site_blocks_turnstile_enabled() ) {
		return false;
	}

	$secret = site_blocks_turnstile_config()['secret_key'];
	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 12,
			'body'    => array(
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	if ( $code < 200 || $code >= 300 || ! is_array( $data ) ) {
		return false;
	}

	return ! empty( $data['success'] );
}
