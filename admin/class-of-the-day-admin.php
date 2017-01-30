<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  http://example.com
 * @since 1.0.0
 *
 * @package    Of_The_Day
 * @subpackage Of_The_Day/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Of_The_Day
 * @subpackage Of_The_Day/admin
 * @author     Your Name <email@example.com>
 */
class Of_The_Day_Admin {



	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $of_the_day    The ID of this plugin.
	 */
	private $of_the_day;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $of_the_day The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $of_the_day, $version ) {

		$this->of_the_day = $of_the_day;
		$this->version = $version;

	}

	public function get_post( $atts = array() ) {
		/*
         Possible attributes
          - type
          - category
          - tag
          - any registered taxonomy

         TODO: Validate that settings are actively registered on the site
        */
		$build_args = array( 'posts_per_page' => 1, 'orderby' => 'rand', 'tax_query' => array() );

		// Check for post type
		if ( isset( $atts['type'] ) && ! empty( $atts['type'] ) ) {
			$build_args['post_type'] = $atts['type'];
			unset( $atts['type'] );
		} else {
			$build_args['post_type'] = 'post';
		}
		// Check for category
		if ( isset( $atts['category'] ) && ! empty( $atts['category'] ) ) {
			$build_args['tax_query'][] = array(
			 'taxonomy' => 'category',
			 'field'       => 'name',
			 'terms'      => $atts['category'],
			);
			unset( $atts['category'] );
		}
		// Check for tags
		if ( isset( $atts['tag'] ) && ! empty( $atts['tag'] ) ) {
			$build_args['tax_query'][] = array(
			 'taxonomy' => 'tag',
			 'field'       => 'name',
			 'terms'      => $atts['tag'],
			);
			unset( $atts['tag'] );
		}

		// If the array still has something in it, lets see if it is a custom taxonomy
		if ( ! empty( $atts ) ) {
			$registered_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ) );
			foreach ( $atts as $taxonomy_name => $taxonomy_value ) {
				if ( in_array( $taxonomy_name, $registered_taxonomies ) ) {
					$build_args['tax_query'][] = array(
					 'taxonomy' => $taxonomy_name,
					 'field'       => 'name',
					 'terms'      => $taxonomy_value,
					   );
				}
			}
		}

		$transient_name = md5( serialize( $build_args ) );
		$exists = get_transient( 'oftheday_' . $transient_name );
		if ( ! empty( $exists ) ) {
			 $post = unserialize( $exists );
		} else {
			 $posts = get_posts( $build_args );
			if ( empty( $posts ) ) { return;
			}
			 $post = $posts[0];
			 $next_day_in_seconds = strtotime( 'tomorrow' ) - current_time( 'timestamp' );
			 set_transient( 'oftheday_' . $transient_name, serialize( $post ), $next_day_in_seconds );
		}

		return $this->format_post( $post );
	}

	public function format_post( $post ) {
		// TODO: Add filter for Dictionary Pro entries
		$format = '<h2>' . $post->post_title . '</h2>';
		$format .= apply_filters( 'the_content', $post->post_content );
		return $format;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Of_The_Day_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Of_The_Day_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->of_the_day, plugin_dir_url( __FILE__ ) . 'css/of-the-day-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Of_The_Day_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Of_The_Day_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->of_the_day, plugin_dir_url( __FILE__ ) . 'js/of-the-day-admin.js', array( 'jquery' ), $this->version, false );

	}

}
