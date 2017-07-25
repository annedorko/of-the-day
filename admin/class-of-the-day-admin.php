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
		$this->options = get_option( 'of-the-day', array() );

	}

	function fb_app_id() {
		$string = $this->options['facebook_app_id'];
		if ( null === $string || empty( $string ) ) {
			return null;
		} else {
			return $string;
		}
	}
	function fb_page_id() {
		$string = $this->options['facebook_choose_page'];
		if ( null === $string || empty( $string ) ) {
			return null;
		} else {
			return $string;
		}
	}

	function menus() {
		add_submenu_page(
			'options-general.php',
			__( 'Of The Day Settings', 'of-the-day' ),
			__( 'Of The Day', 'of-the-day' ),
			'manage_options',
			'of-the-day',
		array( $this, 'display_general_plugin_settings' ) );
		$this->settings = $this->set_settings();
	}

	function display_general_plugin_settings() {
		$settings = $this->settings;
		include OTD_DIRECTORY . '/admin/partials/admin-settings.php';
	}

	function set_settings() {
		$settings = new Of_The_Day_Settings;
		// ACTIVATION AND CONFIGURATION
		$settings->set_tabs(array(
		  'schedule' => array( 'icon' => 'calendar', 'label' => 'Scheduling Posts' ),
			'facebook-app' => array( 'icon' => 'facebook-alt', 'label' => 'Facebook Login' )
		));
			$settings->add_section(
				'schedule',
				'configuration',
				__( 'Automatic Posting', 'of-the-day' ),
				'<p>' . __( 'To automatically post Of the Day options to Facebook, please enable this feature and choose a sharing schedule.' ) . '</p>'
			);
				$checkbox = '<label><input type="checkbox"> Check box to activate automatic posting.</label>';
				if ( !$this->fb_app_id() ) {
					$checkbox = 'Please authorize Facebook to get started.';
				} else if ( !$this->fb_page_id() ) {
					$checkbox = 'Please choose a Facebook Page to get started.';
				}
				$settings->add_field(
				 'configuration',
				 'enable_feature',
				 __( 'Enable Scheduling', 'of-the-day' ),
				 $checkbox
				);
				$settings->add_field(
				 'configuration',
				 'time_to_share',
				 __( 'Time to Share', 'of-the-day' ),
				 ''
				);
		//  FACEBOOK AUTHORIZATION
			$settings->add_section(
				'facebook-app',
				'facebook_settings',
				__( 'Facebook App', 'of-the-day' ),
				'<p>' . __( 'In order to automatically share the post of the day to Facebook, you need to set your Facebook app information here.', 'of-the-day' ) . '</p>'
			);
				if ( $this->fb_app_id() ) {
					 $settings->add_field(
							'facebook_settings',
							'facebook_login',
							__( 'Facebook Login', 'of-the-day' ),
							'<fb:login-button scope="manage_pages,publish_pages" onlogin="checkLoginState();"></fb:login-button>'
						);
					$settings->add_field(
						'facebook_settings',
						'facebook_choose_page',
						__( 'Choose Page', 'of-the-day' ),
						'<select></select>
						<p id="fb-page-name" class="description"></p>'
					);
				} else {
					$settings->add_field(
						'facebook_settings',
						'facebook_app_id',
						__( 'App ID', 'of-the-day' ),
						'<input type="text" class="regular-text" placeholder="Enter your app ID" value="" />'
					);
				}

		$settings->register_settings();
		return $settings;
	}

	function facebook_app_javascript() {
			$script = '';
			if ( !empty($this->fb_app_id()) ) {
			$script = "<script>
			// This is called with the results from from FB.getLoginStatus().
			function statusChangeCallback(response) {
				console.log('statusChangeCallback');
				console.log(response);
				// The response object is returned with a status field that lets the
				// app know the current login status of the person.
				// Full docs on the response object can be found in the documentation
				// for FB.getLoginStatus().
				if (response.status === 'connected') {
					// Load pages
					getFacebookPages();
				} else {
					// The person is not logged into your app or we are unable to tell.
					// document.getElementById('status').innerHTML = 'Please log ' +
					// 	'into this app.';
					// myFacebookLogin();
				}
			}

			// This function is called when someone finishes with the Login
			// Button.  See the onlogin handler attached to it in the sample
			// code below.
			function checkLoginState() {
				FB.getLoginStatus(function(response) {
					statusChangeCallback(response);
				});
			}

			// Called for starting the Facebook login process
			// function myFacebookLogin() {
			// 	jQuery('#facebook_choose_page').replaceWith(\"<fb:login-button scope='manage_pages,publish_pages' onlogin='checkLoginState();'></fb:login-button>\")
			// }

			//  This function is called for managing pages
			function getFacebookPages() {
				jQuery('#fb-page-name').html('Loading...');
				FB.api('/me/accounts',function(apiresponse){
					var data=apiresponse['data'];
					var ids = new Array();
					for (var i=0; i<data.length; i++ ) {
						// ids[i]=data[i].id;
						id = data[i].id;
						name = data[i].name;
						current = '';
						if ( id == " . $this->fb_page_id() . " ) {
							current = ' selected=\"selected\"';
							jQuery('#fb-page-name').html('<strong>' + name + '</strong> – #' + id );
						}
						jQuery('#facebook_choose_page').append(\"<option value='\" + id + \"'\" + current + \">\" + name + \"</option>\");
					}
					has_page = document.getElementById('fb-page-name');
					if (has_page.selectedIndex != null) {
			       has_page.selectedIndex = 'No page chosen yet.';
				  }
				});
			}

			// This function is for displaying the chosen page
			function showFacebookPage() {
				FB.api(
				    '/" . $this->fb_page_id() . "',
				    function (response) {
				      if (response && !response.error) {
				        /* handle the result */
								console.log(response);
				      }
				    }
				);
			}

			window.fbAsyncInit = function() {
				FB.init({
					appId      : '" . $this->fb_app_id() . "',
					cookie     : true,  // enable cookies to allow the server to access
															// the session
					xfbml      : true,  // parse social plugins on this page
					version    : 'v2.8' // use graph api version 2.8
				});

			// Now that we've initialized the JavaScript SDK, we call
			// FB.getLoginStatus().  This function gets the state of the
			// person visiting this page and can return one of three states to
			// the callback you provide.  They can be:
			//
			// 1. Logged into your app ('connected')
			// 2. Logged into Facebook, but not your app ('not_authorized')
			// 3. Not logged into Facebook and can't tell if they are logged into
			//    your app or not.
			//
			// These three cases are handled in the callback function.

				FB.getLoginStatus(function(response) {
					statusChangeCallback(response);
				});

			};

			// Load the SDK asynchronously
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = '//connect.facebook.net/en_US/sdk.js';
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));

			</script>";
		}
		echo $script;
	}


	/**
	 * get_post		Returns one random post to feature for the day.
	 * @param  array   $atts An array of attributes to filter posts by
	 * @return {[type]       [description]
	 */
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

		// Check for Facebook (share by default)
		$share = true;
		if ( isset( $atts['share'] ) && ! empty( $atts['share'] ) ) {
			if ( false == $atts['share'] ) {
				$share = false;
			}
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
