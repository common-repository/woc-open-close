<?php
/*
	Plugin Name: Open Close WooCommerce Store
	Plugin URI: https://pluginbazar.com/plugin/woocommerce-open-close/
	Description: Open Close WooCommerce store automatically with predefined schedules. Stop getting orders when your store is closed.
	Version: 4.9.3
	Text Domain: woc-open-close
	Author: Jaed Mosharraf & Pluginbazar Team
	Author URI: https://pluginbazar.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) || exit;
defined( 'WOOOPENCLOSE_PLUGIN_URL' ) || define( 'WOOOPENCLOSE_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'WOOOPENCLOSE_PLUGIN_DIR' ) || define( 'WOOOPENCLOSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'WOOOPENCLOSE_PLUGIN_FILE' ) || define( 'WOOOPENCLOSE_PLUGIN_FILE', plugin_basename( __FILE__ ) );
defined( 'WOOOPENCLOSE_TICKET_URL' ) || define( 'WOOOPENCLOSE_TICKET_URL', 'https://pluginbazar.com/supports/open-close-woocommerce-store/#new-topic-0' );
defined( 'WOOOPENCLOSE_PLUGIN_LINK' ) || define( 'WOOOPENCLOSE_PLUGIN_LINK', 'https://pluginbazar.com/plugin/open-close-woocommerce-store/' );
defined( 'WOOOPENCLOSE_DOCS_URL' ) || define( 'WOOOPENCLOSE_DOCS_URL', 'https://docs.pluginbazar.com/plugin/open-close-woocommerce-store/' );
defined( 'WOOOPENCLOSE_WP_REVIEW_URL' ) || define( 'WOOOPENCLOSE_WP_REVIEW_URL', 'https://wordpress.org/support/plugin/woc-open-close/reviews/' );
defined( 'WOOOPENCLOSE_VERSION' ) || define( 'WOOOPENCLOSE_VERSION', '4.9.3' );


if ( ! function_exists( 'wooopenclose_is_plugin_active' ) ) {
	function wooopenclose_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

if ( ! wooopenclose_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}


if ( ! function_exists( 'wooopenclose_check_pro_version' ) ) {
	/**
	 * Check pro version.
	 *
	 * @return void
	 */
	function wooopenclose_check_pro_version() {

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$pro_version_slug = 'woc-open-close-pro/woc-open-close-pro.php';
		if ( wooopenclose_is_plugin_active( $pro_version_slug ) ) {
			$pro_version_file = WP_CONTENT_DIR . '/plugins/' . $pro_version_slug;
			$pro_version_data = get_plugin_data( $pro_version_file );
			$pro_version_now  = isset( $pro_version_data['Version'] ) ? $pro_version_data['Version'] : '';
			$upgrade_notice   = 'We have a new pro version of WooCommerce Open Close, Please download it from here - <a href="https://go.pluginbazar.com/download/">https://go.pluginbazar.com/download/</a> and reload this page to skip the error.';

			if ( wooopenclose_is_plugin_active( $pro_version_slug ) && version_compare( $pro_version_now, '1.3.4', '<' ) ) {

				// Deactivate the older version
				deactivate_plugins( $pro_version_file );

				// Showing message
				printf( '<div class="notice notice-error"><p>%s</p></div>', $upgrade_notice );
			}
		}
	}
}
wooopenclose_check_pro_version();


