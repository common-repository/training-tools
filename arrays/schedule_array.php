<?php
/** 
 * Default schedule array for Training Tools plugin 
 * 
 * @package Training Tools
 * @author Ryan Bayne   
 * @since 0.0.1
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

$trainingtools_schedule_array = array();
// history
$trainingtools_schedule_array['history']['lastreturnreason'] = __( 'None', 'trainingtools' );
$trainingtools_schedule_array['history']['lasteventtime'] = time();
$trainingtools_schedule_array['history']['lasteventtype'] = __( 'None', 'trainingtools' );
$trainingtools_schedule_array['history']['day_lastreset'] = time();
$trainingtools_schedule_array['history']['hour_lastreset'] = time();
$trainingtools_schedule_array['history']['hourcounter'] = 1;
$trainingtools_schedule_array['history']['daycounter'] = 1;
$trainingtools_schedule_array['history']['lasteventaction'] = __( 'None', 'trainingtools' );
// times/days
$trainingtools_schedule_array['days']['monday'] = true;
$trainingtools_schedule_array['days']['tuesday'] = true;
$trainingtools_schedule_array['days']['wednesday'] = true;
$trainingtools_schedule_array['days']['thursday'] = true;
$trainingtools_schedule_array['days']['friday'] = true;
$trainingtools_schedule_array['days']['saturday'] = true;
$trainingtools_schedule_array['days']['sunday'] = true;
// times/hours
$trainingtools_schedule_array['hours'][0] = true;
$trainingtools_schedule_array['hours'][1] = true;
$trainingtools_schedule_array['hours'][2] = true;
$trainingtools_schedule_array['hours'][3] = true;
$trainingtools_schedule_array['hours'][4] = true;
$trainingtools_schedule_array['hours'][5] = true;
$trainingtools_schedule_array['hours'][6] = true;
$trainingtools_schedule_array['hours'][7] = true;
$trainingtools_schedule_array['hours'][8] = true;
$trainingtools_schedule_array['hours'][9] = true;
$trainingtools_schedule_array['hours'][10] = true;
$trainingtools_schedule_array['hours'][11] = true;
$trainingtools_schedule_array['hours'][12] = true;
$trainingtools_schedule_array['hours'][13] = true;
$trainingtools_schedule_array['hours'][14] = true;
$trainingtools_schedule_array['hours'][15] = true;
$trainingtools_schedule_array['hours'][16] = true;
$trainingtools_schedule_array['hours'][17] = true;
$trainingtools_schedule_array['hours'][18] = true;
$trainingtools_schedule_array['hours'][19] = true;
$trainingtools_schedule_array['hours'][20] = true;
$trainingtools_schedule_array['hours'][21] = true;
$trainingtools_schedule_array['hours'][22] = true;
$trainingtools_schedule_array['hours'][23] = true;
// limits
$trainingtools_schedule_array['limits']['hour'] = '1000';
$trainingtools_schedule_array['limits']['day'] = '5000';
$trainingtools_schedule_array['limits']['session'] = '300';
// event types (update event_action() if adding more eventtypes)
// deleteuserswaiting - this is the auto deletion of new users who have not yet activated their account 
$trainingtools_schedule_array['eventtypes']['deleteuserswaiting']['name'] = __( 'Delete Users Waiting', 'trainingtools' ); 
$trainingtools_schedule_array['eventtypes']['deleteuserswaiting']['switch'] = 'disabled';
// send emails - rows are stored in wp_c2pmailing table for mass email campaigns 
$trainingtools_schedule_array['eventtypes']['sendemails']['name'] = __( 'Send Emails', 'trainingtools' ); 
$trainingtools_schedule_array['eventtypes']['sendemails']['switch'] = 'disabled';    
?>