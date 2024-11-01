<?php
/** 
 * Part of a system for detected specific database issues and handling them
 * with minimum fuss to the user. The idea is to generate good debug information
 * for sending to WebTechGlobal or repair tables quickly.
 *     
 * Database tables information for past and new versions.
 * 
 * This file is not fully in use yet. The intention is to migrate it to the
 * installation class and rather than an array I will simply store every version
 * of each tables query. Each query can be broken down to compare against existing 
 * tables. I find this array approach too hard to maintain over many plugins.
 * 
 * @package Training Tools
 * @author Ryan Bayne   
 * @version 8.1.2
 * 
 * @todo move this to installation class but also reduce the array to actual queries per version.
 * 
 * @todo create the ability to create the array using the tables once they are created.
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
 
 
/*   Column Array Example Returned From "mysql_query( "SHOW COLUMNS FROM..."
        
          array(6) {
            [0]=>
            string(5) "row_id"
            [1]=>
            string(7) "int(11)"
            [2]=>
            string(2) "NO"
            [3]=>
            string(3) "PRI"
            [4]=>
            NULL
            [5]=>
            string(14) "auto_increment"
          }
                  
    +------------+----------+------+-----+---------+----------------+
    | Field      | Type     | Null | Key | Default | Extra          |
    +------------+----------+------+-----+---------+----------------+
    | Id         | int(11)  | NO   | PRI | NULL    | auto_increment |
    | Name       | char(35) | NO   |     |         |                |
    | Country    | char(3)  | NO   | UNI |         |                |
    | District   | char(20) | YES  | MUL |         |                |
    | Population | int(11)  | NO   |     | 0       |                |
    +------------+----------+------+-----+---------+----------------+            
*/
   
global $wpdb;   

$trainingtools_tables_array =  array(
    'tables' => array ( 
        'webtechglobal_projects' => array( 'name' => $wpdb->prefix . 'webtechglobal_projects' ), 
        'tt_pages' => array( 'name' => $wpdb->prefix . 'tt_pages' ), 
        'tt_pagesmeta' => array( 'name' => $wpdb->prefix . 'tt_pagesmeta' ), 
        'tt_postboxes' => array( 'name' => $wpdb->prefix . 'tt_postboxes' ), 
        'tt_postboxesmeta' => array( 'name' => $wpdb->prefix . 'tt_postboxesmeta' ), 
        'tt_forms' => array( 'name' => $wpdb->prefix . 'tt_forms' ), 
        'tt_formsmeta' => array( 'name' => $wpdb->prefix . 'tt_formsmeta' ), 
    )
);

##################################################################################
#                                 webtechglobal_log                              #
##################################################################################        
$trainingtools_tables_array['tables']['webtechglobal_log']['name'] = $wpdb->prefix . 'webtechglobal_log';
$trainingtools_tables_array['tables']['webtechglobal_log']['required'] = false;// required for all installations or not (boolean)
$trainingtools_tables_array['tables']['webtechglobal_log']['pluginversion'] = '0.0.1';
$trainingtools_tables_array['tables']['webtechglobal_log']['usercreated'] = false;// if the table is created as a result of user actions rather than core installation put true
$trainingtools_tables_array['tables']['webtechglobal_log']['version'] = '0.0.1';// used to force updates based on version alone rather than individual differences
$trainingtools_tables_array['tables']['webtechglobal_log']['primarykey'] = 'row_id';
$trainingtools_tables_array['tables']['webtechglobal_log']['uniquekey'] = 'row_id';
// webtechglobal_log - row_id
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['row_id']['type'] = 'bigint(20)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['row_id']['null'] = 'NOT NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['row_id']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['row_id']['default'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['row_id']['extra'] = 'AUTO_INCREMENT';
// webtechglobal_log - outcome
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['outcome']['type'] = 'tinyint(1)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['outcome']['null'] = 'NOT NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['outcome']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['outcome']['default'] = '1';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['outcome']['extra'] = '';
// webtechglobal_log - timestamp
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['type'] = 'timestamp';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['null'] = 'NOT NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['default'] = 'CURRENT_TIMESTAMP';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['extra'] = '';
// webtechglobal_log - line
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['line']['type'] = 'int(11)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['line']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['line']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['line']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['line']['extra'] = '';
// webtechglobal_log - file
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['file']['type'] = 'varchar(250)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['file']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['file']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['file']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['file']['extra'] = '';
// webtechglobal_log - function
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['function']['type'] = 'varchar(250)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['function']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['function']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['function']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['function']['extra'] = '';
// webtechglobal_log - sqlresult
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['type'] = 'blob';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['extra'] = '';
// webtechglobal_log - sqlquery
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['extra'] = '';
// webtechglobal_log - sqlerror
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['type'] = 'mediumtext';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['extra'] = '';
// webtechglobal_log - wordpresserror
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['type'] = 'mediumtext';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['extra'] = '';
// webtechglobal_log - screenshoturl
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['type'] = 'varchar(500)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['extra'] = '';
// webtechglobal_log - userscomment
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['type'] = 'mediumtext';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['extra'] = '';
// webtechglobal_log - page
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['page']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['page']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['page']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['page']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['page']['extra'] = '';
// webtechglobal_log - version
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['version']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['version']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['version']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['version']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['version']['extra'] = '';
// webtechglobal_log - panelid
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelid']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelid']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelid']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelid']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelid']['extra'] = '';
// webtechglobal_log - panelname
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelname']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelname']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelname']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelname']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['panelname']['extra'] = '';
// webtechglobal_log - tabscreenid
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['extra'] = '';
// webtechglobal_log - tabscreenname
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['extra'] = '';
// webtechglobal_log - dump
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['dump']['type'] = 'longblob';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['dump']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['dump']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['dump']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['dump']['extra'] = '';
// webtechglobal_log - ipaddress
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['extra'] = '';
// webtechglobal_log - userid
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userid']['type'] = 'int(11)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userid']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userid']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userid']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['userid']['extra'] = '';
// webtechglobal_log - comment
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['comment']['type'] = 'mediumtext';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['comment']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['comment']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['comment']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['comment']['extra'] = '';
// webtechglobal_log - type
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['type']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['type']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['type']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['type']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['type']['extra'] = '';
// webtechglobal_log - category
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['category']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['category']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['category']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['category']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['category']['extra'] = '';
// webtechglobal_log - action
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['action']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['action']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['action']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['action']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['action']['extra'] = '';
// webtechglobal_log - priority
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['priority']['type'] = 'varchar(45)';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['priority']['null'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['priority']['key'] = '';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['priority']['default'] = 'NULL';
$trainingtools_tables_array['tables']['webtechglobal_log']['columns']['priority']['extra'] = '';              
?>