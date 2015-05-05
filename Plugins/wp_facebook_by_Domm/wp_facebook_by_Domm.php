<?php
/**
 * @package WP Facebook By Domm
 */
/*
Plugin Name: WP Facebook By Domm
Plugin URI: http://plugin.domm98.cz
Description: This plugin adds facebook features in the WP. 
Version: 1.0
Author: Domm
Author URI: http://domm98.cz
License: GPLv2 or later
Text Domain: wp_facebook_by_Domm
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

register_activation_hook(__FILE__, 'wp_facebook_activate');  
register_uninstall_hook(__FILE__, 'wp_facebook_deactivate');
add_action('admin_init', 'wp_facebook_insall');
add_action('wp_head','wp_facebook_fb_head');
add_action('wp_head','wp_facebook_public_styles');
add_action('admin_head','wp_facebook_admin_styles');

function wp_facebook_fb_head()
{
  $fb = get_option("wp_facebook:fb_app_id");
  if(!EMPTY($fb)) 
  {
    ?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/cs_CZ/sdk.js#xfbml=1&version=v2.3&appId=<?php echo $fb;?>";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script><?php 
    echo "\n";
  }
}
function wp_facebook_public_styles()
{
  echo "<link rel='stylesheet' id='wp_facebook_by_Domm_css' href='".plugins_url( 'wp_facebook_by_Domm/css/public.css' )."' type='text/css' media='all' />\n";
}
function wp_facebook_admin_styles()
{
  echo "<link rel='stylesheet' id='wp_facebook_by_Domm_css' href='".plugins_url( 'wp_facebook_by_Domm/css/admin.css' )."' type='text/css' media='all' />\n";
}

function wp_facebook_activate() 
{
  add_option('wp_facebook_plugin_name', 'WP Facebook by Domm'); 
  add_option('wp_facebook_plugin_shortname', 'WP Facebook'); 
  add_option('wp_facebook_plugin_version', '1.0'); 
  add_option('wp_facebook_do_insall', true);
  add_option('wp_facebook_lang', '');
  add_option('wp_facebook:fb_page_url', '');
  add_option('wp_facebook:fb_app_id', '');
  add_option('wp_facebook:fb_app_secret', '');
  add_option('wp_facebook:fb_login_url', '');
}  

function wp_facebook_deactivate()
{
  delete_option('wp_facebook_plugin_name');
  delete_option('wp_facebook_plugin_version');
  delete_option('wp_facebook_do_insall');
  delete_option('wp_facebook_lang');
  delete_option('wp_facebook:fb_page_url');
  delete_option('wp_facebook:fb_app_id');
  delete_option('wp_facebook:fb_app_secret');
  delete_option('wp_facebook:fb_login_url');
}              

function wp_facebook_insall() 
{
  if (get_option('wp_facebook_do_insall', false)) 
  {
    delete_option('wp_facebook_do_insall');
    wp_redirect('admin.php?page=wp_facebook_by_Domm/wp_facebook_by_Domm.php');
  }
}

function register_menu() {

	add_menu_page(get_option("wp_facebook_plugin_shortname"), get_option("wp_facebook_plugin_shortname"), 'administrator', __FILE__, 'wp_facebook_settings_page', plugins_url( 'wp_facebook_by_Domm/images/icon.png' ));
}

add_action('admin_menu', 'register_menu');

function locale($msg='', $lang='')
{
  if( empty($lang) ) $lang = 'English';
  $fail_text = "Message ".$lang."-".$msg." can't be found.";

  $file_path = plugin_dir_path( __FILE__ )."/lang/".$lang.".php";
  if(file_exists($file_path)) include(plugin_dir_path( __FILE__ )."/lang/".$lang.".php");
  else return $fail_text;

  if( isset($wp_facebook_langs[$msg]) && !empty($wp_facebook_langs[$msg])) return $wp_facebook_langs[$msg];
	else return $fail_text;
}

add_action('login_form', 'wp_facebook_showlogin');

function wp_facebook_showlogin() 
{
  $fb = get_option("wp_facebook:fb_app_id");
  if(!EMPTY($fb)) 
  { 
    echo "<center><h3>".locale("LOGIN:T1", get_option("wp_facebook_lang"))."</h3></center>";
    echo "<a href='".get_option("wp_facebook:fb_login_url")."'><img src='".plugins_url( 'images/fb_login.png', __FILE__ )."' alt='Facebook Login' width='270px'></a>";
  }
}

function wp_facebook_settings_page()
{
  if (!current_user_can('administrator')) wp_die($wp_facebook_langs["ADMIN:ERROR"]);
  ?>
    <div class='wp_fb_settings_page'>
      <table width='100%' class='wp_fb_settings_title'>
      
        <tr class='title'>
          <td width='90%' class='title_left'>
            <h3><?php echo get_option("wp_facebook_plugin_name");?></h3>
          </td>
          <td width='10%' class='title_right'>
            <h4>v<?php echo get_option("wp_facebook_plugin_version");?></h4>
          </td>
        </tr>
      </table>
      
      <form method="POST" action="">
        <table width='100%' class='wp_fb_settings_table'>
        
        <tr class='normal'>
          <td class='text' width='20%'>
            <?php echo locale("ADMIN:LANGS", get_option("wp_facebook_lang")); ?>
          </td>
          <td width='80%'>
            <?php
            $lang_dir = plugin_dir_path( __FILE__ )."/lang/";
            $lang_str = null;
            $lang_str .= "<select name='wp_facebook_lang'>";
            if ($open_dir = opendir($lang_dir)) 
            {
              while (($file = readdir($open_dir)) !== false) 
              {                           
                if ($file != "." && $file != ".." && $file != "index.php")
                { 
                  $file = explode(".", $file);
                  if($file[0] == get_option("wp_facebook_lang")) $lang_str .= "<option value='".$file[0]."' selected>".$file[0]."</option>";
                  //else if(empty(get_option("wp_facebook_lang"))) $lang_str .= "<option value='".$file."'>".$file."</option>"; 
                  else $lang_str .= "<option value='".$file[0]."'>".$file[0]."</option>";
                }
              }
              closedir($open_dir);
            }
            $lang_str .= "</select>";
            echo $lang_str;
            ?>
          </td> 
        </tr>
        
        <tr class='normal'>
          <td class='text' width='20%'>
            <?php echo locale("ADMIN:S1", get_option("wp_facebook_lang")); ?>
          </td>
          <td width='80%'>
            <input type='text' name='wp_facebook:fb_page_url' value='<?php echo get_option("wp_facebook:fb_page_url");?>' width='100%'> 
          </td> 
        </tr>
        
        <tr class='normal'>
          <td class='text' width='20%'>
            <?php echo locale("ADMIN:S2", get_option("wp_facebook_lang")); ?>
          </td>
          <td width='80%'>
            <input type='text' name='wp_facebook:fb_app_id' value='<?php echo get_option("wp_facebook:fb_app_id");?>' width='100%'> 
          </td> 
        </tr>
        
        <tr class='normal'>
          <td class='text' width='20%'>
            <?php echo locale("ADMIN:S3", get_option("wp_facebook_lang")); ?>
          </td>
          <td width='80%'>
            <input type='text' name='wp_facebook:fb_app_secret' value='<?php echo get_option("wp_facebook:fb_app_secret");?>' width='100%'> 
          </td> 
        </tr>
        
        <tr class='normal'>
          <td class='text' width='20%'>
            <?php echo locale("ADMIN:S4", get_option("wp_facebook_lang")); ?>
          </td>
          <td width='80%'>
            <?php 
              $url = get_option("wp_facebook:fb_login_url");
              if( !empty($url) ) echo $url;
              else echo locale("ADMIN:URL_E", get_option("wp_facebook_lang"));
            ?> 
          </td> 
        </tr>
        
        <tr><td colspan='2' align='right'><p style='padding-right:15px;'><input type="submit" value="<?php echo locale("ADMIN:SAVE", get_option("wp_facebook_lang"));?>" name="update_settings" class="button-primary" /></p></td></tr>
      </table>                  
    </form>
    <?php
    if(isset($_POST["update_settings"])) 
    {  
      update_option("wp_facebook:fb_page_url", $_POST["wp_facebook:fb_page_url"]); 
      update_option("wp_facebook:fb_app_id", $_POST["wp_facebook:fb_app_id"]); 
      update_option("wp_facebook:fb_app_secret", $_POST["wp_facebook:fb_app_secret"]); 
      
      echo "<div class='save_message'><p>".locale("ADMIN:SAVED", get_option("wp_facebook_lang"))."</p></div>";
      
      update_option("wp_facebook:fb_login_url", site_url()."/?login=facebook");
      update_option("wp_facebook_lang", $_POST["wp_facebook_lang"]); 
      
      echo "<meta http-equiv='refresh' content='2'>";
    }
    ?>
    
    <table width='100%' class='wp_fb_settings_title_small'>
      
        <tr class='title'>
          <td colspan='2' width='100%' class='title_left'>
            <h3><?php echo locale("SHORTS:TITLE", get_option("wp_facebook_lang"));?></h3>
          </td>
        </tr>
        
    </table>
    
    <table width='100%' class='wp_fb_settings_table'> 
        
        <tr>
          <td class='text' width='20%'><b>[fb_post post_id='POST_ID']</b></td>
          <td width='80%'><?php echo locale("SHORTS:S1", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>[fb_like url='URL']</b></td>
          <td width='80%'><?php echo locale("SHORTS:S2", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>[fb_recommend url='URL']</b></td>
          <td width='80%'><?php echo locale("SHORTS:S3", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>[fb_share url='URL' counter='0/1']</b></td>
          <td width='80%'><?php echo locale("SHORTS:S4", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>[fb_send url='URL']</b></td>
          <td width='80%'><?php echo locale("SHORTS:S5", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td colspan='2' width='100%'><hr></td> 
        </tr>
        
        <tr>
          <td class='text' width='20%'><b><?php echo locale("ADMIN:S1", get_option("wp_facebook_lang"));?></b></td>
          <td width='80%'><?php echo locale("SHORTS:I4", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>POST_ID</b></td>
          <td width='80%'><?php echo locale("SHORTS:I1", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>URL</b></td>
          <td width='80%'><?php echo locale("SHORTS:I2", get_option("wp_facebook_lang"));?></td>
        </tr>
        
        <tr>
          <td class='text' width='20%'><b>0/1</b></td>
          <td width='80%'><?php echo locale("SHORTS:I3", get_option("wp_facebook_lang"));?></td>
        </tr>
    
    </table>
    
    <table width='100%' class='wp_fb_settings_title_small'>
      
        <tr class='title'>
          <td colspan='2' width='100%' class='title_left'>
            <h3><?php echo locale("WIDGET:TITLE", get_option("wp_facebook_lang"));?></h3>
          </td>
        </tr>
        
    </table>
    
    <table width='100%' class='wp_fb_settings_table'> 
      <tr>
        <td class='text' width='20%'><b><?php echo locale("WIDGET:W1", get_option("wp_facebook_lang"));?></b></td>
        <td width='80%'><?php echo locale("WIDGET:I1", get_option("wp_facebook_lang"));?></td>
      </tr>
    </table>
    
  </div>
  <?php
}

/* SHORT CODES */

