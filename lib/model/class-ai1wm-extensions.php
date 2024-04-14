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

class Ai1wm_Extensions {

	/**
	 * Get active extensions
	 *
	 * @return array
	 */
	public static function get() {
		$extensions = array();

		// Add Dropbox Extension
		if ( defined( 'AI1WMDE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMDE_PLUGIN_NAME ] = array(
				'key'      => AI1WMDE_PLUGIN_KEY,
				'about'    => AI1WMDE_PLUGIN_ABOUT,
				'basename' => AI1WMDE_PLUGIN_BASENAME,
				'version'  => AI1WMDE_VERSION,
				'requires' => '3.14',
				'short'    => AI1WMDE_PLUGIN_SHORT,
			);
		}

		// Add Google Drive Extension
		if ( defined( 'AI1WMGE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMGE_PLUGIN_NAME ] = array(
				'key'      => AI1WMGE_PLUGIN_KEY,
				'about'    => AI1WMGE_PLUGIN_ABOUT,
				'basename' => AI1WMGE_PLUGIN_BASENAME,
				'version'  => AI1WMGE_VERSION,
				'requires' => '2.15',
				'short'    => AI1WMGE_PLUGIN_SHORT,
			);
		}

		// Add Amazon S3 extension
		if ( defined( 'AI1WMSE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMSE_PLUGIN_NAME ] = array(
				'key'      => AI1WMSE_PLUGIN_KEY,
				'about'    => AI1WMSE_PLUGIN_ABOUT,
				'basename' => AI1WMSE_PLUGIN_BASENAME,
				'version'  => AI1WMSE_VERSION,
				'requires' => '3.8',
				'short'    => AI1WMSE_PLUGIN_SHORT,
			);
		}

		// Add Multisite Extension
		if ( defined( 'AI1WMME_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMME_PLUGIN_NAME ] = array(
				'key'      => AI1WMME_PLUGIN_KEY,
				'about'    => AI1WMME_PLUGIN_ABOUT,
				'basename' => AI1WMME_PLUGIN_BASENAME,
				'version'  => AI1WMME_VERSION,
				'requires' => '3.41',
				'short'    => AI1WMME_PLUGIN_SHORT,
			);
		}

		// Add Unlimited Extension
		if ( defined( 'AI1WMUE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMUE_PLUGIN_NAME ] = array(
				'key'      => AI1WMUE_PLUGIN_KEY,
				'about'    => AI1WMUE_PLUGIN_ABOUT,
				'basename' => AI1WMUE_PLUGIN_BASENAME,
				'version'  => AI1WMUE_VERSION,
				'requires' => '2.6',
				'short'    => AI1WMUE_PLUGIN_SHORT,
			);
		}

		// Add FTP Extension
		if ( defined( 'AI1WMFE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMFE_PLUGIN_NAME ] = array(
				'key'      => AI1WMFE_PLUGIN_KEY,
				'about'    => AI1WMFE_PLUGIN_ABOUT,
				'basename' => AI1WMFE_PLUGIN_BASENAME,
				'version'  => AI1WMFE_VERSION,
				'requires' => '2.11',
				'short'    => AI1WMFE_PLUGIN_SHORT,
			);
		}

		// Add URL Extension
		if ( defined( 'AI1WMLE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMLE_PLUGIN_NAME ] = array(
				'key'      => AI1WMLE_PLUGIN_KEY,
				'about'    => AI1WMLE_PLUGIN_ABOUT,
				'basename' => AI1WMLE_PLUGIN_BASENAME,
				'version'  => AI1WMLE_VERSION,
				'requires' => '2.9',
				'short'    => AI1WMLE_PLUGIN_SHORT,
			);
		}

		// Add OneDrive Extension
		if ( defined( 'AI1WMOE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMOE_PLUGIN_NAME ] = array(
				'key'      => AI1WMOE_PLUGIN_KEY,
				'about'    => AI1WMOE_PLUGIN_ABOUT,
				'basename' => AI1WMOE_PLUGIN_BASENAME,
				'version'  => AI1WMOE_VERSION,
				'requires' => '1.6',
				'short'    => AI1WMOE_PLUGIN_SHORT,
			);
		}

		// Add Box Extension
		if ( defined( 'AI1WMBE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMBE_PLUGIN_NAME ] = array(
				'key'      => AI1WMBE_PLUGIN_KEY,
				'about'    => AI1WMBE_PLUGIN_ABOUT,
				'basename' => AI1WMBE_PLUGIN_BASENAME,
				'version'  => AI1WMBE_VERSION,
				'requires' => '1.0',
				'short'    => AI1WMBE_PLUGIN_SHORT,
			);
		}

		// Add Mega Extension
		if ( defined( 'AI1WMEE_PLUGIN_NAME' ) ) {
			$extensions[ AI1WMEE_PLUGIN_NAME ] = array(
				'key'      => AI1WMEE_PLUGIN_KEY,
				'about'    => AI1WMEE_PLUGIN_ABOUT,
				'basename' => AI1WMEE_PLUGIN_BASENAME,
				'version'  => AI1WMEE_VERSION,
				'requires' => '1.0',
				'short'    => AI1WMEE_PLUGIN_SHORT,
			);
		}

		return $extensions;
	}
}
