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

class Ai1wm_Http_Curl extends Ai1wm_Http_Abstract {

	public function get( $url, $blocking = false ) {

		$headers = array();

		// Set headers
		foreach ( $this->headers as $key => $value ) {
			$headers[] = "{$key}: {$value}";
		}

		// Set scheme
		$scheme = parse_url( $url, PHP_URL_SCHEME );

		// Set host
		$host = parse_url( $url, PHP_URL_HOST );

		// Set port
		$port = parse_url( $url, PHP_URL_PORT );

		// Set cURL client
		$handle = curl_init();

		// Set cURL options
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $handle, CURLOPT_TIMEOUT, 5 );
		curl_setopt( $handle, CURLOPT_URL, $url );
		curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $handle, CURLOPT_HEADER, false );
		curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );

		// Send data to server
		if ( ! curl_exec( $handle ) ) {
			if ( $scheme === 'https' ) {
				if ( empty( $port ) ) {
					curl_setopt( $handle, CURLOPT_URL, str_replace( "https://{$host}", "http://{$host}:443", $url ) );
				} else {
					curl_setopt( $handle, CURLOPT_URL, str_replace( "https://{$host}:{$port}", "http://{$host}:{$port}", $url ) );
				}

				// Re-send data to server
				curl_exec( $handle );
			}
		}

		// Close cURL handle
		curl_close( $handle );
	}
}
