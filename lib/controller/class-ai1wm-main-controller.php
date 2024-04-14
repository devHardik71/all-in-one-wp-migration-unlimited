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

class Ai1wm_Main_Controller {

	/**
	 * Main Application Controller
	 *
	 * @return Ai1wm_Main_Controller
	 */
	public function __construct() {
		register_activation_hook( AI1WM_PLUGIN_BASENAME, array( $this, 'activation_hook' ) );

		// Activate hooks
		$this->activate_actions()
			->activate_filters()
			->activate_textdomain();
	}

	/**
	 * Activation hook callback
	 *
	 * @return Object Instance of this class
	 */
	public function activation_hook() {

	}

	/**
	 * Initializes language domain for the plugin
	 *
	 * @return Object Instance of this class
	 */
	private function activate_textdomain() {
		load_plugin_textdomain( AI1WM_PLUGIN_NAME, false, false );

		return $this;
	}

	/**
	 * Register listeners for actions
	 *
	 * @return Object Instance of this class
	 */
	private function activate_actions() {
		// Init
		add_action( 'admin_init', array( $this, 'init' ) );

		// Router
		add_action( 'admin_init', array( $this, 'router' ) );

		// Admin header
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		// Create initial folders
		add_action( 'admin_init', array( $this, 'create_folders' ) );

		// All in One WP Migration
		add_action( 'plugins_loaded', array( $this, 'ai1wm_loaded' ), 20 );

		// Export and import commands
		add_action( 'plugins_loaded', array( $this, 'ai1wm_commands' ), 10 );

		// Export and import buttons
		add_action( 'plugins_loaded', array( $this, 'ai1wm_buttons' ), 10 );

		// Add export scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_export_scripts_and_styles' ), 5 );

		// Add import scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_import_scripts_and_styles' ), 5 );

		// Add backups scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_backups_scripts_and_styles' ), 5 );

		// Add updater scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_updater_scripts_and_styles' ), 5 );

		return $this;
	}

