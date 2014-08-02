<?php
/**
 * Plugin Name: Yaloub
 * Plugin URI: 
 * Description: Yaloub WordPress plugin facilitate the access to the file, and API explorer of the Yaloub Application Network.
 * Version: 0.1
 * Author: Yaloub
 * Author URI: https://yaloub.com/
 * License: GPLv2
 */
/*  Copyright 2013  Yaloub  (email : webmaster@yaloub.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook( __FILE__, array( &$this, 'yaloub_activate' ) );
register_uninstall_hook( __FILE__, array( &$this, 'yaloub_uninstall' ) );

function yaloub_activate() {

    //add_user_meta( $user_id, $meta_key, $meta_value, $unique );
    
    //add_option('yaloub_client_id', '', '', 'no');
    //add_option('yaloub_secret', '', '', 'no');
    //wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

function yaloub_uninstall() {
    //if uninstall not called from WordPress exit
    /*if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
        exit();

    $option_client_id = 'yaloub_client_id';
    $option_secret = 'yaloub_secret';

    // For Single site
    if ( !is_multisite() )
    {
        delete_option( $option_client_id );
        delete_option( $option_secret );
    } 
    // For Multisite
    else 
    {
        // For regular options.
        global $wpdb;
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        $original_blog_id = get_current_blog_id();
        foreach ( $blog_ids as $blog_id ) 
        {
            switch_to_blog( $blog_id );
            delete_option( $option_client_id );
            delete_option( $option_secret );
        }
        switch_to_blog( $original_blog_id );

        // For site options.
        delete_site_option( $option_client_id );
        delete_site_option( $option_secret );
    }*/
}

class Yaloub {

    protected $url_base;

    function __construct()
    {
        $url_env = get_site_url();
        $url_env_array = explode('/', $url_env, 4);
        $env = $url_env_array[2];

        if($env == 'localhost:8888') {
            $this->url_base = 'http://localhost:8888/Yaloub/public';
        } else {
            $this->url_base = '//yaloub.com';
        }
        
        add_filter( 'media_upload_tabs', array( &$this, 'yaloub_file_explorer_upload_tab' ), 10, 1 );
        add_action( 'media_upload_yaloub_file_explorer_tab', array( &$this, 'add_my_new_form' ) );
        add_action( 'admin_menu', array( &$this, 'yaloub_menu_settings' ) );
    }

    public function yaloub_file_explorer_upload_tab( $tab ) {
        $newtab = array('yaloub_file_explorer_tab' => 'Yaloub - File Explorer');
        return array_merge($tab,$newtab);
    }

    public function add_my_new_form() { ?>
        <style>body {margin: 0 !important;}</style>
        <iframe src="<?php echo $this->url_base;  ?>/widget/files" style="width:100%; height:100%; border: 0 none;"></iframe>
        <?php
    }

    /*add_action('shutdown','instrument_hooks');
    function instrument_hooks () {

    }*/

    /** Step 2 (from text above). */

    /** Step 1. */
    public function yaloub_menu_settings() {
        //add_options_page( 'Yaloub Plugin Options', 'Yaloub', 'manage_options', 'yaloub-settings', 'yaloub_plugin_options' );
        add_action( 'admin_init', array( &$this, 'register_yaloub_settings') );
    }

    /** Step 3. */
    /*public function yaloub_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        $client_id = get_option('yaloub_client_id', '');
        $secret = get_option('yaloub_secret', '');

        echo '<div class="wrap">
              <h2>Yaloub Plugin Settings</h2>
              <br><br>
                <form method="post" action="options.php">';
                settings_fields( 'yaloub-settings-group' );
                do_settings_sections( 'yaloub-settings-group' );
                echo '
                  <table class="form-table">
                    <tbody>
                    <tr valign="top">
                    <th scope="row"><label for="yaloub_client_id">Client ID</label></th>
                    <td><input name="yaloub_client_id" type="text" id="yaloub_client_id" value="' . $client_id . '" class="regular-text">
                    </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><label for="yaloub_secret">Secret</label></th>
                    <td><input name="yaloub_secret" type="text" id="yaloub_secret" value="' . $secret . '" class="regular-text">
                    </td>
                    </tr>
                    <tr><td colspan="2" class="description" style="padding: 25px 0 10px 0;">Your keys is found inside your <a href="https://yaloub.com/user" target="_blank">Yaloub account settings</a>.</td>
                    </tr>
                    </tbody>
                  </table>
                  <br>
                    <button id="yaloub-test-connection" class="button">Test connection</button>';
                    echo submit_button();
                echo '</form>
              </div>';
    }*/

