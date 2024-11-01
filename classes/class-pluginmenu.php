<?php
/**
* Beta testing only (check if in use yet) - phasing array files into classes of their own then calling into the main class
* 
* @package Training Tools
* @author Ryan Bayne   
* @since 0.0.1
* @version 1.2 
*/
class TRAININGTOOLS_TabMenu {
    public function menu_array() {
        $menu_array = array();
        
        ######################################################
        #                                                    #
        #                        MAIN                        #
        #                                                    #
        ######################################################
        // can only have one view in main right now until WP allows pages to be hidden from showing in
        // plugin menus. This may provide benefit of bringing user to the latest news and social activity
        // main page
        $menu_array['main']['groupname'] = 'main';        
        $menu_array['main']['slug'] = 'trainingtools';// home page slug set in main file
        $menu_array['main']['menu'] = __( 'Plugins Dashboard', 'trainingtools' );// plugin admin menu
        $menu_array['main']['pluginmenu'] = __( 'Plugins Dashboard' ,'trainingtools' );// for tabbed menu
        $menu_array['main']['name'] = "main";// name of page (slug) and unique
        $menu_array['main']['title'] = 'Training Tools Dashboard';// title at the top of the admin page
        $menu_array['main']['parent'] = 'parent';// either "parent" or the name of the parent - used for building tab menu         
        $menu_array['main']['tabmenu'] = false;// boolean - true indicates multiple pages in section, false will hide tab menu and show one page 

        ######################################################
        #                                                    #
        #               PUBLIC CONTENT SECTION               #
        #        guides focusing on a page, tutorials        #
        #                                                    #
        ###################################################### 
        /*
        // publiccontenttable - table of all content meant for public viewing
        $menu_array['publiccontenttable']['groupname'] = 'publiccontentsection';
        $menu_array['publiccontenttable']['slug'] = 'trainingtools_publiccontenttable'; 
        $menu_array['publiccontenttable']['menu'] = __( 'Public Content', 'trainingtools' );
        $menu_array['publiccontenttable']['pluginmenu'] = __( 'Public Content Table', 'trainingtools' );
        $menu_array['publiccontenttable']['name'] = "publiccontenttable";
        $menu_array['publiccontenttable']['title'] = __( 'Public Content Table', 'trainingtools' ); 
        $menu_array['publiccontenttable']['parent'] = 'parent'; 
        $menu_array['publiccontenttable']['tabmenu'] = true;     
   
        // publishpageguides - focuses on explaining a specific page.
        // every page gets an ID so that other sections in this plugin can use it.
        $menu_array['publishpageguides']['groupname'] = 'publiccontentsection';
        $menu_array['publishpageguides']['slug'] = 'trainingtools_buildpageguides'; 
        $menu_array['publishpageguides']['menu'] = __( 'Page Guides', 'trainingtools' );
        $menu_array['publishpageguides']['pluginmenu'] = __( 'Build Page Guides', 'trainingtools' );
        $menu_array['publishpageguides']['name'] = "buildpageguides";
        $menu_array['publishpageguides']['title'] = __( 'Build Page Guides', 'trainingtools' ); 
        $menu_array['publishpageguides']['parent'] = 'publiccontenttable'; 
        $menu_array['publishpageguides']['tabmenu'] = true;     

        // createtutorials - create a tutorial, mainly using WYSIWYG editor but
        // can use shortcodes to import help content already stored
        // on creating, a tutorial post (custom post type) will be made.
        // The content of the new post can already have shortcodes in it.
        $menu_array['createtutorials']['groupname'] = 'publiccontentsection';
        $menu_array['createtutorials']['slug'] = 'trainingtools_createtutorials'; 
        $menu_array['createtutorials']['menu'] = __( 'Create Tutorials', 'trainingtools' );
        $menu_array['createtutorials']['pluginmenu'] = __( 'Create Tutorials', 'trainingtools' );
        $menu_array['createtutorials']['name'] = "createtutorials";
        $menu_array['createtutorials']['title'] = __( 'Create Tutorials', 'trainingtools' ); 
        $menu_array['createtutorials']['parent'] = 'publiccontenttable'; 
        $menu_array['createtutorials']['tabmenu'] = true;     
        */
        ######################################################
        #                                                    #
        #                   UI HELP SECTION                  #
        #             publishing, edit and upkeep            #
        #                                                    #
        ######################################################
               
        // tablepages
        $menu_array['tablepages']['groupname'] = 'uihelpsection';
        $menu_array['tablepages']['slug'] = 'trainingtools_tablepages'; 
        $menu_array['tablepages']['menu'] = __( 'UI Help Content', 'trainingtools' );
        $menu_array['tablepages']['pluginmenu'] = __( 'Pages Table', 'trainingtools' );
        $menu_array['tablepages']['name'] = "tablepages";
        $menu_array['tablepages']['title'] = __( 'Pages Table', 'trainingtools' ); 
        $menu_array['tablepages']['parent'] = 'parent'; 
        $menu_array['tablepages']['tabmenu'] = true;     
               
        // tableforms
        $menu_array['tableforms']['groupname'] = 'uihelpsection';
        $menu_array['tableforms']['slug'] = 'trainingtools_tableforms'; 
        $menu_array['tableforms']['menu'] = __( 'UI Help Content', 'trainingtools' );
        $menu_array['tableforms']['pluginmenu'] = __( 'Forms Table', 'trainingtools' );
        $menu_array['tableforms']['name'] = "tableforms";
        $menu_array['tableforms']['title'] = __( 'Forms Table', 'trainingtools' ); 
        $menu_array['tableforms']['parent'] = 'tablepages'; 
        $menu_array['tableforms']['tabmenu'] = true;     
               
        // tableinputs
        $menu_array['tableinputs']['groupname'] = 'uihelpsection';
        $menu_array['tableinputs']['slug'] = 'trainingtools_tableinputs'; 
        $menu_array['tableinputs']['menu'] = __( 'UI Help Content', 'trainingtools' );
        $menu_array['tableinputs']['pluginmenu'] = __( 'Inputs Table', 'trainingtools' );
        $menu_array['tableinputs']['name'] = "tableinputs";
        $menu_array['tableinputs']['title'] = __( 'Inputs Table', 'trainingtools' ); 
        $menu_array['tableinputs']['parent'] = 'tablepages'; 
        $menu_array['tableinputs']['tabmenu'] = true;     

        // pagehelp - help content that covers all fields on a form 
        $menu_array['pagehelp']['groupname'] = 'uihelpsection';
        $menu_array['pagehelp']['slug'] = 'trainingtools_pagehelp'; 
        $menu_array['pagehelp']['menu'] = __( 'Page Help', 'trainingtools' );
        $menu_array['pagehelp']['pluginmenu'] = __( 'Page Help', 'trainingtools' );
        $menu_array['pagehelp']['name'] = "pagehelp";
        $menu_array['pagehelp']['title'] = __( 'Page Help', 'trainingtools' ); 
        $menu_array['pagehelp']['parent'] = 'tablepages'; 
        $menu_array['pagehelp']['tabmenu'] = true;
        
        // formhelp - help content that covers all fields on a form 
        $menu_array['formhelp']['groupname'] = 'uihelpsection';
        $menu_array['formhelp']['slug'] = 'trainingtools_formhelp'; 
        $menu_array['formhelp']['menu'] = __( 'Form Help', 'trainingtools' );
        $menu_array['formhelp']['pluginmenu'] = __( 'Form Help', 'trainingtools' );
        $menu_array['formhelp']['name'] = "formhelp";
        $menu_array['formhelp']['title'] = __( 'Form Help', 'trainingtools' ); 
        $menu_array['formhelp']['parent'] = 'tablepages'; 
        $menu_array['formhelp']['tabmenu'] = true;          
                                                          
        ######################################################
        #                                                    #
        #                  SERVICES SECTION                  #
        #         YouTube, cloud and sharing services        #
        #                                                    #
        ######################################################
        
        // YouTube video management (integrated with WTG plugins for videos)
        
        // Google Drive - converting tutorials into documents
        
        // PDF Create and Manage
                
        ######################################################
        #                                                    #
        #                   CAPTURE SECTION                  #
        #            implement capture technology            #
        #                                                    #
        ###################################################### 
        /*
        // media section 
        $menu_array['mediasection']['groupname'] = 'mediatools';
        $menu_array['mediasection']['slug'] = 'trainingtools_mediasection'; 
        $menu_array['mediasection']['menu'] = __( 'Media', 'trainingtools' );
        $menu_array['mediasection']['pluginmenu'] = __( 'Media', 'trainingtools' );
        $menu_array['mediasection']['name'] = "mediasection";
        $menu_array['mediasection']['title'] = __( 'Media', 'trainingtools' ); 
        $menu_array['mediasection']['parent'] = 'parent'; 
        $menu_array['mediasection']['tabmenu'] = true;
        */
        
        # add ability to create a database of applicable youtube videos
        # track where the videos are used already and tools to replace videos
                
        ######################################################
        #                                                    #
        #                 COMMUNITY SECTION                  #
        #     wiki type features, ratings and suggestions    #
        #                                                    #
        ###################################################### 
        /*
        // pages section 
        $menu_array['pagessection']['groupname'] = 'pagestools';
        $menu_array['pagessection']['slug'] = 'trainingtools_pagessection'; 
        $menu_array['pagessection']['menu'] = __( 'Pages', 'trainingtools' );
        $menu_array['pagessection']['pluginmenu'] = __( 'Pages', 'trainingtools' );
        $menu_array['pagessection']['name'] = "pagessection";
        $menu_array['pagessection']['title'] = __( 'Pages', 'trainingtools' ); 
        $menu_array['pagessection']['parent'] = 'parent'; 
        $menu_array['pagessection']['tabmenu'] = true;  
        */
                      
        ######################################################
        #                                                    #
        #                   UNITS SECTION                    #
        #    create, delivery and monitor training units     #
        #                                                    #
        ###################################################### 
        /*
        // units section 
        $menu_array['commentssection']['groupname'] = 'commentstools';
        $menu_array['commentssection']['slug'] = 'trainingtools_commentssection'; 
        $menu_array['commentssection']['menu'] = __( 'Comments', 'trainingtools' );
        $menu_array['commentssection']['pluginmenu'] = __( 'Comments', 'trainingtools' );
        $menu_array['commentssection']['name'] = "commentssection";
        $menu_array['commentssection']['title'] = __( 'Comments', 'trainingtools' ); 
        $menu_array['commentssection']['parent'] = 'parent'; 
        $menu_array['commentssection']['tabmenu'] = true;  
        */
        
        ######################################################
        #                                                    #
        #                  COURSES SECTION                   #
        #      many units make a course, deliver, score      #
        #                                                    #
        ###################################################### 
        /*
        // appearance section 
        $menu_array['appearancesection']['groupname'] = 'appearancetools';
        $menu_array['appearancesection']['slug'] = 'trainingtools_appearancesection'; 
        $menu_array['appearancesection']['menu'] = __( 'Appearance', 'trainingtools' );
        $menu_array['appearancesection']['pluginmenu'] = __( 'Appearance', 'trainingtools' );
        $menu_array['appearancesection']['name'] = "appearancesection";
        $menu_array['appearancesection']['title'] = __( 'Appearance', 'trainingtools' ); 
        $menu_array['appearancesection']['parent'] = 'parent'; 
        $menu_array['appearancesection']['tabmenu'] = true;     
        */
          
        ######################################################
        #                                                    #
        #                   AWARDS SECTION                   #
        #    create and issue possibly use 3rd party site    #
        #                                                    #
        ###################################################### 
        /*
        // plugins section 
        $menu_array['pluginssection']['groupname'] = 'pluginstools';
        $menu_array['pluginssection']['slug'] = 'trainingtools_pluginssection'; 
        $menu_array['pluginssection']['menu'] = __( 'Plugins', 'trainingtools' );
        $menu_array['pluginssection']['pluginmenu'] = __( 'Plugins', 'trainingtools' );
        $menu_array['pluginssection']['name'] = "pluginssection";
        $menu_array['pluginssection']['title'] = __( 'Plugins', 'trainingtools' ); 
        $menu_array['pluginssection']['parent'] = 'parent'; 
        $menu_array['pluginssection']['tabmenu'] = true;               
        */
                                                                               
        return $menu_array;
    }
} 
?>
