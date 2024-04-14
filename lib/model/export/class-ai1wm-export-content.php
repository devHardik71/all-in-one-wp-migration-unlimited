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

class Ai1wm_Export_Content {

	public static function execute( $params ) {

		// Set file bytes offset
		if ( isset( $params['file_bytes_offset'] ) ) {
			$file_bytes_offset = (int) $params['file_bytes_offset'];
		} else {
			$file_bytes_offset = 0;
		}

		// Set filemap bytes offset
		if ( isset( $params['filemap_bytes_offset'] ) ) {
			$filemap_bytes_offset = (int) $params['filemap_bytes_offset'];
		} else {
			$filemap_bytes_offset = 0;
		}

		// Get total files count
		if ( isset( $params['total_files_count'] ) ) {
			$total_files_count = (int) $params['total_files_count'];
		} else {
			$total_files_count = 1;
		}

		// Get total files size
		if ( isset( $params['total_files_size'] ) ) {
			$total_files_size = (int) $params['total_files_size'];
		} else {
			$total_files_size = 1;
		}

		// Get processed files size
		if ( isset( $params['processed_files_size'] ) ) {
			$processed_files_size = (int) $params['processed_files_size'];
		} else {
			$processed_files_size = 0;
		}

		// What percent of files have we processed?
		$progress = (int) min( ( $processed_files_size / $total_files_size ) * 100, 100 );

		// Set progress
		Ai1wm_Status::info( sprintf( __( 'Archiving %d files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_files_count, $progress ) );

		// Flag to hold if file data has been processed
		$completed = true;

		// Start time
		$start = microtime( true );

		// Get map file
		$filemap = ai1wm_open( ai1wm_filemap_path( $params ), 'r' );

		// Set filemap pointer at the current index
		if ( fseek( $filemap, $filemap_bytes_offset ) !== -1 ) {

			// Get archive
			$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

			// Loop over files
			while ( $path = trim( fgets( $filemap ) ) ) {
				$file_bytes_written = 0;

				// Add file to archive
				if ( ( $completed = $archive->add_file( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path, $path, $file_bytes_written, $file_bytes_offset, 10 ) ) ) {
					$file_bytes_offset = 0;

					// Set filemap bytes offset
					$filemap_bytes_offset = ftell( $filemap );
				}

				// Increment processed files size
				$processed_files_size += $file_bytes_written;

				// What percent of files have we processed?
				$progress = (int) min( ( $processed_files_size / $total_files_size ) * 100, 100 );

				// Set progress
				Ai1wm_Status::info( sprintf( __( 'Archiving %d files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_files_count, $progress ) );

				// More than 10 seconds have passed, break and do another request
				if ( ( microtime( true ) - $start ) > 10 ) {
					$completed = false;
					break;
				}
			}

			// Close the archive file
			$archive->close();
		}

		// End of the filemap?
		if ( feof( $filemap ) ) {

			// Unset file bytes offset
			unset( $params['file_bytes_offset'] );

			// Unset filemap bytes offset
			unset( $params['filemap_bytes_offset'] );

			// Unset processed files size
			unset( $params['processed_files_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Set file bytes offset
			$params['file_bytes_offset'] = $file_bytes_offset;

			// Set filemap bytes offset
			$params['filemap_bytes_offset'] = $filemap_bytes_offset;

			// Set processed files size
			$params['processed_files_size'] = $processed_files_size;

			// Set completed flag
			$params['completed'] = $completed;
		}

		// Close the filemap file
		ai1wm_close( $filemap );

		return $params;
	}
}
