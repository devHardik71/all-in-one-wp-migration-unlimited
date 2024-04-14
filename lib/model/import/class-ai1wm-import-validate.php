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

class Ai1wm_Import_Validate {

	public static function execute( $params ) {

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

		// Set progress
		Ai1wm_Status::info( __( 'Unpacking archive...', AI1WM_PLUGIN_NAME ) );

		// Open the archive file for reading
		$archive = new Ai1wm_Extractor( ai1wm_archive_path( $params ) );

		// Set the file pointer to the one that we have saved
		$archive->set_file_pointer( null, $archive_offset );

		// Validate the archive file consistency
		if ( ! $archive->is_valid() ) {
			throw new Ai1wm_Import_Exception(
				__(
					'The archive file is corrupted. Follow this article to resolve the problem: ' .
					'<a href="https://help.servmask.com/knowledgebase/corrupted-archive/" target="_blank">https://help.servmask.com/knowledgebase/corrupted-archive/</a>',
					AI1WM_PLUGIN_NAME
				)
			);
		}

		// Obtain the name of the archive
		$name = ai1wm_archive_name( $params );

		// Obtain the size of the archive
		$size = ai1wm_archive_bytes( $params );

		// Check file size of the archive
		if ( false === $size ) {
			throw new Ai1wm_Not_Accesible_Exception(
				sprintf( __( 'Unable to get the file size of <strong>%s</strong>', AI1WM_PLUGIN_NAME ), $name )
			);
		}

		$allowed_size = apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE );

		// Let's check the size of the file to make sure it is less than the maximum allowed
		if ( ( $allowed_size > 0 ) && ( $size > $allowed_size ) ) {
			throw new Ai1wm_Import_Exception(
				sprintf(
					__(
						'The file that you are trying to import is over the maximum upload file size limit of <strong>%s</strong>.<br />' .
						'You can remove this restriction by purchasing our ' .
						'<a href="https://servmask.com/products/unlimited-extension" target="_blank">Unlimited Extension</a>.',
						AI1WM_PLUGIN_NAME
					),
					size_format( $allowed_size )
				)
			);
		}

		if ( $archive->has_not_reached_eof() ) {

			// Unpack package.json, multisite.json and database.sql files
			if ( ( $current_offset = $archive->extract_by_files_array( ai1wm_storage_path( $params ), array( AI1WM_PACKAGE_NAME, AI1WM_MULTISITE_NAME, AI1WM_DATABASE_NAME ), $content_offset, 10 ) ) ) {

				// Set content offset
				$content_offset = $current_offset;

				// Set archive offset
				$archive_offset = $archive->get_file_pointer();

			} else {

				// Set content offset
				$content_offset = 0;

				// Set archive offset
				$archive_offset = $archive->get_file_pointer();
			}
		}

		// End of the archive?
		if ( $archive->has_reached_eof() ) {

			// Check package.json file
			if ( false === is_file( ai1wm_package_path( $params ) ) ) {
				throw new Ai1wm_Import_Exception(
					__( 'Invalid archive file. It should contain <strong>package.json</strong> file.', AI1WM_PLUGIN_NAME )
				);
			}

			// Unset content offset
			unset( $params['content_offset'] );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Set content offset
			$params['content_offset'] = $content_offset;

			// Set archive offset
			$params['archive_offset'] = $archive_offset;

			// Set completed flag
			$params['completed'] = false;
		}

		// Close the archive file
		$archive->close();

		return $params;
	}
}
