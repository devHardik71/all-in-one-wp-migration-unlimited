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

class Ai1wm_Export_Database_File {

	public static function execute( $params ) {

		// Set exclude database
		if ( isset( $params['options']['no_database'] ) ) {
			return $params;
		}

		$database_bytes_written = 0;

		// Set database bytes offset
		if ( isset( $params['database_bytes_offset'] ) ) {
			$database_bytes_offset = (int) $params['database_bytes_offset'];
		} else {
			$database_bytes_offset = 0;
		}

		// Get total database size
		if ( isset( $params['total_database_size'] ) ) {
			$total_database_size = (int) $params['total_database_size'];
		} else {
			$total_database_size = 1;
		}

		// What percent of database have we processed?
		$progress = (int) min( ( $database_bytes_offset / $total_database_size ) * 100, 100 );

		// Set progress
		Ai1wm_Status::info( sprintf( __( 'Archiving database...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $progress ) );

		// Get archive
		$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

		// Add file to archive
		if ( $archive->add_file( ai1wm_database_path( $params ), AI1WM_DATABASE_NAME, $database_bytes_written, $database_bytes_offset, 10 ) ) {

			// Set progress
			Ai1wm_Status::info( __( 'Done archiving database.', AI1WM_PLUGIN_NAME ) );

			// Unset database bytes offset
			unset( $params['database_bytes_offset'] );

			// Unset total database size
			unset( $params['total_database_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Get total database size
			$total_database_size = ai1wm_database_bytes( $params );

			// What percent of database have we processed?
			$progress = (int) min( ( $database_bytes_offset / $total_database_size ) * 100, 100 );

			// Set progress
			Ai1wm_Status::info( sprintf( __( 'Archiving database...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $progress ) );

			// Set database bytes offset
			$params['database_bytes_offset'] = $database_bytes_offset;

			// Set total database size
			$params['total_database_size'] = $total_database_size;

			// Set completed flag
			$params['completed'] = false;
		}

		// Close the archive file
		$archive->close();

		return $params;
	}
}
