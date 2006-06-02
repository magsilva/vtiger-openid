<?php 
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
********************************************************************************/

$vtiger_version = '4.2.3';

$release_date = '23 December 2005';

//World clock & Calculator will be displayed if these variables are 'true' otherwise will not be displayed.
$WORLD_CLOCK_DISPLAY = 'true';
$CALCULATOR_DISPLAY = 'true';

//This is the URL for customer portal.(Ex. http://vtiger.com/portal)
$PORTAL_URL = 'http://your-domain.com/customerportal';

//These two are the HelpDesk support email id and the support name. (Ex. 'support@vtiger.com' and 'vtiger Support')
$HELPDESK_SUPPORT_EMAIL_ID = 'support@your-domain.com';
$HELPDESK_SUPPORT_NAME = 'your-domain Name';
/* Database configuration
      db_host_name:     MySQL Database Hostname
      db_user_name:    	MySQL Username
      db_password:     	MySQL Password
      db_name:     		MySQL Database Name
*/
$dbconfig['db_host_name'] = 	'localhost';
$dbconfig['db_user_name'] = 	'test';
$dbconfig['db_password'] = 		'test';
$dbconfig['db_name'] = 			'vtiger';
$dbconfig['db_type'] = 'mysql';

$dbconfig['log_sql'] = false;
$dbconfigoption['persistent'] = true;
$dbconfigoption['autofree'] = false;
$dbconfigoption['debug'] = 0;
$dbconfigoption['seqname_format'] = '%s_seq';
$dbconfigoption['portability'] = 0;
$dbconfigoption['ssl'] = false;

$host_name = 'localhost';

$site_URL = 'http://localhost:80/~magsilva/Projects/ideais/vtiger';

$root_directory = '/home/magsilva/public_html/Projects/ideais/vtiger/';

$cache_dir = 'cache/';
$mail_server = '';
$mail_server_username = '';
$mail_server_password = '';
$tmp_dir = 'cache/images/';
$import_dir = 'cache/import/';

// Maximum file size for uploaded files (in bytes)
// also used when uploading import files
$upload_maxsize = 3000000;
// Flag to allow export functionality
// use 'all' to allow anyone to use exports 
// use 'admin' to only allow admins to export 
// use 'none' to block exports completely 
$allow_exports = 'all';
$upload_dir = 'cache/upload/';
// Files with one of these extensions will have '.txt' appended to their filename on upload
$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm');

// This is the full path to the include directory including the trailing slash
$includeDirectory = $root_directory.'include/';

$list_max_entries_per_page = '20';

$history_max_viewed = '5';

//define list of menu tabs
//$moduleList = Array('Home', 'Dashboard', 'Contacts', 'Accounts', 'Opportunities', 'Cases', 'Notes', 'Calls', 'Emails', 'Meetings', 'Tasks','MessageBoard');

// Map Sugar language codes to jscalendar language codes
// Unimplemented until jscalendar language files are fixed
// $cal_codes = array('en_us'=>'en', 'ja'=>'jp', 'sp_ve'=>'sp', 'it_it'=>'it', 'tw_zh'=>'zh', 'pt_br'=>'pt', 'se'=>'sv', 'cn_zh'=>'zh', 'ge_ge'=>'de', 'ge_ch'=>'de', 'fr'=>'fr');

$default_module = 'Home';
$default_action = 'index';

//set default theme
$default_theme = 'blue';

// If true, the time to compose each page is placed in the browser.
$calculate_response_time = true;
// Default Username - The default text that is placed initially in the login form for user name.
$default_user_name = '';
// Default Password - The default text that is placed initially in the login form for password.
$default_password = '';
// Create default user - If true, a user with the default username and password is created.
$create_default_user = false;
$default_user_is_admin = false;
// disable persistent connections - If your MySQL/PHP configuration does not support persistent connections set this to true to avoid a large performance slowdown
$disable_persistent_connections = false;
// Defined languages available.  The key must be the language file prefix.  E.g. 'en_us' is the prefix for every 'en_us.lang.php' file. 
$languages = Array('en_us'=>'US English',);
// Default charset if the language specific character set is not found.
$default_charset = 'ISO-8859-1';
// Default language in case all or part of the user's language pack is not available.
$default_language = 'en_us';
// Translation String Prefix - This will add the language pack name to every translation string in the display.
$translation_string_prefix = false;
?>