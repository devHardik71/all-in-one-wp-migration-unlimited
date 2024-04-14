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

class Ai1wm_Http_Stream extends Ai1wm_Http_Abstract {

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

		// Set path
		$path = parse_url( $url, PHP_URL_PATH );

		// Set query
		$query = parse_url( $url, PHP_URL_QUERY );

		// Set port
		if ( empty( $port ) ) {
			if ( $scheme === 'https' ) {
				$port = 443;
			} else {
				$port = 80;
			}
		}

		// Set stream context
		$context = stream_context_create( array(
			'ssl' => array(
				'verify_peer'       => false,
				'verify_peer_name'  => false,
				'capture_peer_cert' => false,
				'allow_self_signed' => true,
			),
		) );

		// Set stream client
		if ( $scheme === 'https' ) {
			if ( ! ( $handle = stream_socket_client( "ssl://{$host}:{$port}", $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context ) ) ) {
				$handle = stream_socket_client( "tcp://{$host}:{$port}", $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context );
			}
		} else {
			$handle = stream_socket_client( "tcp://{$host}:{$port}", $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context );
		}

		// Ensure the stream is ready to write to
		$no_streams    = array();
		$write_streams = array( $handle );
		stream_select( $no_streams, $write_streams, $no_streams, 0, 200000 );

		// Prepare headers
		$headers = implode( "\r\n", $headers );

		// Prepare request
		$request = "GET {$path}?{$query} HTTP/1.0\r\n{$headers}\r\n\r\n";

		// Send data to server
		if ( ( $length = fwrite( $handle, $request ) ) !== strlen( $request ) ) {
			trigger_error( sprintf( 'fwrite wrote only %d instead of %d', $length, strlen( $request ) ) );
		}

		// Set blocking/non-blocking mode on a stream
		if ( $blocking ) {
			fread( $handle, 1024 );
		} else {
			stream_set_blocking( $handle, 0 );
			// What we observed is that when the stream is non-blocking, it takes time for the webserver to start a new php thread.
			// By sleeping for 3s, we give some time for the webserver to start a new php process to process the request.
			// This is a temporary solution and a new one will be addressed in WM-651
			sleep( 3 );
		}

		// Close stream handle
		fclose( $handle );
	}
}
