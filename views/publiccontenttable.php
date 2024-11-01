<?php
/**
 * A table of all content for general search and quick path to editing.  
 *
 * @package Training Tools
 * @subpackage Views
 * @author Ryan Bayne   
 * @since 0.0.1
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class TRAININGTOOLS_Publiccontenttable_View extends TRAININGTOOLS_View {

    protected $screen_columns = 2;
    
    protected $view_name = 'contenttable';
    
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
        // array of meta boxes + used to register dashboard widgets (id, title, callback, context, priority, callback arguments (array), dashboard widget (boolean) )   
        return $this->meta_boxes_array = array(
            // array( id, title, callback (usually parent, approach created by Ryan Bayne), context (position), priority, call back arguments array, add to dashboard (boolean), required capability
            //array( $this->view_name . '-iconstest', __( 'Icons Test', 'trainingtools' ), array( $this, 'parent' ), 'normal','default',array( 'formid' => 'iconstest' ), true, 'activate_plugins' ),
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
        $title_text = __( 'About Content Table', 'trainingtools' );
        $intro_text = __( 'This tables main function is to browse all help content. Eventually I would like it to offer quick editing and different modes i.e. list flagged content, display content that helped the least, display content that has been edited a lot by the community.', 'trainingtools' );
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
    * post box function for testing
    * 
    * @author Ryan Bayne
    * @package Training Tools
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_postssection_iconstest( $data, $box ) {    
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'Import both project and in the verse branding to the media gallery i.e. corporation logos.', 'trainingtools' ), true );        
        $this->FORMS->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );  
         
        global $trainingtools_settings;

        $this->UI->postbox_content_footer( __( 'Import Now', 'trainingtools' ) );
    } 
}?>