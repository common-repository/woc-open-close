<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

if ( ! class_exists( 'WOOOPENCLOSE_Hooks' ) ) {
	/**
	 * Class WOOOPENCLOSE_Hooks
	 */
	class WOOOPENCLOSE_Hooks {

		/**
		 * WOOOPENCLOSE_Hooks constructor.
		 */
		function __construct() {

			if ( ! is_admin() ) {
				add_action( 'init', array( $this, 'ob_start' ) );
				add_action( 'wp_footer', array( $this, 'ob_end' ) );
			}

			add_action( 'init', array( $this, 'register_everything' ) );
			add_action( 'wp_footer', array( $this, 'display_popup_statusbar' ) );
			add_action( 'admin_notices', array( $this, 'manage_admin_notices' ) );
			add_action( 'admin_bar_menu', array( $this, 'handle_admin_bar_menu' ), 9999, 1 );

			add_filter( 'widget_text', 'do_shortcode', 11 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 2 );
			add_filter( 'plugin_action_links_' . WOOOPENCLOSE_PLUGIN_FILE, array( $this, 'add_plugin_actions' ), 10, 2 );
			add_filter( 'post_updated_messages', array( $this, 'filter_update_messages' ), 10, 1 );

			add_action( 'wp_ajax_woc_add_schedule', array( $this, 'ajax_add_schedule' ) );
			add_action( 'wp_ajax_woc_switch_active', array( $this, 'ajax_switch_active' ) );
			add_action( 'wp_ajax_woc_update_timezone', array( $this, 'ajax_update_timezone' ) );
			add_action( 'Pluginbazar/Settings/Option/before_woc_instant_force', array( $this, 'display_instant_controller' ) );
			add_action( 'in_admin_header', array( $this, 'render_admin_loader' ), 0 );


			// Disable checkout button
			if ( ! wooopenclose()->is_open() && Utils::get_option( 'disable_checkout_button', false ) ) {

				// Remove on cart page
				remove_action( 'woocommerce_proceed_to_checkout', 'wc_get_pay_buttons', 10 );
				remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
				add_filter( 'wc_stripe_show_payment_request_on_cart', '__return_false' );

				// Remove on cart sidebar
				remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

				// Remove on checkout page
				add_filter( 'woocommerce_order_button_html', '__return_empty_string', 0 );
			}
		}


		/**
		 * Render preloader in Admin
		 */
		function render_admin_loader() {

			global $current_screen;

			if ( $current_screen->post_type == 'woc_hour' ) {
				printf( '<div class="wooopenclose-loader-wrap"><div class="wooopenclose-loader"></div></div>' );
			}
		}


		/**
		 * Display instant controller
		 */
		function display_instant_controller() {

			$shop_status = wooopenclose()->is_open();
			$disabled    = woc_pro_available() ? '' : 'disabled';

			printf( '<label class="hint--top-right wooopenclose-quick-switch %s" aria-label="%s"><input %s type="checkbox" %s name="woc_instant_force"><span class="wooopenclose-quick-switch-bubble"></span></label>',
				$disabled,
				sprintf( esc_html__( 'Status: %s, Click here to %s your shop forcefully. It will ignore all other settings', 'woc-open-close' ),
					$shop_status ? 'Open' : 'Close',
					$shop_status ? 'Close' : 'Open'
				),
				$disabled,
				$shop_status ? 'checked' : ''
			);
		}


		/**
		 * Ajax update timezone
		 */
		function ajax_update_timezone() {
			$timezone           = isset( $_POST['time_zone'] ) ? sanitize_text_field( $_POST['time_zone'] ) : '';
			$woc_timezone_nonce = isset( $_POST['woc_timezone_nonce'] ) ? sanitize_text_field( $_POST['woc_timezone_nonce'] ) : '';

			if ( current_user_can( 'manage_options' ) && wp_verify_nonce( $woc_timezone_nonce, 'woc-verify-timezone' ) ) {

				if ( empty( $timezone ) ) {
					wp_send_json_error();
				}
				update_option( 'timezone_string', $timezone );
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}


		/**
		 * Ajax switch activate
		 */
		function ajax_switch_active() {

			$post_id    = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
			$woc_active = isset( $_POST['woc_active'] ) ? sanitize_text_field( $_POST['woc_active'] ) : 'false';
			$woc_nonce  = isset( $_POST['woc_nonce'] ) ? sanitize_text_field( $_POST['woc_nonce'] ) : 'false';

			if ( current_user_can( 'manage_options' ) && wp_verify_nonce( $woc_nonce, 'woc-verify' ) ) {

				if ( empty( $post_id ) || $post_id == 0 ) {
					wp_send_json_error();
				}

				if ( $woc_active == 'true' ) {
					Utils::update_option( 'woc_active_set', $post_id );
				}

				if ( $woc_active == 'false' ) {
					Utils::update_option( 'woc_active_set', '' );
				}

				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}


		/**
		 * Ajax add schedule
		 */
		function ajax_add_schedule() {

			$day_id = isset( $_POST['day_id'] ) ? sanitize_text_field( $_POST['day_id'] ) : '';
			$open   = isset( $_POST['open'] ) ? sanitize_text_field( $_POST['open'] ) : '';
			$close  = isset( $_POST['close'] ) ? sanitize_text_field( $_POST['close'] ) : '';

			ob_start();

			wooopenclose()->generate_woc_schedule(
				array(
					'day_id' => $day_id,
					'open'   => $open,
					'close'  => $close,
				)
			);

			wp_send_json_success( ob_get_clean() );
		}


		/**
		 * Display Countdown timer content
		 */
		function display_countdown_timer() {

			$timer_style  = Utils::get_option( 'woc_timer_style', '1' );
			$text_to_show = '';

			if ( Utils::get_option( 'woc_timer_display_text', true ) ) {
				$text_to_show = wooopenclose()->is_open() ? Utils::get_option( 'woc_timer_text_open', esc_html__( 'This store will be close within', 'woc-open-close' ) ) : Utils::get_option( 'woc_timer_text_close', esc_html__( 'This store will be open within', 'woc-open-close' ) );
				$text_to_show = empty( $text_to_show ) ? '' : sprintf( '<p>%s</p> ', esc_html( $text_to_show ) );
			}

			printf( '<div class="wooopenclose-countdown-wrapper">%s%s</div>', $text_to_show, wooopenclose()->get_countdown_timer( $timer_style ) );
		}


		/**
		 * Add hooks dynamically to display countdown timer on multiple pages
		 */
		function display_countdown_timer_dynamically() {

			$show_timer_on = Utils::get_option( 'woc_timer_display_on', array() );

			foreach ( $show_timer_on as $timer_place ) {

				$action_hook     = '';
				$action_priority = 10;

				switch ( $timer_place ) {
					case 'before_cart_table' :
						$action_hook = 'woocommerce_before_cart_table';
						break;
					case 'after_cart_table' :
						$action_hook = 'woocommerce_after_cart_table';
						break;
					case 'before_cart_total' :
						$action_hook = 'woocommerce_before_cart_totals';
						break;
					case 'after_cart_total' :
						$action_hook = 'woocommerce_after_cart_totals';
						break;
					case 'before_checkout_form' :
						$action_hook = 'woocommerce_before_checkout_form';
						break;
					case 'after_checkout_form' :
						$action_hook = 'woocommerce_after_checkout_form';
						break;
					case 'before_order_review' :
						$action_hook = 'woocommerce_checkout_before_order_review';
						break;
					case 'after_order_review' :
						$action_hook     = 'woocommerce_checkout_order_review';
						$action_priority = 11;
						break;
					case 'top_on_myaccount' :
						$action_hook = 'woocommerce_account_navigation';
						break;
					case 'before_cart_single' :
						$action_hook     = 'woocommerce_single_product_summary';
						$action_priority = 29;
						break;
				}

				add_action( $action_hook, array( $this, 'display_countdown_timer' ), $action_priority );
			}
		}


		/**
		 * Update messages for this post type
		 *
		 * @param array $messages
		 *
		 * @return array
		 */
		function filter_update_messages( $messages = array() ) {

			if ( get_post_type() === 'woc_hour' ) {
				$messages['post'][1] = esc_html__( 'Business schedule has been updated successfully', 'woc-open-close' );
				$messages['post'][6] = esc_html__( 'Business schedule has been published successfully', 'woc-open-close' );
			}

			return $messages;
		}


		/**
		 * Add custom links to Plugin actions
		 *
		 * @param $links
		 *
		 * @return array
		 */
		function add_plugin_actions( $links ) {

			$action_links = array_merge( array(
				'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=woc_hour&page=settings' ), esc_html__( 'Settings', 'woc-open-close' ) ),
			), $links );

			if ( ! woc_pro_available() ) {
				$action_links['go-pro'] = sprintf( '<a target="_blank" class="wooopenclose-plugin-meta-buy" href="%s">%s</a>', esc_url( WOOOPENCLOSE_PLUGIN_LINK ), esc_html__( 'Go Pro', 'woc-open-close' ) );
			}

			return $action_links;
		}


		/**
		 * Add custom links to plugin meta
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		function add_plugin_meta( $links, $file ) {

			if ( WOOOPENCLOSE_PLUGIN_FILE === $file ) {

				$row_meta = array(
					'documentation' => sprintf( '<a class="wooopenclose-doc" target="_blank" href="%s">%s</a>', esc_url( WOOOPENCLOSE_DOCS_URL ), esc_html__( 'Documentation', 'woc-open-close' ) ),
					'support'       => sprintf( '<a class="wooopenclose-support" target="_blank" href="%s">%s</a>', esc_url( WOOOPENCLOSE_TICKET_URL ), esc_html__( 'Support Ticket', 'woc-open-close' ) ),
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}


		/**
		 * Add nodes to WP Admin Bar
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		function handle_admin_bar_menu( \WP_Admin_Bar $wp_admin_bar ) {

			if ( get_post_type() == 'woc_hour' ) {
				$wp_admin_bar->remove_menu( 'view' );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$main_node_id = wooopenclose()->is_open() ? 'wooopenclose-shop-open' : 'wooopenclose-shop-close';

			$wp_admin_bar->add_node(
				array(
					'id'     => $main_node_id,
					'title'  => wooopenclose()->is_open() ? esc_html__( 'Shop Open', 'woc-open-close' ) : esc_html__( 'Shop Close', 'woc-open-close' ),
					'parent' => false,
				)
			);

			$wp_admin_bar->add_node(
				array(
					'id'     => 'all-schedules',
					'title'  => esc_html__( 'Edit Active Schedule', 'woc-open-close' ),
					'parent' => $main_node_id,
					'href'   => get_edit_post_link( wooopenclose()->get_active_schedule_id() ),
				)
			);

			$wp_admin_bar->add_node(
				array(
					'id'     => 'wooopenclose-settings',
					'title'  => __( 'Settings - Options', 'woc-open-close' ),
					'parent' => $main_node_id,
					'href'   => esc_url( admin_url( 'edit.php?post_type=woc_hour&page=settings' ) ),
				)
			);
		}


		/**
		 * Show admin step-by-step guide as Notice
		 */
		function manage_admin_notices() {

			// Check any Schedule available or not
			if ( count( get_posts( 'post_type=woc_hour' ) ) == 0 ) {

				wooopenclose()->print_notice(
					sprintf( '<p>%s. <a href="%s" class="button-primary">%s</a></p>',
						esc_html__( 'No schedules found for this store', 'woc-open-close' ),
						admin_url( 'post-new.php?post_type=woc_hour' ),
						esc_html__( 'Create Schedule', 'woc-open-close' )
					)
				);

				return;
			}

			// Check Active Schedule
			if ( empty( wooopenclose()->get_active_schedule_id() ) ) {

				wooopenclose()->print_notice(
					sprintf( '<p>%s. <a href="%s" class="button-primary">%s</a></p>',
						esc_html__( 'No active schedule found!', 'woc-open-close' ),
						admin_url( 'edit.php?post_type=woc_hour&page=settings#tab=options/general-settings' ),
						esc_html__( 'Select schedule', 'woc-open-close' )
					), 'warning'
				);

				return;
			}

			// Check is_open()
			if ( Utils::get_option( 'show_admin_status', false ) ) {

				$buy_notice    = ! woc_pro_available() ? sprintf( ' <a target="_blank" class="button-primary" href="https://pluginbazar.com/plugin/woocommerce-open-close/">%s</a>', __( 'Get Pro to Restrict Order while shop Closed', 'woc-open-close' ) ) : '';
				$status_notice = wooopenclose()->is_open() ? __( 'Shop is now accepting order from customers', 'woc-open-close' ) : __( 'Shop is currently closed from taking orders', 'woc-open-close' );
				$status_notice = sprintf( '<p>%s. %s <a href="%s" class="wooopenclose-notice-link">%s</a></p>', $status_notice, $buy_notice,
					esc_url( admin_url( 'edit.php?post_type=woc_hour&page=settings#tab=options/general-settings' ) ),
					esc_html__( 'Disable this Notice', 'woc-open-close' )
				);

				wooopenclose()->print_notice( $status_notice, 'warning', false );
			}
		}


		/**
		 * Display Footer Content of Popup and Statusbar
		 */
		function display_popup_statusbar() {

			global $wp_query;

			woc_get_template( 'close-popup.php' );

			if ( wooopenclose()->is_open() ) {
				return;
			}

			$wp_query->set( 'in_status_bar', true );
			woc_get_template( 'shop-status-bar.php' );
			$wp_query->set( 'in_status_bar', true );
		}


		/**
		 * render shortcode schedule
		 *
		 * @param array $atts
		 *
		 * @return false|string
		 */
		function render_schedule( $atts = array() ) {

			ob_start();
			woc_get_template( 'business-schedules.php', (array) $atts );

			return ob_get_clean();
		}


		/**
		 * Render countdown timer
		 *
		 * @param array $atts
		 *
		 * @return false|string
		 */
		function render_countdown_timer( $atts = array() ) {

			extract( is_array( $atts ) ? $atts : array() );

			ob_start();
			woc_get_template( 'countdown-timer.php', $atts );

			return ob_get_clean();
		}


		/**
		 * Register Post Types and Settings
		 */
		function register_everything() {

			global $wooopenclose_wpdk;

			/**
			 * Register Post Types
			 */
			$wooopenclose_wpdk->utils()->register_post_type( 'woc_hour', array(
				'singular'  => esc_html__( 'Schedule', 'woc-open-close' ),
				'plural'    => esc_html__( 'Schedules', 'woc-open-close' ),
				'labels'    => array(
					'menu_name' => esc_html__( 'Schedules', 'woc-open-close' ),
				),
				'menu_icon' => 'dashicons-clock',
				'supports'  => array( '' ),
			) );

			/**
			 * Register shortcodes
			 */
			$wooopenclose_wpdk->utils()->register_shortcode( 'woc_open_close', array( $this, 'render_schedule' ) );
			$wooopenclose_wpdk->utils()->register_shortcode( 'schedule', array( $this, 'render_schedule' ) );
			$wooopenclose_wpdk->utils()->register_shortcode( 'woc_countdown_timer', array( $this, 'render_countdown_timer' ) );


			$this->display_countdown_timer_dynamically();

			/**
			 * Controlling action when store status changed
			 */
			$transient_status = get_transient( 'wooopenclose_status' );
			$current_status   = wooopenclose()->is_open() ? 'open' : 'close';

			if ( empty( $transient_status ) ) {
				set_transient( 'wooopenclose_status', $current_status, 86400 );
			}

			/**
			 * Cache support
			 */
			if ( ( $transient_status === 'open' && $current_status === 'close' ) || ( $transient_status === 'close' && $current_status === 'open' ) ) {

				// Clear LiteSpeed cache
				if ( class_exists( '\LiteSpeed\Purge' ) ) {
					\LiteSpeed\Purge::purge_all();
				}

				// Clear Fastest Cache
				if ( function_exists( 'wpfc_clear_all_cache' ) ) {
					wpfc_clear_all_cache();
				}

				set_transient( 'wooopenclose_status', $current_status, 86400 );
			}
		}


		/**
		 * Return Buffered Content
		 *
		 * @param $buffer
		 *
		 * @return mixed
		 */
		function ob_callback( $buffer ) {
			return $buffer;
		}


		/**
		 * Start of Output Buffer
		 */
		function ob_start() {
			ob_start( array( $this, 'ob_callback' ) );
		}


		/**
		 * End of Output Buffer
		 */
		function ob_end() {
			if ( ob_get_length() ) {
				ob_end_flush();
			}
		}
	}
}

new WOOOPENCLOSE_Hooks();