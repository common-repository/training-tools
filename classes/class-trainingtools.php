<?php  
/** 
 * Main package file.
 * 
 * All functions unique to the package go here.      
 * 
 * @package Training Tools
 * @author Ryan Bayne   
 * @since 0.0.1
 * @version 1.0
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
                                           
class TRAININGTOOLS extends TRAININGTOOLS_WTG {
    
    /**
     * Page hooks (i.e. names) WordPress uses for the TRAININGTOOLS admin screens,
     * populated in add_admin_menu_entry()
     *
     * @since 0.0.1
     *
     * @var array
     */
    protected $page_hooks = array();
    
    /**
     * TRAININGTOOLS version
     *
     * Increases everytime the plugin changes
     *
     * @since 0.0.1
     *
     * @const string
     */
    const version = '0.0.1';
    
    /**
     * TRAININGTOOLS major version
     *
     * Increases on major releases
     *
     * @since 0.0.1
     *
     * @const string
     */    
    const majorversion = 'Beta';  
    
    protected
        $filters = array(),
        $actions = array(),    
                                
        // add_action() controller
        // Format: array( event | function in this class(in an array if optional arguments are needed) | loading circumstances)
        // Other class requiring WordPress hooks start here also, with a method in this main class that calls one or more methods in one or many classes
        // create a method in this class for each hook required plugin wide
        $plugin_actions = array( 
            array( 'admin_menu',                     'set_admin_globals',                                      'all' ),        
            array( 'admin_menu',                     'admin_menu',                                             'all' ),
            array( 'admin_init',                     'process_admin_POST_GET',                                 'all' ),
            array( 'admin_init',                     'add_adminpage_actions',                                  'all' ), 
            //array( 'init',                           'event_check',                                            'all' ),
            //array( 'eventcheckwpcron',               'eventcheckwpcron',                                       'all' ),
            //array( 'event_check_servercron',         'event_check_servercron',                                 'all' ),
            array( 'wp_dashboard_setup',             'add_dashboard_widgets',                                  'all' ),
            array( 'wp_insert_post',                 'hook_insert_post',                                       'all' ),
            array( 'admin_footer',                   'pluginmediabutton_popup',                                'pluginscreens' ),
            array( 'media_buttons_context',          'pluginmediabutton_button',                               'pluginscreens' ),
            array( 'admin_enqueue_scripts',          'plugin_admin_enqueue_scripts',                           'pluginscreens' ),
            array( 'init',                           'plugin_admin_register_styles',                           'pluginscreens' ),
            array( 'init',                           'debugmode',                                              'all' ),
            array( 'admin_print_styles',             'plugin_admin_print_styles',                              'pluginscreens' ),
            array( 'wp_enqueue_scripts',             'plugin_enqueue_public_styles',                           'publicpages' ),            
            array( 'admin_notices',                  'admin_notices',                                          'admin_notices' ),
            array( 'init',                           'plugin_shortcodes',                                      'all' ),
            array( 'wp_before_admin_bar_render',     array('admin_toolbars',999),                              'pluginscreens' ),
        ),        
                              
        $plugin_filters = array(
            /*
                Examples - last value are the sections the filter apply to
                    array( 'plugin_row_meta',                     array( 'examplefunction1', 10, 2),         'all' ),
                    array( 'page_link',                             array( 'examplefunction2', 10, 2),             'downloads' ),
                    array( 'admin_footer_text',                     'examplefunction3',                         'monetization' ),
                    
            */
        ),     
        
        $plugin_shorcodes = array (
            //array( 'shortcodepart',    'function' ),
        );     
        
    /**
    * This class is being introduced gradually, we will move various lines and config functions from the main file to load here eventually
    */
    public function __construct() {
        global $trainingtools_settings;
 
        // load class used at all times
        $this->DB = self::load_class( 'TRAININGTOOLS_DB', 'class-wpdb.php', 'classes' );
        $this->PHP = self::load_class( 'TRAININGTOOLS_PHP', 'class-phplibrary.php', 'classes' );
        $this->Install = self::load_class( 'TRAININGTOOLS_Install', 'class-install.php', 'classes' );
        $this->Files = self::load_class( 'TRAININGTOOLS_Files', 'class-files.php', 'classes' );
  
        $trainingtools_settings = self::adminsettings();
  
        $this->add_actions();
        $this->add_filters();

        if( is_admin() ){
        
            // admin globals 
            global $trainingtools_notice_array;
            
            $trainingtools_notice_array = array();// set notice array for storing new notices in (not persistent notices)
            
            // load class used from admin only                   
            $this->UI = self::load_class( 'TRAININGTOOLS_UI', 'class-ui.php', 'classes' );
            $this->Helparray = self::load_class( 'TRAININGTOOLS_Help', 'class-help.php', 'classes' );        
        
        }            
    }
                            
    /**
    * Set variables that are required on most pages.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function set_admin_globals() {
        global $trainingtools_menu_array;
        
        // set menu array
        $TRAININGTOOLS_TabMenu = self::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        $trainingtools_menu_array = $TRAININGTOOLS_TabMenu->menu_array();   
        
        // set page name (it's my own approach, each tab/view has a name which is shorter than the WP view ID)
        $trainingtools_page_name = self::get_admin_page_name();             
    }
        
    /**
    * Registers shortcodes. 
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function plugin_shortcodes() { 
        foreach( $this->plugin_shorcodes as $shortcode )
        {
            add_shortcode( $shortcode[0], array( $this, $shortcode[1] ) );    
        }   
    }
        
    /**
    * register admin only .css must be done before printing styles
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function plugin_admin_register_styles() {
        wp_register_style( 'trainingtools_css_notification',plugins_url( 'trainingtools/css/notifications.css' ), array(), '1.0.0', 'screen' );
        wp_register_style( 'trainingtools_css_admin',plugins_url( 'trainingtools/css/admin.css' ), __FILE__);          
    }
    
    /**
    * print admin only .css - the css must be registered first
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function plugin_admin_print_styles() {
        wp_enqueue_style( 'trainingtools_css_notification' );  
        wp_enqueue_style( 'trainingtools_css_admin' );               
    }    
    
    /**
    * queues .js that is registered already
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function plugin_admin_enqueue_scripts() {
        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_style( 'wp-pointer' );          
    }    

    /**
     * Enqueue a CSS file with ability to switch from .min for debug
     *
     * @since 0.0.1
     *
     * @param string $name Name of the CSS file, without extension(s)
     * @param array $dependencies List of names of CSS stylesheets that this stylesheet depends on, and which need to be included before this one
     */
    public function enqueue_style( $name, array $dependencies = array() ) {
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        $css_file = "css/{$name}{$suffix}.css";
        $css_url = plugins_url( $css_file, TRAININGTOOLS__FILE__ );
        wp_enqueue_style( "trainingtools-{$name}", $css_url, $dependencies, TRAININGTOOLS::version );
    }
    
    /**
     * Enqueue a JavaScript file, can switch from .min for debug,
     * possibility with dependencies and extra information
     *
     * @since 0.0.1
     *
     * @param string $name Name of the JS file, without extension(s)
     * @param array $dependencies List of names of JS scripts that this script depends on, and which need to be included before this one
     * @param bool|array $localize_script (optional) An array with strings that gets transformed into a JS object and is added to the page before the script is included
     * @param bool $force_minified Always load the minified version, regardless of SCRIPT_DEBUG constant value
     */
    public function enqueue_script( $name, array $dependencies = array(), $localize_script = false, $force_minified = false ) {
        $suffix = ( ! $force_minified && defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        $js_file = "js/{$name}{$suffix}.js";
        $js_url = plugins_url( $js_file, TRAININGTOOLS__FILE__ );
        wp_enqueue_script( "trainingtools-{$name}", $js_url, $dependencies, TRAININGTOOLS::version, true );
    }  
    
    /**
    * Register and enqueue CSS for public pages. This method is all that is needed
    * from within this plugin to applying styling to none admin pages.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function plugin_enqueue_public_styles() {    
        wp_register_style( 'wtgportalmanager_css_public',plugins_url( 'wtgportalmanager/css/public.css' ), __FILE__);
        wp_enqueue_style( 'wtgportalmanager_css_public' );
    }
            
    /**
    * Create a new instance of the $class, which is stored in $file in the $folder subfolder
    * of the plugin's directory.
    * 
    * One bad thing about using this is suggestive code does not work on the object that is returned
    * making development a little more difficult. This behaviour is experienced in phpEd 
    *
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    *
    * @param string $class Name of the class
    * @param string $file Name of the PHP file with the class
    * @param string $folder Name of the folder with $class's $file
    * @param mixed $params (optional) Parameters that are passed to the constructor of $class
    * @return object Initialized instance of the class
    */
    public static function load_class( $class, $file, $folder, $params = null ) {
        /**
         * Filter name of the class that shall be loaded.
         *
         * @since 0.0.1
         *
         * @param string $class Name of the class that shall be loaded.
         */        
        $class = apply_filters( 'trainingtools_load_class_name', $class );
        if ( ! class_exists( $class ) ) {   
            self::load_file( $file, $folder );
        }
        
        // we can avoid creating a new object, we can use "new" after the load_class() line
        // that way functions in the lass are available in code suggestion
        if( is_array( $params ) && in_array( 'noreturn', $params ) ){
            return true;   
        }
        
        $the_class = new $class( $params );
        return $the_class;
    }
    
    /**
    * returns the TRAININGTOOLS_WPMain class object already created in this TRAININGTOOLS class
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function class_wpmain() {
        return $this->wpmain;
    }

    /**
     * Load a file with require_once(), after running it through a filter
     *
     * @since 0.0.1
     *
     * @param string $file Name of the PHP file with the class
     * @param string $folder Name of the folder with $class's $file
     */
    public static function load_file( $file, $folder ) {      
        $full_path = TRAININGTOOLS_ABSPATH . $folder . '/' . $file;
        
        /**
         * Filter the full path of a file that shall be loaded.
         *
         * @since 0.0.1
         *
         * @param string $full_path Full path of the file that shall be loaded.
         * @param string $file      File name of the file that shall be loaded.
         * @param string $folder    Folder name of the file that shall be loaded.
         */
        $full_path = apply_filters( 'trainingtools_load_file_full_path', $full_path, $file, $folder );
        if ( $full_path ) {   
            require_once $full_path;
        }
    }  
    
    /**  
     * Set up actions for each page
     *
     * @since 0.0.1
     */
    public function add_adminpage_actions() {
        // register callbacks to trigger load behavior for admin pages
        foreach ( $this->page_hooks as $page_hook ) {
            add_action( "load-{$page_hook}", array( $this, 'load_admin_page' ) );
        }
    }
        
    /**
     * Render the view that has been initialized in load_admin_page() (called by WordPress when the actual page content is needed)
     *
     * @since 0.0.1
     */
    public function show_admin_page() {   
        $this->view->render();
    }    
    
    /**
     * Create a new instance of the $view, which is stored in the "views" subfolder, and set it up with $data
     * 
     * Requires a main view file to be stored in the "views" folder, unlike the original view approach.
     * 
     * Do not move this to another file not even interface classes 
     *
     * @since 0.0.1
     * @uses load_class()
     *
     * @param string $view Name of the view to load
     * @param array $data (optional) Parameters/PHP variables that shall be available to the view
     * @return object Instance of the initialized view, already set up, just needs to be render()ed
     */
    public static function load_draggableboxes_view( $page_slug, array $data = array() ) {
        // include the view class
        require_once( TRAININGTOOLS_ABSPATH . 'classes/class-view.php' );
        
        // make first letter uppercase for a better looking naming pattern
        $ucview = ucfirst( $page_slug );// this is page name 
        
        // get the file name using $page and $tab_number
        $dir = 'views';
        
        // include the view file and run the class in that file                                
        $the_view = self::load_class( "TRAININGTOOLS_{$ucview}_View", "{$page_slug}.php", $dir );
                       
        $the_view->setup( $page_slug , $data );
        
        return $the_view;
    }

    /**
     * Generate the complete nonce string, from the nonce base, the action and an item
     *
     * @since 0.0.1
     *
     * @param string $action Action for which the nonce is needed
     * @param string|bool $item (optional) Item for which the action will be performed, like "table"
     * @return string The resulting nonce string
     */
    public static function nonce( $action, $item = false ) {
        $nonce = "trainingtools_{$action}";
        if ( $item ) {
            $nonce .= "_{$item}";
        }
        return $nonce;
    }
    
    /**
     * Begin render of admin screen
     * 1. determining the current action
     * 2. load necessary data for the view
     * 3. initialize the view
     * 
     * @uses load_draggableboxes_view() which includes class-view.php
     * 
     * @author Ryan Bayne
     * @package Training Tools
     * @since 0.0.1
     * @version 1.1
     */
    public function load_admin_page() {
        // remove "trainingtools_" from page value in URL which leaves the page name as used in the menu array
        $page = 'main';
        if( isset( $_GET['page'] ) && $_GET['page'] !== 'trainingtools' ){    
            $page = substr( $_GET['page'], strlen( 'trainingtools_' ) );
        }

        // pre-define data for passing to views
        $data = array( 'datatest' => 'A value for testing' );

        // depending on page load extra data
        switch ( $page ) {          
            case 'updateplugin':
   
                break;            
            case 'betatesting':
                $data['mydatatest'] = 'Testing where this goes and how it can be used during call for ' . $page;
                break;
        }
          
        // prepare and initialize draggable panel view for prepared pages
        // if this method is not called the plugin uses the old view method
        $this->view = $this->load_draggableboxes_view( $page, $data );
    }   
                   
    protected function add_actions() {          
        foreach( $this->plugin_actions as $actionArray ) {        
            list( $action, $details, $whenToLoad) = $actionArray;
                                   
            if(!$this->filteraction_should_beloaded( $whenToLoad) ) {      
                continue;
            }
                 
            switch(count( $details) ) {         
                case 3:
                    add_action( $action, array( $this, $details[0] ), $details[1], $details[2] );     
                break;
                case 2:
                    add_action( $action, array( $this, $details[0] ), $details[1] );   
                break;
                case 1:
                default:
                    add_action( $action, array( $this, $details) );
            }
        }    
    }
    
    protected function add_filters() {
        foreach( $this->plugin_filters as $filterArray ) {
            list( $filter, $details, $whenToLoad) = $filterArray;
                           
            if(!$this->filteraction_should_beloaded( $whenToLoad) ) {
                continue;
            }
            
            switch(count( $details) ) {
                case 3:
                    add_filter( $filter, array( $this, $details[0] ), $details[1], $details[2] );
                break;
                case 2:
                    add_filter( $filter, array( $this, $details[0] ), $details[1] );
                break;
                case 1:
                default:
                    add_filter( $filter, array( $this, $details) );
            }
        }    
    }    
    
    /**
    * Should the giving action or filter be loaded?
    * 
    * 1. we can add security and check settings per case, the goal is to load on specific pages/areas
    * 2. each case is a section and we use this approach to load action or filter for specific section
    * 3. In early development all sections are loaded, this function is prep for a modular plugin
    * 4. addons will require core functions like this to be updated rather than me writing dynamic functions for any possible addons
    *  
    * @param mixed $whenToLoad
    */
    private function filteraction_should_beloaded( $whenToLoad) {
        $trainingtools_settings = $this->adminsettings();
          
        switch( $whenToLoad) {
            case 'all':    
                return true;
            break;
            case 'adminpages':
                // load when logged into admin and on any admin page
                if( is_admin() ){return true;}
                return false;    
            break;
            case 'pluginscreens':
       
                // load when on a Training Tools admin screen
                if( isset( $_GET['page'] ) && strstr( $_GET['page'], 'trainingtools' ) ){return true;}
                
                return false;    
            break;            
            case 'pluginanddashboard':

                if( self::is_dashboard() ) {
                    return true;    
                }

                if( isset( $_GET['page'] ) && strstr( $_GET['page'], 'trainingtools' ) ){
                    return true;
                }
                
                return false;    
            break;
            case 'projects':
                return true;    
            break;            
            case 'systematicpostupdating':  
                if(!isset( $trainingtools_settings['standardsettings']['systematicpostupdating'] ) || $trainingtools_settings['standardsettings']['systematicpostupdating'] != 'enabled' ){
                    return false;    
                }      
                return true;
            break;
            case 'admin_notices':                         

                if( self::is_dashboard() ) {
                    return true;    
                }
                                                           
                if( isset( $_GET['page'] ) && strstr( $_GET['page'], 'trainingtools' ) ){
                    return true;
                }
                                                                                                   
                return false;
            break;
        }

        return true;
    }   
    
    /**
    * Determine if on the dashboard page. 
    * 
    * $current_screen is not set early enough for calling in some actions. So use this
    * function instead.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function is_dashboard() {
        global $pagenow;
        // method one: check $pagenow value which could be "index.php" and that means the dashboard
        if( isset( $pagenow ) && $pagenow == 'index.php' ) { return true; }
        // method two: should $pagenow not be set, check the server value
        return strstr( $this->PHP->currenturl(), 'wp-admin/index.php' );
    }
       
    /**
    * Admin toolbar for developers.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    function admin_toolbars() {   
        // admin only
        if( user_can( get_current_user_id(), 'activate_plugins' ) ) {
            self::developer_toolbar();
        }        
    }
    
    /**
    * Admin toolbar for developers.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    function developer_toolbar() {

        global $wp_admin_bar;
        
        //
        $args = array(
            'id'     => 'webtechglobal-toolbarmenu-developers',
            'title'  => __( 'Developers', 'text_domain' ),          
        );
        $wp_admin_bar->add_menu( $args );
        
        // error display switch        
        $href = wp_nonce_url( admin_url() . 'admin.php?page=' . $_GET['page'] . '&trainingtoolsaction=' . 'debugmodeswitch'  . '', 'debugmodeswitch' );
        $debug_status = get_option( 'webtechglobal_displayerrors' );
        if($debug_status){
            $error_display_title = __( 'Hide Errors', 'trainingtools' );
        } else {
            $error_display_title = __( 'Display Errors', 'trainingtools' );
        }
        $args = array(
            'id'     => 'webtechglobal-toolbarmenu-errordisplay',
            'parent' => 'webtechglobal-toolbarmenu-developers',
            'title'  => $error_display_title,
            'href'   => $href,            
        );
        $wp_admin_bar->add_menu( $args );

        // reinstall plugin settings array 
        $thisaction = 'trainingtoolsreinstallsettings';    
        $href = wp_nonce_url( admin_url() . 'admin.php?page=' . $_GET['page'] . '&trainingtoolsaction=' . $thisaction, $thisaction );
        $args = array(
            'id'     => 'webtechglobal-toolbarmenu-reinstallsettings',
            'parent' => 'webtechglobal-toolbarmenu-developers',
            'title'  => __( 'Re-Install Settings', 'trainingtools' ),
            'href'   => $href,            
        );
        $wp_admin_bar->add_menu( $args );
        
        // reinstall all database tables
        $thisaction = 'trainingtoolsreinstalltables';
        $href = wp_nonce_url( admin_url() . 'admin.php?page=' . $_GET['page'] . '&trainingtoolsaction=' . $thisaction, $thisaction );
        $args = array(
            'id'     => 'webtechglobal-toolbarmenu-reinstallalldatabasetables',
            'parent' => 'webtechglobal-toolbarmenu-developers',
            'title'  => __( 'Re-Install Tables', 'trainingtools' ),
            'href'   => $href,            
        );
        $wp_admin_bar->add_menu( $args );        

    }

    /**
    * "The wp_insert_post action is called with the same parameters as the save_post action 
    * (the post ID for the post being created), but is only called for new posts and only 
    * after save_post has run." 
    * 
    * @author Ryan R. Bayne
    * @package CSV 2 POST
    * @since 0.0.1
    * @version 1.0
    */
    public function hook_insert_post( $post_id ){
        /*
        // establish correct procedure for the post type that was inserted
        $post_type = get_post_type( $post_id );
      
        switch ( $post_type) {
            case 'exampleone':
                
                break;
            case 'c2pnotinuseyet':
                
                break;
        } 
        */
    }
    
    /**
    * Gets option value for trainingtools _adminset or defaults to the file version of the array if option returns invalid.
    * 1. Called in the main trainingtools.php file.
    * 2. Installs the admin settings option record if it is currently missing due to the settings being required by all screens, this is to begin applying and configuring settings straighta away for a per user experience 
    */
    public function adminsettings() {
        $result = $this->option( 'trainingtools_settings', 'get' );
        $result = maybe_unserialize( $result); 
        if(is_array( $result) ){
            return $result; 
        }else{     
            return $this->install_admin_settings();
        }  
    }
    
    /**
    * Control WordPress option functions using this single function.
    * This function will give us the opportunity to easily log changes and some others ideas we have.
    * 
    * @param mixed $option
    * @param mixed $action add, get, wtgget (own query function) update, delete
    * @param mixed $value
    * @param mixed $autoload used by add_option only
    */
    public function option( $option, $action, $value = 'No Value', $autoload = 'yes' ){
        if( $action == 'add' ){  
            return add_option( $option, $value, '', $autoload );            
        }elseif( $action == 'get' ){
            return get_option( $option);    
        }elseif( $action == 'update' ){        
            return update_option( $option, $value );
        }elseif( $action == 'delete' ){
            return delete_option( $option);        
        }
    }
                      
    /**
     * Add a widget to the dashboard.
     *
     * This function is hooked into the 'wp_dashboard_setup' action below.
     */
     
    /**
    * Hooked by wp_dashboard_setup
    * 
    * @uses TRAININGTOOLS_UI::add_dashboard_widgets() which has the widgets
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function add_dashboard_widgets() {
        $this->UI->add_dashboard_widgets();            
    }  
            
    /**
    * Determines if the plugin is fully installed or not
    * 
    * NOT IN USE - I've removed a global and a loop pending a new class that will need to be added to this function
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 6.0.0
    * @version 1.0
    */       
    public function is_installed() {
        return true;        
    }                       

    public function screen_options() {
        global $pippin_sample_page;
        $screen = get_current_screen();

        // toplevel_page_trainingtools (main page)
        if( $screen->id == 'toplevel_page_trainingtools' ){
            $args = array(
                'label' => __( 'Members per page' ),
                'default' => 1,
                'option' => 'trainingtools_testoption'
            );
            add_screen_option( 'per_page', $args );
        }     
    }

    public function save_screen_option( $status, $option, $value ) {
        if ( 'trainingtools_testoption' == $option ) return $value;
    }
      
    /**
    * WordPress Help tab content builder
    * 
    * Using class-help.php we can make use of help information and add extensive support text.
    * The plan is to use a SOAP API that gets the help text from the WebTechGlobal server.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0.2
    */
    public function help_tab () {
                               
        // get the current screen array
        $screen = get_current_screen();
        
        // load help class which contains help content array
        $TRAININGTOOLS_Help = self::load_class( 'TRAININGTOOLS_Help', 'class-help.php', 'classes' );

        // call the array
        $help_array = $TRAININGTOOLS_Help->get_help_array();
        
        // load tab menu class which contains help content array
        $TRAININGTOOLS_TabMenu = self::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        
        // call the menu_array
        $menu_array = $TRAININGTOOLS_TabMenu->menu_array();
             
        // get page name i.e. trainingtools_page_trainingtools_affiliates would return affiliates
        $page_name = $this->PHP->get_string_after_last_character( $screen->id, '_' );
        
        // if on main page "trainingtools" then set tab name as main
        if( $page_name == 'trainingtools' ){$page_name = 'main';}
     
        // does the page have any help content? 
        if( !isset( $menu_array[ $page_name ] ) ){
            return false;
        }
        
        // set view name
        $view_name = $page_name;

        // does the view have any help content
        if( !isset( $help_array[ $page_name ][ $view_name ] ) ){
            return false;
        }
              
        // build the help content for the view
        $help_content = '<p>' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewabout' ] . '</p>';

        // add a link encouraging user to visit site and read more OR visit YouTube video
        if( isset( $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewreadmoreurl' ] ) ){
            $help_content .= '<p>';
            $help_content .= __( 'You are welcome to visit the', 'trainingtools' ) . ' ';
            $help_content .= '<a href="' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewreadmoreurl' ] . '"';
            $help_content .= 'title="' . __( 'Visit the Training Tools website and read more about', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ] . '"';
            $help_content .= 'target="_blank"';
            $help_content .= '>';
            $help_content .= __( 'Training Tools Website', 'trainingtools' ) . '</a> ' . __( 'to read more about', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ];           
            $help_content .= '.</p>';
        }  
        
        // add a link to a Youtube
        if( isset( $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewvideourl' ] ) ){
            $help_content .= '<p>';
            $help_content .= __( 'There is a', 'trainingtools' ) . ' ';
            $help_content .= '<a href="' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewvideourl' ] . '"';
            $help_content .= 'title="' . __( 'Go to YouTube and watch a video about', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ] . '"';
            $help_content .= 'target="_blank"';
            $help_content .= '>';            
            $help_content .= __( 'YouTube Video', 'trainingtools' ) . '</a> ' . __( 'about', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ];           
            $help_content .= '.</p>';
        }

        // add a link to a Youtube
        if( isset( $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewdiscussurl' ] ) ){
            $help_content .= '<p>';
            $help_content .= __( 'We invite you to take discuss', 'trainingtools' ) . ' ';
            $help_content .= '<a href="' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewdiscussurl' ] . '"';
            $help_content .= 'title="' . __( 'Visit the WebTechGlobal forum to discuss', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ] . '"';
            $help_content .= 'target="_blank"';
            $help_content .= '>';            
            $help_content .= $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ] . '</a> ' . __( 'on the WebTechGlobal Forum', 'trainingtools' );           
            $help_content .= '.</p>';
        }         

        // finish by adding the first tab which is for the view itself (soon to become registered pages) 
        $screen->add_help_tab( array(
            'id'    => $page_name,
            'title'    => __( 'About', 'trainingtools' ) . ' ' . $help_array[ $page_name ][ $view_name ][ 'viewinfo' ][ 'viewtitle' ] ,
            'content'    => $help_content,
        ) );
  
        // add a tab per form
        $help_content = '';
        foreach( $help_array[ $page_name ][ $view_name ][ 'forms' ] as $form_id => $value ){
                                
            // the first content is like a short introduction to what the box/form is to be used for
            $help_content .= '<p>' . $value[ 'formabout' ] . '</p>';
                         
            // add a link encouraging user to visit site and read more OR visit YouTube video
            if( isset( $value[ 'formreadmoreurl' ] ) ){
                $help_content .= '<p>';
                $help_content .= __( 'You are welcome to visit the', 'trainingtools' ) . ' ';
                $help_content .= '<a href="' . $value[ 'formreadmoreurl' ] . '"';
                $help_content .= 'title="' . __( 'Visit the Training Tools website and read more about', 'trainingtools' ) . ' ' . $value[ 'formtitle' ] . '"';
                $help_content .= 'target="_blank"';
                $help_content .= '>';
                $help_content .= __( 'Training Tools Website', 'trainingtools' ) . '</a> ' . __( 'to read more about', 'trainingtools' ) . ' ' . $value[ 'formtitle' ];           
                $help_content .= '.</p>';
            }  
            
            // add a link to a Youtube
            if( isset( $value[ 'formvideourl' ] ) ){
                $help_content .= '<p>';
                $help_content .= __( 'There is a', 'trainingtools' ) . ' ';
                $help_content .= '<a href="' . $value[ 'formvideourl' ] . '"';
                $help_content .= 'title="' . __( 'Go to YouTube and watch a video about', 'trainingtools' ) . ' ' . $value[ 'formtitle' ] . '"';
                $help_content .= 'target="_blank"';
                $help_content .= '>';            
                $help_content .= __( 'YouTube Video', 'trainingtools' ) . '</a> ' . __( 'about', 'trainingtools' ) . ' ' . $value[ 'formtitle' ];           
                $help_content .= '.</p>';
            }

            // add a link to a Youtube
            if( isset( $value[ 'formdiscussurl' ] ) ){
                $help_content .= '<p>';
                $help_content .= __( 'We invite you to discuss', 'trainingtools' ) . ' ';
                $help_content .= '<a href="' . $value[ 'formdiscussurl' ] . '"';
                $help_content .= 'title="' . __( 'Visit the WebTechGlobal forum to discuss', 'trainingtools' ) . ' ' . $value[ 'formtitle' ] . '"';
                $help_content .= 'target="_blank"';
                $help_content .= '>';            
                $help_content .= $value[ 'formtitle' ] . '</a> ' . __( 'on the WebTechGlobal Forum', 'trainingtools' );           
                $help_content .= '.</p>';
            } 
                               
            // loop through options
            foreach( $value[ 'options' ] as $key_two => $option_array ){  
                $help_content .= '<h3>' . $option_array[ 'optiontitle' ] . '</h3>';
                $help_content .= '<p>' . $option_array[ 'optiontext' ] . '</p>';
                            
                if( isset( $option_array['optionurl'] ) ){
                    $help_content .= ' <a href="' . $option_array['optionurl'] . '"';
                    $help_content .= ' title="' . __( 'Read More about', 'trainingtools' )  . ' ' . $option_array['optiontitle'] . '"';
                    $help_content .= ' target="_blank">';
                    $help_content .= __( 'Read More', 'trainingtools' ) . '</a>';      
                }
      
                if( isset( $option_array['optionvideourl'] ) ){
                    $help_content .= ' - <a href="' . $option_array['optionvideourl'] . '"';
                    $help_content .= ' title="' . __( 'Watch a video about', 'trainingtools' )  . ' ' . $option_array['optiontitle'] . '"';
                    $help_content .= ' target="_blank">';
                    $help_content .= __( 'Video', 'trainingtools' ) . '</a>';      
                }
            }
            
            // add the tab for this form and its help content
            $screen->add_help_tab( array(
                'id'    => $page_name . $view_name,
                'title'    => $help_array[ $page_name ][ $view_name ][ 'forms' ][ $form_id ][ 'formtitle' ],
                'content'    => $help_content,
            ) );                
                
        }
  
    }  

    /**
    * Gets the required capability for the plugins page from the page array
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    *  
    * @param mixed $trainingtools_page_name
    * @param mixed $default
    */
    public function get_page_capability( $page_name ){
        $capability = 'administrator';// script default for all outcomes

        // get stored capability settings 
        $saved_capability_array = get_option( 'trainingtools_capabilities' );
                
        if( isset( $saved_capability_array['pagecaps'][ $page_name ] ) && is_string( $saved_capability_array['pagecaps'][ $page_name ] ) ) {
            $capability = $saved_capability_array['pagecaps'][ $page_name ];
        }
                   
        return $capability;   
    }   
    
    /**
    * WordPress plugin menu
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 6.0.0
    * @version 1.3
    */
    public function admin_menu() {    
        global $trainingtools_menu_array;
         
        $TRAININGTOOLS_Menu = $trainingtools_menu_array;

        // set the callback, we can change this during the loop and call methods more dynamically
        // this approach allows us to call the same function for all pages
        $subpage_callback = array( $this, 'show_admin_page' );
  
        // add menu
        $this->page_hooks[] = add_menu_page( $TRAININGTOOLS_Menu['main']['title'], 
        __( 'Training Tools', 'trainingtools' ), 
        'administrator', 
        'trainingtools',  
        $subpage_callback ); 
        
        // help tab                                                 
        add_action( 'load-toplevel_page_trainingtools', array( $this, 'help_tab' ) );

        // track which group has already been displayed using the parent name
        $groups = array();

        // get all group menu titles
        $group_titles_array = array();
        foreach( $TRAININGTOOLS_Menu as $key_pagename => $page_array ){ 
            if( $page_array['parent'] === 'parent' ){                
                $group_titles_array[ $page_array['groupname'] ]['grouptitle'] = $page_array['menu'];
            }
        }          
        
        // loop through sub-pages - remove pages that are not to be registered
        foreach( $TRAININGTOOLS_Menu as $key_pagename => $page_array ){                 

            // if not visiting this plugins pages, simply register all the parents
            if( !isset( $_GET['page'] ) || !strstr( $_GET['page'], 'trainingtools' ) ){
                
                // remove none parents
                if( $page_array['parent'] !== 'parent' ){    
                    unset( $TRAININGTOOLS_Menu[ $key_pagename ] ); 
                }        
            
            }elseif( isset( $_GET['page'] ) && strstr( $_GET['page'], 'trainingtools' ) ){
                
                // remove pages that are not the main, the current visited or a parent
                if( $key_pagename !== 'main' && $page_array['slug'] !== $_GET['page'] && $page_array['parent'] !== 'parent' ){
                    unset( $TRAININGTOOLS_Menu[ $key_pagename ] );
                }     
                
            } 
            
            // remove the parent of a group for the visited page
            if( isset( $_GET['page'] ) && $page_array['slug'] === $_GET['page'] ){
                unset( $TRAININGTOOLS_Menu[ $TRAININGTOOLS_Menu[ $key_pagename ]['parent'] ] );
            }
            
            // remove update page as it is only meant to show when new version of files applied
            if( $page_array['slug'] == 'trainingtools_pluginupdate' ) {
                unset( $TRAININGTOOLS_Menu[ $key_pagename ] );
            }
        }
           
        foreach( $TRAININGTOOLS_Menu as $key_pagename => $page_array ){ 
                                
            $new_hook = add_submenu_page( 'trainingtools', 
                   $group_titles_array[ $page_array['groupname'] ]['grouptitle'], 
                   $group_titles_array[ $page_array['groupname'] ]['grouptitle'], 
                   self::get_page_capability( $key_pagename ), 
                   $TRAININGTOOLS_Menu[ $key_pagename ]['slug'], 
                   $subpage_callback );     
         
            $this->page_hooks[] = $new_hook;
                   
            // help tab                                                 
            add_action( 'load-trainingtools_page_trainingtools_' . $key_pagename, array( $this, 'help_tab' ) );              
        }
    }
    
    /**
     * Tabs menu loader - calls function for css only menu or jquery tabs menu
     * 
     * @param string $thepagekey this is the screen being visited
     */
    public function build_tab_menu( $current_page_name ){           
        // load tab menu class which contains help content array
        $TRAININGTOOLS_TabMenu = TRAININGTOOLS::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        
        // call the menu_array
        $menu_array = $TRAININGTOOLS_TabMenu->menu_array();
                
        echo '<h2 class="nav-tab-wrapper">';
        
        // get the current pages viewgroup for building the correct tab menu
        $view_group = $menu_array[ $current_page_name ][ 'groupname'];
            
        foreach( $menu_array as $page_name => $values ){
                                                         
            if( $values['groupname'] === $view_group ){
                
                $activeclass = 'class="nav-tab"';
                if( $page_name === $current_page_name ){                      
                    $activeclass = 'class="nav-tab nav-tab-active"';
                }
                
                echo '<a href="' . self::create_adminurl( $values['slug'] ) . '" '.$activeclass.'>' . $values['pluginmenu'] . '</a>';       
            }
        }      
        
        echo '</h2>';
    }   
        
    /**
    * $_POST and $_GET request processing procedure.
    * 
    * function was reduced to two lines, the contents mode to TRAININGTOOLS_Requests itself.
    *
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.5
    */
    public function process_admin_POST_GET() {
        // no processing for autosaves in this plugin
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
               
        // no processing during these actions, processing for these may be handled elsewhere
        // i.e. processing for post types is handed by save_post hook
        $views_to_avoid = array( 'editpost' );
        if( isset( $_POST['action'] ) && in_array( $_POST['action'], $views_to_avoid ) ) {
            return;    
        }
                 
        $method = 'unknown';
        $function = 'nofunctionestablished123';
                                 
        // $_POST request check then nonce validation 
        if( isset( $_POST['trainingtools_admin_action'] ) ) {
            $early_deny_request = false;
                
            // run nonce security for a form submission
            if( !isset( $_POST['trainingtools_form_name'] ) ) {    
                return false;
            }

            check_admin_referer( $_POST['trainingtools_form_name'] );# exists here if failed
            $function = $_POST['trainingtools_form_name'];
                    
            // set method - used to apply the correct security procedures
            $method = 'post';
        }          
             
        // $_GET reuest check by plugin OR a WP core $_GET request that is handled by the plugin
        if( isset( $_GET['trainingtoolsaction'] )  ) {
             
            check_admin_referer( $_GET['trainingtoolsaction'] );# exists here if failed
            $function = $_GET['trainingtoolsaction'];  

            // set method - used to apply the correct security procedures
            $method = 'get';  
        }
             
        // include the class that processes form submissions and nonce links
        if( $method !== 'unknown' ) {   
            $TRAININGTOOLS_REQ = self::load_class( 'TRAININGTOOLS_Requests', 'class-requests.php', 'classes' );
            $TRAININGTOOLS_REQ->process_admin_request( $method, $function );
        }
    } 

    /**
    * Used to display this plugins notices on none plugin pages i.e. dashboard.
    * 
    * filteraction_should_beloaded() decides if the admin_notices hook is called, which hooks this function.
    * I think that check should only check which page is being viewed. Anything more advanced might need to
    * be performed in display_users_notices().
    * 
    * @uses display_users_notices()
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function admin_notices() {
        $this->UI->display_users_notices();
    }
                                 
    /**
    * Popup and content for media button displayed just above the WYSIWYG editor 
    */
    public function pluginmediabutton_popup() {
        global $trainingtools_settings; ?>
        
        <div id="trainingtools_popup_container" style="display:none;">

        </div>
        
        <?php
    }    
    
    /**
    * HTML for a media button that displays above the WYSIWYG editor
    * 
    * @param mixed $context
    */
    public function pluginmediabutton_button( $context) {
        //append the icon
        $context = "<a class='button thickbox' title='Training Tools Column Replacement Tokens (CTRL + C then CTRL + V)'
        href='#TB_inline?width=400&inlineId=trainingtools_popup_container'>Training Tools</a>";
        return $context;
    }  
      
    /**
    * Used in admin page headers to constantly check the plugins status while administrator logged in 
    */
    public function diagnostics_constant() {
        if( is_admin() && current_user_can( 'manage_options' ) ){
            
            // avoid diagnostic if a $_POST, $_GET or Ajax request made (it is installation state diagnostic but active debugging)                                          
            if( self::request_made() ){
                return;
            }
                              
        }
    }
    
    /**
    * DO NOT CALL DURING FULL PLUGIN INSTALL
    * This function uses update. Do not call it during full install because user may be re-installing but
    * wishing to keep some existing option records.
    * 
    * Use this function when installing admin settings during use of the plugin. 
    */
    public function install_admin_settings() {
        require_once( TRAININGTOOLS_ABSPATH . 'arrays/settings_array.php' );
        return $this->option( 'trainingtools_settings', 'update', $trainingtools_settings );# update creates record if it does not exist   
    } 
     
    /**
    * includes a file per custom post type, we can customize this to include or exclude based on settings
    */
    public function custom_post_types() { 
        global $trainingtools_settings;      
        
        // has the WebTechGlobal Flag system been activated for this package?                    
        if( isset( $trainingtools_settings['posttypes']['wtgflags']['status'] ) && $trainingtools_settings['posttypes']['wtgflags']['status'] === 'enabled' ) {    
            @include_once( TRAININGTOOLS_ABSPATH . 'posttypes/flags.php' );   
        }
    
        // Page Guides                                                      
        if( isset( $trainingtools_settings['posttypes']['pageguides']['status'] ) && $trainingtools_settings['posttypes']['pageguides']['status'] === 'enabled' ) {    
            @include_once( TRAININGTOOLS_ABSPATH . 'posttypes/pageguides.php' );   
        }
    }
 
    /**
    * Admin Triggered Automation
    */
    public function admin_triggered_automation() {
        // clear out log table (48 hour log)
        self::log_cleanup();
    }
    
    /**
    * gets the specific row/s for a giving post ID
    * 
    * UPDATE: "c2p_postid != $post_id" was in use but this is wrong. I'm not sure how this has gone
    * undetected considering where the function has been used. 
    *
    * @param mixed $project_id
    * @param mixed $total
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function get_posts_rows( $project_id, $post_id, $idcolumn = false ){
        $this->DB = self::load_class( 'TRAININGTOOLS_DB', 'class-wpdb.php', 'classes' );
        $tables_array = $this->get_dbtable_sources( $project_id );
        return $this->DB->query_multipletables( $tables_array, $idcolumn, 'c2p_postid = '.$post_id );
    }
    
    /**
    * gets one or more rows from imported data for specific post created by specific project
    * 
    * @uses get_posts_rows() which does a join query 
    * 
    * @param mixed $project_id
    * @param mixed $post_id
    * @param mixed $idcolumn
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0 
    */
    public function get_posts_record( $project_id, $post_id, $idcolumn = false ){
        return self::get_posts_rows( $project_id, $post_id, $idcolumn );
    } 
    
    /**
    * Gets the MySQL version of column
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    * 
    * @returns false if no column set
    */
    public function get_category_column( $project_id, $level ) {
        if( isset( $this->current_project_settings['categories']['data'][$level]['column'] ) ){
            return $this->current_project_settings['categories']['data'][$level]['column'];    
        }           
        
        return false;
    } 

    /**
    * Determines if process request of any sort has been requested
    * 1. used to avoid triggering automatic processing during proccess requests
    * 
    * @returns true if processing already requested else false
    */
    public function request_made() {
        // ajax
        if(defined( 'DOING_AJAX' ) && DOING_AJAX){
            return true;    
        } 
        
        // form submissions - if $_POST is set that is fine, providing it is an empty array
        if( isset( $_POST) && !empty( $_POST) ){
            return true;
        }
        
        // Training Tools own special processing triggers
        if( isset( $_GET['trainingtoolsaction'] ) || isset( $_GET['nonceaction'] ) ){
            return true;
        }
        
        return false;
    } 
   
    /**
    * Used to build history, flag items and schedule actions to be performed.
    * 1. it all falls under log as we would probably need to log flags and scheduled actions anyway
    *
    * @global $wpdb
    * @uses extract, shortcode_atts
    * 
    * @link http://www.trainingtools.com/hacking/log-table
    */
    public function newlog( $atts ){     
        global $trainingtools_settings, $wpdb;

        $table_name = $wpdb->prefix . 'webtechglobal_log';
        
        // if ALL logging is off - if ['uselog'] not set then logging for all files is on by default
        if( isset( $trainingtools_settings['globalsettings']['uselog'] ) && $trainingtools_settings['globalsettings']['uselog'] == 0){
            return false;
        }
        
        // if log table does not exist return false
        if( !$this->DB->does_table_exist( $table_name ) ){
            return false;
        }
             
        // if a value is false, it will not be added to the insert query, we want the database default to kick in, NULL mainly
        extract( shortcode_atts( array(  
            'outcome' => 1,# 0|1 (overall outcome in boolean) 
            'line' => false,# __LINE__ 
            'function' => false,# __FUNCTION__
            'file' => false,# __FILE__ 
            'sqlresult' => false,# dump of sql query result 
            'sqlquery' => false,# dump of sql query 
            'sqlerror' => false,# dump of sql error if any 
            'wordpresserror' => false,# dump of a wp error 
            'screenshoturl' => false,# screenshot URL to aid debugging 
            'userscomment' => false,# beta testers comment to aid debugging (may double as other types of comments if log for other purposes) 
            'page' => false,# related page 
            'version' => TRAININGTOOLS_VERSION, 
            'panelid' => false,# id of submitted panel
            'panelname' => false,# name of submitted panel 
            'tabscreenid' => false,# id of the menu tab  
            'tabscreenname' => false,# name of the menu tab 
            'dump' => false,# dump anything here 
            'ipaddress' => false,# users ip 
            'userid' => false,# user id if any    
            'noticemessage' => false,# when using log to create a notice OR if logging a notice already displayed      
            'comment' => false,# dev comment to help with troubleshooting
            'type' => false,# general|error|trace 
            'category' => false,# createposts|importdata|uploadfile|deleteuser|edituser 
            'action' => false,# 3 posts created|22 posts updated (the actuall action performed)
            'priority' => false,# low|normal|high (use high for errors or things that should be investigated, use low for logs created mid procedure for tracing progress)                        
            'triga' => false# autoschedule|cronschedule|wpload|manualrequest
        ), $atts ) );
        
        // start query
        $query = "INSERT INTO $table_name";
        
        // add columns and values
        $query_columns = '(outcome';
        $query_values = '(1';
        
        if( $line){$query_columns .= ',line';$query_values .= ', "'.$line.'"';}
        if( $file){$query_columns .= ',file';$query_values .= ', "'.$file.'"';}                                                                           
        if( $function){$query_columns .= ',function';$query_values .= ', "'.$function.'"';}  
        if( $sqlresult){$query_columns .= ',sqlresult';$query_values .= ', "'.$sqlresult.'"';}     
        if( $sqlquery ){$query_columns .= ',sqlquery';$query_values .= ', "'.$sqlquery.'"';}     
        if( $sqlerror){$query_columns .= ',sqlerror';$query_values .= ', "'.$sqlerror.'"';}    
        if( $wordpresserror){$query_columns .= ',wordpresserror';$query_values .= ', "'.$wordpresserror.'"';}     
        if( $screenshoturl){$query_columns .= ',screenshoturl';$query_values .= ', "'.$screenshoturl.'"' ;}     
        if( $userscomment){$query_columns .= ',userscomment';$query_values .= ', "'.$userscomment.'"';}     
        if( $page){$query_columns .= ',page';$query_values .= ', "'.$page.'"';}     
        if( $version){$query_columns .= ',version';$query_values .= ', "'.$version.'"';}     
        if( $panelid){$query_columns .= ',panelid';$query_values .= ', "'.$panelid.'"';}     
        if( $panelname){$query_columns .= ',panelname';$query_values .= ', "'.$panelname.'"';}     
        if( $tabscreenid){$query_columns .= ',tabscreenid';$query_values .= ', "'.$tabscreenid.'"';}     
        if( $tabscreenname){$query_columns .= ',tabscreenname';$query_values .= ', "'.$tabscreenname.'"';}     
        if( $dump){$query_columns .= ',dump';$query_values .= ', "'.$dump.'"';}     
        if( $ipaddress){$query_columns .= ',ipaddress';$query_values .= ', "'.$ipaddress.'"';}     
        if( $userid){$query_columns .= ',userid';$query_values .= ', "'.$userid.'"';}     
        if( $noticemessage){$query_columns .= ',noticemessage';$query_values .= ', "'.$noticemessage.'"';}     
        if( $comment){$query_columns .= ',comment';$query_values .= ', "'.$comment.'"';}     
        if( $type){$query_columns .= ',type';$query_values .= ', "'.$type.'"';}     
        if( $category ){$query_columns .= ',category';$query_values .= ', "'.$category.'"';}     
        if( $action){$query_columns .= ',action';$query_values .= ', "'.$action.'"';}     
        if( $priority ){$query_columns .= ',priority';$query_values .= ', "'.$priority.'"';}     
        if( $triga){$query_columns .= ',triga';$query_values .= ', "'.$triga.'"';}
        
        $query_columns .= ' )';
        $query_values .= ' )';
        $query .= $query_columns .' VALUES '. $query_values;  
        $wpdb->query( $query );     
    } 
    
    /**
    * Use this to log automated events and track progress in automated scripts.
    * Mainly used in schedule function but can be used in any functions called by add_action() or
    * other processing that is triggered by user events but not specifically related to what the user is doing.
    * 
    * @param mixed $outcome
    * @param mixed $trigger schedule, hook (action hooks such as text spinning could be considered automation), cron, url, user (i.e. user does something that triggers background processing)
    * @param mixed $line
    * @param mixed $file
    * @param mixed $function
    */
    public function log_schedule( $comment, $action, $outcome, $category = 'scheduledeventaction', $trigger = 'autoschedule', $line = 'NA', $file = 'NA', $function = 'NA' ){
        $atts = array();   
        $atts['logged'] = self::datewp();
        $atts['comment'] = $comment;
        $atts['action'] = $action;
        $atts['outcome'] = $outcome;
        $atts['category'] = $category;
        $atts['line'] = $line;
        $atts['file'] = $file;
        $atts['function'] = $function;
        $atts['trigger'] = $function;
        // set log type so the log entry is made to the required log file
        $atts['type'] = 'automation';
        self::newlog( $atts);    
    } 
   
    /**
     * Checks existing plugins and displays notices with advice or informaton
     * This is not only for code conflicts but operational conflicts also especially automated processes
     *
     * $return $critical_conflict_result true or false (true indicatesd a critical conflict found, prevents installation, this should be very rare)
     */
    function conflict_prevention( $outputnoneactive = false ){
        // track critical conflicts, return the result and use to prevent installation
        // only change $conflict_found to true if the conflict is critical, if it only effects partial use
        // then allow installation but warn user
        $conflict_found = false;
            
        // we create an array of profiles for plugins we want to check
        $plugin_profiles = array();

        // Tweet My Post (javascript conflict and a critical one that breaks entire interface)
        $plugin_profiles[0]['switch'] = 1;//used to use or not use this profile, 0 is no and 1 is use
        $plugin_profiles[0]['title'] = __( 'Tweet My Post', 'trainingtools' );
        $plugin_profiles[0]['slug'] = 'tweet-my-post/tweet-my-post.php';
        $plugin_profiles[0]['author'] = 'ksg91';
        $plugin_profiles[0]['title_active'] = __( 'Tweet My Post Conflict', 'trainingtools' );
        $plugin_profiles[0]['message_active'] = __( 'Please deactivate Twitter plugins before performing mass post creation. This will avoid spamming Twitter and causing more processing while creating posts.', 'trainingtools' );
        $plugin_profiles[0]['message_inactive'] = __( 'If you activate this or any Twitter plugin please ensure the plugins options are not setup to perform mass tweets during post creation.', 'trainingtools' );
        $plugin_profiles[0]['type'] = 'info';//passed to the message function to apply styling and set type of notice displayed
        $plugin_profiles[0]['criticalconflict'] = true;// true indicates that the conflict will happen if plugin active i.e. not specific settings only, simply being active has an effect
                             
        // loop through the profiles now
        if( isset( $plugin_profiles) && $plugin_profiles != false ){
            foreach( $plugin_profiles as $key=>$plugin){   
                if( is_plugin_active( $plugin['slug'] ) ){ 
                   
                    // recommend that the user does not use the plugin
                    $this->notice_depreciated( $plugin['message_active'], 'warning', 'Small', $plugin['title_active'], '', 'echo' );

                    // if the conflict is critical, we will prevent installation
                    if( $plugin['criticalconflict'] == true){
                        $conflict_found = true;// indicates critical conflict found
                    }
                    
                }elseif(is_plugin_inactive( $plugin['slug'] ) ){
                    
                    if( $outputnoneactive)
                    {   
                        $this->n_incontent_depreciated( $plugin['message_inactive'], 'warning', 'Small', $plugin['title'] . ' Plugin Found' );
                    }
        
                }
            }
        }

        return $conflict_found;
    }     
    
    /**
    * Cleanup log table - currently keeps 2 days of logs
    */
    public function log_cleanup() {
        global $wpdb;     
        if( $this->DB->database_table_exist( $wpdb->webtechglobal_log ) ){
            global $wpdb;
            $twodays_time = strtotime( '2 days ago midnight' );
            $twodays = date( "Y-m-d H:i:s", $twodays_time);
            $wpdb->query( 
                "
                    DELETE FROM $wpdb->webtechglobal_log
                    WHERE timestamp < '".$twodays."'
                "
            );
        }
    }
    
    public function send_email( $recipients, $subject, $content, $content_type = 'html' ){     
                           
        if( $content_type == 'html' )
        {
            add_filter( 'wp_mail_content_type', 'trainingtools_set_html_content_type' );
        }
        
        $result = wp_mail( $recipients, $subject, $content );

        if( $content_type == 'html' )
        {    
            remove_filter( 'wp_mail_content_type', 'trainingtools_set_html_content_type' );  
        }   
        
        return $result;
    }    
    
    /**
    * Creates url to an admin page
    *  
    * @param mixed $page, registered page slug i.e. trainingtools_install which results in wp-admin/admin.php?page=trainingtools_install   
    * @param mixed $values, pass a string beginning with & followed by url values
    */
    public function url_toadmin( $page, $values = '' ){                                  
        return get_admin_url() . 'admin.php?page=' . $page . $values;
    }
    
    /**
    * Adds <button> with jquerybutton class and </form>, for using after a function that outputs a form
    * Add all parameteres or add none for defaults
    * @param string $buttontitle
    * @param string $buttonid
    */
    public function formend_standard( $buttontitle = 'Submit', $buttonid = 'notrequired' ){
            if( $buttonid == 'notrequired' ){
                $buttonid = 'trainingtools_notrequired'.rand(1000,1000000);# added during debug
            }else{
                $buttonid = $buttonid.'_formbutton';
            }?>

            <p class="submit">
                <input type="submit" name="trainingtools_wpsubmit" id="<?php echo $buttonid;?>" class="button button-primary" value="<?php echo $buttontitle;?>">
            </p>

        </form><?php
    }
    
    /**
     * Echos the html beginning of a form and beginning of widefat post fixed table
     * 
     * @param string $name (a unique value to identify the form)
     * @param string $method (optional, default is post, post or get)
     * @param string $action (optional, default is null for self submission - can give url)
     * @param string $enctype (pass enctype="multipart/form-data" to create a file upload form)
     */
    public function formstart_standard( $name, $id = 'none', $method = 'post', $class, $action = '', $enctype = '' ){
        if( $class){
            $class = 'class="'.$class.'"';
        }else{
            $class = '';         
        }
        echo '<form '.$class.' '.$enctype.' id="'.$id.'" method="'.$method.'" name="trainingtools_request_'.$name.'" action="'.$action.'">
        <input type="hidden" id="trainingtools_admin_action" name="trainingtools_admin_action" value="true">';
    } 
        
    /**
    * Adds Script Start and Stylesheets to the beginning of pages
    */
    public function pageheader( $pagetitle, $layout ){
        global $current_user, $trainingtools_settings, $trainingtools_menu_array;

        // get admin settings again, all submissions and processing should update settings
        // if the interface does not show expected changes, it means there is a problem updating settings before this line
        $trainingtools_settings = self::adminsettings(); 

        get_currentuserinfo();?>
                    
        <div id="trainingtools-page" class="wrap">
            <?php self::diagnostics_constant();?>
        
            <div id="icon-options-general" class="icon32"><br /></div>
            
            <?php 
            // build page H2 title
            $h2_title = '';
            
            // if not "Training Tools" set this title
            if( $pagetitle !== 'Training Tools' ) {
                $h2_title = 'Training Tools: ' . $pagetitle;    
            }

            // if build only has one page, shorten the title
            // this is to make plugin building a little quicker
            $pages = count( $trainingtools_menu_array );
            if( $pages == 1 ){
                $h2_title = 'YouTube Sidebar';
            }           
            ?>
            
            <h2><?php echo $h2_title;?></h2>

            <?php 
            // run specific admin triggered automation tasks, this way an output can be created for admin to see
            self::admin_triggered_automation();  

            // check existing plugins and give advice or warnings
            self::conflict_prevention();
                     
            // display form submission result notices
            $this->UI->output_depreciated();// now using display_all();
            $this->UI->display_all();              
          
            // process global security and any other types of checks here such such check systems requirements, also checks installation status
            $c2p_requirements_missing = self::check_requirements(true);
    }                          
    
    /**
    * Checks if the cores minimum requirements are met and displays notices if not
    * Checks: Internet Connection (required for jQuery ), PHP version, Soap Extension
    */
    public function check_requirements( $display ){
        // variable indicates message being displayed, we will only show 1 message at a time
        $requirement_missing = false;

        // php version
        if(defined(TRAININGTOOLS_PHPVERSIONMINIMUM) ){
            if(TRAININGTOOLS_PHPVERSIONMINIMUM > phpversion() ){
                $requirement_missing = true;
                if( $display == true){
                    self::notice_depreciated(sprintf( __( 'The plugin detected an older PHP version than the minimum requirement which 
                    is %s. You can requests an upgrade for free from your hosting, use .htaccess to switch
                    between PHP versions per WP installation or sometimes hosting allows customers to switch using their control panel.', 'trainingtools' ),TRAININGTOOLS_PHPVERSIONMINIMUM)
                    , 'warning', 'Large', __( 'Training Tools Requires PHP ', 'trainingtools' ) . TRAININGTOOLS_PHPVERSIONMINIMUM);                
                }
            }
        }
        
        return $requirement_missing;
    }               
    
    /**       
     * Generates a username using a single value by incrementing an appended number until a none used value is found
     * @param string $username_base
     * @return string username, should only fail if the value passed to the function causes so
     * 
     * @todo log entry functions need to be added, store the string, resulting username
     */
    public function create_username( $username_base){
        $attempt = 0;
        $limit = 500;// maximum trys - would we ever get so many of the same username with appended number incremented?
        $exists = true;// we need to change this to false before we can return a value

        // clean the string
        $username_base = preg_replace( '/([^@]*).*/', '$1', $username_base );

        // ensure giving string does not already exist as a username else we can just use it
        $exists = username_exists( $username_base );
        if( $exists == false )
        {
            return $username_base;
        }
        else
        {
            // if $suitable is true then the username already exists, increment it until we find a suitable one
            while( $exists != false )
            {
                ++$attempt;
                $username = $username_base.$attempt;

                // username_exists returns id of existing user so we want a false return before continuing
                $exists = username_exists( $username );

                // break look when hit limit or found suitable username
                if( $attempt > $limit || $exists == false ){
                    break;
                }
            }

            // we should have our login/username by now
            if ( $exists == false ) 
            {
                return $username;
            }
        }
    }
    
    /**
    * Wrapper, uses trainingtools_url_toadmin to create local admin url
    * 
    * @param mixed $page
    * @param mixed $values 
    */
    public function create_adminurl( $page, $values = '' ){
        return self::url_toadmin( $page, $values);    
    }
    
    /**
    * Returns the plugins standard date (MySQL Date Time Formatted) with common format used in WordPress.
    * Optional $time parameter, if false will return the current time().
    * 
    * @param integer $timeaddition, number of seconds to add to the current time to create a future date and time
    * @param integer $time optional parameter, by default causes current time() to be used
    */
    public function datewp( $timeaddition = 0, $time = false, $format = false ){
        // initialize time string
        if( $time != false && is_numeric( $time) ){$thetime = $time;}else{$thetime = time();}
        // has a format been past
        if( $format == 'gm' ){
            return gmdate( 'Y-m-d H:i:s', $thetime + $timeaddition);
        }elseif( $format == 'mysql' ){
            // return actual mysql database current time
            return current_time( 'mysql',0);// example 2005-08-05 10:41:13
        }
        
        // default to standard PHP with a common format used by WordPress and MySQL but not the actual database time
        return date( 'Y-m-d H:i:s', $thetime + $timeaddition);    
    }   
    
    public function get_installed_version() {
        return get_option( 'trainingtools_installedversion' );    
    }  
    
    /**
    * Use to start a new result array which is returned at the end of a function. It gives us a common set of values to work with.

    * @uses self::arrayinfo_set()
    * @param mixed $description use to explain what array is used for
    * @param mixed $line __LINE__
    * @param mixed $function __FUNCTION__
    * @param mixed $file __FILE__
    * @param mixed $reason use to explain why the array was updated (rather than what the array is used for)
    * @return string
    */                                   
    public function result_array( $description, $line, $function, $file ){
        $array = self::arrayinfo_set(array(), $line, $function, $file );
        $array['description'] = $description;
        $array['outcome'] = true;// boolean
        $array['failreason'] = false;// string - our own typed reason for the failure
        $array['error'] = false;// string - add php mysql wordpress error 
        $array['parameters'] = array();// an array of the parameters passed to the function using result_array, really only required if there is a fault
        $array['result'] = array();// the result values, if result is too large not needed do not use
        return $array;
    }         
    
    /**
    * Get arrays next key (only works with numeric key )
    * 
    * @version 0.2 - return 0 if not array, used to return 1 but no longer a reason to do that
    * @author Ryan Bayne
    */
    public function get_array_nextkey( $array ){
        if(!is_array( $array ) || empty( $array ) ){
            return 0;   
        }
        
        ksort( $array );
        end( $array );
        return key( $array ) + 1;
    }
    
    /**
    * Gets the schedule array from wordpress option table.
    * Array [times] holds permitted days and hours.
    * Array [limits] holds the maximum post creation numbers 
    */
    public static function get_option_schedule_array() {
        $trainingtools_schedule_array = get_option( 'trainingtools_schedule' );
        return maybe_unserialize( $trainingtools_schedule_array );    
    }
    
    /**
    * Builds text link, also validates it to ensure it still exists.
    * 
    * The idea of this function is to ensure links used throughout the plugins interface
    * are not broken. Over time links may no longer point to a page that exists, we want to 
    * know about this quickly then replace the url.
    * 
    * @return $link, return or echo using $response parameter
    * 
    * @param mixed $text
    * @param mixed $url
    * @param mixed $htmlentities, optional (string of url passed variables)
    * @param string $target, _blank _self etc
    * @param string $class, css class name (common: button)
    * @param strong $response [echo][return]
    * 
    * @todo add functionality to report invalid URL in use
    */
    public function link( $text, $url, $htmlentities = '', $target = '_blank', $class = '', $response = 'echo', $title = '' ){
        // add ? to $middle if there is no proper join after the domain
        $middle = '';
                                 
        // decide class
        if( $class != '' ){$class = 'class="'.$class.'"';}
        
        // build final url
        $finalurl = $url.$middle.htmlentities( $htmlentities );
        
        // check the final result is valid else use a default fault page
        $valid_result = self::validate_url( $finalurl );
        
        if( $valid_result ){
            $link = '<a href="'.$finalurl.'" '.$class.' target="'.$target.'" title="'.$title.'">'.$text.'</a>';
        }else{
            $linktext = __( 'Invalid Link, Click To Report' );
            $link = '<a href="http://www.webtechglobal.co.uk/wtg-blog/invalid-application-link/" target="_blank">'.$linktext.'</a>';        
        }
        
        if( $response == 'echo' ){
            echo $link;
        }else{
            return $link;
        }     
    }     
    
    /**
    * Updates the schedule array from wordpress option table.
    * Array [times] holds permitted days and hours.
    * Array [limits] holds the maximum post creation numbers 
    */
    public function update_option_schedule_array( $schedule_array ){
        $schedule_array_serialized = maybe_serialize( $schedule_array );
        return update_option( 'trainingtools_schedule', $schedule_array_serialized);    
    }
    
    public function update_settings( $trainingtools_settings ){
        $admin_settings_array_serialized = maybe_serialize( $trainingtools_settings );
        return update_option( 'trainingtools_settings', $admin_settings_array_serialized);    
    }
    
    /**
    * Returns WordPress version in short
    * 1. Default returned example by get_bloginfo( 'version' ) is 3.6-beta1-24041
    * 2. We remove everything after the first hyphen
    */
    public function get_wp_version() {
        $longversion = get_bloginfo( 'version' );
        return strstr( $longversion , '-', true );
    }
    
    /**
    * Determines if the giving value is a Training Tools page or not
    */
    public function is_plugin_page( $page){
        return strstr( $page, 'trainingtools' );  
    } 

    /**
    * Get POST ID using post_name (slug)
    * 
    * @param string $name
    * @return string|null
    */
    public function get_post_ID_by_postname( $name){
        global $wpdb;
        // get page id using custom query
        return $wpdb->get_var( "SELECT ID 
        FROM $wpdb->posts 
        WHERE post_name = '".$name."' 
        AND post_type='page' ");
    }       
    
    /**
    * Returns all the columns in giving database table that hold data of the giving data type.
    * The type will be determined with PHP not based on MySQL column data types. 
    * 1. Table must have one or more records
    * 2. 1 record will be queried 
    * 3. Each columns values will be tested by PHP to determine data type
    * 4. Array returned with column names that match the giving type
    * 5. If $dt is false, all columns will be returned with their type however that is not the main purpose of this function
    * 6. Types can be custom, using regex etc. The idea is to establish if a value is of the pattern suitable for intended use.
    * 
    * @param string $tableName table name
    * @param string $dataType data type URL|IMG|NUMERIC|STRING|ARRAY
    * 
    * @returns false if no record could be found
    */
    public function cols_by_datatype( $tableName, $dataType = false ){
        global $wpdb;
        
        $ra = array();// returned array - our array of columns matching data type
        $matchCount = 0;// matches
        $ra['arrayinfo']['matchcount'] = $matchCount;

        $rec = $wpdb->get_results( 'SELECT * FROM '. $tableName .'  LIMIT 1', ARRAY_A );
        if(!$rec){return false;}
        
        $knownTypes = array();
        foreach( $rec as $id => $value_array ){
            foreach( $value_array as $column => $value ){     
                             
                $isURL = self::is_url( $value );
                if( $isURL){++$matchCount;$ra['matches'][] = $column;}
           
            }       
        }
        
        $ra['arrayinfo']['matchcount'] = $matchCount;
        return $ra;
    }  
    
    public function querylog_bytype( $type = 'all', $limit = 100){
        global $wpdb;

        // where
        $where = '';
        if( $type != 'all' ){
          $where = 'WHERE type = "'.$type.'"';
        }

        // limit
        $limit = 'LIMIT ' . $limit;
        
        // get_results
        $rows = $wpdb->get_results( 
        "
        SELECT * 
        FROM trainingtools_log
        ".$where."
        ".$limit."

        ", ARRAY_A );

        if(!$rows){
            return false;
        }else{
            return $rows;
        }
    }  
    
    /**
    * Determines if all tables in a giving array exist or not
    * @returns boolean true if all table exist else false if even one does not
    */
    public function tables_exist( $tables_array ){
        if( $tables_array && is_array( $tables_array ) ){         
            // foreach table in array, if one does not exist return false
            foreach( $tables_array as $key => $table_name){
                $table_exists = $this->DB->does_table_exist( $table_name);  
                if(!$table_exists){          
                    return false;
                }
            }        
        }
        return true;    
    } 
    
    /**
    * builds a url for form action, allows us to force the submission to specific tabs
    * 
    * @param mixed $values_array
    * 
    * @todo assumes $_GET['page] exists - consider using get_admin_page_name() 
    * then apply any changes to all WTG plugins.
    */
    public function form_action( $values_array = false ){
        $get_values = '';

        // apply passed values
        if(is_array( $values_array ) ){
            foreach( $values_array as $varname => $value ){
                $get_values .= '&' . $varname . '=' . $value;
            }
        }
        
        echo self::url_toadmin( $_GET['page'], $get_values);    
    }
    
    /**
    * count the number of posts in the giving month for the giving post type
    * 
    * @param mixed $month
    * @param mixed $year
    * @param mixed $post_type
    */
    public function count_months_posts( $month, $year, $post_type){                    
        $countposts = get_posts( "year=$year&monthnum=$month&post_type=$post_type");
        return count( $countposts);    
    }     
    
    /**
    * Create new posts/pages
    * 
    * CURRENTLY NOT READY FOR USE - was taking from CSV 2 POST but not suitable to call in general use yet
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0.2
    * 
    * @param mixed $project_id
    * @param mixed $total - apply a limit for this import (global settings can offer a default limit suitable for server also)
    * @param mixed $row_ids - only use if creating posts using specific row ID's 
    */
    public function create_posts( $project_id, $total = 1 ){
        global $trainingtools_settings;
        
        $autoblog = new TRAININGTOOLS_InsertPost();
        $autoblog->settings = $trainingtools_settings;
        $autoblog->projectid = $project_id;
        $autoblog->maintable = $database_table;
        $autoblog->projectsettings = maybe_unserialize( $autoblog->project->projectsettings );// unserialize settings
        $autoblog->projectcolumns = $columnheaders_array;
        $autoblog->idcolumn = $idcolumn;
        
        // we will control how and when we end the operation
        $autoblog->finished = false;// when true, output will be complete and foreach below will discontinue, this can be happen if maximum execution time is reached
        
        $foreach_done = 0;
        foreach( $unused_rows as $key => $row){
            ++$foreach_done;
                    
            // to get the output at the end, tell the class we are on the final post, only required in "manual" requestmethod
            if( $foreach_done == $total){    
                $autoblog->finished = true;// not completely finished, indicates this is the last post
            }
            
            // pass row to $autob
            $autoblog->row = $row;    
            // create a post - start method is the beginning of many nested functions
            $autoblog->start();
        }
    }
    
    /**
    * Update one or more posts
    * 1. can pass a post ID and force update even if imported row has not changed
    * 2. Do not pass a post ID and query is done to get changed imported rows only to avoid over processing
    * 
    * CURRENTLY NOT READY FOR USE - was taking from CSV 2 POST but not suitable to call in general use yet
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 7.0.0
    * @version 1.0.2
    * 
    * @param integer $project_id
    * @param integer $total
    * @param mixed $post_id boolean false or integer post ID
    * @param array $atts
    */
    public function update_posts( $project_id, $total = 1, $post_id = false, $atts = array() ){
        global $trainingtools_settings;
        
        extract( shortcode_atts( array( 
            'rows' => false
        ), $atts ) );
                
        $autoblog = new TRAININGTOOLS_UpdatePost();
        $autoblog->settings = $trainingtools_settings;
        $autoblog->maintable = $database_table;

        // we will control how and when we end the operation
        $autoblog->finished = false;// when true, output will be complete and foreach below will discontinue, this can be happen if maximum execution time is reached
        
        $idcolumn = false;
        if( isset( $autoblog->projectsettings['idcolumn'] ) ){
            $idcolumn = $autoblog->projectsettings['idcolumn'];    
        }
                               
        // get rows updated and not yet applied, this is a default query
        // pass a query result to $updated_rows to use other rows
        if( $post_id === false ){
            $updated_rows = self::get_updated_rows( $project_id, $total, $idcolumn);
        }else{
            $updated_rows = self::get_posts_record( $project_id, $post_id, $idcolumn);
        }
        
        if( !$updated_rows ){
            $this->UI->create_notice( __( 'None of your imported rows have been updated since their original import.' ), 'info', 'Small', 'No Rows Updated' );
            return;
        }
            
        $foreach_done = 0;
        foreach( $updated_rows as $key => $row){
            ++$foreach_done;
                        
            // to get the output at the end, tell the class we are on the final post, only required in "manual" requestmethod
            if( $foreach_done == $total){
                $autoblog->finished = true;
            }            
            // pass row to $autob
            $autoblog->row = $row;    
            // create a post - start method is the beginning of many nested functions
            $autoblog->start();
        }          
    }
    
    /**
    * determines if the giving term already exists within the giving level
    * 
    * this is done first by checking if the term exists in the blog anywhere at all, if not then it is an instant returned false.
    * if a match term name is found, then we investigate its use i.e. does it have a parent and does that parent have a parent. 
    * we count the number of levels and determine the existing terms level
    * 
    * if term exists in level then that terms ID is returned so that we can make use of it
    * 
    * @param mixed $term_name
    * @param mixed $level
    * 
    * @deprecated TRAININGTOOLS_Categories class created
    */
    public function term_exists_in_level( $term_name = 'No Term Giving', $level = 0){                 
        global $wpdb;
        $all_terms_array = $this->DB->selectwherearray( $wpdb->terms, "name = '$term_name'", 'term_id', 'term_id' );
        if(!$all_terms_array ){return false;}

        $match_found = false;
                
        foreach( $all_terms_array as $key => $term_array ){
                     
            $term = get_term( $term_array['term_id'], 'category',ARRAY_A);

            // if level giving is zero and the current term does not have a parent then it is a match
            // we return the id to indicate that the term exists in the level
            if( $level == 0 && $term['parent'] === 0){      
                return $term['term_id'];
            }
             
            // get the current terms parent and the parent of that parent
            // keep going until we reach level one
            $toplevel = false;
            $looped = 0;    
            $levels_counted = 0;
            $parent_termid = $term['parent'];
            while(!$toplevel){    
                                
                // we get the parent of the current term
                $category = get_category( $parent_termid );  

                if( is_wp_error( $category )|| !isset( $category->category_parent ) || $category->category_parent === 0){
                    
                    $toplevel = true;
                    
                }else{ 
                    
                    // term exists and must be applied as a parent for the new category
                    $parent_termid = $category->category_parent;
                    
                }
                      
                ++$looped;
                if( $looped == 20){break;}
                
                ++$levels_counted;
            }  
            
            // so after the while we have a count of the number of levels above the "current term"
            // if that count + 1 matches the level required for the giving term term then we have a match, return current term_id
            $levels_counted = $levels_counted;
            if( $levels_counted == $level){
                return $term['term_id'];
            }       
        }
                  
        // arriving here means no match found, either create the term or troubleshoot if there really is meant to be a match
        return false;
    }
    
    /**
    * removes plugins name from $_GET['page'] and returns the rest, else returns main to indicate parent
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function get_admin_page_name() {
        if( !isset( $_GET['page'] ) ){
            return 'main';
        }
        $exloded = explode( '_', $_GET['page'] );
        return end( $exloded );        
    }

    /**
    * Get all capabilities in array.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function capabilities() {
        global $wp_roles; 
        $capabilities_array = array();
        foreach( $wp_roles->roles as $role => $role_array ) { 
            $capabilities_array = array_merge( $capabilities_array, $role_array['capabilities'] );    
        }
        return $capabilities_array;
    }
    
    /**
    * Create a new project with minimum of details. The 
    * focus is on creating an ID that will be shared with
    * other plugins.
    * 
    * Then build the projects training information.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    * 
    * @todo set new project as current active (focused) project
    */
    public function insert_project( $project_name ) {
        global $wpdb;
        $new_project_id = $this->DB->insert( $wpdb->webtechglobal_projects, array( 'projectname' => $project_name ) );   
        if( !is_numeric( $new_project_id ) ) {
            return false;    
        }        
    }                   
    
    /**
    * Query projects.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0             
    * 
    * @param mixed $id_or_name
    */
    public function get_projects() {
        global $wpdb;
        return $this->DB->selectwherearray( $wpdb->webtechglobal_projects, 'archived != true', 'project_id', '*', 'ARRAY_A' );
    }

    /**
    * Insert (register) a new page into the training system.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function register_ttpage( $projectid, $title, $uiid, $intro, $videourl ) {
        global $wpdb;
        
        // insert new page then add page meta
        $fields = array(
            'project_id' => $projectid,
            'title' => $title,
            'view_id' => $uiid,
        );    
        
        $new_ttpage_id = $this->DB->insert( $wpdb->tt_pages, $fields );
        
        if( $new_ttpage_id ) {    
            // add tt page meta
            if( !empty ( $intro ) ) {    
                self::add_ttpage_meta( $new_ttpage_id, 'introduction', $intro, true );   
            }
            
            if( !empty ( $videourl ) ) {      
                self::add_ttpage_meta( $new_ttpage_id, 'videourl', $videourl, false );    
            }
        }
    }

    /**
    * Register new form.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function enter_ttform( $ttpage_id, $form_id, $form_name ) {
        global $wpdb;

        $fields = array(
            'location_id' => $ttpage_id,
            'location_type' => 'wtgadminpage',
            'form_id' => $form_id,
            'form_name' => $form_name            
        );

        $result = $this->DB->insert( $wpdb->tt_forms, $fields );  
        
        return $result;
    }

    /**
    * Insert form input.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function enter_ttforminput( $form_id, $inputs_title, $attributename, $attributeid ) {
        global $wpdb;

        $fields = array(

            'form_id' => $form_id,
            'att_id' => $attributeid,
            'att_name' => $attributename,

        );
      
        $result = $this->DB->insert( $wpdb->tt_inputs, $fields );  
        
        return $result;
    }
       
    /**
    * Get ttpage meta data.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function get_ttpage_meta( $ttpage_id, $meta_key, $single = true ) {   
        return get_metadata( 'tt_pages', $ttpage_id, $meta_key, $single );
    }
        
    /**
    * Add ttpage meta value to tt_pagesmeta table.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function add_ttpage_meta( $ttpage_id, $meta_key, $meta_value, $unique = true ) {
        return add_metadata( 'tt_pages', $ttpage_id, $meta_key, $meta_value, $unique );    
    }

    /**
    * Update a ttpage meta value in the tt_pagesmeta table. 
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function update_ttpage_meta( $ttpage_id, $meta_key, $meta_value, $prev_value = null ) {
        return update_metadata( 'tt_pages', $ttpage_id, $meta_key, $meta_value, $prev_value );   
    }

    /**
    * Delete ttpage meta from the tt_pagesmeta table.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function delete_ttpage_meta( $ttpage_id, $meta_key, $meta_value = null, $delete_all = null ) {
        return delete_metadata( 'tt_pages', $ttpage_id, $meta_key, $meta_value, $delete_all );    
    }

    /**
    * Returns a single ttpage with all of its meta.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function get_ttpage_byid( $ttpage_id ) {
        global $wpdb;
        return $this->DB->selectrow( $wpdb->tt_pages, 'ttpage_id = "' . $ttpage_id . '"', '*' );      
    }
       
    /**
    * Delete a TT Page and all data related to it.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function deletepage ( $ttpage_id ) {
        global $wpdb;
        
        // delete main page record
        $tablename = $wpdb->tt_pages;
        $condition = "ttpage_id = $ttpage_id";
        $wpdb->query( "DELETE FROM $tablename WHERE $condition ");  
        
        // delete page meta
        self::delete_ttpage_meta( $ttpage_id, 'introduction' );
        self::delete_ttpage_meta( $ttpage_id, 'videourl' );          
    }   
    
    /**
    * Query data in wp_tt_pages table.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function get_pages() {
        global $wpdb;
        return $wpdb->get_results( "
        
            SELECT * 
            FROM $wpdb->tt_pages
            
        ", ARRAY_A);    
    }
    
    /**
    * Query data in wp_tt_forms table.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function get_forms() {
        global $wpdb;
        return $wpdb->get_results( "
        
            SELECT * 
            FROM $wpdb->tt_forms
            
        ", ARRAY_A);    
    }
        
    /**
    * Query data in wp_tt_inputs table.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function get_inputs() {
        global $wpdb;
        return $wpdb->get_results( "
        
            SELECT * 
            FROM $wpdb->tt_inputs
            
        ", ARRAY_A);    
    }
                       
}// end TRAININGTOOLS class 

if(!class_exists( 'WP_List_Table' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
        
/**
* Lists tickets post type using standard WordPress list table
*/
class TRAININGTOOLS_Log_Table extends WP_List_Table {
    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct() {
        global $status, $page;
             
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default( $item, $column_name ){
             
        $attributes = "class=\"$column_name column-$column_name\"";
                
        switch( $column_name ){
            case 'row_id':
                return $item['row_id'];    
                break;
            case 'timestamp':
                return $item['timestamp'];    
                break;                
            case 'outcome':
                return $item['outcome'];
                break;
            case 'category':
                echo $item['category'];  
                break;
            case 'action':
                echo $item['action'];  
                break;  
            case 'line':
                echo $item['line'];  
                break;                 
            case 'file':
                echo $item['file'];  
                break;                  
            case 'function':
                echo $item['function'];  
                break;                  
            case 'sqlresult':
                echo $item['sqlresult'];  
                break;       
            case 'sqlquery':
                echo $item['sqlquery'];  
                break; 
            case 'sqlerror':
                echo $item['sqlerror'];  
                break;       
            case 'wordpresserror':
                echo $item['wordpresserror'];  
                break;       
            case 'screenshoturl':
                echo $item['screenshoturl'];  
                break;       
            case 'userscomment':
                echo $item['userscomment'];  
                break;  
            case 'page':
                echo $item['page'];  
                break;
            case 'version':
                echo $item['version'];  
                break;
            case 'panelname':
                echo $item['panelname'];  
                break; 
            case 'tabscreenname':
                echo $item['tabscreenname'];  
                break;
            case 'dump':
                echo $item['dump'];  
                break; 
            case 'ipaddress':
                echo $item['ipaddress'];  
                break; 
            case 'userid':
                echo $item['userid'];  
                break; 
            case 'comment':
                echo $item['comment'];  
                break;
            case 'type':
                echo $item['type'];  
                break; 
            case 'priority':
                echo $item['priority'];  
                break;  
            case 'thetrigger':
                echo $item['thetrigger'];  
                break; 
                                        
            default:
                return 'No column function or default setup in switch statement';
        }
    }
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns() {
        $columns = array(
            'row_id' => 'Row ID',
            'timestamp' => 'Timestamp',
            'category'     => 'Category'
        );
        
        if( isset( $this->action ) ){
            $columns['action'] = 'Action';
        }                                       
           
        if( isset( $this->line ) ){
            $columns['line'] = 'Line';
        } 
                     
        if( isset( $this->file ) ){
            $columns['file'] = 'File';
        }
                
        if( isset( $this->function ) ){
            $columns['function'] = 'Function';
        }        
  
        if( isset( $this->sqlresult ) ){
            $columns['sqlresult'] = 'SQL Result';
        }

        if( isset( $this->sqlquery ) ){
            $columns['sqlquery'] = 'SQL Query';
        }
 
        if( isset( $this->sqlerror ) ){
            $columns['sqlerror'] = 'SQL Error';
        }
          
        if( isset( $this->wordpresserror ) ){
            $columns['wordpresserror'] = 'WP Error';
        }

        if( isset( $this->screenshoturl ) ){
            $columns['screenshoturl'] = 'Screenshot';
        }
        
        if( isset( $this->userscomment ) ){
            $columns['userscomment'] = 'Users Comment';
        }
 
        if( isset( $this->columns_array->page ) ){
            $columns['page'] = 'Page';
        }

        if( isset( $this->version ) ){
            $columns['version'] = 'Version';
        }
 
        if( isset( $this->panelname ) ){
            $columns['panelname'] = 'Panel Name';
        }
  
        if( isset( $this->tabscreenid ) ){
            $columns['tabscreenid'] = 'Screen ID';
        }

        if( isset( $this->tabscreenname ) ){
            $columns['tabscreenname'] = 'Screen Name';
        }

        if( isset( $this->dump ) ){
            $columns['dump'] = 'Dump';
        }

        if( isset( $this->ipaddress) ){
            $columns['ipaddress'] = 'IP Address';
        }

        if( isset( $this->userid ) ){
            $columns['userid'] = 'User ID';
        }

        if( isset( $this->comment ) ){
            $columns['comment'] = 'Comment';
        }

        if( isset( $this->type ) ){
            $columns['type'] = 'Type';
        }
                                    
        if( isset( $this->priority ) ){
            $columns['priority'] = 'Priority';
        }
       
        if( isset( $this->thetrigger ) ){
            $columns['thetrigger'] = 'Trigger';
        }

        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items_further() and sort
     * your data accordingly (usually by modifying your query ).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array( 'data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            //'post_title'     => array( 'post_title', false ),     //true means it's already sorted
        );
        return $sortable_columns;
    }
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(

        );
        return $actions;
    }
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items_further()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die( 'Items deleted (or they would be if we had items to delete)!' );
        }
        
    }
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items_further( $data, $per_page = 5) {
        global $wpdb; //This is used only if making any database queries        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array( $columns, $hidden, $sortable);
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
      
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count( $data);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice( $data,(( $current_page-1)*$per_page), $per_page);
 
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
  
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil( $total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
?>