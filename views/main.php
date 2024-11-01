<?php
/**
 * Main [section] - Projects [page]
 * 
 * @package Training Tools
 * @subpackage Views
 * @author Ryan Bayne   
 * @since 0.0.1
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * View class for Main [section] - Projects [page]
 * 
 * @package Training Tools
 * @subpackage Views
 * @author Ryan Bayne
 * @since 0.0.1
 */
class TRAININGTOOLS_Main_View extends TRAININGTOOLS_View {

    /**
     * Number of screen columns for post boxes on this screen
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected $screen_columns = 2;
    
    protected $view_name = 'main';
    
    public $purpose = 'normal';// normal, dashboard

    /**
    * Array of meta boxes, looped through to register them on views and as dashboard widgets
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function meta_box_array() {
        global $trainingtools_settings;

        // array of meta boxes + used to register dashboard widgets (id, title, callback, context, priority, callback arguments (array), dashboard widget (boolean) )   
        $this->meta_boxes_array = array(
            // array( id, title, callback (usually parent, approach created by Ryan Bayne), context (position), priority, call back arguments array, add to dashboard (boolean), required capability
            array( 'main-welcome', __( 'Support WordPress Plugin Development', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'welcome' ), true, 'activate_plugins' ),
            array( 'main-mailchimp', __( 'Subscribe for Updates', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'mailchimp' ), true, 'activate_plugins' ),
            array( 'main-setactiveproject', __( 'Set Active Project', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'setactiveproject' ), true, 'activate_plugins' ),
             
            // framework settings
            array( 'main-addnewproject', __( 'Add New Project', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'addnewproject' ), true, 'activate_plugins' ),          
            array( 'main-projectlist', __( 'Project List', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'projectlist' ), true, 'activate_plugins' ),
            
            array( 'main-globalswitches', __( 'Global Switches', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'globalswitches' ), true, 'activate_plugins' ),
            array( 'main-logsettings', __( 'Log Settings', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'logsettings' ), true, 'activate_plugins' ),
            array( 'main-pagecapabilitysettings', __( 'Page Capability Settings', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'pagecapabilitysettings' ), true, 'activate_plugins' ),
            array( 'main-posttypessettings', __( 'Post Type Settings', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'posttypessettings' ), true, 'activate_plugins' ),          
        
            // side boxes
            Array( 'main-facebook', __( 'Facebook', 'trainingtools' ), array( $this, 'parent' ), 'side','default',array( 'formid' => 'facebook' ), true, 'activate_plugins' ),
            array( 'main-twitterupdates', __( 'Twitter Updates', 'trainingtools' ), array( $this, 'parent' ), 'side','default',array( 'formid' => 'twitterupdates' ), true, 'activate_plugins' ),
            array( 'main-support', __( 'Support', 'trainingtools' ), array( $this, 'parent' ), 'side','default',array( 'formid' => 'support' ), true, 'activate_plugins' ),            
        );
        
        // add meta boxes that have conditions i.e. a global switch
        if( isset( $trainingtools_settings['widgetsettings']['dashboardwidgetsswitch'] ) && $trainingtools_settings['widgetsettings']['dashboardwidgetsswitch'] == 'enabled' ) {
            $this->meta_boxes_array[] = array( 'main-dashboardwidgetsettings', __( 'Dashboard Widget Settings', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'dashboardwidgetsettings' ), true, 'activate_plugins' );   
        }
        
        return $this->meta_boxes_array;                
    }
          
    /**
     * Set up the view with data and do things that are specific for this view
     *
     * @since 0.0.1
     *
     * @param string $action Action for this view
     * @param array $data Data for this view
     */
    public function setup( $action, array $data ) {
        global $trainingtools_settings;
        
        // create constant for view name
        if(!defined( "TRAININGTOOLS_VIEWNAME") ){define( "TRAININGTOOLS_VIEWNAME", $this->view_name );}
        
        // create class objects
        $this->TRAININGTOOLS = TRAININGTOOLS::load_class( 'TRAININGTOOLS', 'class-trainingtools.php', 'classes' );
        $this->UI = TRAININGTOOLS::load_class( 'TRAININGTOOLS_UI', 'class-ui.php', 'classes' );  
        $this->DB = TRAININGTOOLS::load_class( 'TRAININGTOOLS_DB', 'class-wpdb.php', 'classes' );
        $this->PHP = TRAININGTOOLS::load_class( 'TRAININGTOOLS_PHP', 'class-phplibrary.php', 'classes' );
        $this->TabMenu = TRAININGTOOLS::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        $this->FORMS = TRAININGTOOLS::load_class( 'TRAININGTOOLS_Formbuilder', 'class-forms.php', 'classes' );
        
        parent::setup( $action, $data );
        
        // only output meta boxes
        if( $this->purpose == 'normal' ) {
            self::metaboxes();// register meta boxes for the current view
        } elseif( $this->purpose == 'dashboard' ) {
            // do nothing - add_dashboard_widgets() in class-ui.php calls dashboard_widgets() from this class
        } elseif( $this->purpose == 'customdashboard' ) {
            return self::meta_box_array();// return meta box array
        } else {
            // do nothing 
        }       
    } 
    