function wp_facebook_shortcode_post( $atts )
{      
  $atts = shortcode_atts(
		array(
			'post_id' => ''
		), $atts, 'fb_post' );   
          
  $url = get_option("wp_facebook:fb_page_url");
  if( !empty($url) )
  {
  ?>
    <div class="fb-post" data-href="https://www.facebook.com/<?php echo $url;?>/posts/<?php echo $atts["post_id"];?>" data-width="500">
      <div class="fb-xfbml-parse-ignore">                                                  
        <blockquote cite="https://www.facebook.com/<?php echo $url;?>/posts/<?php echo $atts["post_id"];?>">
        </blockquote>
      </div>
    </div>
    <?php  
  }
  else echo locale("SHORT:URL_E", get_option("wp_facebook_lang")); 
} 
add_shortcode( 'fb_post', 'wp_facebook_shortcode_post' );

function wp_facebook_shortcode_like( $atts )
{   
  $atts = shortcode_atts(
		array(
			'url' => ''
		), $atts, 'fb_like' );         

  ?>
  <div class="fb-like" data-href="<?php echo $atts["url"];?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
  <?php  
} 
add_shortcode( 'fb_like', 'wp_facebook_shortcode_like' );

function wp_facebook_shortcode_recommend( $atts )
{   
  $atts = shortcode_atts(
		array(
			'url' => ''
		), $atts, 'fb_recommend' );         

  ?>
  <div class="fb-like" data-href="<?php echo $atts["url"];?>" data-layout="standard" data-action="recommend" data-show-faces="true" data-share="true"></div>
  <?php  
} 
add_shortcode( 'fb_recommend', 'wp_facebook_shortcode_recommend' );

