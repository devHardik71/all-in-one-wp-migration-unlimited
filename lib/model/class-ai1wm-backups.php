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

class Ai1wm_Backups {

	/**
	 * Get all backup files
	 *
	 * @return array
	 */
	public function get_files() {
		$backups = array();

		// Get backup files
		$iterator = new Ai1wm_Extension_Filter(
			new DirectoryIterator( AI1WM_BACKUPS_PATH ),
			array( 'wpress', 'bin' )
		);

		foreach ( $iterator as $item ) {
			try {
				$backups[] = array(
					'filename' => $item->getFilename(),
					'mtime'    => $item->getMTime(),
					'size'     => $item->getSize(),
				);
			} catch ( Exception $e ) {
				$backups[] = array(
					'filename' => $item->getFilename(),
					'mtime'    => null,
					'size'     => ai1wm_filesize( $item->getPathname() ),
				);
			}
		}

		// Sort backups modified date
		usort( $backups, array( $this, 'compare' ) );

		return $backups;
	}

	/**
	 * Delete file
	 *
	 * @param  string  $file File name
	 * @return boolean
	 */
	public function delete_file( $file ) {
		if ( empty( $file ) ) {
			throw new Ai1wm_Backups_Exception( __( 'File name is not specified.', AI1WM_PLUGIN_NAME ) );
		} else if ( ! unlink( AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . $file ) ) {
			throw new Ai1wm_Backups_Exception(
				sprintf(
					__( 'Unable to delete <strong>"%s"</strong> file.', AI1WM_PLUGIN_NAME ),
					AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . $file
				)
			);
		}

		return true;
	}

	/**
	 * Compare backup files by modified time
	 *
	 * @param  array $a File item A
	 * @param  array $b File item B
	 * @return integer
	 */
	public function compare( $a, $b ) {
		if ( $a['mtime'] === $b['mtime'] ) {
			return 0;
		}

		return ( $a['mtime'] > $b['mtime'] ) ? - 1 : 1;
	}
}