    public function register_yaloub_settings() { // whitelist options
      register_setting( 'yaloub-settings-group', 'yaloub_client_id' );
      register_setting( 'yaloub-settings-group', 'yaloub_secret' );
      
      /*add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );*/

        add_settings_field(
            'yaloub_client_id', // ID
            'Client ID', // Title
            'update_client_id', // Callback
            'yaloub-settings' // Page          
        );

        add_settings_field(
            'yaloub_secret',
            'Secret',
            'yaloub_update_secret',
            'yaloub-settings'
        );
    }

    public function update_client_id() {
        //update_option('yaloub_client_id', '');

        //update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
    }

    public function update_secret() {
        //update_option('yaloub_secret', '');

        //update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
    }   

}

new Yaloub();

/*add_filter( 'plupload_init', 'yaloub_plupload_init');
function yaloub_plupload_init($val) {
    var_dump($val);
}*/

/*function wpse_76980_media_upload() {
    return '<div style="border: 1px solid #000"><iframe src="http://localhost:8888/Yaloub/public/"></iframe></div>';
}
add_action( 'media_upload_tab_slug', 'wpse_76980_media_upload' );*/

/*add_filter('media_view_strings', 'custom_media_string', 10, 2);
function custom_media_string($strings,  $post){
    $strings['customMenuTitle'] = __('Custom Menu Title', 'custom');
    $strings['customButton'] = __('Custom Button', 'custom');
    return $strings;
}*/

/**
 * Plugin Name: Custom Upload UI
 */
class CustomUploadUI {

  static function getLabel() {
    // change here the label of your custom upload button
    return 'Yaloub - File Explorer';
  }

  static function getUrl() {
    // change here the url of your custom upload button
    return add_query_arg( array('page'=>'yaloub-upload'), admin_url('upload.php') );
  }