if ( ! class_exists( 'WOOOPENCLOSE_Main' ) ) {
	/**
	 * Class WOOOPENCLOSE_Main
	 */
	class WOOOPENCLOSE_Main {

		protected static $_instance = null;

		/**
		 * WOOOPENCLOSE_Main constructor.
		 */
		function __construct() {

			$this->define_scripts();
			$this->define_classes_functions();

			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'woc-open-close', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Register Widgets
		 */
		function register_widgets() {
			register_widget( 'WOOOPENCLOSE_WIDGET_Schedule' );
		}


		/**
		 * Include Classes and Functions
		 */
		function define_classes_functions() {

			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-functions.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/functions.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-hooks.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-settings.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-post-meta.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-columns.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-widget-schedule.php';
			require_once WOOOPENCLOSE_PLUGIN_DIR . 'includes/classes/class-schedule.php';

		}


		/**
		 * Localize Scripts
		 *
		 * @return mixed|void
		 */
		function localize_scripts() {
			return apply_filters( 'woc_filters_localize_scripts', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'copyText'   => esc_html__( 'Copied !', 'woc-open-close' ),
				'removeConf' => esc_html__( 'Are you really want to remove this schedule?', 'woc-open-close' ),
			) );
		}


		/**
		 * Load Front Scripts
		 */
		function front_scripts() {

			wp_enqueue_script( 'jbox-popup', plugins_url( '/assets/front/js/jBox.all.min.js', __FILE__ ), array( 'jquery' ), WOOOPENCLOSE_VERSION, true );
			wp_enqueue_script( 'wooopenclose-front', plugins_url( '/assets/front/js/scripts.js', __FILE__ ), array( 'jquery' ), WOOOPENCLOSE_VERSION, true );
			wp_localize_script( 'wooopenclose-front', 'wooopenclose', $this->localize_scripts() );

			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'wooopenclose-core', WOOOPENCLOSE_PLUGIN_URL . 'assets/front/css/pb-core-styles.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'jbox-popup', WOOOPENCLOSE_PLUGIN_URL . 'assets/front/css/jBox.all.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'wooopenclose-front', WOOOPENCLOSE_PLUGIN_URL . 'assets/front/css/style.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'wooopenclose-tool-tip', WOOOPENCLOSE_PLUGIN_URL . 'assets/hint.min.css', array(), WOOOPENCLOSE_VERSION );

			if ( woc_pro_available() ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-time-picker', WOOOPENCLOSE_PLUGIN_URL . '/assets/jquery-timepicker.js', array( 'jquery' ), WOOOPENCLOSE_VERSION );
				wp_enqueue_script( 'wooopenclose-global', plugins_url( '/assets/scripts.js', __FILE__ ), array( 'jquery' ), WOOOPENCLOSE_VERSION, true );
				wp_localize_script( 'wooopenclose-global', 'wooopenclose', $this->localize_scripts() );

				wp_enqueue_style( 'wooopenclose-schedules', WOOOPENCLOSE_PLUGIN_URL . 'assets/admin/css/schedule-style.css', array(), WOOOPENCLOSE_VERSION );
				wp_enqueue_style( 'jquery-timepicker', WOOOPENCLOSE_PLUGIN_URL . 'assets/jquery-timepicker.css', array(), WOOOPENCLOSE_VERSION );
			}
		}


		/**
		 * Load Admin Scripts
		 */
		function admin_scripts() {

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-time-picker', WOOOPENCLOSE_PLUGIN_URL . '/assets/jquery-timepicker.js', array( 'jquery' ), WOOOPENCLOSE_VERSION );
			wp_enqueue_script( 'jquery-chosen', WOOOPENCLOSE_PLUGIN_URL . '/assets/chosen.jquery.min.js', array( 'jquery' ), WOOOPENCLOSE_VERSION );
			wp_enqueue_script( 'wooopenclose-admin', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ), WOOOPENCLOSE_VERSION );
			wp_localize_script( 'wooopenclose-admin', 'wooopenclose', $this->localize_scripts() );

			wp_enqueue_script( 'wooopenclose-global', plugins_url( '/assets/scripts.js', __FILE__ ), array( 'jquery' ), WOOOPENCLOSE_VERSION, true );
			wp_localize_script( 'wooopenclose-global', 'wooopenclose', $this->localize_scripts() );

			wp_enqueue_style( 'wooopenclose-admin', WOOOPENCLOSE_PLUGIN_URL . 'assets/admin/css/style.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'wooopenclose-schedules', WOOOPENCLOSE_PLUGIN_URL . 'assets/admin/css/schedule-style.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'wooopenclose-tool-tip', WOOOPENCLOSE_PLUGIN_URL . 'assets/hint.min.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'jquery-timepicker', WOOOPENCLOSE_PLUGIN_URL . 'assets/jquery-timepicker.css', array(), WOOOPENCLOSE_VERSION );
			wp_enqueue_style( 'jquery-chosen', WOOOPENCLOSE_PLUGIN_URL . 'assets/chosen.min.css', array(), WOOOPENCLOSE_VERSION );
		}


		/**
		 * Load Scripts
		 */
		function define_scripts() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		}


		/**
		 * @return WOOOPENCLOSE_Main
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

function wpdk_init_woc_open_close() {

	if ( ! function_exists( 'get_plugins' ) ) {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	if ( ! class_exists( 'WPDK\Client' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wpdk/classes/class-client.php' );
	}

	global $wooopenclose_wpdk;

	$wooopenclose_wpdk = new WPDK\Client( esc_html( 'Store Open Close for WooCommerce' ), 'woc-open-close', 15, __FILE__ );

	do_action( 'wpdk_init_woc_open_close', $wooopenclose_wpdk );
}

/**
 * @global \WPDK\Client $wooopenclose_wpdk
 */
global $wooopenclose_wpdk;

wpdk_init_woc_open_close();

add_action( 'plugins_loaded', array( 'WOOOPENCLOSE_Main', 'instance' ), 100 );

