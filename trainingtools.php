<?php         
/*
Plugin Name: Training Tools Beta
Version: 0.0.2
Plugin URI: http://www.webtechglobal.co.uk/training-tools/
Description: Tools for designing and delivering interactive user inerface training.
Author: WebTechGlobal
Author URI: http://www.webtechglobal.co.uk/
Last Updated: September 2015
Text Domain: trainingtools
Domain Path: /languages

GPL v3 

This program is free software downloaded from WordPress.org: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. This means
it can be provided for the sole purpose of being developed further
and we do not promise it is ready for any one persons specific needs.
See the GNU General Public License for more details.

See <http://www.gnu.org/licenses/>.
*/           
  
// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'Direct script access is not allowed!' );

// exit early if Training Tools doesn't have to be loaded
if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) // Login screen
    || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
    || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
    return;
}
    
// plugin package constants... 
if(!defined( "TRAININGTOOLS_VERSION") ){define( "TRAININGTOOLS_VERSION", '0.0.2' );}                          
if(!defined( "TRAININGTOOLS_NAME") ){define( "TRAININGTOOLS_NAME", 'Training Tools' );} 
if(!defined( "TRAININGTOOLS__FILE__") ){define( "TRAININGTOOLS__FILE__", __FILE__);}
if(!defined( "TRAININGTOOLS_BASENAME") ){define( "TRAININGTOOLS_BASENAME",plugin_basename( TRAININGTOOLS__FILE__ ) );}
if(!defined( "TRAININGTOOLS_ABSPATH") ){define( "TRAININGTOOLS_ABSPATH", plugin_dir_path( __FILE__) );}//C:\AppServ\www\wordpress-testing\wtgplugintemplate\wp-content\plugins\wtgplugintemplate/  
if(!defined( "TRAININGTOOLS_PHPVERSIONMINIMUM") ){define( "TRAININGTOOLS_PHPVERSIONMINIMUM", '5.3.0' );}// The minimum php version that will allow the plugin to work                                
if(!defined( "TRAININGTOOLS_IMAGES_URL") ){define( "TRAININGTOOLS_IMAGES_URL",plugins_url( 'images/' , __FILE__ ) );}
if(!defined( "TRAININGTOOLS_PORTAL" ) ){define( "TRAININGTOOLS_PORTAL", 'http://www.webtechglobal.co.uk/wtg-plugin-framework-wordpress/' );}
if(!defined( "TRAININGTOOLS_FORUM" ) ){define( "TRAININGTOOLS_FORUM", 'http://forum.webtechglobal.co.uk/viewforum.php?f=43' );}
if(!defined( "TRAININGTOOLS_TWITTER" ) ){define( "TRAININGTOOLS_TWITTER", 'http://www.twitter.com/WebTechGlobal' );}
if(!defined( "TRAININGTOOLS_FACEBOOK" ) ){define( "TRAININGTOOLS_FACEBOOK", 'https://www.facebook.com/WebTechGlobal1/' );}
if(!defined( "TRAININGTOOLS_YOUTUBEPLAYLIST" ) ){define( "TRAININGTOOLS_YOUTUBEPLAYLIST", 'https://www.youtube.com/playlist?list=PLMYhfJnWwPWAh49jnSfNRwR_HSfnhCdF4' );}

// WebTechGlobal constants applicable to all projects...
if(!defined( "WEBTECHGLOBAL_FULLINTEGRATION") ){define( "WEBTECHGLOBAL_FULLINTEGRATION", false );}// change to true to force tables and files to be shared among WTG plugins automatically
if(!defined( "WEBTECHGLOBAL_FORUM" ) ){define( "WEBTECHGLOBAL_FORUM", 'http://forum.webtechglobal.co.uk/' );}
if(!defined( "WEBTECHGLOBAL_TWITTER" ) ){define( "WEBTECHGLOBAL_TWITTER", 'http://www.twitter.com/WebTechGlobal/' );}
if(!defined( "WEBTECHGLOBAL_FACEBOOK" ) ){define( "WEBTECHGLOBAL_FACEBOOK", 'https://www.facebook.com/WebTechGlobal1/' );}
if(!defined( "WEBTECHGLOBAL_REGISTER" ) ){define( "WEBTECHGLOBAL_REGISTER", 'http://www.webtechglobal.co.uk/login/?action=register' );}
if(!defined( "WEBTECHGLOBAL_LOGIN" ) ){define( "WEBTECHGLOBAL_LOGIN", 'http://www.webtechglobal.co.uk/login/' );}
if(!defined( "WEBTECHGLOBAL_YOUTUBE" ) ){define( "WEBTECHGLOBAL_YOUTUBE", 'https://www.youtube.com/user/WebTechGlobal' );}

require_once( TRAININGTOOLS_ABSPATH . 'classes/class-webtechglobal.php' );
require_once( TRAININGTOOLS_ABSPATH . 'classes/class-trainingtools.php' );

$TRAININGTOOLS = new TRAININGTOOLS();
$TRAININGTOOLS->custom_post_types();

function trainingtools_textdomain() {
    load_plugin_textdomain( 'trainingtools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action( 'plugins_loaded', 'trainingtools_textdomain' );                                                                                                       
?>