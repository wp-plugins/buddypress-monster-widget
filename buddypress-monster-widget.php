<?php
/*
 * Plugin Name:       BuddyPress Monster Widget
 * Plugin URI:        http://wordpress.org/extend/plugins/buddypress-monster/widget/
 * Description:       A widget that allows for quick and easy testing of multiple BuddyPress widgets. Not intended for production sites.
 * Version:           0.3
 * License:           GPLv2 or later
 * Author:            mercime, imath
 * Author URI:        http://buddypress.org
 * Text Domain:       buddypress-monster-widget
 * load_plugin_textdomain( 'buddypress-monster-widget' );
 */

/**
 * Register the BuddyPress Monster Widget.
 *
 * Hooks into the widgets_init action.
 *
 * @since 0.2
 */
function register_buddypress_monster_widget() {
	register_widget( 'BuddyPress_Monster_Widget' );
}
add_action( 'bp_widgets_init', 'register_buddypress_monster_widget' );

/**
 * BuddyPress Monster Widget.
 *
 * A widget that allows for quick and easy testing of multiple BuddyPress widgets.
 *
 * @since 0.1
 */
class BuddyPress_Monster_Widget extends WP_Widget {

	/**
	 * Iterator (int).
	 *
	 * Used to set a unique html id attribute for each
	 * widget instance generated by BP_Monster_Widget::widget().
	 *
	 * @since 0.1
	 */
	static $iterator = 1;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'BuddyPressMonster', __( 'BuddyPress Monster Widget', 'buddypress-monster-widget' ), array(
			'classname'   => 'buddypress_monster_widget',
			'description' => __( 'Test multiple BuddyPress widgets at the same time.', 'buddypress-monster-widget' )
		) );
	}

	/**
	 * Print the BuddyPress Monster widget on the front-end.
	 *
	 * @uses $wp_registered_sidebars
	 * @uses BuddyPress_Monster_Widget::$iterator
	 * @uses BuddyPress_Monster_Widget::get_widget_class()
	 * @uses $this->get_widget_config()
	 *
	 * @since 0.1
	 */
	public function widget( $args, $instance ) {
		global $wp_registered_sidebars;

		$id = $args['id'];
		$args = $wp_registered_sidebars[$id];
		$before_widget = $args['before_widget'];

		foreach( $this->get_widget_config() as $widget ) {
			$_instance = ( isset( $widget[1] ) ) ? $widget[1] : null;

			$args['before_widget'] = sprintf(
				$before_widget,
				'buddypress-monster-widget-placeholder-' . self::$iterator,
				$this->get_widget_class( $widget[0] )
			);

			the_widget( $widget[0], $_instance, $args );

			self::$iterator++;
		}
    }

	/**
	 * Widgets (array).
	 *
	 * Numerically indexed array of Pre-configured widgets to
	 * display in every instance of a BuddyPress Monster widget. 
	 * Each entry requires two values:
	 *
	 * 0 - The name of the widget's class as registered with register_widget().
	 * 1 - An associative array representing an instance of the widget.
	 *
	 * This list can be altered by using the `buddypress-monster-widget-config` filter.
	 *
	 * @return array Widget configuration.
	 * @since 0.1
	 */
	public function get_widget_config() {
		$widgets = array(

			array( 'BP_Core_Login_Widget', array(
				'title'          => __( 'BuddyPress Login', 'buddypress-monster-widget' ),
			) ),

			array( 'BP_Core_Members_Widget', array(
				'title'          => __( 'BuddyPress Members', 'buddypress-monster-widget' ),
				'max_members'    => 5,
				'member_default' => 'active',
				'link_title'     => false,
			) ),

			array( 'BP_Core_Whos_Online_Widget', array(
				'title'          => __( 'BuddyPress Who\'s Online', 'buddypress-monster-widget' ),
				'max_members'    => 15,
			) ),

			array( 'BP_Core_Recently_Active_Widget', array(
				'title'          => __( 'BuddyPress Recently Active Members', 'buddypress-monster-widget' ),
				'max_members'    => 15,
			) ),

		);
	
		// Be sure the blogs component is active
		if ( bp_is_active( 'blogs' ) && is_multisite() ) {
			$widgets[] = array( 'BP_Blogs_Recent_Posts_Widget', array(
				'title'          => __( 'BuddyPress Recent Networkwide Posts', 'buddypress-monster-widget' ),
				'max_posts'      => 10,
				'link_title'     => true,
			) );
		}

		// Be sure the friends component is active
		if ( bp_is_active( 'friends' ) ) {
			$widgets[] = array( 'BP_Core_Friends_Widget', array(
				'title'          => __( 'BuddyPress Friends', 'buddypress-monster-widget' ),
				'max_friends'    => 5,
				'friend_default' => 'active',
				'link_title'     => false
			) );
		}

		// Be sure the groups component is active
		if ( bp_is_active( 'groups' ) ) {
			$widgets[] = array( 'BP_Groups_Widget', array(
				'title'          => __( 'BuddyPress Groups', 'buddypress-monster-widget' ),
				'max_groups'     => 5,
				'group_default'  => 'active',
				'link_title'     => false,
			) );
		}

		// Be sure the message component is active
		if ( bp_is_active( 'messages' ) ) {
			$widgets[] = array( 'BP_Messages_Sitewide_Notices_Widget', array(
				'title'          => __( 'BuddyPress Sitewide Notices', 'buddypress-monster-widget' ),
			) );
		}

		return apply_filters( 'buddypress-monster-widget-config', $widgets );

	}

	/**
	 * Get the html class attribute value for a given widget.
	 *
	 * @uses $wp_widget_factory
	 *
	 * @param string $widget The name of a registered widget class.
	 * @return string Dynamic class name a given widget.
	 *
	 * @since 0.1
	 */
	public function get_widget_class( $widget ) {
		global $wp_widget_factory;

		$widget_obj = '';
		if ( isset( $wp_widget_factory->widgets[$widget] ) )
			$widget_obj = $wp_widget_factory->widgets[$widget];

		if ( ! is_a( $widget_obj, 'WP_Widget') )
			return '';

		if ( ! isset( $widget_obj->widget_options['classname'] ) )
			return '';

		return $widget_obj->widget_options['classname'];
	}

}