  public function render() {
    // this is the function that render your custom upload system
    if ( ! current_user_can( 'upload_files' ) ) {
      echo '<h2>Sorry, you are not allowed to upload files.</h2>';
      return;
    }
  ?>
    <!--<style>
      #wpfooter {
          display: none;
      }
    </style>-->
    <div class="wrap" style="height:100% !important;">
    <h2>Yaloub - File Explorer</h2>
    <br/>
    <iframe src="http://localhost:8888/Yaloub/public/widget/files" style="width: 100%; height:80%; border: 0 none;" id="yaloub-explorer-files-iframe"></iframe>
    <!--<script>jquery( document ).ready(function() {console.log($(window).height());});</script>-->
    <script>
            jQuery(document).ready(function(){
                //set the initial body width
                var originalWidth = jQuery('body').width();
                /*I need to go through all target divs because i don't know
                how many divs are here and what are their initial height*/

                function resize() {
                    //This will only set this._originalHeight once
                    var wrap = jQuery('.wrap');
                    var top = jQuery('#yaloub-explorer-files-iframe').position()['top'];
                    var height = jQuery('body').height();
                    /*console.log(this._originalHeight);
                    //get the new body width
                    var bodyWidth = jQuery("body").width(); 
                    //get the difference in width, needed for hight calculation 
                    var widthDiff = bodyWidth - originalWidth; 
                    //new hight based on initial div height
                    var newHeight = this._originalHeight + (widthDiff / 10);*/
                    //sets the different height for every needed div
                    //jQuery(this).css();
                    //console.log(height);
                    //jQuery('#aloub-explorer-files-iframe').css("height", height);
                    jQuery('#yaloub-explorer-files-iframe').height(height - (top));
                }

                //jQuery(".target").each(resize);
                jQuery(window).resize(function(){
                    //jQuery(".target").each(resize);
                    //resize();
                });
              });
        </script>
    </div>
  <?php
  }

  public function __construct() {
    add_action('load-upload.php', array( &$this, 'indexButton' ));
    add_action('admin_menu', array( &$this, 'submenu' ) );
    add_action( 'wp_before_admin_bar_render', array( &$this, "adminBar" ) );
    add_action('post-plupload-upload-ui', array( &$this, 'mediaButton' ));
    //add_filter('admin_footer_text', array( &$this, 'yaloub_media_custom_footer' ), 9999 );
    //add_filter( 'update_footer', array( &$this, 'yaloub_media_custom_footer_version'), 9999 );
  }

  public function yaloub_media_custom_footer() {
      echo '';
  }

  public function yaloub_media_custom_footer_version() {
      echo "";
  }

  public function submenu() {
    // plugin_dir_url( __FILE__ )
    add_media_page( self::getLabel(), self::getLabel(), 'upload_files', 'yaloub-upload', array(&$this, 'render') );
    add_menu_page( 'Yaloub', 'Yaloub', 'upload_files', 'yaloub', array(&$this, 'yaloub_admin_page') );
    add_action( 'admin_init', array( &$this, 'register_yaloub_settings') );
  }

  public function yaloub_admin_page() {

    if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

    //get_user_meta($user_id, $key, $single);
    $client_id = get_option('yaloub_client_id', '');
    $secret = get_option('yaloub_secret', '');

      echo '<div class="wrap">
    <h2>Yaloub - Settings</h2>
    <br/><br>
        <form method="post" action="options.php">';
        settings_fields( 'yaloub-settings-group' );
        do_settings_sections( 'yaloub-settings-group' );
        echo '
          <table class="form-table">
            <tbody>
            <tr valign="top">
            <th scope="row"><label for="yaloub_client_id">Client ID</label></th>
            <td><input name="yaloub_client_id" type="text" id="yaloub_client_id" value="' . $client_id . '" class="regular-text">
            </td>
            </tr>
            <tr valign="top">
            <th scope="row"><label for="yaloub_secret">Secret</label></th>
            <td><input name="yaloub_secret" type="text" id="yaloub_secret" value="' . $secret . '" class="regular-text">
            </td>
            </tr>
            <tr><td colspan="2" class="description" style="padding: 25px 0 10px 0;">Your keys is found inside your <a href="https://yaloub.com/user" target="_blank">Yaloub account settings</a>.</td>
            </tr>
            </tbody>
          </table>
          <br>
            <button id="yaloub-test-connection" class="button">Test connection</button>';
            echo submit_button();
        echo '</form>
      </div>';
  }

  public function register_yaloub_settings() { // whitelist options
      register_setting( 'yaloub-settings-group', 'yaloub_client_id' );
      register_setting( 'yaloub-settings-group', 'yaloub_secret' );
      
      /*add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );*/

        add_settings_field(
            'yaloub_client_id', // ID
            'Client ID', // Title
            'update_client_id', // Callback
            'yaloub-settings' // Page          
        );

        add_settings_field(
            'yaloub_secret',
            'Secret',
            'yaloub_update_secret',
            'yaloub-settings'
        );
    }

    public function update_client_id() {
        update_option('yaloub_client_id', '');
    }

    public function update_secret() {
        update_option('yaloub_secret', '');
    }

  public function adminBar() {
    if ( ! current_user_can( 'upload_files' ) || ! is_admin_bar_showing() ) return;
    global $wp_admin_bar;
    $wp_admin_bar->add_node( array(
      'parent' => 'new-content',
      'id' => 'custom-upload-link',
      'title' => self::getLabel(),
      'href' => self::getUrl()
    ) );
  }


  public function mediaButton() {
    if ( current_user_can( 'upload_files' ) ) {
      echo '<div><p align="center">';
      echo '<input id="custom-browse-button" type="button" value="' . self::getLabel() . '" class="button" />';
      echo '</p></div>';
      $this->mediaButtonScript();
    }
  }

  public function mediaButtonScript() {
    if ( ! current_user_can( 'upload_files' ) ) return;
  ?>
    <script>
    jQuery(document).on('click', '#custom-browse-button', function(e) {
      e.preventDefault();
      window.location = '<?php echo self::getUrl(); ?>';
    });
    </script>
  <?php
  }

  public function indexButton() {
    if ( ! current_user_can( 'upload_files' ) ) return;
    add_filter( 'esc_html', array(__CLASS__, 'h2Button'), 999, 2 );
  }

  public static function h2Button( $safe_text, $text ) {
    if ( ! current_user_can( 'upload_files' ) ) return;
    if ( $text === __('Media Library') && did_action( 'all_admin_notices' ) ) {
      remove_filter( 'esc_html', array(__CLASS__, 'h2Button'), 999, 2 );
      $format = ' <a href="%s" class="add-new-h2">%s</a>';
      $mybutton = sprintf($format, esc_url(self::getUrl()), esc_html(self::getLabel()) );
      $safe_text .= $mybutton;
    }
    return $safe_text;
  }

}

$ui = new CustomUploadUI;

?>