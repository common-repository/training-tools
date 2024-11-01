<?php
/** 
 * Core WebTechGlobal plugin class. 
 * 
 * All classes and all functions are used in all WebTechGlobal plugins. Any 
 * function that requires a custom approach should be moved to the main package
 * file/class.  
 * 
 * @package WebTechGlobal WordPress Plugins
 * @author Ryan Bayne   
 * @version 1.0
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
                                           
class TRAININGTOOLS_WTG {
    /**
    * Error display and debugging 
    * 
    * When request will display maximum php errors including WordPress errors 
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 0.1
    */
    public function debugmode() {

        $debug_status = get_option( 'webtechglobal_displayerrors' );
        if( !$debug_status ){ return false; }
        
        // times when this error display is normally not  required
        if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) )
                || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
                || ( defined( 'DOING_CRON' ) && DOING_CRON )
                || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                    return false;
        }    
            
        global $wpdb;
        ini_set( 'display_errors',1);
        error_reporting(E_ALL);      
        if(!defined( "WP_DEBUG_DISPLAY") ){define( "WP_DEBUG_DISPLAY", true);}
        if(!defined( "WP_DEBUG_LOG") ){define( "WP_DEBUG_LOG", true);}
        //add_action( 'all', create_function( '', 'var_dump( current_filter() );' ) );
        //define( 'SAVEQUERIES', true );
        //define( 'SCRIPT_DEBUG', true );
        $wpdb->show_errors();
        $wpdb->print_error();
        
        // constant required for package - everything before now is global to all
        // of WordPress and the error display switch is global to all WTG plugins
        if(!defined( "WEBTECHGLOBAL_ERRORDISPLAY") ){define( "WEBTECHGLOBAL_ERRORDISPLAY", true );}
    }  
    
}  
?>
