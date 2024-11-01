<?php
/**
 * Class Schedule
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WOOOPENCLOSE_Schedule' ) ) {
	class WOOOPENCLOSE_Schedule {

		public $id = null;

		private $data = array();

		/**
		 * WOOOPENCLOSE_Schedule constructor.
		 */
		function __construct() {

			add_action( 'save_post', array( $this, 'store_schedules_data' ) );

			if ( empty( $this->id = Utils::get_option( 'woc_active_set' ) ) ) {
				return;
			}

			$this->init();
		}


		function get_schedules() {

			$schedules = array();

			foreach ( wooopenclose()->get_days() as $day_id => $day ) {
				$schedule = Utils::get_args_option( "{$day_id}_schedules", $this->data, array() );

				echo "<pre>";
				print_r( $schedule );
				echo "</pre>";

				$schedules[ $day_id ] = $schedule;
			}

			return apply_filters( 'WOOOPENCLOSE/Filters/get_schedules', $schedules );
		}


		/**
		 * Get anything from data array.
		 *
		 * @param $key
		 * @param $default
		 *
		 * @return array|bool|mixed|string
		 */
		public function get( $key, $default ) {
			return Utils::get_args_option( $key, $this->data, $default );
		}


		/**
		 * Initialize the class.
		 */
		private function init() {
			$this->data = Utils::get_meta( 'wooopenclose_data', $this->id, array() );

			$this->data['post_title'] = get_the_title( $this->id );
		}


		/**
		 * Get products and store those into the meta data
		 *
		 * @param $post_id
		 */
		public function store_schedules_data( $post_id ) {

			$wooopenclose_data = Utils::get_args_option( 'wooopenclose_data', wp_unslash( $_POST ) );

			foreach ( wooopenclose()->get_days() as $day_id => $day ) {
				foreach ( Utils::get_args_option( "{$day_id}_schedules", $wooopenclose_data, array() ) as $schedule_id => $schedule ) {
					$products   = array();
					$query_args = array(
						'post_type'      => 'product',
						'posts_per_page' => - 1,
					);

					if ( isset( $schedule['by_categories'] ) && ! empty( $by_categories = $schedule['by_categories'] ) ) {
						$query_args['tax_query'][] = array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $by_categories,
							'operator' => 'IN',
						);
					}

					if ( isset( $schedule['by_tags'] ) && ! empty( $by_tags = $schedule['by_tags'] ) ) {
						$query_args['tax_query'][] = array(
							'taxonomy' => 'product_tag',
							'field'    => 'term_id',
							'terms'    => $by_tags,
							'operator' => 'IN',
						);
					}

					if ( isset( $query_args['tax_query'] ) && ! empty( $query_args['tax_query'] ) ) {
						$query_args['tax_query'][] = array( 'relation' => 'OR', );
					}

					foreach ( get_posts( $query_args ) as $product ) {
						$products[] = $product->ID;
					}

					if ( isset( $schedule['by_products'] ) && ! empty( $by_products = $schedule['by_products'] ) ) {
						$products = array_merge( $products, $by_products );
					}

					$wooopenclose_data["{$day_id}_schedules"][ $schedule_id ]['open']     = Utils::get_args_option( 'open', $schedule );
					$wooopenclose_data["{$day_id}_schedules"][ $schedule_id ]['close']    = Utils::get_args_option( 'close', $schedule );
					$wooopenclose_data["{$day_id}_schedules"][ $schedule_id ]['products'] = array_filter( $products );
				}
			}

			update_post_meta( $post_id, 'wooopenclose_data', $wooopenclose_data );
		}
	}
}

wooopenclose()->schedule = new WOOOPENCLOSE_Schedule();