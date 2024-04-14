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

class Ai1wm_Http {

	public static function get( $url, $params = array(), Ai1wm_Http_Abstract $http = null ) {

		// Get IP address
		$ip = get_option( AI1WM_URL_IP );

		// Get adapter
		$adapter = get_option( AI1WM_URL_ADAPTER );

		// Get host
		$host = parse_url( $url, PHP_URL_HOST );

		// Get port
		$port = parse_url( $url, PHP_URL_PORT );

		// Set HTTP client
		if ( empty( $http ) ) {
			$http = Ai1wm_Http_Factory::create( $adapter );
		}

		// Set HTTP host
		if ( empty( $port ) ) {
			$http->set_header( 'Host', $host );
		} else {
			$http->set_header( 'Host', "{$host}:{$port}" );
		}

		// Set HTTP authorization
		if ( ( $user = get_option( AI1WM_AUTH_USER ) ) && ( $password = get_option( AI1WM_AUTH_PASSWORD ) ) ) {
			if ( ( $hash = base64_encode( "{$user}:{$password}" ) ) ) {
				$http->set_header( 'Authorization', "Basic {$hash}" );
			}
		}

		$blocking = false;

		// Run non-blocking HTTP request
		$http->get( add_query_arg( ai1wm_urlencode( $params ), str_replace( "//{$host}", "//{$ip}", $url ) ), $blocking );
	}

	public static function resolve( $url, $params = array(), Ai1wm_Http_Abstract $http = null ) {

		// Reset IP address and adapter
		delete_option( AI1WM_URL_IP );
		delete_option( AI1WM_URL_ADAPTER );

		// Set secret
		$secret_key = get_option( AI1WM_SECRET_KEY );

		// Set host
		$host = parse_url( $url, PHP_URL_HOST );

		// Get port
		$port = parse_url( $url, PHP_URL_PORT );

		// Set server IP address
		if ( ! empty( $_SERVER['SERVER_ADDR'] ) ) {
			$server = $_SERVER['SERVER_ADDR'];
		} elseif ( ! empty( $_SERVER['LOCAL_ADDR'] ) ) {
			$server = $_SERVER['LOCAL_ADDR'];
		} else {
			$server = '127.0.0.1';
		}

		// Set local IP address
		$local = gethostbyname( $host );

		// HTTP resolve
		foreach ( array( 'stream', 'curl' ) as $adapter ) {
			foreach ( array( $server, $local, $host ) as $ip ) {

				// Add IPv6 support
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
					$ip = "[$ip]";
				}

				// Set HTTP params
				$params = array_merge( $params, array(
					'secret_key'  => $secret_key,
					'url_ip'      => $ip,
					'url_adapter' => $adapter,
				) );

				// Set HTTP client
				if ( empty( $http ) ) {
					$http = Ai1wm_Http_Factory::create( $adapter );
				}

				// Set HTTP host
				if ( empty( $port ) ) {
					$http->set_header( 'Host', $host );
				} else {
					$http->set_header( 'Host', "{$host}:{$port}" );
				}

				// Set HTTP authorization
				if ( ( $user = get_option( AI1WM_AUTH_USER ) ) && ( $password = get_option( AI1WM_AUTH_PASSWORD ) ) ) {
					if ( ( $hash = base64_encode( "{$user}:{$password}" ) ) ) {
						$http->set_header( 'Authorization', "Basic {$hash}" );
					}
				}

				$blocking = true;

				// Run blocking HTTP request
				$http->get( add_query_arg( ai1wm_urlencode( $params ), str_replace( "//{$host}", "//{$ip}", $url ) ), $blocking );

				// Flush WP cache
				ai1wm_cache_flush();

				// Is valid adapter?
				if ( get_option( AI1WM_URL_IP ) && get_option( AI1WM_URL_ADAPTER ) ) {
					return;
				}

				// Reset HTTP client
				$http = null;
			}
		}

		// No connection
		throw new Ai1wm_Http_Exception( __(
			'There was a problem while reaching your server.<br />' .
			'Contact <a href="mailto:support@servmask.com">support@servmask.com</a> for assistance.',
			AI1WM_PLUGIN_NAME
		) );
	}
}
