<?php
/**
 * This view allows user to enter help content specific for
 * a specific page. This would include an overview/introduction. 
 * 
 * A main tutorial can be added.
 * 
 * A main forum thread can be added.
 * 
 * Links will show on the right side of the WordPress admin Help tab.
 * 
 * @package Training Tools
 * @subpackage Views
 * @author Ryan Bayne   
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class TRAININGTOOLS_Pagehelp_View extends TRAININGTOOLS_View {

    protected $screen_columns = 2;
    
    protected $view_name = 'pagehelp';
    
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
            array( $this->view_name . '-newpage', __( 'New Page', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'newpage' ), true, 'activate_plugins' ),
            array( $this->view_name . '-updatepageintroduction', __( 'Update Page Introduction', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'updatepageintroduction' ), true, 'activate_plugins' ),
            array( $this->view_name . '-addnewttpagevideourl', __( 'Add New Page Video', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'addnewttpagevideourl' ), true, 'activate_plugins' ),
            array( $this->view_name . '-deletepage', __( 'Delete Page', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'deletepage' ), true, 'activate_plugins' ),
            array( $this->view_name . '-lastpageregistered', __( 'Last Page Registered', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'lastpageregistered' ), true, 'activate_plugins' ),
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
    public function postbox_pagehelp_newpage( $data, $box ) {   
     
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Add a new page to your training system for the current active project.', 
        'trainingtools' ), false );        
        
        $this->FORMS->form_start( $form_id, $form_id, $box['title'] );   
        
        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newpagetitle', 'newpagetitle', 
        __( 'Pages UI Title', 'trainingtools' ), $current_value, true, array() );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newpageuiid', 'newpageuiid', 
        __( 'Pages UI ID', 'trainingtools' ), $current_value, true, array() );        
        
        $current_value = '';
        $this->FORMS->textarea_basic( $form_id, 'newpageintroduction', 'newpageintroduction', 
        __( 'Introduction', 'trainingtools' ), $current_value, false, $rows = 10, $cols = 20, array() );
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'newpagevideourl', 'newpagevideourl', 
        __( 'Video URL', 'trainingtools' ), $current_value, false, array() );
                
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
    public function postbox_pagehelp_updatepageintroduction( $data, $box ) {    
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Add a description to an existing page for the current active project.', 'trainingtools' ), false );  
              
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   
        
        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'thepageid', 'thepageid', 
        __( 'Page ID', 'trainingtools' ), $current_value, true, array( 'numeric') );
                
        $current_value = '';
        $this->FORMS->textarea_basic( $form_id, 'thepageintroduction', 'thepageintroduction', 
        __( 'Introduction', 'trainingtools' ), $current_value, true, $rows = 10, $cols = 20, array( 'alphanumeric' ) );
        
        echo '</table>';
        
        $this->UI->postbox_content_footer( __( 'Submit', 'trainingtools' ) );
    }    
             
    /**
    * Add a new page title and it's attributes within the plugin.
    * 
    * TT will create a unique ID for the page that never changes should
    * the page name, title, slug or ID within the package has to change.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_pagehelp_addnewttpagevideourl( $data, $box ) {    
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Add a video URL to an existing page for the current active project.', 'trainingtools' ), false ); 
               
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   
        
        echo '<table class="form-table">';
        
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'thepageid2', 'thepageid2', 
        __( 'Page ID', 'trainingtools' ), $current_value, true, array( 'numeric' => array() ) );
                
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'thepagevideourl', 'thepagevideourl', 
        __( 'Video URL', 'trainingtools' ), $current_value, true, array( 'url' => array() ) );
        
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
    public function postbox_pagehelp_deletepage( $data, $box ) {    
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Deleting a page also deletes all related help and training data for
        that page. Use with caution as this plugin does not currently put data into
        a trash system and does not have a backup system either.', 'trainingtools' ), false );    
        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );   
                
        echo '<table class="form-table">';
            
        $current_value = '';
        $this->FORMS->text_basic( $form_id, 'thepageid3', 'thepageid3', 
        __( 'Page ID', 'trainingtools' ), $current_value, true, array( 'numeric' => array() ) );
         
        echo '</table>';
        
        $this->UI->postbox_content_footer( __( 'Submit', 'trainingtools' ) );
    }
    
    /**
    * Displays the last page registered to help admin keep track of where
    * they are in their process of adding all pages.
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @version 1.0
    */
    public function postbox_pagehelp_lastpageregistered( $data, $box ) {    
        global $wpdb;
        
        $form_id = $box['args']['formid'];
        
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], 
        __( 'Information about the last page registered for the current active project.', 
        'trainingtools' ), false );    

        $query = "SELECT ttpage_id 
        FROM " . $wpdb->tt_pages . " 
        ORDER BY ttpage_id 
        DESC LIMIT 1";
        
        $result = $wpdb->get_results( $query, ARRAY_A );

        if( $result === false || !isset( $result[0]['ttpage_id'] ) ) {
            echo __( 'You have not registered any pages.', 'trainingtools' );
            return;
        }
        
        $ttpage = $this->TRAININGTOOLS->get_ttpage_byid( $result[0]['ttpage_id'] );
        
        $intro = $this->TRAININGTOOLS->get_ttpage_meta( $result[0]['ttpage_id'], 'introduction', true );
        
        $url = $this->TRAININGTOOLS->get_ttpage_meta( $result[0]['ttpage_id'], 'videourl', true );
        
        echo '<table class="form-table">';

        $this->FORMS->input_subline( $ttpage->ttpage_id, __( 'TT Page ID', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttpage->project_id, __( 'Project ID', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttpage->view_id, __( 'View ID', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttpage->title, __( 'Page Title', 'trainingtools' ) );
        $this->FORMS->input_subline( $ttpage->timestamp, __( 'Created', 'trainingtools' ) );
        $this->FORMS->input_subline( $intro, __( 'Introduction', 'trainingtools' ) );
        $this->FORMS->input_subline( $url, __( 'Main Video URL', 'trainingtools' ) );
        
        echo '</table>';

    }      
}?>