	/**
	 * Register listeners for filters
	 *
	 * @return Object Instance of this class
	 */
	private function activate_filters() {
		// Add a links to plugin list page
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		// Add custom schedules
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ), 9999 );

		return $this;
	}

	/**
	 * Export and import commands
	 *
	 * @return void
	 */
	public function ai1wm_commands() {
		// Add export commands
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Init::execute', 5 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Compatibility::execute', 5 );

		// Resolve URL address
		if ( ai1wm_is_scheduled_backup() ) {
			add_filter( 'ai1wm_export', 'Ai1wm_Export_Resolve::execute', 5 );
		}

		add_filter( 'ai1wm_export', 'Ai1wm_Export_Archive::execute', 10 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Config::execute', 50 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Enumerate::execute', 100 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Content::execute', 150 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Database::execute', 200 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Database_File::execute', 220 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Download::execute', 250 );
		add_filter( 'ai1wm_export', 'Ai1wm_Export_Clean::execute', 300 );

		// Add import commands
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Upload::execute', 5 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Compatibility::execute', 10 );

		// Resolve URL address
		if ( ai1wm_is_scheduled_backup() ) {
			add_filter( 'ai1wm_import', 'Ai1wm_Import_Resolve::execute', 10 );
		}

		add_filter( 'ai1wm_import', 'Ai1wm_Import_Validate::execute', 50 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Confirm::execute', 100 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Blogs::execute', 150 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Enumerate::execute', 200 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Content::execute', 250 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Mu_Plugins::execute', 270 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Database::execute', 300 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Done::execute', 350 );
		add_filter( 'ai1wm_import', 'Ai1wm_Import_Clean::execute', 400 );
	}

	/**
	 * Export and import buttons
	 *
	 * @return void
	 */
	public function ai1wm_buttons() {
		// Add export buttons
		add_filter( 'ai1wm_export_buttons', 'Ai1wm_Export_Controller::buttons' );

		// Add import buttons
		add_filter( 'ai1wm_import_buttons', 'Ai1wm_Import_Controller::buttons' );
	}

	/**
	 * All in One WP Migration loaded
	 *
	 * @return void
	 */
	public function ai1wm_loaded() {
		if ( is_multisite() ) {
			if ( apply_filters( 'ai1wm_multisite_menu', false ) ) {
				add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
			} else {
				add_action( 'network_admin_notices', array( $this, 'multisite_notice' ) );
			}
		} else {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		// Add automatic plugins update
		add_action( 'wp_maybe_auto_update', 'Ai1wm_Updater_Controller::check_for_updates' );

		// Add chunk size limit
		add_filter( 'ai1wm_max_chunk_size', 'Ai1wm_Import_Controller::max_chunk_size' );

		// Add plugins api
		add_filter( 'plugins_api', 'Ai1wm_Updater_Controller::plugins_api', 20, 3 );

		// Add plugins updates
		add_filter( 'pre_set_site_transient_update_plugins', 'Ai1wm_Updater_Controller::pre_update_plugins' );

		// Add plugins metadata
		add_filter( 'site_transient_update_plugins', 'Ai1wm_Updater_Controller::update_plugins' );

		// Add "Check for updates" link to plugin list page
		add_filter( 'plugin_row_meta', 'Ai1wm_Updater_Controller::plugin_row_meta', 10, 2 );
	}

	/**
	 * Display multisite notice
	 *
	 * @return void
	 */
	public function multisite_notice() {
		Ai1wm_Template::render( 'main/multisite-notice' );
	}

	/**
	 * Display notice for storage directory
	 *
	 * @return void
	 */
	public function storage_path_notice() {
		Ai1wm_Template::render( 'main/storage-path-notice' );
	}

	/**
	 * Display notice for index file in storage directory
	 *
	 * @return void
	 */
	public function storage_index_notice() {
		Ai1wm_Template::render( 'main/storage-index-notice' );
	}

	/**
	 * Display notice for backups directory
	 *
	 * @return void
	 */
	public function backups_path_notice() {
		Ai1wm_Template::render( 'main/backups-path-notice' );
	}

	/**
	 * Display notice for .htaccess file in backups directory
	 *
	 * @return void
	 */
	public function backups_htaccess_notice() {
		Ai1wm_Template::render( 'main/backups-htaccess-notice' );
	}

	/**
	 * Display notice for web.config file in backups directory
	 *
	 * @return void
	 */
	public function backups_webconfig_notice() {
		Ai1wm_Template::render( 'main/backups-webconfig-notice' );
	}

	/**
	 * Display notice for index file in backups directory
	 *
	 * @return void
	 */
	public function backups_index_notice() {
		Ai1wm_Template::render( 'main/backups-index-notice' );
	}

	/**
	 * Add a links to plugin list page
	 *
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $file == AI1WM_PLUGIN_BASENAME ) {
			$links[] = Ai1wm_Template::get_content( 'main/get-support' );
		}

		return $links;
	}

	/**
	 * Create folders needed for plugin operation, if they don't exist
	 *
	 * @return void
	 */
	public function create_folders() {
		// Check if storage folder is created
		if ( ! is_dir( AI1WM_STORAGE_PATH ) ) {
			if ( ! mkdir( AI1WM_STORAGE_PATH ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'storage_path_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'storage_path_notice' ) );
				}
			}
		}

		// Check if index.php is created in storage folder
		if ( ! is_file( AI1WM_STORAGE_INDEX ) ) {
			if ( ! Ai1wm_File_Index::create( AI1WM_STORAGE_INDEX ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'storage_index_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'storage_index_notice' ) );
				}
			}
		}

		// Check if ai1wm-backups folder is created
		if ( ! is_dir( AI1WM_BACKUPS_PATH ) ) {
			if ( ! mkdir( AI1WM_BACKUPS_PATH ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'backups_path_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'backups_path_notice' ) );
				}
			}
		}

		// Check if .htaccess is created in backups folder
		if ( ! is_file( AI1WM_BACKUPS_HTACCESS ) ) {
			if ( ! Ai1wm_File_Htaccess::create( AI1WM_BACKUPS_HTACCESS ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'backups_htaccess_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'backups_htaccess_notice' ) );
				}
			}
		}

		// Check if web.config is created in backups folder
		if ( ! is_file( AI1WM_BACKUPS_WEBCONFIG ) ) {
			if ( ! Ai1wm_File_Webconfig::create( AI1WM_BACKUPS_WEBCONFIG ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'backups_webconfig_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'backups_webconfig_notice' ) );
				}
			}
		}

		// Check if index.php is created in backups folder
		if ( ! is_file( AI1WM_BACKUPS_INDEX ) ) {
			if ( ! Ai1wm_File_Index::create( AI1WM_BACKUPS_INDEX ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'backups_index_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'backups_index_notice' ) );
				}
			}
		}
	}

	/**
	 * Register plugin menus
	 *
	 * @return void
	 */
	public function admin_menu() {
		// top level WP Migration menu
		add_menu_page(
			'All-in-One WP Migration',
			'All-in-One WP Migration',
			'export',
			'site-migration-export',
			'Ai1wm_Export_Controller::index',
			'',
			'76.295'
		);

		// sublevel Export menu
		add_submenu_page(
			'site-migration-export',
			__( 'Export', AI1WM_PLUGIN_NAME ),
			__( 'Export', AI1WM_PLUGIN_NAME ),
			'export',
			'site-migration-export',
			'Ai1wm_Export_Controller::index'
		);

		// sublevel Import menu
		add_submenu_page(
			'site-migration-export',
			__( 'Import', AI1WM_PLUGIN_NAME ),
			__( 'Import', AI1WM_PLUGIN_NAME ),
			'import',
			'site-migration-import',
			'Ai1wm_Import_Controller::index'
		);

		// sublevel Backups menu
		add_submenu_page(
			'site-migration-export',
			__( 'Backups', AI1WM_PLUGIN_NAME ),
			__( 'Backups', AI1WM_PLUGIN_NAME ),
			'import',
			'site-migration-backups',
			'Ai1wm_Backups_Controller::index'
		);
	}

	/**
	 * Register scripts and styles for Export Controller
	 *
	 * @return void
	 */
	public function register_export_scripts_and_styles( $hook ) {
		if ( stripos( 'toplevel_page_site-migration-export', $hook ) === false ) {
			return;
		}

		do_action( 'ai1mw_register_export_scripts_and_styles' );

		// we don't want heartbeat to occur when exporting
		wp_deregister_script( 'heartbeat' );

		// we don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		wp_enqueue_style(
			'ai1wm-css-export',
			Ai1wm_Template::asset_link( 'css/export.min.css' )
		);
		wp_enqueue_script(
			'ai1wm-js-export',
			Ai1wm_Template::asset_link( 'javascript/export.min.js' ),
			array( 'jquery' )
		);
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_feedback', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_feedback' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_report', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_report' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_export', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_export' ) ),
			),
			'status'     => array(
				'url' => wp_make_link_relative( add_query_arg( array( 'secret_key' => get_option( AI1WM_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=ai1wm_status' ) ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
	}

	/**
	 * Register scripts and styles for Import Controller
	 *
	 * @return void
	 */
	public function register_import_scripts_and_styles( $hook ) {
		if ( stripos( 'all-in-one-wp-migration_page_site-migration-import', $hook ) === false ) {
			return;
		}

		do_action( 'ai1mw_register_import_scripts_and_styles' );

		// we don't want heartbeat to occur when importing
		wp_deregister_script( 'heartbeat' );

		// we don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		wp_enqueue_style(
			'ai1wm-css-import',
			Ai1wm_Template::asset_link( 'css/import.min.css' )
		);
		wp_enqueue_script(
			'ai1wm-js-import',
			Ai1wm_Template::asset_link( 'javascript/import.min.js' ),
			array( 'jquery' )
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_uploader', array(
			'chunk_size'  => apply_filters( 'ai1wm_max_chunk_size', AI1WM_MAX_CHUNK_SIZE ),
			'max_retries' => apply_filters( 'ai1wm_max_chunk_retries', AI1WM_MAX_CHUNK_RETRIES ),
			'url'         => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_import' ) ),
			'params'      => array(
				'priority'   => 5,
				'secret_key' => get_option( AI1WM_SECRET_KEY ),
			),
			'filters'     => array(
				'ai1wm_archive_extension' => array( 'wpress', 'bin' ),
				'ai1wm_archive_size'      => apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ),
			),
		) );
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_feedback', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_feedback' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_report', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_report' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_import', array(
			'ajax'              => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_import' ) ),
			),
			'status'            => array(
				'url' => wp_make_link_relative( add_query_arg( array( 'secret_key' => get_option( AI1WM_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=ai1wm_status' ) ) ),
			),
			'secret_key'        => get_option( AI1WM_SECRET_KEY ),
			'oversize'          => sprintf(
				__(
					'The file that you are trying to import is over the maximum upload file size limit of <strong>%s</strong>.<br />' .
					'You can remove this restriction by purchasing our ' .
					'<a href="https://servmask.com/products/unlimited-extension" target="_blank">Unlimited Extension</a>.',
					AI1WM_PLUGIN_NAME
				),
				size_format( apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ) )
			),
			'invalid_extension' => sprintf(
				__(
					'Version 2.1.1 of All in One WP Migration introduces new compression algorithm. ' .
					'It makes exporting and importing 10 times faster.' .
					'<br />Unfortunately, the new format is not back compatible with backups made with earlier ' .
					'versions of the plugin.' .
					'<br />You can either create a new backup with the latest version of the ' .
					'plugin, or convert the archive to the new format using our tools ' .
					'<a href="%s" target="_blank">here</a>.',
					AI1WM_PLUGIN_NAME
				),
				AI1WM_ARCHIVE_TOOLS_URL
			),
		) );
	}

	/**
	 * Register scripts and styles for Backups Controller
	 *
	 * @return void
	 */
	public function register_backups_scripts_and_styles( $hook ) {
		if ( stripos( 'all-in-one-wp-migration_page_site-migration-backups', $hook ) === false ) {
			return;
		}

		do_action( 'ai1mw_register_backups_scripts_and_styles' );

		// we don't want heartbeat to occur when restoring
		wp_deregister_script( 'heartbeat' );

		// we don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		wp_enqueue_style(
			'ai1wm-css-backups',
			Ai1wm_Template::asset_link( 'css/backups.min.css' )
		);
		wp_enqueue_script(
			'ai1wm-js-backups',
			Ai1wm_Template::asset_link( 'javascript/backups.min.js' ),
			array( 'jquery' )
		);
		wp_localize_script( 'ai1wm-js-backups', 'ai1wm_feedback', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_feedback' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-backups', 'ai1wm_report', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_report' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-backups', 'ai1wm_backups', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_backups' ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
		wp_localize_script( 'ai1wm-js-backups', 'ai1wm_import', array(
			'ajax'       => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_import' ) ),
			),
			'status'     => array(
				'url' => wp_make_link_relative( add_query_arg( array( 'secret_key' => get_option( AI1WM_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=ai1wm_status' ) ) ),
			),
			'secret_key' => get_option( AI1WM_SECRET_KEY ),
		) );
	}

	/**
	 * Register scripts and styles for Updater Controller
	 *
	 * @return void
	 */
	public function register_updater_scripts_and_styles( $hook ) {
		if ( 'plugins.php' !== strtolower( $hook ) ) {
			return;
		}

		do_action( 'ai1mw_register_updater_scripts_and_styles' );

		wp_enqueue_style(
			'ai1wm-css-updater',
			Ai1wm_Template::asset_link( 'css/updater.min.css' )
		);
		wp_enqueue_script(
			'ai1wm-js-updater',
			Ai1wm_Template::asset_link( 'javascript/updater.min.js' ),
			array( 'jquery' )
		);
		wp_localize_script( 'ai1wm-js-updater', 'ai1wm_updater', array(
			'ajax' => array(
				'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wm_updater' ) ),
			),
		) );
	}

	/**
	 * Outputs menu icon between head tags
	 *
	 * @return void
	 */
	public function admin_head() {
		global $wp_version;

		// Admin header
		Ai1wm_Template::render( 'main/admin-head', array( 'version' => $wp_version ) );
	}

	/**
	 * Register initial parameters
	 *
	 * @return void
	 */
	public function init() {
		// Set secret key
		if ( ! get_option( AI1WM_SECRET_KEY ) ) {
			update_option( AI1WM_SECRET_KEY, wp_generate_password( 12, false ) );
		}

		// Set username
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
			update_option( AI1WM_AUTH_USER, $_SERVER['PHP_AUTH_USER'] );
		} elseif ( isset( $_SERVER['REMOTE_USER'] ) ) {
			update_option( AI1WM_AUTH_USER, $_SERVER['REMOTE_USER'] );
		}

		// Set password
		if ( isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			update_option( AI1WM_AUTH_PASSWORD, $_SERVER['PHP_AUTH_PW'] );
		}

		// Check for updates
		if ( isset( $_GET['ai1wm_updater'] ) ) {
			if ( current_user_can( 'update_plugins' ) && check_admin_referer( 'ai1wm_updater_nonce' ) ) {
				Ai1wm_Updater::check_for_updates();
			}
		}
	}

	/**
	 * Register initial router
	 *
	 * @return void
	 */
	public function router() {
		// Public actions
		add_action( 'wp_ajax_nopriv_ai1wm_export', 'Ai1wm_Export_Controller::export' );
		add_action( 'wp_ajax_nopriv_ai1wm_import', 'Ai1wm_Import_Controller::import' );
		add_action( 'wp_ajax_nopriv_ai1wm_status', 'Ai1wm_Status_Controller::status' );
		add_action( 'wp_ajax_nopriv_ai1wm_resolve', 'Ai1wm_Resolve_Controller::resolve' );
		add_action( 'wp_ajax_nopriv_ai1wm_backups', 'Ai1wm_Backups_Controller::delete' );
		add_action( 'wp_ajax_nopriv_ai1wm_feedback', 'Ai1wm_Feedback_Controller::feedback' );
		add_action( 'wp_ajax_nopriv_ai1wm_report', 'Ai1wm_Report_Controller::report' );

		// Private actions
		add_action( 'wp_ajax_ai1wm_export', 'Ai1wm_Export_Controller::export' );
		add_action( 'wp_ajax_ai1wm_import', 'Ai1wm_Import_Controller::import' );
		add_action( 'wp_ajax_ai1wm_status', 'Ai1wm_Status_Controller::status' );
		add_action( 'wp_ajax_ai1wm_resolve', 'Ai1wm_Resolve_Controller::resolve' );
		add_action( 'wp_ajax_ai1wm_backups', 'Ai1wm_Backups_Controller::delete' );
		add_action( 'wp_ajax_ai1wm_feedback', 'Ai1wm_Feedback_Controller::feedback' );
		add_action( 'wp_ajax_ai1wm_report', 'Ai1wm_Report_Controller::report' );

		// Update
		if ( current_user_can( 'update_plugins' ) ) {
			add_action( 'wp_ajax_ai1wm_updater', 'Ai1wm_Updater_Controller::updater' );
		}
	}

	/**
	 * Add custom cron schedules
	 *
	 * @param  array $schedules List of schedules
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['weekly']  = array(
			'display'  => __( 'Weekly', AI1WM_PLUGIN_NAME ),
			'interval' => 60 * 60 * 24 * 7,
		);
		$schedules['monthly'] = array(
			'display'  => __( 'Monthly', AI1WM_PLUGIN_NAME ),
			'interval' => ( strtotime( '+1 month' ) - time() ),
		);

		return $schedules;
	}
}
