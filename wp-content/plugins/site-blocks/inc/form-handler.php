<?php
/**
 * Contact form handler via wp_mail().
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle contact form submission.
 */
function site_blocks_handle_contact_form(): void {
	if ( ! isset( $_POST['site_contact_action'] ) || 'submit' !== $_POST['site_contact_action'] ) {
		return;
	}

	if ( ! isset( $_POST['site_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['site_contact_nonce'] ) ), 'site_contact_form' ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ?: home_url( '/contact/' ) ) );
		exit;
	}

	$redirect = wp_get_referer() ?: home_url( '/contact/' );

	if ( function_exists( 'site_blocks_turnstile_enabled' ) && site_blocks_turnstile_enabled() ) {
		$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
		if ( $token === '' || ! site_blocks_verify_turnstile( $token ) ) {
			wp_safe_redirect( add_query_arg( 'contact', 'captcha', $redirect ) );
			exit;
		}
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_subject'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	if ( '' === $name || '' === $email || '' === $message || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'invalid', $redirect ) );
		exit;
	}

	require_once SITE_BLOCKS_DIR . 'inc/safeguard-contact-details.php';
	$details = site_blocks_get_safeguard_contact_details();
	$to      = $details['email'];
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$mail_subject = $subject ? $subject : sprintf(
		/* translators: %s: sender name */
		__( 'Contact inquiry from %s', 'site-blocks' ),
		$name
	);

	$body  = __( 'New contact form submission', 'site-blocks' ) . "\n\n";
	$body .= __( 'Name:', 'site-blocks' ) . ' ' . $name . "\n";
	$body .= __( 'Email:', 'site-blocks' ) . ' ' . $email . "\n";
	if ( $subject ) {
		$body .= __( 'Subject:', 'site-blocks' ) . ' ' . $subject . "\n";
	}
	$body .= "\n" . __( 'Message:', 'site-blocks' ) . "\n" . $message . "\n";

	$sent = wp_mail( $to, $mail_subject, $body, $headers );
	$telegram_sent = false;
	$ghl_saved     = false;
	if ( class_exists( 'Slc_Plugin' ) ) {
		$ghl = Slc_Plugin::instance()->ghl();
		if ( $ghl->isConfigured() ) {
			$ghl_saved = $ghl->saveEnquiry( $name, $email, $subject, $message );
		}
		$telegram = Slc_Plugin::instance()->telegram();
		if ( $telegram->isConfigured() ) {
			$lines   = array(
				'<b>Contact form lead</b>',
				'',
				'<b>Name:</b> ' . esc_html( $name ),
				'<b>Email:</b> ' . esc_html( $email ),
			);
			if ( $subject ) {
				$lines[] = '<b>Subject:</b> ' . esc_html( $subject );
			}
			$lines[] = '';
			$lines[] = 'Saved in GoHighLevel.';
			$result  = $telegram->sendMessage( $telegram->staffChatId(), implode( "\n", $lines ) );
			$telegram_sent = is_array( $result );
		}
	}

	$ghl_ready = class_exists( 'Slc_Plugin' ) && Slc_Plugin::instance()->ghl()->isConfigured();
	$delivered = $ghl_ready ? $ghl_saved : ( $sent || $telegram_sent );

	if ( $delivered ) {
		wp_safe_redirect( add_query_arg( 'contact', 'sent', $redirect ) );
	} else {
		wp_safe_redirect( add_query_arg( 'contact', 'failed', $redirect ) );
	}
	exit;
}
add_action( 'admin_post_nopriv_site_contact_form', 'site_blocks_handle_contact_form' );
add_action( 'admin_post_site_contact_form', 'site_blocks_handle_contact_form' );

/**
 * Get form status from query string.
 *
 * @return string
 */
function site_blocks_get_form_status(): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
}
