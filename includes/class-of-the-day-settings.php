<?php

class Of_The_Day_Settings {

	private $tab;
	private $page_title;
	private $menu_title;
	private $url;
	private $tabs;
	private $sections = array();
	private $fields = array();

	function __construct() {
    $this->options = get_option( 'of-the-day', array() );
	}

	public function url() {
		return $this->url;
	}
	public function page_title() {
		return $this->page_title;
	}
	public function menu_title() {
		return $this->menu_title;
	}
	public function tabs() {
		return $this->tabs;
	}
	public function sections() {
		return $this->sections;
	}
	public function fields() {
		return $this->fields;
	}

  public function set_tabs( $tabs ) {
    $this->tabs = $tabs;
  }

	function current_tab( $tab = '' ) {
		$tab = 'schedule';
		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		}
		return sanitize_title( $tab );
	}

	function validate_tab_options( $input ) {

		$options = $this->options;

		// Check for false checkboxes ( makes checkboxes work with tabs )
		if ( isset( $input['checkbox'] ) and is_array( $input['checkbox'] ) ) {
			foreach ( $input['checkbox'] as $checkboxes ) {
				if ( ! isset( $input[ $checkboxes ] ) || false === $input[ $checkboxes ] ) {
					$input[ $checkboxes ] = false;
				}
			}
			unset( $input['checkbox'] );
		}

		if ( is_array( $input ) and ! empty( $input ) ) {
			// Only update inputs that have incoming values
			foreach ( $input as $key => $changed ) {
				$options[ $key ] = $changed;
			}
			if ( isset( $flush ) && true === $flush ) {
				// Rewrite permalinks if a slug was set
				// NOTE: This is recorded as bad practice, look for alternatives
				flush_rewrite_rules( false );
			}
		}
		return $options;
	}

	/**
	 * Functions for adding sections and options from the outside
	 */

	function new_tab( $uid ) {
		$this->tabs[ $uid ] = $uid;
		return $this->tabs;
	}

	function add_section( $tab, $uid, $label, $callback = '' ) {
		// TODO: Validate tabs, sanitize, etc.
		$this->sections[ $uid ] = array( 'uid' => $uid, 'label' => $label, 'tab' => $tab, 'callback' => $callback );
		return $this->sections;
	}

	function add_field( $section, $uid, $label, $callback = '' ) {
		// TODO: Validate tabs, sanitize, etc.
		$this->fields[ $section ][ $uid ] = array( 'uid' => $uid, 'label' => $label, 'callback' => $callback );
		return $this->sections;
	}


	/**
	 * Functions for registering new sections and options
	 */

	function register_settings() {
		foreach ( $this->sections as $section_uid => $section_values ) {
			$this->new_section( $section_uid, $section_values['label'], $section_values['tab'] );
		}
	}

	function new_section( $uid, $label, $tab ) {
		add_settings_section( $uid, $label, array( $this, 'section_callback' ), $tab );
		if ( isset( $this->fields[ $uid ] ) ) {
			foreach ( $this->fields[ $uid ] as $field_uid => $field_values ) {
				add_settings_field(
					$field_uid,
					$field_values['label'],
					array( $this, 'fields_callback' ),
					$tab,
					$uid,
				array( 'section_uid' => $uid, 'field_uid' => $field_uid ) );
			}
		}
		register_setting( $tab, 'of-the-day', array( $this, 'validate_tab_options' ) );
	}

	function section_callback( $args ) {
		$id = $args['id'];
		$callback = $this->sections[ $id ]['callback'];
		$this->function_or_string( $callback );
	}

	function fields_callback( $args ) {
		$section = $args['section_uid'];
		$field = $args['field_uid'];
		$callback = $this->fields[ $section ][ $field ]['callback'];
		$this->function_or_string( $callback, $field );
	}

	function function_or_string( $callback, $field = '' ) {

		if ( is_callable( $callback ) ) {
			$string = $callback( $this->dictionary );
		} else {
			$string = $callback;
		}

		$do_not_format = array( 'field_order', 'fields' );

		if ( true === in_array( $field, $do_not_format ) ) {
			// Do nothing
		} else if (
				false !== strpos( $string, '<input' ) |
				false !== strpos( $string, '<select' ) |
				false !== strpos( $string, '<textarea' )
			) {
			$string = $this->set_input_defaults( $string, $field );
		}

		echo $string;
	}

	function set_input_defaults( $string, $field ) {
		$option_name = 'of-the-day';
		$options = $this->options;
		$field_name = $option_name . '[' . $field . ']';

		if ( false !== strpos( $string, 'input' ) ) {
			$string = preg_replace( '/<input /', '<input name="' . $field_name . '" id="' . $field . '" ', $string, 1 );
		}
		if ( false !== strpos( $string, 'select' ) ) {
			$string = preg_replace( '/<select/', '<select name="' . $field_name . '" id="' . $field . '" ', $string, 1 );
		}
		if ( false !== strpos( $string, 'textarea' ) ) {
			$string = preg_replace( '/<textarea/', '<textarea name="' . $field_name . '" id="' . $field . '" ', $string, 1 );
			// Set value
			$value = '';
			if ( isset( $options[ $field ] ) and ! empty( $options[ $field ] ) ) {
				$value = $options[ $field ];
				$string = str_replace( '></', '>' . $value . '</', $string );
			}
		}

		if ( false !== strpos( $string, 'type="checkbox"' ) ) {
			// Detect CHECKED value
			if ( isset( $options[ $field ] ) and ! empty( $options[ $field ] ) ) {
				$checked = ( $options[ $field ] ) ? 'checked' : '';
				$string = str_replace( '<input ', "<input $checked ", $string );
			}
			// Add trick for confirming checkboxes that are empty
			$string .= '<input type="hidden" name="' . $option_name . '[checkbox][]" value="' . $field . '" />';
		}
		if ( false !== strpos( $string, 'value=""' ) ) {
			$value = '';
			if ( isset( $options[ $field ] ) and ! empty( $options[ $field ] ) ) {
				$value = $options[ $field ];
				$string = str_replace( 'value=""', 'value="' . $value . '"', $string );
			}
		}
		return $string;
	}
}
