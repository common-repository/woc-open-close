<?php
/**
 * Settings class
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

class WOOOPENCLOSE_Settings {

	/**
	 * WOOOPENCLOSE_Settings constructor.
	 */
	public function __construct() {

		global $wooopenclose_wpdk;

		// Generate settings page
		$settings_args = array(
			'framework_title'     => esc_html__( 'WooCommerce Open Close Settings', 'woc-open-close' ),
			'menu_title'          => esc_html__( 'Settings', 'woc-open-close' ),
			'menu_slug'           => 'settings',
			'menu_type'           => 'submenu',
			'menu_parent'         => 'edit.php?post_type=woc_hour',
			'database'            => 'option',
			'menu_capability'     => 'manage_woocommerce',
			'theme'               => 'light',
			'show_bar_menu'       => false,
			'show_search'         => false,
			'product_url'         => WOOOPENCLOSE_PLUGIN_LINK,
			'product_version'     => $wooopenclose_wpdk->plugin_version,
			'product_version_pro' => '',
			'quick_links'         => array(
				'supports' => array(
					'label' => esc_html__( 'Supports', 'woc-open-close' ),
					'url'   => WOOOPENCLOSE_TICKET_URL,
				),
				'docs'     => array(
					'label' => esc_html__( 'Documentations', 'woc-open-close' ),
					'url'   => WOOOPENCLOSE_DOCS_URL,
				),
			),
			'pro_url'             => WOOOPENCLOSE_PLUGIN_LINK,
		);

		WPDK_Settings::createSettingsPage( $wooopenclose_wpdk->plugin_unique_id, $settings_args, $this->get_settings_pages() );
	}


	/**
	 * Return settings pages
	 *
	 * @return mixed|void
	 */
	function get_settings_pages() {

		$field_sections['options'] = array(
			'title'    => esc_html__( 'Options', 'woc-open-close' ),
			'sections' => array(
				array(
					'title'  => esc_html__( 'General Settings', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'          => 'woc_active_set',
							'title'       => esc_html__( 'Active Schedule', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'Default schedule for the store.', 'woc-open-close' ),
							'desc'        => esc_html__( 'The system will follow this schedule for your WooCommerce Shop', 'woc-open-close' ),
							'placeholder' => esc_html__( 'Select Schedule', 'woc-open-close' ),
							'type'        => 'select',
							'options'     => 'posts',
							'chosen'      => true,
							'settings'    => array(
								'width' => '30%',
							),
							'query_args'  => array(
								'post_type' => 'woc_hour',
							),
						),
						array(
							'id'       => 'woc_start_of_week',
							'title'    => esc_html__( 'Week Starts On', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Set from which day your week starts on.', 'woc-open-close' ),
							'type'     => 'select',
							'options'  => array(
								3 => esc_html( 'Monday' ),
								4 => esc_html( 'Tuesday' ),
								5 => esc_html( 'Wednesday' ),
								6 => esc_html( 'Thursday' ),
								7 => esc_html( 'Friday' ),
								1 => esc_html( 'Saturday' ),
								2 => esc_html( 'Sunday' ),
							),
						),
						array(
							'id'       => 'show_admin_status',
							'title'    => esc_html__( 'Dashboard Notice', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Inside WP-Admin only.', 'woc-open-close' ),
							'type'     => 'switcher',
							'default'  => false,
						),
						array(
							'id'       => 'disable_checkout_button',
							'title'    => esc_html__( 'Disable Checkout Button', 'woc-open-close' ),
							'subtitle' => esc_html__( 'When store is closed.', 'woc-open-close' ),
							'label'    => esc_html__( 'The checkout button will not work.', 'woc-open-close' ),
							'type'     => 'switcher',
							'default'  => false,
						),
					),
				),
				array(
					'title'  => esc_html__( 'Cart Settings', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'           => 'woc_empty_cart_on_close',
							'title'        => esc_html__( 'Empty cart when close', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Cart will be empty as soon as the store become closed.', 'woc-open-close' ),
							'type'         => 'switcher',
							'default'      => false,
							'availability' => ! woc_pro_available() ? 'pro' : '',
						),
						array(
							'id'           => 'woc_allow_add_cart_on_close',
							'title'        => esc_html__( 'Allow add to cart', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Allow customers to add products in cart even the store is closed.', 'woc-open-close' ),
							'label'        => esc_html__( 'This settings will override the "Empty cart when close" option.', 'woc-open-close' ),
							'type'         => 'switcher',
							'default'      => false,
							'availability' => ! woc_pro_available() ? 'pro' : '',
						),
					),
				),
				array(
					'title'  => esc_html__( 'Countdown Timer', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'       => 'woc_timer_display_on',
							'title'    => esc_html__( 'Display countdown timer', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Select the places where you want to display the countdown timer on your shop.', 'woc-open-close' ),
							'desc'     => esc_html__( 'When your shop is closed then it will show how much time left for your shop to open, and vice verse.', 'woc-open-close' ),
							'type'     => 'select',
							'chosen'   => true,
							'multiple' => true,
							'settings' => array(
								'width' => '50%',
							),
							'options'  => array(
								'before_cart_table'    => esc_html__( 'Before cart table on Cart page', 'woc-open-close' ),
								'after_cart_table'     => esc_html__( 'After cart table on Cart page', 'woc-open-close' ),
								'before_cart_total'    => esc_html__( 'Before cart total on Cart page', 'woc-open-close' ),
								'after_cart_total'     => esc_html__( 'After cart total on Cart page', 'woc-open-close' ),
								'before_checkout_form' => esc_html__( 'Before checkout form on Checkout Page', 'woc-open-close' ),
								'after_checkout_form'  => esc_html__( 'After checkout form on Checkout Page', 'woc-open-close' ),
								'before_order_review'  => esc_html__( 'Before order review on Checkout Page', 'woc-open-close' ),
								'after_order_review'   => esc_html__( 'After order review on Checkout Page', 'woc-open-close' ),
								'before_cart_single'   => esc_html__( 'Before cart button on Single Product Page', 'woc-open-close' ),
								'top_on_myaccount'     => esc_html__( 'Top on My-Account Page', 'woc-open-close' ),
							),
						),
						array(
							'id'       => 'woc_timer_style',
							'title'    => esc_html__( 'Countdown timer style', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Select the style for the countdown timer', 'woc-open-close' ),
							'type'     => 'select',
							'options'  => array(
								'1' => esc_html__( 'Style - 1', 'woc-open-close' ),
								'2' => esc_html__( 'Style - 2', 'woc-open-close' ),
								'3' => esc_html__( 'Style - 3', 'woc-open-close' ),
								'4' => esc_html__( 'Style - 4', 'woc-open-close' ),
								'5' => esc_html__( 'Style - 5', 'woc-open-close' ),
							),
						),
						array(
							'id'       => 'woc_timer_display_text',
							'title'    => esc_html__( 'Timer Text', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Display a timer text before the countdown timer.', 'woc-open-close' ),
							'type'     => 'switcher',
							'default'  => true,
						),
						array(
							'id'          => 'woc_timer_text_open',
							'title'       => esc_html__( 'Timer Text when Open', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'This text will visible before the countdown timer when store is open.', 'woc-open-close' ),
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'This store will be closed within', 'woc-open-close' ),
							'attributes'  => array(
								'rows'  => '3',
								'cols'  => '50',
								'style' => 'width:auto;min-height: auto;',
							),
							'dependency'  => array( 'woc_timer_display_text', '==', true ),
						),
						array(
							'id'          => 'woc_timer_text_close',
							'title'       => esc_html__( 'Timer Text when Closed', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'This text will visible before the countdown timer when store is closed.', 'woc-open-close' ),
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'This store will be open within', 'woc-open-close' ),
							'attributes'  => array(
								'rows'  => '3',
								'cols'  => '50',
								'style' => 'width:auto;min-height: auto;',
							),
							'dependency'  => array( 'woc_timer_display_text', '==', true ),
						),
						array(
							'id'          => 'woc_timer_text_hours',
							'title'       => esc_html__( 'Timer text - Hour', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'Change default text of "Hour" to your own.', 'woc-open-close' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Hours', 'woc-open-close' ),
							'attributes'  => array(
								'style' => 'width: 220px;',
							),
						),
						array(
							'id'          => 'woc_timer_text_minutes',
							'title'       => esc_html__( 'Timer text - Minutes', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'Change default text of "Minutes" to your own.', 'woc-open-close' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Minutes', 'woc-open-close' ),
							'attributes'  => array(
								'style' => 'width: 220px;',
							),
						),
						array(
							'id'          => 'woc_timer_text_seconds',
							'title'       => esc_html__( 'Timer text - Seconds', 'woc-open-close' ),
							'subtitle'    => esc_html__( 'Change default text of "Seconds" to your own.', 'woc-open-close' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Seconds', 'woc-open-close' ),
							'attributes'  => array(
								'style' => 'width: 220px;',
							),
						),
					),
				),
			),
		);

		$field_sections['fore_rules'] = array(
			'title'    => esc_html__( 'Force Rules', 'woc-open-close' ),
			'sections' => array(
				array(
					'title'  => esc_html__( 'Instant Controlling', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'           => 'woc_instant_controls',
							'title'        => esc_html__( 'Enable instant controlling', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Override pre defined setting and follow this settings.', 'woc-open-close' ),
							'type'         => 'switcher',
							'default'      => false,
							'desc'         => esc_html__( 'If you are using Open Close with Dokan, please leave this field unchecked, it can interrupt your multi-vendor experience.', 'woc-open-close' ),
							'availability' => woc_pro_available() ? '' : 'pro',
						),
						array(
							'id'           => 'woc_instant_force',
							'title'        => esc_html__( 'Store Status', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Control store status overriding all other rules.', 'woc-open-close' ),
							'type'         => 'switcher',
							'text_on'      => esc_html__( 'Store Opened', 'woc-open-close' ),
							'text_off'     => esc_html__( 'Store Closed', 'woc-open-close' ),
							'text_width'   => 150,
							'default'      => true,
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_instant_controls', '==', true ) : '',
						),
						array(
							'id'           => 'woc_instant_force_msg',
							'title'        => esc_html__( 'Custom Message', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'When store is forcefully closed, set a different message for the customers.', 'woc-open-close' ),
							'type'         => 'textarea',
							'placeholder'  => esc_html__( 'We are completely off till next update', 'woc-open-close' ),
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_instant_controls', '==', true ) : '',
						),
					),
				),
				array(
					'title'  => esc_html__( 'When Opened', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'           => 'woc_disallowed_products',
							'title'        => esc_html__( 'Disallow Products', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Customers will not able to purchase these even store is opened.', 'woc-open-close' ),
							'type'         => 'select',
							'chosen'       => true,
							'multiple'     => true,
							'settings'     => array(
								'width' => '50%',
							),
							'options'      => 'posts',
							'query_args'   => array(
								'post_type' => 'product',
							),
							'availability' => woc_pro_available() ? '' : 'pro',
						),
					),
				),
				array(
					'title'  => esc_html__( 'When Closed', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'           => 'woc_allowed_products',
							'title'        => esc_html__( 'Allow Products', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Customers will able to purchase these even store is closed.', 'woc-open-close' ),
							'type'         => 'select',
							'chosen'       => true,
							'multiple'     => true,
							'settings'     => array(
								'width' => '50%',
							),
							'disabled'     => ! woc_pro_available(),
							'options'      => 'posts',
							'query_args'   => array(
								'post_type'      => 'product',
								'posts_per_page' => - 1,
							),
							'availability' => woc_pro_available() ? '' : 'pro',
						),
					),
				),
			),

		);

		$field_sections['preorder'] = array(
			'title'    => esc_html__( 'Preorder', 'woc-open-close' ),
			'sections' => array(
				array(
					'title'  => esc_html__( 'Preorder Settings', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'           => 'woc_enable_preorder',
							'title'        => esc_html__( 'Enable Preorder Feature', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Enabling this will open some extra preorder settings', 'woc-open-close' ),
							'type'         => 'switcher',
							'default'      => false,
							'availability' => woc_pro_available() ? '' : 'pro',
						),
						array(
							'id'           => 'woc_preorder_for',
							'title'        => esc_html__( 'Preorder for', 'woc-open-close' ),
							'type'         => 'button_set',
							'options'      => array(
								'all_products'      => array(
									'label' => esc_html__( 'All Products', 'woc-open-close' ),
								),
								'specific_products' => array(
									'label' => esc_html__( 'Specific Products', 'woc-open-close' ),
								),
							),
							'default'      => 'all_products',
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_enable_preorder', '==', true ) : '',
						),
						array(
							'id'           => 'woc_preorder_products',
							'title'        => esc_html__( 'Preorder products', 'woc-open-close' ),
							'type'         => 'select',
							'chosen'       => true,
							'multiple'     => true,
							'settings'     => array(
								'width' => '50%',
							),
							'options'      => 'posts',
							'query_args'   => array(
								'post_type' => 'product',
							),
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_preorder_for', '==', 'specific_products' ) : '',
						),
						array(
							'id'           => 'woc_preorder_message',
							'title'        => esc_html__( 'Preorder message', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Set a custom message for the customers about preorder.', 'woc-open-close' ),
							'desc'         => wp_kses_data( 'You can use replacer like <code>%item_title%</code> and <code>%next_opening_time%</code>', 'woc-open-close' ),
							'type'         => 'textarea',
							'placeholder'  => esc_html__( 'The item "%item_title%" will be delivered in next available time %next_opening_time%', 'woc-open-close' ),
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_enable_preorder', '==', true ) : '',
						),
						array(
							'id'           => 'woc_preorder_button_text',
							'title'        => esc_html__( 'Preorder Button Text', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'This button will be visible in the single product page.', 'woc-open-close' ),
							'type'         => 'text',
							'placeholder'  => esc_html__( 'Preorder', 'woc-open-close' ),
							'availability' => woc_pro_available() ? '' : 'pro',
							'dependency'   => woc_pro_available() ? array( 'woc_enable_preorder', '==', true ) : '',
						),
					),
				),
			),

		);

		$field_sections['styling'] = array(
			'title'    => esc_html__( 'Styling', 'woc-open-close' ),
			'sections' => array(
				array(
					'title'  => esc_html__( 'Business Hour Design', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'    => 'woc_bh_image_open',
							'title' => esc_html__( 'Status Images when open', 'woc-open-close' ),
							'desc'  => esc_html__( 'For - Status Open, This image will display at the top of the business schedules', 'woc-open-close' ),
							'type'  => 'media',
						),
						array(
							'id'    => 'woc_bh_image_close',
							'title' => esc_html__( 'Status Images when closed', 'woc-open-close' ),
							'desc'  => esc_html__( 'For - Status Closed, This image will display at the top of the business schedules', 'woc-open-close' ),
							'type'  => 'media',
						),
						array(
							'id'       => 'woc_bh_check_icon',
							'title'    => esc_html__( 'Display Check Icon', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Show a check icon before the day names.', 'woc-open-close' ),
							'type'     => 'switcher',
							'default'  => true,
						),
					),
				),
				array(
					'title'  => esc_html__( 'Popup Design', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'          => 'woc_pp_effect',
							'title'       => esc_html__( 'Popup Effect', 'woc-open-close' ),
							'desc'        => esc_html__( 'Change popup box effect while opening or closing', 'woc-open-close' ),
							'type'        => 'select',
							'placeholder' => esc_html__( 'Select your choice' ),
							'options'     => array(
								'flash'      => esc_html__( 'Flash', 'woc-open-close' ),
								'shake'      => esc_html__( 'Shake', 'woc-open-close' ),
								'pulseDown'  => esc_html__( 'Pulse', 'woc-open-close' ),
								'tada'       => esc_html__( 'Tada', 'woc-open-close' ),
								'tadaSmall'  => esc_html__( 'Tada Small', 'woc-open-close' ),
								'popIn'      => esc_html__( 'Pop In', 'woc-open-close' ),
								'popOut'     => esc_html__( 'Pop Out', 'woc-open-close' ),
								'fadeIn'     => esc_html__( 'Fade In', 'woc-open-close' ),
								'slideUp'    => esc_html__( 'Slide Up', 'woc-open-close' ),
								'slideRight' => esc_html__( 'Slide Right', 'woc-open-close' ),
								'slideLeft'  => esc_html__( 'Slide Left', 'woc-open-close' ),
								'slideDown'  => esc_html__( 'Slide Down', 'woc-open-close' ),
							),
						),
					),
				),
				array(
					'title'  => esc_html__( 'Store Status Bar', 'woc-open-close' ),
					'fields' => array(
						array(
							'id'      => 'woc_bar_where',
							'title'   => esc_html__( 'Bar Position', 'woc-open-close' ),
							'desc'    => esc_html__( 'Where you want to display the store status bar? Default: Footer', 'woc-open-close' ),
							'type'    => 'button_set',
							'options' => array(
								'wooopenclose-bar-footer' => array( 'label' => esc_html__( 'Footer', 'woc-open-close' ), ),
								'wooopenclose-bar-header' => array( 'label' => esc_html__( 'Header', 'woc-open-close' ), ),
								'wooopenclose-bar-none'   => array( 'label' => esc_html__( 'Disable notice bar', 'woc-open-close' ), ),
							),
							'default' => 'wooopenclose-bar-footer',
						),
						array(
							'id'      => 'woc_bar_hide_permanently',
							'title'   => esc_html__( 'Hide Status Bar', 'woc-open-close' ),
							'desc'    => esc_html__( 'Hide permanently the status bar using the cookie for a certain time.', 'woc-open-close' ),
							'type'    => 'switcher',
							'default' => false,
						),
						array(
							'id'      => 'woc_bar_btn_display',
							'title'   => esc_html__( 'Show Hide Button', 'woc-open-close' ),
							'desc'    => esc_html__( 'Do you want to display the Hide notice button? Default: Yes', 'woc-open-close' ),
							'type'    => 'switcher',
							'default' => true,
						),
						array(
							'id'          => 'woc_bar_hide_text',
							'title'       => esc_html__( 'Hide Button Text', 'woc-open-close' ),
							'desc'        => esc_html__( 'Set custom text for \'Hide Message\' Button. Default: Hide Message', 'woc-open-close' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Hide Message', 'woc-open-close' ),
						),
					),
				),

			),

		);

		return apply_filters( 'woc_filters_settings_pages', $field_sections );
	}
}

new WOOOPENCLOSE_Settings();

