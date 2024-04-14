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

class Ai1wm_Export_Download {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Renaming exported file...', AI1WM_PLUGIN_NAME ) );

		// Close achive file
		$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

		// Append EOF block
		$archive->close( true );

		// Rename archive file
		if ( rename( ai1wm_archive_path( $params ), ai1wm_download_path( $params ) ) ) {

			// Set archive details
			$link = ai1wm_backups_url( $params );
			$size = ai1wm_download_size( $params );
			$name = ai1wm_site_name();

			// Set progress
			Ai1wm_Status::download(
				sprintf(
					__(
						'<a href="%s" class="ai1wm-button-green ai1wm-emphasize">' .
						'<span>Download %s</span>' .
						'<em>Size: %s</em>' .
						'</a>',
						AI1WM_PLUGIN_NAME
					),
					$link,
					$name,
					$size
				)
			);
		}

		return $params;
	}
}
