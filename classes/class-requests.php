<?php
/** 
* Class for handling $_POST and $_GET requests
* 
* The class is called in the process_admin_POST_GET() method found in the TRAININGTOOLS class. 
* The process_admin_POST_GET() method is hooked at admin_init. It means requests are handled in the admin
* head, globals can be updated and pages will show the most recent data. Nonce security is performed
* within process_admin_POST_GET() then the require method for processing the request is used.
* 
* Methods in this class MUST be named within the form or link itself, basically a unique identifier for the form.
* i.e. the Section Switches settings have a form name of "sectionswitches" and so the method in this class used to
* save submission of the "sectionswitches" form is named "sectionswitches".
* 
* process_admin_POST_GET() uses eval() to call class + method 
* 
* @package Training Tools
* @author Ryan Bayne   
* @since 0.0.1
* @version 1.2
*/

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class TRAININGTOOLS_Requests {  
    public function __construct() {
        global $trainingtools_settings;
    
        // create class objects
        $this->TRAININGTOOLS = TRAININGTOOLS::load_class( 'TRAININGTOOLS', 'class-trainingtools.php', 'classes' ); # plugin specific functions
        $this->UI = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_UI', 'class-ui.php', 'classes' ); # interface, mainly notices
        $this->DB = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_DB', 'class-wpdb.php', 'classes' ); # database interaction
        $this->PHP = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_PHP', 'class-phplibrary.php', 'classes' ); # php library by Ryan R. Bayne
        $this->Files = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_Files', 'class-files.php', 'classes' );
        $this->FORMS = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_Formbuilder', 'class-forms.php', 'classes' );
        $this->TabMenu = $this->TRAININGTOOLS->load_class( "TRAININGTOOLS_TabMenu", "class-pluginmenu.php", 'classes','pluginmenu' );    
    }
    
    /**
    * Applies WebTechGlobals own security for $_POST and $_GET requests. It involves
    * a range of validation, including ensuring HTML source edit was not performed before
    * users submission.
    * 
    * This function is called by process_admin_POST_GET() which is hooked by admin_init.
    * None security is done in that function before this class-request.php file is loaded.
    * 
    * @parameter $method is post or get or ajax
    * @parameter $function the method for completing the request, to be found in this class
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.1
    */
    public function process_admin_request( $method, $function ) { 
          
        // arriving here means check_admin_referer() security is positive       
        global $cont;

        $this->PHP->var_dump( $_POST, '<h1>$_POST</h1>' );           
        $this->PHP->var_dump( $_GET, '<h1>$_GET</h1>' );    
                              
        // $_POST security
        if( $method == 'post' || $method == 'POST' || $method == '$_POST' ) {                      
            // check_admin_referer() wp_die()'s if security fails so if we arrive here WordPress security has been passed
            // now we validate individual values against their pre-registered validation method
            // some generic notices are displayed - this system makes development faster
            $post_result = true;
            $post_result = $this->FORMS->apply_form_security();// ensures $_POST['trainingtools_form_formid'] is set, so we can use it after this line
            
            // apply my own level of security per individual input
            if( $post_result ){ $post_result = $this->FORMS->apply_input_security(); }// detect hacking of individual inputs i.e. disabled inputs being enabled 
            
            // validate users values
            if( $post_result ){ $post_result = $this->FORMS->apply_input_validation( $_POST['trainingtools_form_formid'] ); }// values (string,numeric,mixed) validation

            // cleanup to reduce registered data
            $this->FORMS->deregister_form( $_POST['trainingtools_form_formid'] );
                    
            // if $overall_result includes a single failure then there is no need to call the final function
            if( $post_result === false ) {        
                return false;
            }
        }
      
        // handle a situation where the submitted form requests a function that does not exist
        if( !method_exists( $this, $function ) ){
            wp_die( sprintf( __( "The method for processing your request was not found. This can usually be resolved quickly. Please report method %s does not exist. <a href='https://www.youtube.com/watch?v=vAImGQJdO_k' target='_blank'>Watch a video</a> explaining this problem.", 'trainingtools' ), 
            $function) ); 
            return false;// should not be required with wp_die() but it helps to add clarity when browsing code and is a precaution.   
        }
        
        // all security passed - call the processing function
        if( isset( $function) && is_string( $function ) ) {
            eval( 'self::' . $function .'();' );
        }
    }  

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */    
    public function request_success( $form_title, $more_info = '' ){  
        $this->UI->create_notice( "Your submission for $form_title was successful. " . $more_info, 'success', 'Small', "$form_title Updated");          
    } 

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */    
    public function request_failed( $form_title, $reason = '' ){
        $this->UI->n_depreciated( $form_title . ' Unchanged', "Your settings for $form_title were not changed. " . $reason, 'error', 'Small' );    
    }

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */    
    public function logsettings() {
        global $trainingtools_settings;
        $trainingtools_settings['globalsettings']['uselog'] = $_POST['trainingtools_radiogroup_logstatus'];
        $trainingtools_settings['globalsettings']['loglimit'] = $_POST['trainingtools_loglimit'];
                                                   
        ##################################################
        #           LOG SEARCH CRITERIA                  #
        ##################################################
        
        // first unset all criteria
        if( isset( $trainingtools_settings['logsettings']['logscreen'] ) ){
            unset( $trainingtools_settings['logsettings']['logscreen'] );
        }
                                                           
        // if a column is set in the array, it indicates that it is to be displayed, we unset those not to be set, we dont set them to false
        if( isset( $_POST['trainingtools_logfields'] ) ){
            foreach( $_POST['trainingtools_logfields'] as $column){
                $trainingtools_settings['logsettings']['logscreen']['displayedcolumns'][$column] = true;                   
            }
        }
                                                                                 
        // outcome criteria
        if( isset( $_POST['trainingtools_log_outcome'] ) ){    
            foreach( $_POST['trainingtools_log_outcome'] as $outcomecriteria){
                $trainingtools_settings['logsettings']['logscreen']['outcomecriteria'][$outcomecriteria] = true;                   
            }            
        } 
        
        // type criteria
        if( isset( $_POST['trainingtools_log_type'] ) ){
            foreach( $_POST['trainingtools_log_type'] as $typecriteria){
                $trainingtools_settings['logsettings']['logscreen']['typecriteria'][$typecriteria] = true;                   
            }            
        }         

        // category criteria
        if( isset( $_POST['trainingtools_log_category'] ) ){
            foreach( $_POST['trainingtools_log_category'] as $categorycriteria){
                $trainingtools_settings['logsettings']['logscreen']['categorycriteria'][$categorycriteria] = true;                   
            }            
        }         

        // priority criteria
        if( isset( $_POST['trainingtools_log_priority'] ) ){
            foreach( $_POST['trainingtools_log_priority'] as $prioritycriteria){
                $trainingtools_settings['logsettings']['logscreen']['prioritycriteria'][$prioritycriteria] = true;                   
            }            
        }         

        ############################################################
        #         SAVE CUSTOM SEARCH CRITERIA SINGLE VALUES        #
        ############################################################
        // page
        if( isset( $_POST['trainingtools_pluginpages_logsearch'] ) && $_POST['trainingtools_pluginpages_logsearch'] != 'notselected' ){
            $trainingtools_settings['logsettings']['logscreen']['page'] = $_POST['trainingtools_pluginpages_logsearch'];
        }   
        // action
        if( isset( $_POST['trainingtools_logactions_logsearch'] ) && $_POST['trainingtools_logactions_logsearch'] != 'notselected' ){
            $trainingtools_settings['logsettings']['logscreen']['action'] = $_POST['trainingtools_logactions_logsearch'];
        }   
        // screen
        if( isset( $_POST['trainingtools_pluginscreens_logsearch'] ) && $_POST['trainingtools_pluginscreens_logsearch'] != 'notselected' ){
            $trainingtools_settings['logsettings']['logscreen']['screen'] = $_POST['trainingtools_pluginscreens_logsearch'];
        }  
        // line
        if( isset( $_POST['trainingtools_logcriteria_phpline'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['line'] = $_POST['trainingtools_logcriteria_phpline'];
        }  
        // file
        if( isset( $_POST['trainingtools_logcriteria_phpfile'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['file'] = $_POST['trainingtools_logcriteria_phpfile'];
        }          
        // function
        if( isset( $_POST['trainingtools_logcriteria_phpfunction'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['function'] = $_POST['trainingtools_logcriteria_phpfunction'];
        }
        // panel name
        if( isset( $_POST['trainingtools_logcriteria_panelname'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['panelname'] = $_POST['trainingtools_logcriteria_panelname'];
        }
        // IP address
        if( isset( $_POST['trainingtools_logcriteria_ipaddress'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['ipaddress'] = $_POST['trainingtools_logcriteria_ipaddress'];
        }
        // user id
        if( isset( $_POST['trainingtools_logcriteria_userid'] ) ){
            $trainingtools_settings['logsettings']['logscreen']['userid'] = $_POST['trainingtools_logcriteria_userid'];
        }
        
        $this->TRAININGTOOLS->update_settings( $trainingtools_settings );
        $this->UI->n_postresult_depreciated( 'success', __( 'Log Settings Saved', 'trainingtools' ), __( 'It may take sometime for new log entries to be created depending on your websites activity.', 'trainingtools' ) );  
    }  
    
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */       
    public function beginpluginupdate() {
        $this->Updates = $this->TRAININGTOOLS->load_class( 'TRAININGTOOLS_Formbuilder', 'class-forms.php', 'classes' );
        
        // check if an update method exists, else the plugin needs to do very little
        eval( '$method_exists = method_exists ( $this->Updates , "patch_' . $_POST['trainingtools_plugin_update_now'] .'" );' );

        if( $method_exists){
            // perform update by calling the request version update procedure
            eval( '$update_result_array = $this->Updates->patch_' . $_POST['trainingtools_plugin_update_now'] .'( "update");' );       
        }else{
            // default result to true
            $update_result_array['failed'] = false;
        } 
      
        if( $update_result_array['failed'] == true){           
            $this->UI->create_notice( __( 'The update procedure failed, the reason should be displayed below. Please try again unless the notice below indicates not to. If a second attempt fails, please seek support.', 'trainingtools' ), 'error', 'Small', __( 'Update Failed', 'trainingtools' ) );    
            $this->UI->create_notice( $update_result_array['failedreason'], 'info', 'Small', 'Update Failed Reason' );
        }else{  
            // storing the current file version will prevent user coming back to the update screen        
            update_option( 'trainingtools_installedversion', TRAININGTOOLS_VERSION);

            $this->UI->create_notice( __( 'Good news, the update procedure was complete. If you do not see any errors or any notices indicating a problem was detected it means the procedure worked. Please ensure any new changes suit your needs.', 'trainingtools' ), 'success', 'Small', __( 'Update Complete', 'trainingtools' ) );
            
            // do a redirect so that the plugins menu is reloaded
            wp_redirect( get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=trainingtools' );
            exit;                
        }
    }
    
    /**
    * Save drip feed limits  
    */
    public function schedulerestrictions() {
        $trainingtools_schedule_array = $this->TRAININGTOOLS->get_option_schedule_array();
        
        // if any required values are not in $_POST set them to zero
        if(!isset( $_POST['day'] ) ){
            $trainingtools_schedule_array['limits']['day'] = 0;        
        }else{
            $trainingtools_schedule_array['limits']['day'] = $_POST['day'];            
        }
        
        if(!isset( $_POST['hour'] ) ){
            $trainingtools_schedule_array['limits']['hour'] = 0;
        }else{
            $trainingtools_schedule_array['limits']['hour'] = $_POST['hour'];            
        }
        
        if(!isset( $_POST['session'] ) ){
            $trainingtools_schedule_array['limits']['session'] = 0;
        }else{
            $trainingtools_schedule_array['limits']['session'] = $_POST['session'];            
        }
                                 
        // ensure $trainingtools_schedule_array is an array, it may be boolean false if schedule has never been set
        if( isset( $trainingtools_schedule_array ) && is_array( $trainingtools_schedule_array ) ){
            
            // if times array exists, unset the [times] array
            if( isset( $trainingtools_schedule_array['days'] ) ){
                unset( $trainingtools_schedule_array['days'] );    
            }
            
            // if hours array exists, unset the [hours] array
            if( isset( $trainingtools_schedule_array['hours'] ) ){
                unset( $trainingtools_schedule_array['hours'] );    
            }
            
        }else{
            // $schedule_array value is not array, this is first time it is being set
            $trainingtools_schedule_array = array();
        }
        
        // loop through all days and set each one to true or false
        if( isset( $_POST['trainingtools_scheduleday_list'] ) ){
            foreach( $_POST['trainingtools_scheduleday_list'] as $key => $submitted_day ){
                $trainingtools_schedule_array['days'][$submitted_day] = true;        
            }  
        } 
        
        // loop through all hours and add each one to the array, any not in array will not be permitted                              
        if( isset( $_POST['trainingtools_schedulehour_list'] ) ){
            foreach( $_POST['trainingtools_schedulehour_list'] as $key => $submitted_hour){
                $trainingtools_schedule_array['hours'][$submitted_hour] = true;        
            }           
        }    

        if( isset( $_POST['deleteuserswaiting'] ) )
        {
            $trainingtools_schedule_array['eventtypes']['deleteuserswaiting']['switch'] = 'enabled';                
        }
        
        if( isset( $_POST['eventsendemails'] ) )
        {
            $trainingtools_schedule_array['eventtypes']['sendemails']['switch'] = 'enabled';    
        }        
  
        $this->TRAININGTOOLS->update_option_schedule_array( $trainingtools_schedule_array );
        $this->UI->notice_depreciated( __( 'Schedule settings have been saved.', 'trainingtools' ), 'success', 'Large', __( 'Schedule Times Saved', 'trainingtools' ) );   
    } 
    
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */       
    public function logsearchoptions() {
        $this->UI->n_postresult_depreciated( 'success', __( 'Log Search Settings Saved', 'trainingtools' ), __( 'Your selections have an instant effect. Please browse the Log screen for the results of your new search.', 'trainingtools' ) );                   
    }
 
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */        
    public function defaultcontenttemplate () {        
        $this->UI->create_notice( __( 'Your default content template has been saved. This is a basic template, other advanced options may be available by activating the Training Tools Templates custom post type (pro edition only) for managing multiple template designs.' ), 'success', 'Small', __( 'Default Content Template Updated' ) );         
    }
        
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */       
    public function reinstalldatabasetables() {
        $installation = new TRAININGTOOLS_Install();
        $installation->reinstalldatabasetables();
        $this->UI->create_notice( 'All tables were re-installed. Please double check the database status list to
        ensure this is correct before using the plugin.', 'success', 'Small', 'Tables Re-Installed' );
    }
     
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */          
    public function globalswitches() {
        global $trainingtools_settings;
        $trainingtools_settings['noticesettings']['wpcorestyle'] = $_POST['uinoticestyle'];                      
        $trainingtools_settings['posttypes']['wtgflags']['status'] = $_POST['flagsystemstatus'];
        $trainingtools_settings['widgetsettings']['dashboardwidgetsswitch'] = $_POST['dashboardwidgetsswitch'];
        $this->TRAININGTOOLS->update_settings( $trainingtools_settings ); 
        $this->UI->create_notice( __( 'Global switches have been updated. These switches can initiate the use of 
        advanced systems. Please monitor your blog and ensure the plugin operates as you expected it to. If
        anything does not appear to work in the way you require please let WebTechGlobal know.' ),
        'success', 'Small', __( 'Global Switches Updated' ) );       
    } 
       
    /**
    * save capability settings for plugins pages
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.1 
    */
    public function pagecapabilitysettings() {
        
        // get the capabilities array from WP core
        $capabilities_array = $this->TRAININGTOOLS->capabilities();

        // get stored capability settings 
        $saved_capability_array = get_option( 'trainingtools_capabilities' );
        
        // get the tab menu 
        $pluginmenu = $this->TabMenu->menu_array();
                
        // to ensure no extra values are stored (more menus added to source) loop through page array
        foreach( $pluginmenu as $key => $page_array ) {
            
            // ensure $_POST value is also in the capabilities array to ensure user has not hacked form, adding their own capabilities
            if( isset( $_POST['pagecap' . $page_array['name'] ] ) && in_array( $_POST['pagecap' . $page_array['name'] ], $capabilities_array ) ) {
                $saved_capability_array['pagecaps'][ $page_array['name'] ] = $_POST['pagecap' . $page_array['name'] ];
            }
                
        }
          
        update_option( 'trainingtools_capabilities', $saved_capability_array );
         
        $this->UI->create_notice( __( 'Capabilities for this plugins pages have been stored. Due to this being security related I recommend testing before you logout. Ensure that each role only has access to the plugin pages you intend.' ), 'success', 'Small', __( 'Page Capabilities Updated' ) );        
    }
    
    /**
    * Saves the plugins global dashboard widget settings i.e. which to display, what to display, which roles to allow access
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function dashboardwidgetsettings() {
        global $trainingtools_settings;
        
        // loop through pages
        $TRAININGTOOLS_TabMenu = TRAININGTOOLS::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        $menu_array = $TRAININGTOOLS_TabMenu->menu_array();       
        foreach( $menu_array as $key => $section_array ) {

            if( isset( $_POST[ $section_array['name'] . 'dashboardwidgetsswitch' ] ) ) {
                $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'dashboardwidgetsswitch'] = $_POST[ $section_array['name'] . 'dashboardwidgetsswitch' ];    
            }
            
            if( isset( $_POST[ $section_array['name'] . 'widgetscapability' ] ) ) {
                $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'widgetscapability'] = $_POST[ $section_array['name'] . 'widgetscapability' ];    
            }

        }

        $this->TRAININGTOOLS->update_settings( $trainingtools_settings );    
        $this->UI->create_notice( __( 'Your dashboard widget settings have been saved. Please check your dashboard to ensure it is configured as required per role.', 'trainingtools' ), 'success', 'Small', __( 'Settings Saved', 'trainingtools' ) );         
    }
    
    /**
    * Import Star Citizen images related to branding to the WordPress media gallery.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function importbranding() {
        $media_created = 0;
        $media_failed = 0;
        $corporations = true;
        $project = true;
        $backgrounds = true;
        $orgs = true;
        
        require_once( TRAININGTOOLS_ABSPATH . 'arrays/brandingmedia_array.php' );
        
        if( $corporations ) {
            foreach( $brandingmedia_array['corporations'] as $url ) {                                       
                $the_result = $this->TRAININGTOOLS->create_localmedia_fromhttp( $url );    
                if( is_numeric( $the_result ) ) {
                    ++$media_created;    
                } else {
                    ++$media_failed;
                }
            }
        }
    
        if( $project ) {
            foreach( $brandingmedia_array['project'] as $url ) {                                       
                $the_result = $this->TRAININGTOOLS->create_localmedia_fromhttp( $url );    
                if( is_numeric( $the_result ) ) {
                    ++$media_created;    
                } else {
                    ++$media_failed;
                }
            }
            }
        
        if( $backgrounds ) {
            foreach( $brandingmedia_array['backgrounds'] as $url ) {                                       
                $the_result = $this->TRAININGTOOLS->create_localmedia_fromhttp( $url );    
                if( is_numeric( $the_result ) ) {
                    ++$media_created;    
                } else {
                    ++$media_failed;
                }
            }
        }
        
        if( $orgs ) {
            foreach( $brandingmedia_array['orgs'] as $url ) {                                       
                $the_result = $this->TRAININGTOOLS->create_localmedia_fromhttp( $url );    
                if( is_numeric( $the_result ) ) {
                    ++$media_created;    
                } else {
                    ++$media_failed;
                }
            }
        }
 
        $this->UI->create_notice( __( "A total of $media_created images were imported to the WP media gallery and $media_failed failed to import.", 'trainingtools' ), 'success', 'Small', __( 'Media Import Request Complete', 'trainingtools' ) );         
    }

    /**
    * Save standard post type settings from main page.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    * 
    * @todo check that each post types file is within package else throw error and set to disabled
    */
    public function posttypessettings () {
        global $trainingtools_settings;
                    
        foreach( $trainingtools_settings['posttypes'] as $type => $posttype ) {
            $trainingtools_settings['posttypes'][ $type ]['status'] = $_POST['cpt' . $type];
        }                                      

        $this->TRAININGTOOLS->update_settings( $trainingtools_settings );
         
        // confirm outcome
        $this->UI->create_notice( __( "Disabling and/or enabling post types should be followed with a review of changes to both the admin and public side of your website. Please ensure that all post types now suit your visitor and administrator needs." ), 'info', 'Small', __( 'Post Type Settings Saved', 'csv2post' ) );               
    }

    /**
    * Process form request to create new project.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function addnewproject() {

        $this->TRAININGTOOLS->insert_project( $_POST['projectname'] ); 
        
        // confirm outcome
        $this->UI->create_notice( __( "You can begin added information to your 
        new project. If there is any type of training tool or feature you cannot 
        find in this plugin please post a request on the WebTechglobal forum. If 
        you are using other WebTechGlobal plugins you may find that your new 
        project also displays in those." ), 'info', 'Small', 
        __( 'New Project Created', 'csv2post' ) );               
    }
    
    /**
    * Handles request to focus on a specific project.
    * 
    * @author Ryan R. Bayne
    * @package WTG Tasks Manager
    * @version 1.0
    */
    public function projectfocus() {
        if( !isset( $_GET['projectid'] ) )
        {
            $this->UI->create_notice( __( 'To request focus on a specific 
            project you must include a project ID in your request.' ),
            'error', 'Small', __( 'Missing Project ID', 'wtgtasksmanager' ) );
            return false;    
        }
        
        if( !is_numeric( $_GET['projectid'] ) ) 
        {
            $this->UI->create_notice( __( 'The project ID you have included in 
            your request does not appear to be valid, please try again.' ),
            'error', 'Small', __( 'Invalid Project ID', 'wtgtasksmanager' ) );
            return false;    
        }
        
        // project focus is an integrated feature    
        update_user_meta( get_current_user_id(), 'wtgprojectfocus', $_GET['projectid'] ); 
        
        $this->UI->create_notice( __( 'Your project with ID ' . 
        $_GET['projectid'] . ' is now being focused on. All of the plugins 
        interfaces will hide information about other projects. This does not 
        apply to the custom post type which allows all projects tasks to be 
        viewed as a secondary method.' ),
        'success', 'Small', 
        __( 'Project Focused', 'wtgtasksmanager' ) );
    }  
    
    /**
    * Debug mode switch.
    * 
    * @author Ryan R. Bayne
    * @package CSV 2 POST
    * @since 0.0.1
    * @version 1.0
    */
    public function debugmodeswitch() {
        $debug_status = get_option( 'webtechglobal_displayerrors' );
        if($debug_status){
            update_option( 'webtechglobal_displayerrors',false );
            $new = 'disabled';
            
            $this->UI->create_notice( __( "Error display mode has been $new." ), 'success', 'Tiny', __( 'Debug Mode Switch', 'csv2post' ) );               
                        
            wp_redirect( get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=' . $_GET['page'] );
            exit;
        } else {
            update_option( 'webtechglobal_displayerrors',true );
            $new = 'enabled';
            
            $this->UI->create_notice( __( "Error display mode has been $new." ), 'success', 'Tiny', __( 'Debug Mode Switch', 'csv2post' ) );               
            
            wp_redirect( get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=' . $_GET['page'] );
            exit;
        }
    }

    /**
    * Re-install admin settings request from the developers toolbar menu.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function trainingtoolsreinstallsettings() {
        $this->TRAININGTOOLS->install_admin_settings();
        
        // confirm outcome
        $this->UI->create_notice( __( "The plugins main settings have been
        re-installed. It is recommended that you check all features and expected
        behaviours of the plugin." ), 'info', 'Small', 
        __( 'Settings Re-Installed', 'trainingtools' ) );               
    }
    
    /**
    * Re-install all database tables. This request is made from the Developers menu.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function trainingtoolsreinstalltables () {
        // delete all tables
        
        // 
        $installation = new TRAININGTOOLS_Install();
        $installation->reinstalldatabasetables();
        
        // confirm outcome
        $this->UI->create_notice( __( "The plugins database tables have been re-installed." ), 
        'success', 'Small', __( 'Tables Re-Installed', 'multitool' ) );               
    }             

    /**
    * Register a new page within the system.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function newpage() {
        // get active project
        $projectid = get_user_meta( get_current_user_id(),'wtgprojectfocus',true);
        
        $title = $_POST['newpagetitle']; 
        $uiid = $_POST['newpageuiid']; 
        $intro = $_POST['newpageintroduction']; 
        $videourl = $_POST['newpagevideourl'];
      
        $this->TRAININGTOOLS->register_ttpage( $projectid, $title, $uiid, $intro, $videourl );
                
        // confirm outcome
        $this->UI->create_notice( __( "You have registered a new page in your
        training system for the current active project. You can begin adding more
        information to your page using the tools provided." ), 'info', 'Small', 
        __( 'Registered Page', 'csv2post' ) );               
    }
    
    /**
    * Handle request to set current active project.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function setactiveproject() {
        global $trainingtools_settings;
        
        // update WTG global value for integrated with other plugins easily
        update_user_meta( get_current_user_id(), 'wtgprojectfocus', $_POST['selectactiveproject'] );

        // confirm outcome
        $this->UI->create_notice( __( "You have changed your current active
        project. Please remember this when using any form. Also if you have other
        WebTechGlobal plugins active and integrated. This change will apply to 
        those to help your productivity within your entire admin." ), 
        'info', 'Small', __( 'Active Project Set', 'traingtools' ) );               
    }

    /**
    * Update a TT Page introduction.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function updatepageintroduction  () {
        $this->TRAININGTOOLS->update_ttpage_meta( $_POST['thepageid'], 'introduction', $_POST['thepageintroduction'] );
        
        // confirm outcome
        $this->UI->create_notice( __( "When the actual page is viewed your new
        introduction might be displayed at the top of the screen or in the case
        of a WordPress plugin it can be found in the Help tab." ), 'info', 'Small', 
        __( 'Page Introduction Updated', 'csv2post' ) );               
    }

    /**
    * Delete a TT Page.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function deletepage() {
        $this->TRAININGTOOLS->deletepage( $_POST['thepageid3'] );
        
        // confirm outcome
        $this->UI->create_notice( __( "Your training page has been removed from
        the system and all related data for that page has also been removed
        from your database. Should this be done in error you may retrieve some 
        of the data from a software package that the hell content has been imported
        into. No actual WordPress page is created so
        no page is deleted in this action. Training Tools uses separate data to
        register information about a page and that is what you have deleted. You
        will no longer see he deleted page within your training system and help
        content will not be exported to software packages for the page." ), 'info', 'Small', 
        __( 'Page Deleted', 'csv2post' ) );               
    }

    /**
    * Add a new URL to a registered TT Page
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function addnewttpagevideourl () {
        $this->TRAININGTOOLS->add_ttpage_meta( $_POST['thepageid2'], 'videourl', 'thepagevideourl', false );
        
        // confirm outcome
        $this->UI->create_notice( __( "Right now Training Tools offers a simple
        video list for each registered page. Your new video will appear at the end
        of any existing list or be the first video if none existed. This system
        can be improved greatly with the addition of meta data per video and giving
        each one a specific purpose. Just let WebTechGlobal know you need it." ), 'info', 'Small', 
        __( 'New Video Assigned', 'csv2post' ) );               
    }

    /**
    * Register a new form for a page already registered within
    * the current active project.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function newform() {
        
        $ttpage_id = $_POST['newformttpageid'];
        $form_id = $_POST['newformattributeid'];
        $form_name = $_POST['newformattributename'];                           
        
        $this->TRAININGTOOLS->enter_ttform( $ttpage_id, $form_id, $form_name );
        
        // confirm outcome
        $this->UI->create_notice( __( "You can begin adding more information
        about the new form. A simple form may not need much input and often
        a simple sentence. A form with many inputs with a lot of options to 
        choose from may need a video to cover it. You can also add technical
        information to help the Training Tools plugin can improve the users
        experience." ), 'info', 'Small', 
        __( 'Form Registered', 'csv2post' ) );               
    }

    /**
    * Register a single input for a giving form. The form ID
    * is the TT database ID and not the HTML ID.
    * 
    * @author Ryan R. Bayne
    * @package WebTechGlobal WordPress Plugins
    * @version 1.0
    */
    public function addinput () {
        
        $form_id = $_POST['newinputformid'];
        $inputs_title = $_POST['newinputtitle'];
        $attributename = $_POST['newinputattributename'];
        $attributeid = $_POST['newinputattributeid'];

        $result = $this->TRAININGTOOLS->enter_ttforminput( $form_id, $inputs_title, $attributename, $attributeid );
  
        // confirm outcome
        $this->UI->create_notice( __( "You can now create a tooltip for your new
        registered input to show an example or instruct on strict requirements." ), 'info', 'Small', 
        __( 'New Input Registered', 'csv2post' ) );               
    }
                        
}// TRAININGTOOLS_Requests       
?>