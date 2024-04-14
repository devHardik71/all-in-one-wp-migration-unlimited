<?php
/**
 * Copyright (C) 2014-2018 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_Export_Compatibility {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Checking extensions compatibility...', AI1WM_PLUGIN_NAME ) );

		// Get messages
		$messages = Ai1wm_Compatibility::get( $params );

		// Set messages
		if ( empty( $messages ) ) {
			return $params;
		}

		// Set progress
		Ai1wm_Status::error( implode( $messages ) );

		// Manual export
		if ( empty( $params['ai1wm_manual_export'] ) ) {
			if ( function_exists( 'wp_mail' ) ) {

				// Set recipient
				$recipient = get_option( 'admin_email', '' );

				// Set subject
				$subject = __( 'Unable to backup your site', AI1WM_PLUGIN_NAME );

				// Set message
				$message = sprintf( __( 'All-in-One WP Migration was unable to backup %s. %s', AI1WM_PLUGIN_NAME ), site_url(), implode( $messages ) );

				// Send email
				wp_mail( $recipient, $subject, $message );
			}
		}

		exit;
	}
}
