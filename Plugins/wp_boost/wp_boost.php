<?php
/**
 * @package WP Boost
 */
/*
Plugin Name: WP Boost
Plugin URI: http://plugin.domm98.cz
Description: Booster for wordpress, more functions, more features.
Version: 1.0
Author: Domm
Author URI: http://domm98.cz
License: GPLv2 or later
Text Domain: wp_boost_by_Domm
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

$hodnosti = array("null", "All", "Subscriber", "Contributor", "Author", "Editor", "Administrator");

function wp_boost_UserShow( $atts )
{   
  global $wp_query, $wpdb, $hodnosti;
    
  $atts = shortcode_atts(
		array(
			'filter' => 'All'
		), $atts, 'user_show' );         

  $args = array('order' => 'ASC');  
  if($atts['filter'] != "All") $user_query = new WP_User_Query( array( 'role' => $atts['filter'] ) ); 
  else $user_query = new WP_User_Query($args);
  
  $string = null;
  $string.= "Filter: ".$atts['filter']."<br>";
  if ( ! empty( $user_query->results ) ) {   
    $string.= "<ul>";              
    foreach ( $user_query->results as $user ) {
      $string.= "<li>".$user->display_name."</li>";
    }
    $string.= "</ul>";
  } else {
    $string.= 'No users found.';
  }
    
	return $string;
} 

add_shortcode( 'user_show', 'wp_boost_UserShow' );

class wp_boost_user_widget extends WP_Widget {
  public function __construct() {
    parent::__construct(
			'wp_boost_user_widget',
			__( 'WP Boost User Widget', 'text_domain' ), 
			array( 'description' => __( 'WP Boost Plugin informations.', 'text_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
    global $wp_query, $wpdb, $hodnosti;
    echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
    
    if($instance['user_filter'] == 1) $filter = $hodnosti[1];
    else if($instance['user_filter'] == 2) $filter = $hodnosti[2];
    else if($instance['user_filter'] == 3) $filter = $hodnosti[3];
    else if($instance['user_filter'] == 4) $filter = $hodnosti[4];
    else if($instance['user_filter'] == 5) $filter = $hodnosti[5];
    else if($instance['user_filter'] == 6) $filter = $hodnosti[6]; 
    echo wp_boost_UserShow(array("filter" => $filter));
    
		echo $args['after_widget'];
	}

	public function form( $instance ) {
    global $hodnosti;
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    $user_filter = ! empty( $instance['user_filter'] ) ? $instance['user_filter'] : _1;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    
    <label for="<?php echo $this->get_field_id( 'user_filter' ); ?>"><?php _e( 'User Filter:' ); ?></label> 
    <select id="<?php echo $this->get_field_id( 'user_filter' ); ?>" name="<?php echo $this->get_field_name( 'user_filter' ); ?>">
      <option value="0">— Select —</option>
      <?php
      for($i = 1; $i < count($hodnosti);$i ++)
      {
        $selected = ($i == $user_filter) ? "selected" : "";
        echo "<option value='".$i."' ".$selected.">".$hodnosti[$i]."</option>";
      }
      ?>	
    </select>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
    $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['user_filter'] = ( ! empty( $new_instance['user_filter'] ) ) ? strip_tags( $new_instance['user_filter'] ) : '';

		return $instance;
	}
}

function wp_boost_widget_register() {
    register_widget( 'wp_boost_user_widget' );
}
add_action( 'widgets_init', 'wp_boost_widget_register' );

function wp_boost_custom_meta() {
  add_meta_box (
    'my_meta_box',
    __('Publikování'),
    'wp_boost_render_meta',
    'post',
    'side',
    'high'
  );
}

add_action('add_meta_boxes', 'wp_boost_custom_meta');

function wp_boost_render_meta($post_id) {
  echo "<label for='spamer'>";
  echo "<input type='checkbox' name='spamer' value='aktivuj'>";  
  echo "Aktivuj spamer";
	echo "</label>";
}

function spamer3000( $post_ID ) {
   $emaily = 'procdo13it@sps-prosek.cz, nekdo@dalsi.neco';
   wp_mail( $emaily, "Text E-Mailu");
   return $post_ID;
}
add_action( 'publish_post', 'spamer3000' );
?>