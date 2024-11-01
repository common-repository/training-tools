<?php
/** 
* Install, uninstall, repair
* 
* The section array can be used to prevent installation of per section elements before activation of the plugin.
* Once activation has been done, section switches can be used to change future activation. This is early stuff
* so not sure if it will be of use.
* 
* @package Training Tools
* @author Ryan Bayne   
* @since 0.0.1
* @version 1.3
*/

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class TRAININGTOOLS_Install {
    
    /**
    * Install __construct persistently registers database tables and is the
    * first point to monitoring installation state 
    */
    public function __construct() {

        // load class used at all times
        // $this->DB = new TRAININGTOOLS_DB(); commeted 14092014
        $this->DB = TRAININGTOOLS::load_class( 'TRAININGTOOLS_DB', 'class-wpdb.php', 'classes' );
        $this->PHP = new TRAININGTOOLS_PHP();
                
        // on activation run install_plugin() method which then runs more methods i.e. create_tables();
        register_activation_hook( TRAININGTOOLS_ABSPATH . 'trainingtools.php', array( $this, 'install_plugin' ) ); 

        // on deactivation run disabled_plugin() - not a full uninstall
        register_deactivation_hook( TRAININGTOOLS_ABSPATH . 'trainingtools.php',  array( $this, 'deactivate_plugin' ) );
        
        // register webtechglobal_log table
        add_action( 'init', array( $this, 'register_webtechglobal_log_table' ) );
        add_action( 'switch_blog', array( $this, 'register_webtechglobal_log_table' ) );
        $this->register_webtechglobal_log_table(); // register tables manually as the hook may have been missed             
         
        // register webtechglobal_projects table
        add_action( 'init', array( $this, 'register_webtechglobal_projects_table' ) );
        add_action( 'switch_blog', array( $this, 'register_webtechglobal_projects_table' ) );
        $this->register_webtechglobal_projects_table(); // register tables manually as the hook may have been missed             
        
        // register tt_pages table
        add_action( 'init', array( $this, 'register_tt_pages_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_pages_table' ) );
        $this->register_tt_pages_table(); // register tables manually as the hook may have been missed             

        // register tt_pagesmeta table
        add_action( 'init', array( $this, 'register_tt_pagesmeta_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_pagesmeta_table' ) );
        $this->register_tt_pagesmeta_table(); // register tables manually as the hook may have been missed             
         
        // register tt_postboxes table
        add_action( 'init', array( $this, 'register_tt_postboxes_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_forms_table' ) );
        $this->register_tt_postboxes_table(); // register tables manually as the hook may have been missed             
        
        // register tt_postboxesmeta table
        add_action( 'init', array( $this, 'register_tt_postboxesmeta_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_formsmeta_table' ) );
        $this->register_tt_postboxesmeta_table(); // register tables manually as the hook may have been missed             
         
        // register tt_forms table
        add_action( 'init', array( $this, 'register_tt_forms_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_forms_table' ) );
        $this->register_tt_forms_table(); // register tables manually as the hook may have been missed             
        
        // register tt_formsmeta table
        add_action( 'init', array( $this, 'register_tt_formsmeta_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_formsmeta_table' ) );
        $this->register_tt_formsmeta_table(); // register tables manually as the hook may have been missed                  
        
        // register tt_inputs table
        add_action( 'init', array( $this, 'register_tt_inputs_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_inputs_table' ) );
        $this->register_tt_inputs_table(); // register tables manually as the hook may have been missed                  
        
        // register tt_inputsmeta table
        add_action( 'init', array( $this, 'register_tt_inputsmeta_table' ) );
        add_action( 'switch_blog', array( $this, 'register_tt_inputsmeta_table' ) );
        $this->register_tt_inputsmeta_table(); // register tables manually as the hook may have been missed                  
    }

    function register_webtechglobal_log_table() {
        global $wpdb;
        $wpdb->webtechglobal_log = "{$wpdb->prefix}webtechglobal_log";
    }    
    
    function register_webtechglobal_projects_table() {
        global $wpdb;
        $wpdb->webtechglobal_projects = "{$wpdb->prefix}webtechglobal_projects";
    }

    function register_tt_pages_table() {
        global $wpdb;
        $wpdb->tt_pages = "{$wpdb->prefix}tt_pages";
    }

    function register_tt_pagesmeta_table() {
        global $wpdb;
        $wpdb->tt_pagesmeta = "{$wpdb->prefix}tt_pagesmeta";
    }

    function register_tt_postboxes_table() {
        global $wpdb;
        $wpdb->tt_postboxes = "{$wpdb->prefix}tt_postboxes";
    }        

    function register_tt_postboxesmeta_table() {
        global $wpdb;
        $wpdb->tt_postboxesmeta = "{$wpdb->prefix}tt_postboxesmeta";
    }

    function register_tt_forms_table() {
        global $wpdb;
        $wpdb->tt_forms = "{$wpdb->prefix}tt_forms";
    }        

    function register_tt_formsmeta_table() {
        global $wpdb;
        $wpdb->tt_formsmeta = "{$wpdb->prefix}tt_formsmeta";
    }
    
    function register_tt_inputs_table() {
        global $wpdb;
        $wpdb->tt_inputs = "{$wpdb->prefix}tt_inputs";
    }
    
    function register_tt_inputsmeta_table() {
        global $wpdb;
        $wpdb->tt_inputsmeta = "{$wpdb->prefix}tt_inputsmeta";
    }
            
    /**
    * Creates the plugins database tables
    *
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.3
    */
    function create_tables() {      
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );         
        self::webtechglobal_log();
        self::webtechglobal_projects();
        self::tt_pages();
        self::tt_pagesmeta();
        self::tt_postboxes();
        self::tt_postboxesmeta();        
        self::tt_forms();
        self::tt_formsmeta();
        self::tt_inputs();
        self::tt_inputsmeta();
    }
    
    /**
    * Global WebTechGlobal log table as used in all WTG plugins.
    * This approach helps to keep the database tidy, while still providing
    * an still improving log system and with all log entries in a single table.
    * Behaviours relating to integration of these plugins can be spotted easier.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.3
    * @version 1.0
    */
    public function webtechglobal_log() {
        global $charset_collate,$wpdb;
        
        $sql_create_table = "CREATE TABLE {$wpdb->webtechglobal_log} (
        row_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        service varchar(250) DEFAULT 'trainingtools',
        outcome tinyint(1) unsigned NOT NULL DEFAULT 1,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        line int(11) unsigned DEFAULT NULL,
        file varchar(250) DEFAULT NULL,
        function varchar(250) DEFAULT NULL,
        sqlresult blob,sqlquery varchar(45) DEFAULT NULL,
        sqlerror mediumtext,
        wordpresserror mediumtext,
        screenshoturl varchar(500) DEFAULT NULL,
        userscomment mediumtext,
        page varchar(45) DEFAULT NULL,
        version varchar(45) DEFAULT NULL,
        panelid varchar(45) DEFAULT NULL,
        panelname varchar(45) DEFAULT NULL,
        tabscreenid varchar(45) DEFAULT NULL,
        tabscreenname varchar(45) DEFAULT NULL,
        dump longblob,ipaddress varchar(45) DEFAULT NULL,
        userid int(11) unsigned DEFAULT NULL,
        comment mediumtext,type varchar(45) DEFAULT NULL,
        category varchar(45) DEFAULT NULL,
        action varchar(45) DEFAULT NULL,
        priority varchar(45) DEFAULT NULL,
        triga varchar(45) DEFAULT NULL,
        PRIMARY KEY (row_id) ) $charset_collate; ";
        
        dbDelta( $sql_create_table );   
        
        // row_id
        // service - the plugin, theme or web service triggering log entry
        // outcome - set a positive (1) or negative (0) outcome
        // timestamp
        // line - __LINE__
        // file - __FILE__
        // function - __FUNCTION__
        // sqlresult - return from the query (dont go mad with this and store large or sensitive data where possible)
        // sqlquery - the query as executed
        // sqlerror - if failed MySQL error in here
        // wordpresserror - if failed store WP error
        // screenshoturl - if screenshot taking and uploaded
        // userscomment - if user is testing they can submit a comment with error i.e. what they done to cause it
        // page - plugin page ID i.e. c2pdownloads
        // version - version of the plugin (plugin may store many logs over many versions)
        // panelid - (will be changed to formid i.e. savebasicsettings)
        // panelname - (will be changed to formname i.e Save Basic Settings)
        // tabscreenid - the tab number i.e. 0 or 1 or 5
        // tabscreenname - the on screen name of the tab in question, if any i.e. Downloads Overview
        // dump - anything the developer thinks will help with debugging or training
        // ipaddress - security side of things, record who is using the site
        // userid - if user logged into WordPress
        // comment - developers comment in-code i.e. recommendation on responding to the log entry
        // type - general|error|trace
        // category - any term that suits the section or system
        // action - what was being attempted, if known 
        // priority - low|medium|high (low should be default, medium if the log might help improve the plugin or user experience or minor PHP errors, high for critical errors especially security related
        // triga - (trigger but that word is taking) not sure we need this        
    }
        
    /**
    * Create WTG global projects table as used with many plugins.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.3
    * @version 1.0
    * 
    * @todo requires unique or consraint on project name
    */
    public function webtechglobal_projects() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                          
        $sql_create_table = "CREATE TABLE {$wpdb->webtechglobal_projects} (
        project_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        projectname varchar(250) DEFAULT NULL,
        description mediumtext,
        mainmanager varchar(45) DEFAULT NULL,
        phase varchar(45) DEFAULT NULL, 
        archived tinyint(1) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (project_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // project_id 
        // timestamp
        // projectname
        // description
        // mainmanager
        // phase    
        // archived (boolean) - archived projects wont show on most interfaces        
    }

    /**
    * Table for holding registered UI pages. The ID
    * of a page is used in all other tables and is 
    * important for integration.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_pages() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                           
        $sql_create_table = "CREATE TABLE {$wpdb->tt_pages} (
        ttpage_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        project_id bigint(20) unsigned NOT NULL,
        view_id bigint(20) unsigned NOT NULL,
        title varchar(255) DEFAULT NULL,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (ttpage_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // ttpage_id 
        // project_id
        // view_id
        // timestamp
    }

    /**
    * Meta data for registered pages.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_pagesmeta() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                          
        $sql_create_table = "CREATE TABLE {$wpdb->tt_pagesmeta} (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tt_pages_id bigint(20),
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (meta_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // meta_id 
        // tt_pages_id (not to be mistaking for PHP package page ID)
        // meta_key
        // meta_value
        // timestamp
    }
   
    /**
    * Table holds registered postboxes and other
    * areas with a HTML ID attribute that include 
    * grouped features such as forms.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_postboxes() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                                   
        $sql_create_table = "CREATE TABLE {$wpdb->tt_postboxes} (
        area_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        postbox_id bigint(20),
        postbox_title varchar(250),
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (area_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // area_id - auto generated 
        // postbox_id - the php ID of WordPress postbox or another area i.e. div, frame
        // postbox_title - string user readable title (mainly for quick queries and browsing database results)
        // timestamp
    }

    /**
    * Meta data for postboxes.
    * 
    * This table will hold special treatment for an area i.e. applying a tooltip
    * to the general area (postbox in WordPress usually) rather than a specific
    * feature within the area.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_postboxesmeta() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                                    
        $sql_create_table = "CREATE TABLE {$wpdb->tt_postboxesmeta} (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tt_postboxes_id bigint(20),
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (meta_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // meta_id
        // tt_postboxes_id - also known as area id, this is the php ID of a postbox/div/other area
        // meta_key
        // meta_value 
        // timestamp
    
    }
                   
    /**
    * Table holds registered postboxes and other
    * areas that include grouped features.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_forms() {
        global $charset_collate,$wpdb;
                                                                                                                                                                                                                                                                                                                                                                                                   
        $sql_create_table = "CREATE TABLE {$wpdb->tt_forms} (
        dbform_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        location_id bigint(20),
        location_type varchar(25),
        form_id varchar(50),
        form_name varchar(50),
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (dbform_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // dbform_id - auto incremented ID
        // location_id - area_id or ttpage_id
        // location_type - area wppostbox, standarddiv or htmlbody (not in div with an ID)
        // ---cont: this is used to determine if form has related area (postbox)
        // ---cont: data or is only related to a page (simply in html body)
        // timestamp
    }

    /**
    * Meta data for forms.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function tt_formsmeta() {
        global $charset_collate,$wpdb;
        
        // tt_formsmeta                                                                                                                                                                                                                                                                                                                                                                                            
        $sql_create_table = "CREATE TABLE {$wpdb->tt_formsmeta} (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tt_formsmeta_id bigint(20),
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (meta_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
         
        // meta_id
        // tt_formsmeta_id 
        // meta_key
        // meta_value
        // timestamp
    }

    /**
    * Form inputs.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function tt_inputs() { 
        global $charset_collate,$wpdb;
      
        $sql_create_table = "CREATE TABLE {$wpdb->tt_inputs} (
        dbinput_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        form_id bigint(20),
        att_id varchar(50),
        att_name varchar(50),
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (dbform_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );   
        
        // dbinput_id
        // form_id
        // att_id (form inputs HTML ID)
        // att_name (form inputs HTML name)
        // timestamp   
    }
    
    /**
    * Meta data for form inputs.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function tt_inputsmeta() {   
        global $charset_collate,$wpdb;
        
        // tt_inputsmeta                                                                                                                                                                                                                                                                                                                                                                                            
        $sql_create_table = "CREATE TABLE {$wpdb->tt_inputsmeta} (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tt_inputs_id bigint(20),
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (meta_id) 
        ) $charset_collate; ";
        
        dbDelta( $sql_create_table );  
        
        // meta_id
        // tt_inputs_id - unique DB generated ID of a single input
        // meta_key
        // meta_value
        // timestamp      
    }
                                                
    /**
    * reinstall all database tables in one go 
    */
    public function reinstalldatabasetables() {
        global $wpdb;
        
        require_once( TRAININGTOOLS_ABSPATH . 'arrays/tableschema_array.php' );
        
        if(is_array( $trainingtools_tables_array ) ){
            foreach( $trainingtools_tables_array['tables'] as $key => $table){
                
                // do not use a $this->prefix setup here as it could be risky
                if( $this->DB->does_table_exist( $table['name'] ) ){         
                    $wpdb->query( 'DROP TABLE '. $table['name'] );
                }        
                                                                     
            }
        } 
        
        return $this->create_tables();
    } 
    
    function install_options() {
        // installation state values
        update_option( 'trainingtools_installedversion', TRAININGTOOLS::version );# will only be updated when user prompted to upgrade rather than activation
        update_option( 'trainingtools_installeddate',time() );# update the installed date, this includes the installed date of new versions
        
        // schedule settings
        require( TRAININGTOOLS_ABSPATH . 'arrays/schedule_array.php' );        
        add_option( 'trainingtools_schedule', serialize( $trainingtools_schedule_array ) );

        // notifications array (persistent notice feature)
        add_option( 'trainingtools_notifications', serialize( array() ) ); 
    }
    
    function install_plugin() {              
        $this->create_tables();
        $this->install_options();
        // if this gets installed we know we arrived here in the installation procedure
        update_option( 'trainingtools_is_installed', true );
    } 
    
    /**
    * Deactivate plugin - can use it for uninstall but usually not
    * 1. can use to cleanup WP CRON schedule, remove plugins scheduled events
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    function deactivate_plugin() {
        
    }            
}
?>