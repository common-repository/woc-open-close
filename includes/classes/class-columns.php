<?php
/**
 * Schedules Column
 *
 * @author Pluginbazar
 */

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WOOOPENCLOSE_Columns' ) ) {
	class WOOOPENCLOSE_Columns {
		/**
		 * WOOOPENCLOSE_Columns constructor.
		 */
		function __construct() {

			add_filter( 'manage_woc_hour_posts_columns', array( $this, 'add_columns' ), 16, 1 );
			add_action( 'manage_woc_hour_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
			add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
			add_filter( 'months_dropdown_results', array( $this, 'remove_date_filter' ), 10, 2 );
		}

		/**
		 * Remove date filter from listing page
		 *
		 * @param $months
		 * @param $post_type
		 *
		 * @return array|mixed
		 */
		function remove_date_filter( $months, $post_type ) {
			if ( $post_type === 'woc_hour' ) {
				return array();
			}

			return $months;
		}


		/**
		 * Remove row actions for Schedules post type
		 *
		 * @param $actions
		 *
		 * @return mixed
		 */
		function remove_row_actions( $actions ) {

			global $post;

			if ( $post->post_type === 'woc_hour' ) {
				unset( $actions['inline hide-if-no-js'] );
				unset( $actions['view'] );
			}

			return $actions;
		}


		/**
		 * Add columns content
		 *
		 * @param $column
		 * @param $post_id
		 */
		function columns_content( $column, $post_id ) {
			if ( $column === 'shortcode' ) {
				printf( '<span class="wooopenclose-shortcode hint--top" aria-label="%s">[schedule id="%s"]</span>',
					esc_html__( 'Click to Copy', 'woc-open-close' ), $post_id
				);
			}

			if ( $column === 'is-default' && $post_id == wooopenclose()->get_active_schedule_id() ) {
				printf( '<div class="wooopenclose-schedule-default">%s</div>', esc_html__( 'Default Schedule', 'woc-open-close' ) );
			}

			if ( $column === 'wooopenclose-date' ) {
				printf( esc_html__( 'Created %s ago', 'woc-open-close' ), human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) );
			}
		}


		/**
		 * Add columns on Schedules listing
		 *
		 * @return string[]
		 */
		function add_columns() {
			return array(
				'title'             => '',
				'shortcode'         => '',
				'is-default'        => '',
				'wooopenclose-date' => '',
			);
		}
	}
}

new WOOOPENCLOSE_Columns();