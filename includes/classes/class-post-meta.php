<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

use \WPDK\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WOOOPENCLOSE_Post_meta' ) ) {
	/**
	 * Class WOOOPENCLOSE_Post_meta
	 */
	class WOOOPENCLOSE_Post_meta {

		/**
		 * Post types that this work in
		 *
		 * @var string[]
		 */
		public $post_types = array( 'woc_hour' );


		/**
		 * WOOOPENCLOSE_Post_meta constructor.
		 */
		function __construct() {

//			if ( isset( $_GET['view'] ) && 'new' == $_GET['view'] ) {
			$this->generate_meta_box();
//			} else {
//				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
//				add_action( 'save_post', array( $this, 'save_meta_data' ) );
//			}

			add_action( 'post_submitbox_misc_actions', array( $this, 'publish_box_content' ) );
		}


		function get_meta_field_sections() {

			$field_sections['settings'] = array(
				'title'  => esc_html__( 'Settings', 'woc-open-close' ),
				'fields' => array(
					array(
						'id'         => 'post_title',
						'title'      => esc_html__( 'Schedule Title', 'woc-open-close' ),
						'type'       => 'text',
						'attributes' => array(
							'required'     => true,
							'autocomplete' => 'off',
						),
					),
					array(
						'id'          => 'message',
						'title'       => esc_html__( 'Message', 'woc-open-close' ),
						'subtitle'    => esc_html__( 'Set offline message here.', 'woc-open-close' ),
						'desc'        => esc_html__( 'This message will appear when the store is closed in the frontend.', 'woc-open-close' ),
						'placeholder' => esc_html__( 'Offline ! We will start taking orders in %countdown%.', 'woc-open-close' ),
						'type'        => 'textarea',
						'attributes'  => array(
							'rows' => 5,
						),
					),
				),
			);

			foreach ( wooopenclose()->get_days() as $index => $day ) {

				$field_sections["section_$index"] = array(
					'title'  => Utils::get_args_option( 'name', $day ),
					'fields' => array(
						array(
							'id'       => "{$index}_default_label",
							'title'    => esc_html__( 'Label', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Custom label for this day.', 'woc-open-close' ),
							'label'    => esc_html__( 'Use default label for this day.', 'woc-open-close' ),
							'type'     => 'switcher',
							'text_on'  => 'yes',
							'text_off' => 'no',
							'default'  => true,
						),
						array(
							'id'          => "{$index}_label",
							'title'       => ' ',
							'type'        => 'text',
							'class'       => 'padding-top-none',
							'placeholder' => Utils::get_args_option( 'name', $day ),
							'dependency'  => array( "{$index}_default_label", '==', false, 'all' ),
						),
						array(
							'id'           => "{$index}_schedules",
							'title'        => esc_html__( 'Schedules', 'woc-open-close' ),
							'subtitle'     => esc_html__( 'Add schedules for this day.', 'woc-open-close' ),
							'button_title' => esc_html__( 'Add Schedule', 'woc-open-close' ),
							'type'         => 'repeater',
//							'max'          => $wooopenclose_wpdk->license()->is_valid() ? 1000 : 3,
							'max_notice'   => esc_html__( 'Upgrade to add unlimited schedules.', 'woc-open-close' ),
							'fields'       => array(
								array(
									'id'          => 'open',
									'title'       => esc_html__( 'Opens - Close Time', 'woc-open-close' ),
									'type'        => 'select',
									'class'       => 'wooopenclose-time-range open',
									'chosen'      => true,
									'placeholder' => esc_html__( 'Select a time', 'woc-open-close' ),
									'settings'    => array(
										'width'           => '200px',
										'search_contains' => true,
									),
									'options'     => wooopenclose()->get_time_options(),
								),
								array(
									'id'          => 'close',
									'title'       => ' ',
									'type'        => 'select',
									'class'       => 'wooopenclose-time-range close',
									'chosen'      => true,
									'placeholder' => esc_html__( 'Select a time', 'woc-open-close' ),
									'settings'    => array(
										'width'           => '200px',
										'search_contains' => true,
									),
									'options'     => wooopenclose()->get_time_options(),
								),
								array(
									'id'      => 'allowed_products_type',
									'title'   => esc_html__( 'Allowed Products', 'woc-open-close' ),
									'desc'    => esc_html__( 'You can limit this session only some products.', 'woc-open-close' ),
									'type'    => 'button_set',
									'options' => array(
										'all_products'      => array(
											'label' => esc_html__( 'All Products', 'woc-open-close' ),
										),
										'specific_products' => array(
											'label'        => esc_html__( 'Specific Products', 'woc-open-close' ),
											'availability' => ! woc_pro_available() ? 'pro' : '',
										),
									),
									'default' => 'all_products',
								),
								array(
									'id'          => 'by_products',
									'title'       => ' ',
									'desc'        => esc_html__( 'Selected products will be added in the allowed list.', 'woc-open-close' ),
									'placeholder' => esc_html__( 'Select Product', 'woc-open-close' ),
									'type'        => 'select',
									'chosen'      => true,
									'multiple'    => true,
									'ajax'        => true,
									'options'     => 'posts',
									'query_args'  => array(
										'post_type'      => 'product',
										'posts_per_page' => '-1',
									),
									'dependency'  => array( 'allowed_products_type', '==', 'specific_products' ),
								),
								array(
									'id'          => 'by_categories',
									'title'       => ' ',
									'desc'        => esc_html__( 'All products under these selected categories will be added in the allowed list', 'woc-open-close' ),
									'placeholder' => esc_html__( 'Select Categories', 'woc-open-close' ),
									'type'        => 'select',
									'chosen'      => true,
									'multiple'    => true,
									'options'     => 'categories',
									'query_args'  => array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
										'count'      => true,
									),
									'dependency'  => array( 'allowed_products_type', '==', 'specific_products' ),
								),
								array(
									'id'          => 'by_tags',
									'title'       => ' ',
									'desc'        => esc_html__( 'All products under these selected tags will be added in the allowed list', 'woc-open-close' ),
									'placeholder' => esc_html__( 'Select Tags', 'woc-open-close' ),
									'type'        => 'select',
									'chosen'      => true,
									'multiple'    => true,
									'options'     => 'tags',
									'query_args'  => array(
										'taxonomy'   => 'product_tag',
										'hide_empty' => false,
										'count'      => true,
									),
									'dependency'  => array( 'allowed_products_type', '==', 'specific_products' ),
								),
							),
							'default'      => array( '' ),
						),
					),
				);
			}


			return $field_sections;
		}

		/**
		 * Generate poll meta box
		 */
		public function generate_meta_box() {

			$prefix = 'wooopenclose_data';

			WPDK_Settings::createMetabox( $prefix,
				array(
					'title'     => __( 'Slider Options', 'woc-open-close' ),
					'post_type' => 'woc_hour',
					'data_type' => 'serialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				)
			);

			foreach ( $this->get_meta_field_sections() as $section ) {
				WPDK_Settings::createSection( $prefix, $section );
			}
		}


		/**
		 * Publish box data
		 */
		function publish_box_content() {

			global $post_type;

			if ( in_array( $post_type, $this->post_types ) ) {
				woc_get_template( 'admin/meta-box-publish.php' );
			}
		}


		/**
		 * Display meta data
		 *
		 * @param $post
		 */
		function render_meta_box( $post ) {

			wp_nonce_field( 'woc_nonce', 'woc_nonce_val' );

			woc_get_template( 'admin/meta-box-hour.php' );
		}


		/**
		 * Add Meta boxes
		 *
		 * @param $post_type
		 */
		function add_meta_boxes( $post_type ) {

			if ( in_array( $post_type, $this->post_types ) ) {
				add_meta_box( 'woc_metabox', esc_html__( 'Schedule data', 'woc-open-close' ), array( $this, 'render_meta_box' ), $post_type, 'normal', 'high' );
			}
		}


		/**
		 * Save post meta data
		 *
		 * @param $post_id
		 */
		function save_meta_data( $post_id ) {

			$posted_data = sanitize_text_field( serialize( $_POST ) );
			$posted_data = unserialize( $posted_data );
			$nonce       = sanitize_text_field( Utils::get_args_option( 'woc_nonce_val', $posted_data ) );

			if ( wp_verify_nonce( $nonce, 'woc_nonce' ) ) {
				$woc_hours_meta = Utils::get_args_option( 'woc_hours_meta', $posted_data, array() );
				update_post_meta( $post_id, 'woc_hours_meta', $woc_hours_meta );
			}
		}
	}
}

new WOOOPENCLOSE_Post_meta();
