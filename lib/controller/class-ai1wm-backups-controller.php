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

class Ai1wm_Backups_Controller {

	public static function index() {
		$model = new Ai1wm_Backups;

		Ai1wm_Template::render(
			'backups/index',
			array(
				'backups'     => $model->get_files(),
				'username'    => get_option( AI1WM_AUTH_USER ),
				'password'    => get_option( AI1WM_AUTH_PASSWORD ),
			)
		);
	}

	public static function delete( $params = array() ) {
		$response = array( 'errors' => array() );

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}

		$model = new Ai1wm_Backups;

		try {
			// Delete file
			$model->delete_file( $archive );
		} catch ( Exception $e ) {
			$response['errors'][] = $e->getMessage();
		}

		echo json_encode( $response );
		exit;
	}
}
