<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'woc_update_global_arguments' ) ) {
	/**
	 * Update global arguments variable
	 *
	 * @param array $args_to_add
	 */
	function woc_update_global_arguments( $args_to_add = array() ) {

		if ( empty( $args_to_add ) ) {
			return;
		}

		global $wooopenclose_args;

		$args_to_add       = is_array( $args_to_add ) ? $args_to_add : array();
		$wooopenclose_args = is_array( $wooopenclose_args ) ? $wooopenclose_args : array();
		$wooopenclose_args = array_merge( $wooopenclose_args, $args_to_add );
	}
}


if ( ! function_exists( 'woc_product_can_preorder' ) ) {
	/**
	 * Return if a product can preorder
	 *
	 * @param false $product_id
	 *
	 * @return bool
	 */
	function woc_product_can_preorder( $product_id = false ) {

		$product_id        = ! $product_id ? get_the_ID() : $product_id;
		$preorder_for      = (array) Utils::get_option( 'woc_preorder_for', array( 'specific_products' ) );
		$preorder_products = Utils::get_option( 'woc_preorder_products', array() );

		if ( is_array( $preorder_for ) && in_array( 'all_products', $preorder_for ) ) {
			return true;
		}

		if ( is_array( $preorder_for ) && in_array( 'specific_products', $preorder_for ) && is_array( $preorder_products ) && in_array( $product_id, $preorder_products ) ) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'woc_product_can_order' ) ) {
	/**
	 * Check if a product is ready to order or not
	 *
	 * @param bool $product_id
	 *
	 * @return bool|mixed|void
	 */
	function woc_product_can_order( $product_id = false ) {

		$product_id          = ! $product_id ? get_the_ID() : $product_id;
		$allowed_products    = Utils::get_option( 'woc_allowed_products', array() );
		$disallowed_products = Utils::get_option( 'woc_disallowed_products', array() );
		$enable_preorder     = Utils::get_option( 'woc_enable_preorder', false );
		$current_schedule    = wooopenclose()->current_schedule_details();

		if ( 'specific_products' == Utils::get_args_option( 'allowed_products_type', $current_schedule ) ) {

			if ( ! empty( $by_products = Utils::get_args_option( 'by_products', $current_schedule, array() ) ) ) {
				$allowed_products = array_merge( $allowed_products, $by_products );
			}

			if ( ! empty( $by_categories = Utils::get_args_option( 'by_categories', $current_schedule, array() ) ) ) {

				$by_categories          = array_map( function ( $term_id ) {
					$term = get_term_by( 'term_id', $term_id, 'product_cat' );

					return $term->slug;
				}, $by_categories );
				$products_by_categories = wc_get_products( [
					'category' => $by_categories,
					'limit'    => - 1,
				] );
				$products_by_categories = array_map( function ( WC_Product $product ) {
					return $product->get_id();
				}, $products_by_categories );
				$allowed_products       = array_merge( $allowed_products, $products_by_categories );
			}

			if ( ! empty( $by_tags = Utils::get_args_option( 'by_tags', $current_schedule, array() ) ) ) {

				$by_tags          = array_map( function ( $term_id ) {
					$term = get_term_by( 'term_id', $term_id, 'product_tag' );

					return $term->slug;
				}, $by_tags );
				$products_by_tags = wc_get_products( [
					'category' => $by_tags,
					'limit'    => - 1,
				] );
				$products_by_tags = array_map( function ( WC_Product $product ) {
					return $product->get_id();
				}, $products_by_tags );
				$allowed_products = array_merge( $allowed_products, $products_by_tags );
			}

			if ( in_array( $product_id, $allowed_products ) ) {
				return true;
			} else {
				return false;
			}
		}

		if ( in_array( $product_id, $allowed_products ) ) {
			return true;
		}

		if ( in_array( $product_id, $disallowed_products ) ) {
			return false;
		}

		if ( $enable_preorder ) {
			return woc_product_can_preorder();
		}

		return wooopenclose()->is_open();
	}
}


