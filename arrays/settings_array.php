<?php
/** 
 * Default administration settings for Training Tools plugin. These settings are installed to the 
 * wp_options table and are used from there by default. 
 * 
 * @package Training Tools
 * @author Ryan Bayne   
 * @since 0.0.1
 * @version 1.0.7
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// install main admin settings option record
$trainingtools_settings = array();
// encoding
$trainingtools_settings['standardsettings']['encoding']['type'] = 'utf8';
// admin user interface settings start
$trainingtools_settings['standardsettings']['ui_advancedinfo'] = false;// hide advanced user interface information by default
// other
$trainingtools_settings['standardsettings']['ecq'] = array();
$trainingtools_settings['standardsettings']['chmod'] = '0750';
$trainingtools_settings['standardsettings']['systematicpostupdating'] = 'enabled';
// testing and development
$trainingtools_settings['standardsettings']['developementinsight'] = 'disabled';
// global switches
$trainingtools_settings['standardsettings']['textspinrespinning'] = 'enabled';// disabled stops all text spin re-spinning and sticks to the last spin

##########################################################################################
#                                                                                        #
#                           SETTINGS WITH NO UI OPTION                                   #
#              array key should be the method/function the setting is used in            #
##########################################################################################
$trainingtools_settings['create_localmedia_fromlocalimages']['destinationdirectory'] = 'wp-content/uploads/importedmedia/';
 
##########################################################################################
#                                                                                        #
#                            DATA IMPORT AND MANAGEMENT SETTINGS                         #
#                                                                                        #
##########################################################################################
$trainingtools_settings['datasettings']['insertlimit'] = 100;

##########################################################################################
#                                                                                        #
#                                    WIDGET SETTINGS                                     #
#                                                                                        #
##########################################################################################
$trainingtools_settings['widgetsettings']['dashboardwidgetsswitch'] = 'disabled';

##########################################################################################
#                                                                                        #
#                               CUSTOM POST TYPE SETTINGS                                #
#                                                                                        #
##########################################################################################
$trainingtools_settings['posttypes']['wtgflags']['status'] = 'disabled'; 
$trainingtools_settings['posttypes']['wtgflags']['title'] = __( 'Flag System', 'trainingtools' ); 

$trainingtools_settings['posttypes']['pageguides']['status'] = 'enabled';
$trainingtools_settings['posttypes']['pageguides']['title'] = __( 'Page Guides', 'trainingtools' );

##########################################################################################
#                                                                                        #
#                                    NOTICE SETTINGS                                     #
#                                                                                        #
##########################################################################################
$trainingtools_settings['noticesettings']['wpcorestyle'] = 'enabled';

##########################################################################################
#                                                                                        #
#                           YOUTUBE RELATED SETTINGS                                     #
#                                                                                        #
##########################################################################################
$trainingtools_settings['youtubesettings']['defaultcolor'] = '&color1=0x2b405b&color2=0x6b8ab6';
$trainingtools_settings['youtubesettings']['defaultborder'] = 'enable';
$trainingtools_settings['youtubesettings']['defaultautoplay'] = 'enable';
$trainingtools_settings['youtubesettings']['defaultfullscreen'] = 'enable';
$trainingtools_settings['youtubesettings']['defaultscriptaccess'] = 'always';

##########################################################################################
#                                                                                        #
#                                  LOG SETTINGS                                          #
#                                                                                        #
##########################################################################################
$trainingtools_settings['logsettings']['uselog'] = 1;
$trainingtools_settings['logsettings']['loglimit'] = 1000;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['outcome'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['timestamp'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['line'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['function'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['page'] = true; 
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['panelname'] = true;   
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['userid'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['type'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['category'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['action'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['priority'] = true;
$trainingtools_settings['logsettings']['logscreen']['displayedcolumns']['comment'] = true;
?>