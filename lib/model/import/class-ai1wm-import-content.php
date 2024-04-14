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

class Ai1wm_Import_Content {

	public static function execute( $params ) {

		// Read blogs.json file
		$handle = ai1wm_open( ai1wm_blogs_path( $params ), 'r' );

		// Parse blogs.json file
		$blogs = ai1wm_read( $handle, filesize( ai1wm_blogs_path( $params ) ) );
		$blogs = json_decode( $blogs, true );

		// Close handle
		ai1wm_close( $handle );

		// Set content offset
		if ( isset( $params['content_offset'] ) ) {
			$content_offset = (int) $params['content_offset'];
		} else {
			$content_offset = 0;
		}

		// Set archive offset
		if ( isset( $params['archive_offset'] ) ) {
			$archive_offset = (int) $params['archive_offset'];
		} else {
			$archive_offset = 0;
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

		// Get processed files
		if ( isset( $params['processed'] ) ) {
			$processed = (int) $params['processed'];
		} else {
			$processed = 0;
		}

		// What percent of files have we processed?
		$progress = (int) ( ( $processed / $total_files_size ) * 100 );

		// Set progress
		if ( empty( $content_offset ) ) {
			Ai1wm_Status::info( sprintf( __( 'Restoring %d files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_files_count, $progress ) );
		}

		// Start time
		$start = microtime( true );

		// Open the archive file for reading
		$archive = new Ai1wm_Extractor( ai1wm_archive_path( $params ) );

		// Set the file pointer to the one that we have saved
		$archive->set_file_pointer( null, $archive_offset );

		$old_paths = array();
		$new_paths = array();

		// Set extract paths
		foreach ( $blogs as $blog ) {
			if ( ai1wm_main_site( $blog['Old']['BlogID'] ) === false ) {
				if ( defined( 'UPLOADBLOGSDIR' ) ) {
					// Old sites dir style
					$old_paths[] = ai1wm_files_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_files_path( $blog['New']['BlogID'] );

					// New sites dir style
					$old_paths[] = ai1wm_sites_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_files_path( $blog['New']['BlogID'] );
				} else {
					// Old sites dir style
					$old_paths[] = ai1wm_files_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_sites_path( $blog['New']['BlogID'] );

					// New sites dir style
					$old_paths[] = ai1wm_sites_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_sites_path( $blog['New']['BlogID'] );
				}
			}
		}

		// Set base site extract paths (should be added at the end of arrays)
		foreach ( $blogs as $blog ) {
			if ( ai1wm_main_site( $blog['Old']['BlogID'] ) === true ) {
				if ( defined( 'UPLOADBLOGSDIR' ) ) {
					// Old sites dir style
					$old_paths[] = ai1wm_files_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_files_path( $blog['New']['BlogID'] );

					// New sites dir style
					$old_paths[] = ai1wm_sites_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_files_path( $blog['New']['BlogID'] );
				} else {
					// Old sites dir style
					$old_paths[] = ai1wm_files_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_sites_path( $blog['New']['BlogID'] );

					// New sites dir style
					$old_paths[] = ai1wm_sites_path( $blog['Old']['BlogID'] );
					$new_paths[] = ai1wm_sites_path( $blog['New']['BlogID'] );
				}
			}
		}

		while ( $archive->has_not_reached_eof() ) {
			try {

				// Exclude WordPress files
				$exclude_files = array_keys( _get_dropins() );

				// Exclude plugin files
				$exclude_files = array_merge( $exclude_files, array(
					AI1WM_PACKAGE_NAME,
					AI1WM_MULTISITE_NAME,
					AI1WM_DATABASE_NAME,
					AI1WM_MUPLUGINS_NAME,
				) );

				// Extract a file from archive to WP_CONTENT_DIR
				if ( ( $current_offset = $archive->extract_one_file_to( WP_CONTENT_DIR, $exclude_files, $old_paths, $new_paths, $content_offset, 10 ) ) ) {

					// What percent of files have we processed?
					if ( ( $processed += ( $current_offset - $content_offset ) ) ) {
						$progress = (int) ( ( $processed / $total_files_size ) * 100 );
					}

					// Set progress
					Ai1wm_Status::info( sprintf( __( 'Restoring %d files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_files_count, $progress ) );

					// Set content offset
					$content_offset = $current_offset;

					// Set archive offset
					$archive_offset = $archive->get_file_pointer();

					break;
				}

				// Increment processed files
				if ( empty( $content_offset ) ) {
					$processed += $archive->get_current_filesize();
				}

				// Set content offset
				$content_offset = 0;

				// Set archive offset
				$archive_offset = $archive->get_file_pointer();

			} catch ( Ai1wm_Quota_Exceeded_Exception $e ) {
				throw new Exception( 'Out of disk space.' );
			} catch ( Exception $e ) {
				// Skip bad file permissions
			}

			// More than 10 seconds have passed, break and do another request
			if ( ( microtime( true ) - $start ) > 10 ) {
				break;
			}
		}

		// End of the archive?
		if ( $archive->has_reached_eof() ) {

			// Unset content offset
			unset( $params['content_offset'] );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset processed files
			unset( $params['processed'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Set content offset
			$params['content_offset'] = $content_offset;

			// Set archive offset
			$params['archive_offset'] = $archive_offset;

			// Set processed files
			$params['processed'] = $processed;

			// Set completed flag
			$params['completed'] = false;
		}

		// Close the archive file
		$archive->close();

		return $params;
	}
}