    /**
     * Outputs the meta boxes
     * 
     * @author Ryan R. Bayne
     * @package Training Tools
     * @since 0.0.1
     * @version 1.0
     */
    public function metaboxes() {
        parent::register_metaboxes( self::meta_box_array() );     
     }

    /**
    * This function is called when on WP core dashboard and it adds widgets to the dashboard using
    * the meta box functions in this class. 
    * 
    * @uses dashboard_widgets() in parent class TRAININGTOOLS_View which loops through meta boxes and registeres widgets
    * 
    * @author Ryan R. Bayne
    * @package TRAININGTOOLS
    * @since 0.0.1
    * @version 1.0
    */
    public function dashboard() { 
        parent::dashboard_widgets( self::meta_box_array() );  
    }                 
    
    /**
    * All add_meta_box() callback to this function to keep the add_meta_box() call simple.
    * 
    * This function also offers a place to apply more security or arguments.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    function parent( $data, $box ) {
        eval( 'self::postbox_' . $this->view_name . '_' . $box['args']['formid'] . '( $data, $box );' );
    }
         
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_welcome( $data, $box ) {    
        echo '<p>' . __( "Most users I talk to own more than I do. Please take that into consideration if 
        my plugin saves you time and helps you to make money. If you donate or hire me it will help me to continue
        my work. Both supporting this plugin and creating more great plugins.", 'trainingtools' ) . '</p>';
    }       

    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_globalswitches( $data, $box ) {    
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'These switches disable or enable systems. Disabling systems you do not require will improve the plugins performance.', 'trainingtools' ), false );        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );
        
        global $trainingtools_settings;
        ?>  

            <table class="form-table">
            <?php        
            $this->UI->option_switch( __( 'WordPress Notice Styles', 'trainingtools' ), 'uinoticestyle', 'uinoticestyle', $trainingtools_settings['noticesettings']['wpcorestyle'] );
            $this->UI->option_switch( __( 'WTG Flag System', 'trainingtools' ), 'flagsystemstatus', 'flagsystemstatus', $trainingtools_settings['posttypes']['wtgflags']['status'] );
            $this->UI->option_switch( __( 'Dashboard Widgets Switch', 'trainingtools' ), 'dashboardwidgetsswitch', 'dashboardwidgetsswitch', $trainingtools_settings['widgetsettings']['dashboardwidgetsswitch'], 'Enabled', 'Disabled', 'disabled' );      
            ?>
            </table> 
            
        <?php 
        $this->UI->postbox_content_footer();
    }

    /**
    * Settings for post types. Provides controls to disable/enable post
    * types but other options can be added.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    * 
    * @todo we should confirm custom post type file is within the package else force it to be disabled and display message
    */
    public function postbox_main_posttypessettings( $data, $box ) { 
        global $trainingtools_settings;
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'Disable or enable post types and other options are possible i.e. hiding post types from the menu but allowing them to be active. Please contact WebTechGlobal with your requirements.', 'trainingtools' ), false );        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );
        ?>  

            <?php        
            if( !isset( $trainingtools_settings['posttypes'] ) && !is_array( $trainingtools_settings['posttypes'] ) ) {
                
                _e( 'This plugin does not currently offer any custom post types. If that changes some settings will become available here to activate new features.', 'trainingtools' );    
            
            } else {?>
                    
            <table class="form-table">

            <?php 
            $current = 'disabled';
            foreach( $trainingtools_settings['posttypes'] as $type => $posttype ) {
     
                if( isset( $posttype['status']) ) {
                    $current = $posttype['status'];
                }
                
                $this->UI->option_switch( $posttype['title'], 'cpt' . $type, 'cpt' . $type, $current );
            }?>
   
            </table> 
            
            <?php 
            }
            ?>
            
        <?php 
        $this->UI->postbox_content_footer();
    }
    
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_logsettings( $data, $box ) {    
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'The plugin has its own log system with multi-purpose use. Not everything is logged for the sake of performance so please request increased log use if required.', 'trainingtools' ), false );        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );
        
        global $trainingtools_settings;
        ?>  

            <table class="form-table">
                <!-- Option Start -->
                <tr valign="top">
                    <th scope="row">Log</th>
                    <td>
                        <?php 
                        // if is not set ['admintriggers']['newcsvfiles']['status'] then it is enabled by default
                        if(!isset( $trainingtools_settings['globalsettings']['uselog'] ) ){
                            $radio1_uselog_enabled = 'checked'; 
                            $radio2_uselog_disabled = '';                    
                        }else{
                            if( $trainingtools_settings['globalsettings']['uselog'] == 1){
                                $radio1_uselog_enabled = 'checked'; 
                                $radio2_uselog_disabled = '';    
                            }elseif( $trainingtools_settings['globalsettings']['uselog'] == 0){
                                $radio1_uselog_enabled = ''; 
                                $radio2_uselog_disabled = 'checked';    
                            }
                        }?>
                        <fieldset><legend class="screen-reader-text"><span>Log</span></legend>
                            <input type="radio" id="logstatus_enabled" name="trainingtools_radiogroup_logstatus" value="1" <?php echo $radio1_uselog_enabled;?> />
                            <label for="logstatus_enabled"> <?php _e( 'Enable', 'trainingtools' ); ?></label>
                            <br />
                            <input type="radio" id="logstatus_disabled" name="trainingtools_radiogroup_logstatus" value="0" <?php echo $radio2_uselog_disabled;?> />
                            <label for="logstatus_disabled"> <?php _e( 'Disable', 'trainingtools' ); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <!-- Option End -->
      
                <?php       
                // log rows limit
                if(!isset( $trainingtools_settings['globalsettings']['loglimit'] ) || !is_numeric( $trainingtools_settings['globalsettings']['loglimit'] ) ){$trainingtools_settings['globalsettings']['loglimit'] = 1000;}
                $this->UI->option_text( 'Log Entries Limit', 'trainingtools_loglimit', 'loglimit', $trainingtools_settings['globalsettings']['loglimit'] );
                ?>
            </table> 
            
                    
            <h4>Outcomes</h4>
            <label for="trainingtools_log_outcomes_success"><input type="checkbox" name="trainingtools_log_outcome[]" id="trainingtools_log_outcomes_success" value="1" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['outcomecriteria']['1'] ) ){echo 'checked';} ?>> Success</label>
            <br> 
            <label for="trainingtools_log_outcomes_fail"><input type="checkbox" name="trainingtools_log_outcome[]" id="trainingtools_log_outcomes_fail" value="0" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['outcomecriteria']['0'] ) ){echo 'checked';} ?>> Fail/Rejected</label>

            <h4>Type</h4>
            <label for="trainingtools_log_type_general"><input type="checkbox" name="trainingtools_log_type[]" id="trainingtools_log_type_general" value="general" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['typecriteria']['general'] ) ){echo 'checked';} ?>> General</label>
            <br>
            <label for="trainingtools_log_type_error"><input type="checkbox" name="trainingtools_log_type[]" id="trainingtools_log_type_error" value="error" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['typecriteria']['error'] ) ){echo 'checked';} ?>> Errors</label>
            <br>
            <label for="trainingtools_log_type_trace"><input type="checkbox" name="trainingtools_log_type[]" id="trainingtools_log_type_trace" value="flag" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['typecriteria']['flag'] ) ){echo 'checked';} ?>> Trace</label>

            <h4>Priority</h4>
            <label for="trainingtools_log_priority_low"><input type="checkbox" name="trainingtools_log_priority[]" id="trainingtools_log_priority_low" value="low" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['prioritycriteria']['low'] ) ){echo 'checked';} ?>> Low</label>
            <br>
            <label for="trainingtools_log_priority_normal"><input type="checkbox" name="trainingtools_log_priority[]" id="trainingtools_log_priority_normal" value="normal" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['prioritycriteria']['normal'] ) ){echo 'checked';} ?>> Normal</label>
            <br>
            <label for="trainingtools_log_priority_high"><input type="checkbox" name="trainingtools_log_priority[]" id="trainingtools_log_priority_high" value="high" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['prioritycriteria']['high'] ) ){echo 'checked';} ?>> High</label>
            
            <h1>Custom Search</h1>
            <p>This search criteria is not currently stored, it will be used on the submission of this form only.</p>
         
            <h4>Page</h4>
            <select name="trainingtools_pluginpages_logsearch" id="trainingtools_pluginpages_logsearch" >
                <option value="notselected">Do Not Apply</option>
                <?php
                $current = '';
                if( isset( $trainingtools_settings['logsettings']['logscreen']['page'] ) && $trainingtools_settings['logsettings']['logscreen']['page'] != 'notselected' ){
                    $current = $trainingtools_settings['logsettings']['logscreen']['page'];
                } 
                $this->UI->page_menuoptions( $current);?> 
            </select>
            
            <h4>Action</h4> 
            <select name="trainingtools_logactions_logsearch" id="trainingtools_logactions_logsearch" >
                <option value="notselected">Do Not Apply</option>
                <?php 
                $current = '';
                if( isset( $trainingtools_settings['logsettings']['logscreen']['action'] ) && $trainingtools_settings['logsettings']['logscreen']['action'] != 'notselected' ){
                    $current = $trainingtools_settings['logsettings']['logscreen']['action'];
                }
                $action_results = $this->DB->log_queryactions( $current);
                if( $action_results){
                    foreach( $action_results as $key => $action){
                        $selected = '';
                        if( $action['action'] == $current){
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="'.$action['action'].'" '.$selected.'>'.$action['action'].'</option>'; 
                    }   
                }?> 
            </select>
            
            <h4>Screen Name</h4>
            <select name="trainingtools_pluginscreens_logsearch" id="trainingtools_pluginscreens_logsearch" >
                <option value="notselected">Do Not Apply</option>
                <?php 
                $current = '';
                if( isset( $trainingtools_settings['logsettings']['logscreen']['screen'] ) && $trainingtools_settings['logsettings']['logscreen']['screen'] != 'notselected' ){
                    $current = $trainingtools_settings['logsettings']['logscreen']['screen'];
                }
                $this->UI->screens_menuoptions( $current);?> 
            </select>
                  
            <h4>PHP Line</h4>
            <input type="text" name="trainingtools_logcriteria_phpline" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['line'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['line'];} ?>">
            
            <h4>PHP File</h4>
            <input type="text" name="trainingtools_logcriteria_phpfile" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['file'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['file'];} ?>">
            
            <h4>PHP Function</h4>
            <input type="text" name="trainingtools_logcriteria_phpfunction" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['function'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['function'];} ?>">
            
            <h4>Panel Name</h4>
            <input type="text" name="trainingtools_logcriteria_panelname" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['panelname'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['panelname'];} ?>">

            <h4>IP Address</h4>
            <input type="text" name="trainingtools_logcriteria_ipaddress" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['ipaddress'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['ipaddress'];} ?>">
           
            <h4>User ID</h4>
            <input type="text" name="trainingtools_logcriteria_userid" value="<?php if( isset( $trainingtools_settings['logsettings']['logscreen']['userid'] ) ){echo $trainingtools_settings['logsettings']['logscreen']['userid'];} ?>">    
          
            <h4>Display Fields</h4>                                                                                                                                        
            <label for="trainingtools_logfields_outcome"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_outcome" value="outcome" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['outcome'] ) ){echo 'checked';} ?>> <?php _e( 'Outcome', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_line"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_line" value="line" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['line'] ) ){echo 'checked';} ?>> <?php _e( 'Line', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_file"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_file" value="file" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['file'] ) ){echo 'checked';} ?>> <?php _e( 'File', 'trainingtools' );?></label> 
            <br>
            <label for="trainingtools_logfields_function"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_function" value="function" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['function'] ) ){echo 'checked';} ?>> <?php _e( 'Function', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_sqlresult"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_sqlresult" value="sqlresult" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['sqlresult'] ) ){echo 'checked';} ?>> <?php _e( 'SQL Result', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_sqlquery"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_sqlquery" value="sqlquery" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['sqlquery'] ) ){echo 'checked';} ?>> <?php _e( 'SQL Query', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_sqlerror"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_sqlerror" value="sqlerror" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['sqlerror'] ) ){echo 'checked';} ?>> <?php _e( 'SQL Error', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_wordpresserror"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_wordpresserror" value="wordpresserror" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['wordpresserror'] ) ){echo 'checked';} ?>> <?php _e( 'WordPress Erro', 'trainingtools' );?>r</label>
            <br>
            <label for="trainingtools_logfields_screenshoturl"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_screenshoturl" value="screenshoturl" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['screenshoturl'] ) ){echo 'checked';} ?>> <?php _e( 'Screenshot URL', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_userscomment"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_userscomment" value="userscomment" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['userscomment'] ) ){echo 'checked';} ?>> <?php _e( 'Users Comment', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_page"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_page" value="page" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['page'] ) ){echo 'checked';} ?>> <?php _e( 'Page', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_version"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_version" value="version" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['version'] ) ){echo 'checked';} ?>> <?php _e( 'Plugin Version', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_panelname"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_panelname" value="panelname" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['panelname'] ) ){echo 'checked';} ?>> <?php _e( 'Panel Name', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_tabscreenname"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_tabscreenname" value="tabscreenname" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['outcome'] ) ){echo 'checked';} ?>> <?php _e( 'Screen Name *', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_dump"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_dump" value="dump" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['dump'] ) ){echo 'checked';} ?>> <?php _e( 'Dump', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_ipaddress"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_ipaddress" value="ipaddress" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['ipaddress'] ) ){echo 'checked';} ?>> <?php _e( 'IP Address', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_userid"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_userid" value="userid" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['userid'] ) ){echo 'checked';} ?>> <?php _e( 'User ID', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_comment"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_comment" value="comment" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['comment'] ) ){echo 'checked';} ?>> <?php _e( 'Developers Comment', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_type"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_type" value="type" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['type'] ) ){echo 'checked';} ?>> <?php _e( 'Entry Type', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_category"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_category" value="category" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['category'] ) ){echo 'checked';} ?>> <?php _e( 'Category', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_action"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_action" value="action" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['action'] ) ){echo 'checked';} ?>> <?php _e( 'Action', 'trainingtools' );?></label>
            <br>
            <label for="trainingtools_logfields_priority"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_priority" value="priority" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['priority'] ) ){echo 'checked';} ?>> <?php _e( 'Priority', 'trainingtools' );?></label> 
            <br>
            <label for="trainingtools_logfields_thetrigger"><input type="checkbox" name="trainingtools_logfields[]" id="trainingtools_logfields_thetrigger" value="thetrigger" <?php if( isset( $trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['thetrigger'] ) ){echo 'checked';} ?>> <?php _e( 'Trigger', 'trainingtools' );?></label> 

    
        <?php 
        $this->UI->postbox_content_footer();
    }    
        
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_iconsexplained( $data, $box ) {    
        ?>  
        <p class="about-description"><?php _e( 'The plugin has icons on the UI offering different types of help...' ); ?></p>
        
        <h3>Help Icon<?php echo $this->UI->helpicon( 'http://www.webtechglobal.co.uk/trainingtools' )?></h3>
        <p><?php _e( 'The help icon offers a tutorial or indepth description on the WebTechGlobal website. Clicking these may open
        take a key page in the plugins portal or post in the plugins blog. On a rare occasion you will be taking to another users 
        website who has published a great tutorial or technical documentation.' )?></p>        
        
        <h3>Discussion Icon<?php echo $this->UI->discussicon( 'http://www.webtechglobal.co.uk/trainingtools' )?></h3>
        <p><?php _e( 'The discussion icon open an active forum discussion or chat on the WebTechGlobal domain in a new tab. If you see this icon
        it means you are looking at a feature or area of the plugin that is a hot topic. It could also indicate the
        plugin author would like to hear from you regarding a specific feature. Occasionally these icons may take you to a discussion
        on other websites such as a Google circles, an official page on Facebook or a good forum thread on a users domain.' )?></p>
                          
        <h3>Info Icon<img src="<?php echo TRAININGTOOLS_IMAGES_URL;?>info-icon.png" alt="<?php _e( 'Icon with an i click it to read more information in a popup.' );?>"></h3>
        <p><?php _e( 'The information icon will not open another page. It will display a pop-up with extra information. This is mostly used within
        panels to explain forms and the status of the panel.' )?></p>        
        
        <h3>Video Icon<?php echo $this->UI->videoicon( 'http://www.webtechglobal.co.uk/trainingtools' )?></h3>
        <p><?php _e( 'clicking on the video icon will open a new tab to a YouTube video. Occasionally it may open a video on another
        website. Occasionally a video may even belong to a user who has created a good tutorial.' )?></p> 
               
        <h3>Trash Icon<?php echo $this->UI->trashicon( 'http://www.webtechglobal.co.uk/trainingtools' )?></h3>
        <p><?php _e( 'The trash icon will be shown beside items that can be deleted or objects that can be hidden.
        Sometimes you can hide a panel as part of the plugins configuration. Eventually I hope to be able to hide
        notices, especially the larger ones..' )?></p>      
      <?php     
    }
    
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_twitterupdates( $data, $box ) {    
        ?>
        <p class="about-description"><?php _e( 'Thank this plugins developers with a Tweet...', 'trainingtools' ); ?></p>    
        <a class="twitter-timeline" href="https://twitter.com/WebTechGlobal" data-widget-id="511630591142268928">Tweets by @WebTechGlobal</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id) ){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script>                                                   
        <?php     
    }    
    
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0.4
    */
    public function postbox_main_support( $data, $box ) {    
        ?>      
        <p><?php _e( 'All users (free and pro editions) are supported. Please register on the <a href="http://www.webtechglobal.co.uk/register/" title="WebTechGlobal Registration" target="_blank">WebTechGlobal</a> site for free support.', 'trainingtools' ); ?></p>                     
        <?php     
    }   
    
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_facebook( $data, $box ) {    
        ?>      
        <p class="about-description"><?php _e( 'Please show your appreciation for this plugin I made for you by clicking Like...', 'trainingtools' ); ?></p>
        <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FWebTechGlobal1&amp;width=350&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=false&amp;show_border=true" scrolling="no" frameborder="0" style="padding: 10px 0 0 0;border:none; overflow:hidden; width:100%; height:290px;" allowTransparency="true"></iframe>                                                                             
        <?php     
    }

    /**
    * Form for setting which captability is required to view the page
    * 
    * By default there is no settings data for this because most people will never use it.
    * However when it is used, a new option record is created so that the settings are
    * independent and can be accessed easier.  
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_pagecapabilitysettings( $data, $box ) {
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'Set the capability a user requires to view any of the plugins pages. This works independently of role plugins such as Role Scoper.', 'trainingtools' ), false );        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );
        
        // get the tab menu 
        $pluginmenu = $this->TabMenu->menu_array();
        ?>
        
        <table class="form-table">
        
        <?php 
        // get stored capability settings 
        $saved_capability_array = get_option( 'trainingtools_capabilities' );
        
        // add a menu for each page for the user selecting the required capability 
        foreach( $pluginmenu as $key => $page_array ) {
            
            // do not add the main page to the list as a strict security measure
            if( $page_array['name'] !== 'main' ) {
                $current = null;
                if( isset( $saved_capability_array['pagecaps'][ $page_array['name'] ] ) && is_string( $saved_capability_array['pagecaps'][ $page_array['name'] ] ) ) {
                    $current = $saved_capability_array['pagecaps'][ $page_array['name'] ];
                }
                
                $this->UI->option_menu_capabilities( $page_array['menu'], 'pagecap' . $page_array['name'], 'pagecap' . $page_array['name'], $current );
            }
        }?>
        
        </table>
        
        <?php 
        $this->UI->postbox_content_footer();        
    }
    
    /**
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_dashboardwidgetsettings( $data, $box ) { 
        global $trainingtools_settings;
           
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'This panel is new and is advanced.   
        Please seek my advice before using it.
        You must be sure and confident that it operates in the way you expect.
        It will add widgets to your dashboard. 
        The capability menu allows you to set a global role/capability requirements for the group of wigets from any giving page. 
        The capability options in the "Page Capability Settings" panel are regarding access to the admin page specifically.', 'trainingtools' ), false );   
             
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );

        echo '<table class="form-table">';

        // now loop through views, building settings per box (display or not, permitted role/capability  
        $TRAININGTOOLS_TabMenu = TRAININGTOOLS::load_class( 'TRAININGTOOLS_TabMenu', 'class-pluginmenu.php', 'classes' );
        $menu_array = $TRAININGTOOLS_TabMenu->menu_array();
        foreach( $menu_array as $key => $section_array ) {

            /*
                'groupname' => string 'main' (length=4)
                'slug' => string 'trainingtools_generalsettings' (length=24)
                'menu' => string 'General Settings' (length=16)
                'pluginmenu' => string 'General Settings' (length=16)
                'name' => string 'generalsettings' (length=15)
                'title' => string 'General Settings' (length=16)
                'parent' => string 'main' (length=4)
            */
            
            // get dashboard activation status for the current page
            $current_for_page = '123nocurrentvalue';
            if( isset( $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'dashboardwidgetsswitch'] ) ) {
                $current_for_page = $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'dashboardwidgetsswitch'];   
            }
            
            // display switch for current page
            $this->UI->option_switch( $section_array['menu'], $section_array['name'] . 'dashboardwidgetsswitch', $section_array['name'] . 'dashboardwidgetsswitch', $current_for_page, 'Enabled', 'Disabled', 'disabled' );
            
            // get current pages minimum dashboard widget capability
            $current_capability = '123nocapability';
            if( isset( $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'widgetscapability'] ) ) {
                $current_capability = $trainingtools_settings['widgetsettings'][ $section_array['name'] . 'widgetscapability'];   
            }
                            
            // capabilities menu for each page (rather than individual boxes, the boxes will have capabilities applied in code)
            $this->UI->option_menu_capabilities( __( 'Capability Required', 'trainingtools' ), $section_array['name'] . 'widgetscapability', $section_array['name'] . 'widgetscapability', $current_capability );
        }

        echo '</table>';
                    
        $this->UI->postbox_content_footer();
    }    

    /**
    * Create new project form. Enter name only to begin the process of establishing
    * the entire scope of the project.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_addnewproject( $data, $box ) { 
        global $trainingtools_settings;
           
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'All projects begin here and are continued using the plugins other pages.', 'trainingtools' ), false );   
             
        $this->FORMS->form_start( $form_id, $box['args']['formid'], $box['title'] );

        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'projectname', 'projectname', __( 'Project Name', 'trainingtools' ), $current_value, true, array() );
 
        echo '</table>';
                    
        $this->UI->postbox_content_footer();
    }    

    /**
    * List of projects with delete ability.
    * 
    * @author Ryan Bayne
    * @package WTG Tasks Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_main_projectlist( $data, $box ) {    
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'A list of projects with quick actions.', 'trainingtools' ), false );        

        global $trainingtools_settings;
        ?>  

            <table class="form-table">
            <?php 
            $projects = $this->TRAININGTOOLS->get_projects();
            foreach( $projects as $key => $project ) {
         
                $link = $this->UI->linkaction( 'trainingtools', 'projectfocus', __( 'Training Tools admin link', 'wtgtasksmanager' ), __( 'Focus', 'wtgtasksmanager' ), '&projectid=' . $project['project_id'], ' class="button trainingtoolsbutton"', 'admin.php' );
                $this->UI->option_subline( $link, $project['projectname'] );
                 
            }       
            ?>
            </table> 
            
        <?php 
    }
    
    /**
    * Mailchimp subscribers list form.
    * 
    * @author Ryan Bayne
    * @package CSV 2 POST
    * @version 1.1
    */
    public function postbox_main_mailchimp( $data, $box ) {  
    ?>

        <!-- Begin MailChimp Signup Form -->
        <link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
            /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
               We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
        </style>
        <div id="mc_embed_signup">
        <form action="//webtechglobal.us9.list-manage.com/subscribe/post?u=99272fe1772de14ff2be02fe6&amp;id=018f5572ec" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
            <div id="mc_embed_signup_scroll">
            <h2>Please Subscribe to WTG Mailing List</h2>
            <h3>Mail will not be frequent from this subscribers list and information sent to you
            may be critical i.e. bug finds. I have no intention of
            generating traffic from nusiance email campaigns.</h3>
        <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
        <div class="mc-field-group">
            <label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
        </label>
            <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
        </div>
        <div class="mc-field-group">
            <label for="mce-FNAME">First Name </label>
            <input type="text" value="" name="FNAME" class="" id="mce-FNAME">
        </div>
        <div class="mc-field-group">
            <label for="mce-LNAME">Last Name </label>
            <input type="text" value="" name="LNAME" class="" id="mce-LNAME">
        </div>
        <div class="mc-field-group input-group">
            <strong>Email Format </strong>
            <ul><li><input type="radio" value="html" name="EMAILTYPE" id="mce-EMAILTYPE-0"><label for="mce-EMAILTYPE-0">html</label></li>
        <li><input type="radio" value="text" name="EMAILTYPE" id="mce-EMAILTYPE-1"><label for="mce-EMAILTYPE-1">text</label></li>
        </ul>
        </div>
        <p>Powered by <a href="http://eepurl.com/2W_2n" title="MailChimp - email marketing made easy and fun">MailChimp</a></p>
            <div id="mce-responses" class="clear">
                <div class="response" id="mce-error-response" style="display:none"></div>
                <div class="response" id="mce-success-response" style="display:none"></div>
            </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
            <div style="position: absolute; left: -5000px;"><input type="text" name="b_99272fe1772de14ff2be02fe6_018f5572ec" tabindex="-1" value=""></div>
            <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
            </div>
        </form>
        </div>

        <!--End mc_embed_signup-->

    <?php   

    }    
    
    /**
    * Use to set a currently active project. Means the user does not
    * need to keep selecting a project on every form.
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_main_setactiveproject( $data, $box ) {
        global $trainingtools_settings;
           
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'The plugin is designed so that you do not need to keep selecting a project
        on every form you use. Instead you must activate the project you want to
        work on and all form submissions will apply to that projects data.', 
        'trainingtools' ), false );   
        
        $projects = $this->TRAININGTOOLS->get_projects();
        
        $items_array = array();
        foreach( $projects as $key => $p ){
            $items_array[ $p['project_id'] ] = $p['projectname'];    
        }
                   
        $this->FORMS->form_start( $form_id, $box['args']['formid'], $box['title'] );

        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->menu_basic( $form_id, 'selectactiveproject', 'selectactiveproject', 
        __( 'Select Project', 'trainingtools' ), $items_array, true, $current_value, array() );

        echo '</table>';
                    
        $this->UI->postbox_content_footer();    
    }
    
}?>