function wp_facebook_shortcode_share( $atts )
{   
  $atts = shortcode_atts(
		array(
			'url' => '',
      'counter' => '1'
		), $atts, 'fb_share' );         

  if($atts["counter"] == "1") echo "<div class='fb-share-button' data-href='".$atts['url']."' data-layout='button_count'></div>\n";
  else echo "<div class='fb-share-button' data-href='".$atts['url']."' data-layout='button'></div>\n";
} 
add_shortcode( 'fb_share', 'wp_facebook_shortcode_share' );

function wp_facebook_shortcode_send( $atts )
{   
  $atts = shortcode_atts(
		array(
			'url' => ''
		), $atts, 'fb_send' );         

  ?>
  <div class="fb-send" data-href="<?php echo $atts["url"];?>" data-colorscheme="light"></div>
  <?php
} 
add_shortcode( 'fb_send', 'wp_facebook_shortcode_send' );

/* WIDGETS */

class wp_facebook_page_widget extends WP_Widget {
  public function __construct() {
    parent::__construct(
			'wp_facebook_page_widget',
			__( 'WP Facebook Page Widget', 'text_domain' ), 
			array( 'description' => __(  locale("WIDGET:I1", get_option("wp_facebook_lang")) , 'text_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
    global $wp_query, $wpdb, $hodnosti;
    echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
    $url = get_option("wp_facebook:fb_page_url");
    if( !empty($url) )
    {
      echo "
      <div class='fb-page' data-href='https://www.facebook.com/".$url."' data-hide-cover='".$instance["cover"]."' data-show-facepile='".$instance["faces"]."' data-show-posts='".$instance["posts"]."'>\n
        <div class='fb-xfbml-parse-ignore'>\n
          <blockquote cite='https://www.facebook.com/".$url."'></blockquote>\n
        </div>\n
      </div>\n
      ";
    }
    else echo locale("WIDGET:URL_E", get_option("wp_facebook_lang"));
    
		echo $args['after_widget'];
	}

	public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    $faces = ! empty( $instance['faces'] ) ? $instance['faces'] : _1;
    $cover = ! empty( $instance['cover'] ) ? $instance['cover'] : _1;
    $posts = ! empty( $instance['posts'] ) ? $instance['posts'] : _1;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    
    <label for="<?php echo $this->get_field_id( 'faces' ); ?>"><?php _e( 'Show Friends\'s Faces:' ); ?></label> 
    <select id="<?php echo $this->get_field_id( 'faces' ); ?>" name="<?php echo $this->get_field_name( 'faces' ); ?>">
    <?php
    if($faces == "true")
    {
      ?>
      <option value="false">Hide</option>
      <option value="true" selected>Show</option>
      <?php
    }
    else
    {
      ?>
      <option value="false" selected>Hide</option>
      <option value="true">Show</option>
      <?php
    }
    ?>
    </select>
    <br>
    
    <label for="<?php echo $this->get_field_id( 'cover' ); ?>"><?php _e( 'Show Cover Photo:' ); ?></label> 
    <select id="<?php echo $this->get_field_id( 'cover' ); ?>" name="<?php echo $this->get_field_name( 'cover' ); ?>">
    <?php
    if($cover == "false")
    {
      ?>
      <option value="true">Hide</option>
      <option value="false" selected>Show</option>
      <?php
    }
    else
    {
      ?>
      <option value="true" selected>Hide</option>
      <option value="false">Show</option>
      <?php
    }
    ?>
    </select>
    <br>
    
    <label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( 'Show Page Posts:' ); ?></label> 
    <select id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>">
    <?php
    if($posts == "true")
    {
      ?>
      <option value="false">Hide</option>
      <option value="true" selected>Show</option>
      <?php
    }
    else
    {
      ?>
      <option value="false" selected>Hide</option>
      <option value="true">Show</option>
      <?php
    }
    ?>
    </select>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
    $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['faces'] = ( ! empty( $new_instance['faces'] ) ) ? strip_tags( $new_instance['faces'] ) : '';
    $instance['cover'] = ( ! empty( $new_instance['cover'] ) ) ? strip_tags( $new_instance['cover'] ) : '';
    $instance['posts'] = ( ! empty( $new_instance['posts'] ) ) ? strip_tags( $new_instance['posts'] ) : '';
		return $instance;
	}
}

function wp_facebook_page_widget_register() {
    register_widget( 'wp_facebook_page_widget' );
}
add_action( 'widgets_init', 'wp_facebook_page_widget_register' );

/* LOGIN */
function wp_facebook_login()
{
  if(isset($_REQUEST['login']) && $_REQUEST['login'] == "facebook")
  { 
    $app_id = get_option("wp_facebook:fb_app_id");
    $app_secret = get_option("wp_facebook:fb_app_secret");
    $my_url = get_option("wp_facebook:fb_login_url");
    $code = $_REQUEST["code"];
    
    //echo "Waiting for facebook..";
    
    if(empty($code)) 
    {
      $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
      .$app_id . "&redirect_uri=" . urlencode($my_url)."&scope=email";
      echo("<script>top.location.href='".$dialog_url."'</script>");
    }
    
    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
    .$app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret="
    .$app_secret . "&code=" . $code;
    
    $ch = curl_init();                    	
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_URL,$token_url);
    $access_token = curl_exec($ch);
    curl_close($ch);
    	
    $graph_url = "https://graph.facebook.com/v2.2/me?".$access_token."&fields=id,name,email";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $graph_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $temp_user = curl_exec($ch);
    curl_close($ch);
    $fb_json = $temp_user;	
    $fb_user = json_decode($fb_json, true);
    //print_r($fb_user);
    //echo $fb_user["email"];
    if(!EMPTY($fb_user["email"]))
    {
      $user_id = username_exists($fb_user["id"]);
      if (!$user_id && email_exists($fb_user["email"]) == false ) 
      {
      	$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
      	$user_id = wp_create_user($fb_user["id"], $random_password, $fb_user["email"]);
        wp_update_user(array( 'ID' => $user_id, 'display_name' => $fb_user["name"]));
        wp_set_auth_cookie( $user_id, true );
      }
      else
      {
			  wp_set_auth_cookie( $user_id, true ); 
      }
   		wp_redirect( site_url() );
			exit;
    }
    else wp_die("Error: Facebook Error.");
  }
}
add_action('init', 'wp_facebook_login');  
?>