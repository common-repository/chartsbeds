<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action( 'widgets_init', 'cbeds_reviews_widget' );

function cbeds_reviews_widget() {
    register_widget( 'CB_reviews_Widget' );
}

class CB_reviews_Widget extends WP_Widget {
    function __construct() {
        $widget_ops = array( 'classname' => 'reviews', 'description' => __('A widget that displays hotels review', 'reviews') );
        $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'reviews-widget' );
        parent::__construct( 'reviews-widget', __('Chartsbeds review recent', 'reviews'), $widget_ops, $control_ops );
    }

    function widget( $args, $instance ) {
        extract( $args );

        //Our variables from the widget settings.
        $title = apply_filters('widget_title', $instance['title'] );
        $show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
		$key = isset( $instance['key'] ) ? $instance['key']:'';
        echo $before_widget."<div class='cb-widget'>";

        // Display the widget title
        if ( $title ){
            echo $before_title . $title . $after_title;
        }
        // Use shortcode in a PHP file (outside the post editor).
        echo do_shortcode( '[chartsbeds-review-recent key="'.$key.'"]' );
        echo $after_widget."</div>";
    }

    //Update the widget
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        //Strip tags from title and name to remove HTML
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['show_info'] = $new_instance['show_info'];
		$instance['key'] = $new_instance['key'];
        return $instance;
    }

    function form( $instance ) {
        //Set up some default widget settings.
        $defaults = array( 'title' => __('Hotel name', 'reviews'), 'name' => __('ChartsBeds', 'reviews'), 'show_info' => true );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>

        <!-- Widget Title: Text Input -->
        <p><label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php echo  _e('Title:', 'reviews'); ?></label>
        <input id="<?php echo $this->get_field_id( 'title' ) ?>" name="<?php echo $this->get_field_name( 'title' ) ?>" value="<?php echo $instance['title'] ?>" style="width:100%;" />
        </p>
		
		<p><label for="<?php echo $this->get_field_id( 'key' ) ?>"><?php echo _e('Key:', 'reviews'); ?></label>
        <input id="<?php echo $this->get_field_id( 'key' ) ?>" name="<?php echo $this->get_field_name( 'key' ) ?>" value="<?php echo $instance['key'] ?>" style="width:100%;" /></p>

    <?php
    }
}