<?php
/**
 * This view allows user to enter help content specific to a
 * single form. The forms ID is entered into another form and it 
 * can then be selected on the main form for entering.
 * 
 * @package Training Tools
 * @subpackage Views
 * @author Ryan Bayne   
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class TRAININGTOOLS_Formhelp_View extends TRAININGTOOLS_View {

    protected $screen_columns = 2;
    
    protected $view_name = 'formhelp';
    
    public $purpose = 'normal';// normal, dashboard

    /**
    * Array of meta boxes, looped through to register them on views and as dashboard widgets
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function meta_box_array() {
        // array of meta boxes + used to register dashboard widgets (id, title, callback, context, priority, callback arguments (array), dashboard widget (boolean) )   
        return $this->meta_boxes_array = array(
            // array( id, title, callback (usually parent, approach created by Ryan Bayne), context (position), priority, call back arguments array, add to dashboard (boolean), required capability
            array( $this->view_name . '-newform', __( 'New Form', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'newform' ), true, 'activate_plugins' ),
            array( $this->view_name . '-addinput(', __( 'Add Input', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'addinput' ), true, 'activate_plugins' ),
            array( $this->view_name . '-deleteform(', __( 'Delete Form', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'deleteform' ), true, 'activate_plugins' ),
            array( $this->view_name . '-latestformregistered(', __( 'Latest Form Registered', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'latestformregistered' ), true, 'activate_plugins' ),
        );    
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
        $this->FORMS = TRAININGTOOLS::load_class( 'TRAININGTOOLS_Formbuilder', 'class-forms.php', 'classes' );
        
        parent::setup( $action, $data );
        
        // view header box - introduction, status, progress or vital information box
        // this function places content below the tab menu and above post-boxes
        $this->add_text_box( 'buildmenu-texttest', array( $this, 'postbox_buildmenu_viewintroduction' ), 'normal', true );
                
        // using array register many meta boxes
        foreach( self::meta_box_array() as $key => $metabox ) {
            // the $metabox array includes required capability to view the meta box
            if( isset( $metabox[7] ) && current_user_can( $metabox[7] ) ) {
                $this->add_meta_box( $metabox[0], $metabox[1], $metabox[2], $metabox[3], $metabox[4], $metabox[5] );   
            }               
        }        
              
    }

    /**
    * Outputs the meta boxes
    * 
    * @author Ryan R. Bayne
    * @package Training Tools
    * @since 0.0.3
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
    * @package Training Tools
    * @since 0.0.2
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
    * This function places content below the tab menu and above post-boxes.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */    
    public function postbox_buildmenu_viewintroduction( $data, $box ) {
        $title_text = __( 'About Page Guides', 'trainingtools' );
        $intro_text = __( 'Page Guides are supported by a custom post type. The custom post type template will use either post content or post meta and taxonomy. By default the post content will be empty and we use this view to create a page guide post to get started. We then assign forms and other features to it which the post type templatet will use to create a standard layout of help information. The goal of a page guide post is to offer a detailed guide of the entire view or a tutorial that may also include technical information.', 'trainingtools' );
        $subtitle_text = false;
        //$subtitle_text = __( 'Post Tools Ideas', 'trainingtools' );
        $footer_text = false;
        //$footer_text = __( 'Please post new tool ideas/requests in the WebTechGlobal forum.', 'trainingtools' );
        $info_area_content = false;
        /*
        $info_area_content = '
        <ol>
            <li>Mass search and replace within the database: on post content.</li>
            <li>Advanced numeric search and replace/alter i.e. increase monitory values by a percentage.</li>
        </ol>';
        */
        
        $this->UI->intro_box( $title_text, $intro_text, true, $subtitle_text, $info_area_content, $footer_text, 'postssectiondismiss' );
    }
         
    /**
    * Setup a new form by entering the forms ID and 
    * display type (admin,public).
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_formhelp_newform( $data, $box ) {    
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Setup a new form to begin building a technical profile about that form
        for using in the creation of advanced training. Help text you enter can be
        exported for using in your software i.e. tooltips. If using in a WordPress
        plugin the help text will be displayed in the admin Help tab.', 
        'trainingtools' ), false );        
        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   

        echo '<table class="form-table">';

        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newformttpageid', 'newformttpageid', 
        __( 'TT Page ID', 'trainingtools' ), $current_value, true, array( 'numeric' ) );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newformattributeid', 'newformattributeid', 
        __( 'Attribute ID', 'trainingtools' ), $current_value, true, array( 'alphanumeric' ) );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newformattributename', 'newformattributename', 
        __( 'Attribute Name', 'trainingtools' ), $current_value, true, array( 'alphanumeric') );
        
        echo '</table>';
                
        $this->UI->postbox_content_footer( __( 'Submit', 'trainingtools' ) );
    } 
             
    /**
    * Add multiple form input ID at once. Use another
    * form to enter more attributes but one input at a time. 
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_formhelp_addinput( $data, $box ) {   
     
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Enter a new form input for a specific form.',
        'trainingtools' ), false );        
        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   
        
        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newinputformid', 'newinputformid', 
        __( 'TT Form ID', 'trainingtools' ), $current_value, true, array( 'numeric' ) );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newinputtitle', 'newinputtitle', 
        __( 'Input UI Title', 'trainingtools' ), $current_value, true, array( 'alphanumeric' ) );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newinputattributename', 'newinputattributename', 
        __( 'Attribute Name', 'trainingtools' ), $current_value, true, array( 'alphanumeric' ) );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newinputattributeid', 'newinputattributeid', 
        __( 'Attribute ID', 'trainingtools' ), $current_value, true, array( 'alphanumeric' ) );
        
        echo '</table>';
                
        $this->UI->postbox_content_footer( __( 'Submit', 'trainingtools' ) );
    }    
                
    /**
    * Delete a form and all of its inputs. 
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_formhelp_deleteform( $data, $box ) {    
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Deleting a form will also delete all data relating to that form 
        including inputs.', 
        'trainingtools' ), false ); 
               
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   
        
        $this->UI->postbox_content_footer( __( 'Submit', 'trainingtools' ) );
    }    
                
    /**
    * Displays the latest form registered.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_formhelp_latestformregistered( $data, $box ) {    
        global $wpdb;
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Information about the last form registered for the current active project.', 
        'trainingtools' ), false );    

        $query = "SELECT * 
        FROM " . $wpdb->tt_forms . " 
        ORDER BY dbform_id 
        DESC LIMIT 1";
        
        $ttform_last = $wpdb->get_results( $query, ARRAY_A );

        if( $ttform_last === false || !isset( $ttform_last[0]['dbform_id'] ) ) {
            echo __( 'You have not registered any forms.', 'trainingtools' );
            return;
        }                                    
        
        echo '<table class="form-table">';

        $this->FORMS->input_subline( $ttform_last[0]['dbform_id'], __( 'TT Form ID', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttform_last[0]['location_id'], __( 'Location ID', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttform_last[0]['location_type'], __( 'Location Type', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttform_last[0]['form_id'], __( 'Forms HTML ID)', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttform_last[0]['form_name'], __( 'Forms HTML Name', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttform_last[0]['timestamp'], __( 'Registered', 'trainingtools' ) );

        echo '</table>';
    } 
}?>