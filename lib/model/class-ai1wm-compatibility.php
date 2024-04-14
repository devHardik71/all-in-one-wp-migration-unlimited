<?php
/**
 * Copyright (C) 2014-2017 ServMask Inc.
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

class Ai1wm_Compatibility {
	public static function get( $params ) {
		$extensions = Ai1wm_Extensions::get();

		foreach ( $extensions as $extension_name => $extension_data ) {
			if ( ! isset( $params[ $extension_data['short'] ] ) ) {
				unset( $extensions[ $extension_name ] );
			}
		}

		// If no extension is used, update everything that is available
		if ( empty( $extensions ) ) {
			$extensions = Ai1wm_Extensions::get();
		}

		$messages = array();
		foreach ( $extensions as $extension_name => $extension_data ) {
			$message = Ai1wm_Compatibility::check( $extension_data );
			if ( ! empty( $message ) ) {
				$messages[] = $message;
			}
		}

		return $messages;
	}

	public static function check( $extension ) {
		if ( $extension['version'] !== 'develop' ) {
			if ( version_compare( $extension['version'], $extension['requires'], '<' ) ) {
				$plugin = get_plugin_data( sprintf( '%s/%s', WP_PLUGIN_DIR, $extension['basename'] ) );
				return sprintf( __(
					'<strong>%s</strong> is not the latest version. ' .
					'You must update the plugin before you can use it. ',
					AI1WM_PLUGIN_NAME
				), $plugin['Name'] );
			}
		}
	}
}
