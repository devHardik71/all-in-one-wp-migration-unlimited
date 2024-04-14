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

abstract class Ai1wm_Archiver {

	/**
	 * Header block format of a file
	 *
	 * Field Name    Offset    Length    Contents
	 * name               0       255    filename (no path, no slash)
	 * size             255        14    size of file contents
	 * mtime            269        12    last modification time
	 * prefix           281      4096    path name, no trailing slashes
	 *
	 * @type string
	 */
	protected $block_format = array(
		'a255',  // filename
		'a14',   // size of file contents
		'a12',   // last time modified
		'a4096', // path
	);

	public function get_block_format() {
		return $this->block_format;
	}

	public function set_block_format( $block_format ) {
		$this->block_format = $block_format;
	}

	/**
	 * Filename including path to the file
	 *
	 * @type string
	 */
	protected $filename = null;

	public function get_filename() {
		return $this->filename;
	}

	public function set_filename( $filename ) {
		$this->filename = $filename;
	}

	/**
	 * Handle to the file
	 *
	 * @type resource
	 */
	protected $file_handle = null;

	public function get_file_handle() {
		return $this->file_handle;
	}

	public function set_file_handle( $file_handle ) {
		$this->file_handle = $file_handle;
	}

	/**
	 * Current file size
	 *
	 * @type int
	 */
	protected $current_filesize = null;

	public function get_current_filesize() {
		return $this->current_filesize;
	}

	public function set_current_filesize( $current_filesize ) {
		$this->current_filesize = $current_filesize;
	}

	/**
	 * End Of File block string
	 *
	 * @type string
	 */
	protected $eof = null;

	public function get_eof() {
		return $this->eof;
	}

	public function set_eof( $eof ) {
		$this->eof = $eof;
	}

	/**
	 * Default constructor
	 *
	 * Initializes filename and end of file block
	 *
	 * @param string $filename Archive file
	 */
	public function __construct( $filename, $write = false ) {
		// initialize file
		$this->filename = $filename;

		// initialize end of file
		$this->eof = pack( 'a4377', '' );

		// open file for writing or reading
		if ( $write ) {
			$this->file_handle = $this->open_file_for_writing( $filename );
		} else {
			$this->file_handle = $this->open_file_for_reading( $filename );
		}
	}

	/**
	 * Open the archive for reading
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_reading( $file ) {
		return $this->open_file_in_mode( $file, 'rb' );
	}

	/**
	 * Open the archive for writing/appending
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_writing( $file ) {
		return $this->open_file_in_mode( $file, 'ab' );
	}

	/**
	 * Open the archive for writing and truncate the file if it exist
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_overwriting( $file ) {
		return $this->open_file_in_mode( $file, 'wb' );
	}

	/**
	 * Opens file in the passed mode
	 *
	 * @param string $file File to be opened
	 * @param string $mode Mode to openthe file in
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_in_mode( $file, $mode ) {
		return ai1wm_open( $file, $mode );
	}

	/**
	 * Write data to a handle and check if the data has been written
	 *
	 * @param resource $handle File handle
	 * @param string   $data   Data to be written - binary
	 *
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	protected function write_to_handle( $handle, $data ) {
		return ai1wm_write( $handle, $data );
	}

	/**
	 * Read data from a handle
	 *
	 * @param resource $handle File handle
	 * @param int      $size   Size of data to be read in bytes
	 *
	 * @return string Content that was read
	 * @throws \Ai1wm_Not_Readable_Exception
	 */
	protected function read_from_handle( $handle, $size ) {
		return ai1wm_read( $handle, $size );
	}

	/**
	 * Appends end of file block to the archive
	 *
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	protected function append_eof() {
		return $this->write_to_handle( $this->file_handle, $this->eof );
	}

	/**
	 * Validate file
	 *
	 * return bool
	 */
	public function is_valid() {
		$offset = ftell( $this->file_handle );

		// set file offset
		if ( fseek( $this->file_handle, -4377, SEEK_END ) !== -1 ) {
			if ( ai1wm_read( $this->file_handle, 4377 ) === $this->eof ) {
				if ( fseek( $this->file_handle, $offset, SEEK_SET ) !== -1 ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Closes the archive file
	 *
	 * We either close the file or append the end of file block if complete argument is set to tru
	 *
	 * @param  bool $complete Flag to append end of file block
	 * @return void
	 *
	 */
	public function close( $complete = false ) {
		// are we done appending to the file?
		if ( true === $complete ) {
			$this->append_eof();
		}

		// close the file
		ai1wm_close( $this->file_handle );
	}
}
