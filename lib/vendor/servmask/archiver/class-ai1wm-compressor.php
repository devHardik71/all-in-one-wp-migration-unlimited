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

class Ai1wm_Compressor extends Ai1wm_Archiver {

	/**
	 * Overloaded constructor that opens the passed file for writing
	 *
	 * @param string $file File to use as archive
	 */
	public function __construct( $file ) {
		// call parent, to initialize variables
		parent::__construct( $file, true );
	}

	/**
	 * Add a file to the archive
	 *
	 * @param string $file             File to add to the archive
	 * @param string $new_filename     Write the file with a different name
	 * @param int    $current_filesize File size
	 * @param int    $offset           File offset
	 * @param int    $timeout          Process timeout
	 *
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Readable_Exception
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	public function add_file( $file, $new_filename = '', $current_filesize = 0, $offset = 0, $timeout = 0 ) {
		// open the file for reading in binary mode
		$handle = $this->open_file_for_reading( $file );

		// set file offset or set file header
		if ( $offset ) {
			// set file offset
			fseek( $handle, $offset, SEEK_SET );

			// set file size
			$this->current_filesize = $current_filesize;
		} else {
			// get file block header of the file we are trying to archive
			$block = $this->get_file_block( $file, $new_filename );

			// write file block header to our archive file
			$this->write_to_handle( $this->file_handle, $block );
		}

		// set file size
		$current_filesize = $this->get_current_filesize() - $offset;

		// start time
		$start = microtime( true );

		// read the file in 512KB chunks
		while ( $current_filesize > 0 ) {
			// read the file in chunks of 512KB
			$chunk_size = $current_filesize > 512000 ? 512000 : $current_filesize;

			// read the file in chunks of 512KB
			$content = $this->read_from_handle( $handle, $chunk_size );

			// remove the amount of bytes we read
			$current_filesize -= $chunk_size;

			// write file contents
			$this->write_to_handle( $this->file_handle, $content );

			// time elapsed
			if ( $timeout ) {
				if ( ( microtime( true ) - $start ) > $timeout ) {
					// set file offset
					$offset = ftell( $handle );

					// close the handle
					ai1wm_close( $handle );

					// get file offset
					return $offset;
				}
			}
		}

		// close the handle
		ai1wm_close( $handle );
	}

	/**
	 * Generate binary block header for a file
	 *
	 * @param string $file         Filename to generate block header for
	 * @param string $new_filename Write the file with a different name
	 *
	 * @return string
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	private function get_file_block( $file, $new_filename = '' ) {
		// get stats about the file
		$stat = stat( $file );
		if ( false === $stat ) {
			// unable to get file data
			throw new Ai1wm_Not_Accesible_Exception( __( 'Unable to get properties of file ' . $file, AI1WM_PLUGIN_NAME ) );
		}

		// get path details
		$pathinfo = pathinfo( $file );

		if ( ! empty( $new_filename ) ) {
			// get path details
			$pathinfo = pathinfo( $new_filename );
		}

		// filename of the file we are accessing
		$name = $pathinfo['basename'];

		// size in bytes of the file
		$size = $stat['7'];

		// last time the file was modified
		$date = $stat['9'];

		// current file size
		$this->current_filesize = $size;

		// replace DIRECTORY_SEPARATOR with / in path, we want to always have /
		$path = str_replace( DIRECTORY_SEPARATOR, '/', $pathinfo['dirname'] );

		// concatenate block format parts
		$format = implode( '', $this->block_format );

		// pack file data into binary string
		return pack( $format, $name, $size, $date, $path );
	}
}