if ( ! function_exists( 'woc_get_template_part' ) ) {
	/**
	 * Get Template Part
	 *
	 * @param $slug
	 * @param string $name
	 * @param array $args
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 */
	function woc_get_template_part( $slug, $name = '', $args = array(), $main_template = false ) {

		$template   = '';
		$plugin_dir = WOOOPENCLOSE_PLUGIN_DIR;

		/**
		 * Locate template
		 */
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				"woc/{$slug}-{$name}.php",
			) );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		// Search in WOC Pro
		if ( strpos( $backtrace_file, 'woc-open-close-pro' ) !== false && defined( 'WOOOPENCLOSE_PRO_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? WOOOPENCLOSE_PLUGIN_DIR : WOOOPENCLOSE_PRO_PLUGIN_DIR;
		}


		/**
		 * Search for Template in Plugin
		 *
		 * @in Plugin
		 */
		if ( ! $template && $name && file_exists( untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php" ) ) {
			$template = untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php";
		}


		/**
		 * Search for Template in Theme
		 *
		 * @in Theme
		 */
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", "woc/{$slug}.php" ) );
		}


		/**
		 * Allow 3rd party plugins to filter template file from their plugin.
		 *
		 * @filter woc_filters_get_template_part
		 */
		$template = apply_filters( 'woc_filters_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
}


if ( ! function_exists( 'woc_get_template' ) ) {
	/**
	 * Get Template
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return WP_Error
	 */
	function woc_get_template( $template_name, $args = array(), $template_path = '', $default_path = '', $main_template = false ) {

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		$located = woc_locate_template( $template_name, $template_path, $default_path, $backtrace_file, $main_template );

		if ( ! file_exists( $located ) ) {
			return new WP_Error( 'invalid_data', __( '%s does not exist.', 'woc-open-close' ), '<code>' . $located . '</code>' );
		}

		$located = apply_filters( 'woc_filters_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'woc_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'woc_after_template_part', $template_name, $template_path, $located, $args );
	}
}


if ( ! function_exists( 'woc_locate_template' ) ) {
	/**
	 *  Locate template
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 * @param string $backtrace_file
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return mixed|void
	 */
	function woc_locate_template( $template_name, $template_path = '', $default_path = '', $backtrace_file = '', $main_template = false ) {

		$plugin_dir = WOOOPENCLOSE_PLUGIN_DIR;

		/**
		 * Template path in Theme
		 */
		if ( ! $template_path ) {
			$template_path = 'woc/';
		}

		// Check for WOC Pro
		if ( ! empty( $backtrace_file ) && strpos( $backtrace_file, 'woc-open-close-pro' ) !== false && defined( 'WOOOPENCLOSE_PRO_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? WOOOPENCLOSE_PLUGIN_DIR : WOOOPENCLOSE_PRO_PLUGIN_DIR;
		}


		/**
		 * Template default path from Plugin
		 */
		if ( ! $default_path ) {
			$default_path = untrailingslashit( $plugin_dir ) . '/templates/';
		}

		/**
		 * Look within passed path within the theme - this is priority.
		 */
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		/**
		 * Get default template
		 */
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		/**
		 * Return what we found with allowing 3rd party to override
		 *
		 * @filter woc_filters_locate_template
		 */
		return apply_filters( 'woc_filters_locate_template', $template, $template_name, $template_path );
	}
}


if ( ! function_exists( 'woc_get_status_bar_classes' ) ) {
	/**
	 * Return status bar classes
	 *
	 * @param $classes
	 *
	 * @return mixed|void
	 */
	function woc_get_status_bar_classes( $classes = '' ) {

		$classes       = is_string( $classes ) ? explode( ' ', $classes ) : array();
		$woc_bar_where = Utils::get_option( 'woc_bar_where', 'wooopenclose-bar-footer' );
		$woc_bar_where = empty( $woc_bar_where ) ? 'wooopenclose-bar-footer' : $woc_bar_where;

		$classes[] = $woc_bar_where;

		return apply_filters( 'woc_filters_status_bar_classes', implode( ' ', apply_filters( 'woc_filters_status_bar_classes_arr', $classes ) ) );
	}
}


if ( ! function_exists( 'wooopenclose_schedules_wrapper_classes' ) ) {
	/**
	 * Render schedules' wrapper classes
	 *
	 * @param $style_id
	 * @param $classes
	 *
	 */
	function wooopenclose_schedules_wrapper_classes( $style_id, $classes = '' ) {

		$classes = is_string( $classes ) ? explode( ' ', $classes ) : array();

		// Default class
		$classes[] = 'wooopenclose-schedules-wrap';

		// Style Class
		$classes[] = 'wooopenclose-schedules-style-' . $style_id;

		// Open/Close class
		$classes[] = wooopenclose()->is_open() ? 'wooopenclose-shop-schedules-open' : 'wooopenclose-shop-schedules-close';

		printf( 'class="%s"', apply_filters( 'woc_filters_schedules_wrapper_classes', implode( ' ', $classes ) ) );
	}
}


if ( ! function_exists( 'wooopenclose_schedule_classes' ) ) {
	/**
	 * Render schedule classes
	 *
	 * @param $day_id
	 * @param string $classes
	 */
	function wooopenclose_schedule_classes( $day_id, $classes = '' ) {

		$classes = is_string( $classes ) ? explode( ' ', $classes ) : array();

		// Default class
		$classes[] = 'wooopenclose-schedule';

		// Status class
		$classes[] = wooopenclose()->get_current_day_id() == $day_id ? 'current opened' : '';

		// Open/Close class
		$classes[] = wooopenclose()->is_open() ? 'shop-open' : 'shop-close';

		printf( 'class="%s"', apply_filters( 'woc_filters_schedule_classes', implode( ' ', $classes ) ) );
	}
}


if ( ! function_exists( 'wooopenclose' ) ) {
	function wooopenclose() {

		global $wooopenclose;

		if ( empty( $wooopenclose ) ) {
			$wooopenclose = new WOOOPENCLOSE_Functions();
		}

		return $wooopenclose;
	}
}


if ( ! function_exists( 'woc_pro_available' ) ) {
	function woc_pro_available() {
		return apply_filters( 'woc_filters_is_pro', class_exists( 'WOOOPENCLOSE_PRO_Main' ) );
	}
}


if ( ! function_exists( 'wooopenclose_day_schedules' ) ) {
	/**
	 * Render sub-schedules for each day
	 *
	 * @param array $schedules
	 */
	function wooopenclose_day_schedules( $schedules = array(), $args = array() ) {

		$defaults  = array(
			'wrapper_class' => 'wooopenclose-day-schedules',
			'item_class'    => 'wooopenclose-day-schedule',
			'show_icon'     => true,
		);
		$args      = wp_parse_args( $args, $defaults );
		$item_icon = Utils::get_args_option( 'show_icon', $args ) ? '<span class="dashicons dashicons-clock"></span>' : '';

		ob_start();

		if ( empty( $schedules ) ) {
			printf( '<div class="%s">%s</div>', Utils::get_args_option( 'item_class', $args ), esc_html__( 'Closed!', 'woc-open-close' ) );
		}

		if ( ! empty( $schedules ) && is_array( $schedules ) ) {
			foreach ( $schedules as $schedule_item ) {

				printf( '<div class="%s">%s%s&nbsp;-&nbsp;%s</div>',
					Utils::get_args_option( 'item_class', $args ),
					$item_icon,
					Utils::get_args_option( 'open', $schedule_item ),
					Utils::get_args_option( 'close', $schedule_item )
				);

			}
		}

		printf( '<div class="%s">%s</div>', Utils::get_args_option( 'wrapper_class', $args ), ob_get_clean() );
	}
}


if ( ! function_exists( 'wooopenclose_day_name' ) ) {
	/**
	 * Render day name
	 *
	 * @param $day_id
	 * @param array $args
	 */
	function wooopenclose_day_name( $day_id, $args = array() ) {

		$defaults = array(
			'wrapper_class'    => 'wooopenclose-day-name',
			'item_class'       => 'wooopenclose-day-schedule',
			'check_icon_class' => 'dashicons dashicons-yes',
			'show_arrow_icon'  => true,
			'arrow_icon_class' => 'wooopenclose-arrow-icon',
			'return_label'     => true,
		);
		$args     = wp_parse_args( $args, $defaults );

		ob_start();

		if ( Utils::get_option( 'woc_bh_check_icon', true ) ) {
			printf( '<span class="%s"></span>', Utils::get_args_option( 'check_icon_class', $args ) );
		}

		printf( '<span>%s</span>', wooopenclose()->get_day_name( $day_id, (bool) Utils::get_args_option( 'return_label', $args ) ) );

		if ( Utils::get_args_option( 'show_arrow_icon', $args ) ) {
			printf( '<span class="%s"></span>', Utils::get_args_option( 'arrow_icon_class', $args ) );
		}

		printf( '<div class="%s">%s</div>', Utils::get_args_option( 'wrapper_class', $args ), ob_get_clean() );
	}
}


if ( ! function_exists( 'wooopenclose_get_schedule_meta' ) ) {
	/**
	 * Return meta key
	 *
	 * @param $meta_key
	 * @param $default
	 * @param $schedule_id
	 *
	 * @return array|bool|mixed|string
	 */
	function wooopenclose_get_schedule_meta( $meta_key = '', $default = '', $schedule_id = '' ) {

		$schedule_id       = empty( $schedule_id ) ?? $schedule_id == 0 ? wooopenclose()->get_active_schedule_id() : $schedule_id;
		$wooopenclose_data = Utils::get_meta( 'wooopenclose_data', $schedule_id );

		return Utils::get_args_option( $meta_key, $wooopenclose_data, $default );
	}
}


if ( ! function_exists( 'array_diff_recursive' ) ) {
	/**
	 * array diff between two associative array.
	 *
	 * @param $array1
	 * @param $array2
	 *
	 * @return array
	 */
	function array_diff_recursive( $array1, $array2 ) {
		$diff = array();

		foreach ( $array1 as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( ! isset( $array2[ $key ] ) || ! is_array( $array2[ $key ] ) ) {
					$diff[ $key ] = $value;
				} else {
					$recursiveDiff = array_diff_recursive( $value, $array2[ $key ] );
					if ( ! empty( $recursiveDiff ) ) {
						$diff[ $key ] = $recursiveDiff;
					}
				}
			} else {
				if ( ! isset( $array2[ $key ] ) || $array2[ $key ] !== $value ) {
					$diff[ $key ] = $value;
				}
			}
		}

		return $diff;
	}
}
