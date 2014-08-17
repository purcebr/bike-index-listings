<?php
/**
 * Bike Index Listings
 *
 * A Widget to show bikes registered with the Bike Index on your wp site.
 *
 * @package   Bike_Index_Listings
 * @author    Bryan Purcell <purcebr@gmail.com>
 * @license   GPL-2.0+
 * @link      http://bikeindex.org
 * @copyright 2014
 *
 * @wordpress-plugin
 * Plugin Name:       Bike Index Listings
 * Plugin URI:        @TODO
 * Description:       A Widget to show bikes registered with the Bike Index on your wp site.
 * Version:           1.0.0
 * Author:            Bryan Purcell
 * Text Domain:       bike-index-listings
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

include_once('lib/api.class.php');

// TODO: change 'Widget_Name' to the name of your plugin
class Bike_Index_Listings extends WP_Widget {

    /**
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'bike-index-listings';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'Bike Index Listings', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Short description of the widget goes here.', $this->get_widget_slug() )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		$this->api = new BikeIndexAPI();

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		$title = "hello";
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset ( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset ( $cache[ $args['widget_id'] ] ) )
			return print $cache[ $args['widget_id'] ];
		
		// go on with your widget logic, put everything into a string and …


		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		ob_start();
		$bikes = $this->get_bikes($instance['zipcode'], $instance['radius'], $instance['max_bikes'], $instance['stolenonly']);
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;


		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $widget_string;

	} // end widget
	
	
	public function flush_widget_cache() 
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		if(strlen($new_instance['zipcode'])==5 && ctype_digit($new_instance['zipcode'])) {
			$instance['zipcode'] = strip_tags($new_instance['zipcode']);
		}
		
		//if(ctype_digit($instance['radius'])) {
		$instance['radius'] = strip_tags($new_instance['radius']);
		//}

		$instance['max_bikes'] = strip_tags($new_instance['max_bikes']);

		if ($new_instance['stolenonly']=="on") {$instance['stolenonly'] = 1;} else {$instance['stolenonly'] = 0;}


		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

	// Check values
	if( $instance) {
		$title = esc_attr($instance['title']);
		$zipcode = esc_attr($instance['zipcode']);
		$radius = esc_attr($instance['radius']);
		$max_bikes = esc_attr($instance['max_bikes']);
	} else {
		$title = '';
		$zipcode = '';
		$radius = 10;
		$max_bikes = 5;
	}

		// TODO: Store the values of the widget in their own variable

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'widget-name' to the name of *your* plugin
		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery') );

	} // end register_widget_scripts

	public function get_bikes($zip, $radius, $max_bikes, $stolenonly) {


		$transient_var = 'bikeindex_' . $zip . '_' . $radius;
		
		//if ( false === ( $bikes = get_transient( $transient_var ) ) ) {
		if ( true ) {
			$data = array("zip" => $zip, "proximity_radius" => $radius);
			if ($stolenonly==1) {$data = array("zip" => $zip, "proximity_radius" => $radius, "stolen" => "true");}
			$action = 'bikes';
			$req = $this->api->post_json($data, $action);
			$bikes_response = json_decode($req);
			$bike = array();
			$bikes = array();
			if($bikes_response) {
				foreach ( $bikes_response->bikes as $bike_response ) {

					$bike['thumb'] = (isset($bike_response->thumb)) ? $bike_response->thumb : "";
					$bike['url'] = (isset($bike_response->url)) ? $bike_response->url : "";
					$bike['frame_model'] = (isset($bike_response->frame_model)) ? $bike_response->frame_model : "";
					$bike['description'] = (isset($bike_response->description)) ? substr($bike_response->description, 0, 100) : "";
					$bike['manufacturer_name'] = (isset($bike_response->manufacturer_name)) ? $bike_response->manufacturer_name : "";
					$bike['year'] = (isset($bike_response->year)) ? $bike_response->year : "";
					$bikes[] = $bike;
				}
				set_transient( $transient_var, $bikes, 0 );
			}
		}

		return $bikes;
	}

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Bike_Index_Listings");' ) );
