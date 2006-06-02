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
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/index.php,v 1.93 2005/04/21 16:17:25 ray Exp $
 * Description: Main file and starting point for the application.  Calls the 
 * theme header and footer files defined for the user as well as the module as 
 * defined by the input parameters.
 ********************************************************************************/

global $entityDel;
global $display;

require_once('include/utils.php');
if (substr(phpversion(), 0, 1) == "5") {
        ini_set("zend.ze1_compatibility_mode", "1");
}

function fetchPermissionDataForTabList()
{
  $permittedTabs = $_SESSION['tab_permission_set'];
  $i=0;$j=0;
  
  while($i<count($permittedTabs))
  {
    $modulesPermitted[$j++]=  $permittedTabs[$i];
    $i++;
  }
  return $modulesPermitted;
}



function fetchPermissionData($module,$action)
{

	global $theme,$display;
	global $others_permission_id;
	global $others_view;
	global $others_create_edit;
	global $others_delete;
	global $current_user;
        
	global $tabid;
	global $actionid;
	global $profile_id;

	require_once('modules/Users/UserInfoUtil.php');
	//Changing the tabid if the module is vendor,pricebook or salesorder
        if($action == 'VendorEditView' || $action == 'VendorDetailView' || $action == 'DeleteVendor' || $action == 'SaveVendor')
        {
                $tabid = getTabid('Vendor');
        }
        elseif($action == 'PriceBookEditView' || $action == 'PriceBookDetailView' || $action == 'DeletePriceBook' || $action == 'SavePriceBook')
        {
                $tabid = getTabid('PriceBook');
        }
        elseif($action == 'SalesOrderEditView' || $action == 'SalesOrderDetailView' || $action == 'DeleteSalesOrder' || $action == 'SaveSalesOrder')
        {
                $tabid = getTabid('SalesOrder');
        }
        else
        {
		$tabid = getTabid($module);
	}
	
	$actionid = getActionid($action);
	$profile_id = $_SESSION['authenticated_user_profileid'];
	$tab_per_Data = getAllTabsPermission($profile_id);

	$permissionData = $_SESSION['action_permission_set'];
	$defSharingPermissionData = $_SESSION['defaultaction_sharing_permission_set'];
	$others_permission_id = $defSharingPermissionData[$tabid];
	
	$i=0;

	

	$accessFlag = false;
	if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $module != 'Notes' && $module != 'Products' && $module != 'Faq')
	{
		$rec_owner_id = getUserId($_REQUEST['record']);
	}
	if($tab_per_Data[$tabid] !=0)
	{
		echo "You are not permitted to execute this operation";
                $display = "No";
	}

	if($permissionData[$tabid][$actionid] !=0)
	{
		echo "You are not permitted to execute this operation";
		$display = "No";
	}
	elseif(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $others_permission_id != '' && $module != 'Notes' && $module != 'Products' && $module != 'Faq' && $rec_owner_id != 0)
	{
		if($rec_owner_id != $current_user->id)
		{
			if($others_permission_id == 0)
			{
				if($action == 'EditView' || $action == 'Delete')
				{
					echo "You are not permitted to execute this operation";
			                $display = "No";	
				}
				else
				{
					return;
				}
			}
			elseif($others_permission_id == 1)
			{
				if($action == 'Delete')
				{
					echo "You are not permitted to execute this operation";
			                $display = "No";	
				}
				else
				{
					return;
				}
			}
			elseif($others_permission_id == 2)
			{
				
				return;
			}
			elseif($others_permission_id == 3)
			{
				if($action == 'DetailView' || $action == 'EditView' || $action == 'Delete')
				{
					echo "You are not permitted to execute this operation";
			                $display = "No";	
				}
				else
				{
					return;
				}
			}
				
			
		}
		else
		{
			return;
		}
	}
	else
	{
		return;
	}

	if(!$accessFlag)
	{
		echo "You are not permitted to execute this operation";
		$display = "No";
	}
}

//we have to do this as there is no UI page for Delete. Hence, when the user clicks delete, it gets stuck halfway and the page looks ugly because the theme is not set
function checkDeletePermission($tabid)
{
	global $entityDel;
	$action ="Delete";
	$actionid = 2;	
	$permissionData = $_SESSION['action_permission_set'];
	$i = 0;
	//keep searching till Delete method is found in the array
	while($i<count($permissionData))
	{
		if($permissionData[$i][0] == $tabid  &&  $permissionData[$i][1] == $actionid)
		{
			$actionpermissionvalue=$permissionData[$i][2];
			if($actionpermissionvalue == 0)
			{
				//if 0, then the delete button should be shown
				$entityDel = true;
			}
			else
			{
				$entityDel = false;
			}
		}
		$i++;
	}

}


function stripslashes_checkstrings($value)
{
        if (is_string($value)) {
                return stripslashes($value);
        }
        return $value;

 }
 
function insert_charset_header()
{
 	global $app_strings, $default_charset;
 	$charset = $default_charset;
 	
 	if(isset($app_strings['LBL_CHARSET']))
 	{
 	        $charset = $app_strings['LBL_CHARSET'];
 	}
 	header('Content-Type: text/html; charset='. $charset);
}

if (get_magic_quotes_gpc() == 1){
        $_REQUEST = array_map("stripslashes_checkstrings", $_REQUEST);
        $_POST = array_map("stripslashes_checkstrings", $_POST);
        $_GET = array_map("stripslashes_checkstrings", $_GET);
}

// Allow for the session information to be passed via the URL for printing.
if (isset($_REQUEST['PHPSESSID']))
{
	session_id($_REQUEST['PHPSESSID']);
	//Setting the same session id to Forums as in CRM
	$sid = $_REQUEST['PHPSESSID'];
}

insert_charset_header();


// Create or reestablish the current session
session_start();

if (!is_file('config.php')) {
    header("Location: install.php");
    exit();
}

require_once('config.php');
if (!isset($dbconfig['db_host_name'])) {
    header("Location: install.php");
    exit();
}

// load up the config_override.php file.  This is used to provide default user settings
if (is_file('config_override.php')) 
{
	require_once('config_override.php');
}
$default_config_values = Array( "allow_exports"=>"all","upload_maxsize"=>"3000000" );
 	
set_default_config($default_config_values);
require_once('include/logging.php');
require_once('modules/Users/User.php');

global $currentModule;

$log =& LoggerManager::getLogger('index');
if (isset($_REQUEST['PHPSESSID']))
	$log->debug("****Starting for session ".$_REQUEST['PHPSESSID']);
else
	$log->debug("****Starting for new session");

// We use the REQUEST_URI later to construct dynamic URLs.  IIS does not pass this field
// to prevent an error, if it is not set, we will assign it to ''
if(!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = '';
}

if(isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];
}

//Code added for 'Path Traversal/File Disclosure' security fix - Philip
$is_module = false;
if(isset($_REQUEST['module']))
{
        $module = $_REQUEST['module'];
        if ($dir = @opendir("./modules"))
        {
                while (($file = readdir($dir)) !== false)
                {
                        if ($file != ".." && $file != "." && $file != "CVS" && $file != "Attic")
                        {
                                if(is_dir("./modules/".$file))
                                {
                                        if(!($file[0] == '.'))
                                        {
                                                if($file=="$module")
                                                {
                                                        $is_module = true;
                                                }
                                        }
                                }
                        }
                }
        }
        if(!$is_module)
        {
                die("Hacking Attempt");
        }
}

//Code added for 'Multiple SQL Injection Vulnerabilities & XSS issue' fixes - Philip
if (isset($_REQUEST['record']) && !is_numeric($_REQUEST['record']) && $_REQUEST['record']!='')
{
        die("An invalid record number specified to view details.");
}

// Check to see if there is an authenticated user in the session.
if (isset($_SESSION["authenticated_user_id"]))
{
	$log->debug("We have an authenticated user id: ".$_SESSION["authenticated_user_id"]);
}
else if(isset($action) && isset($module) && $action=="Authenticate" && $module=="Users")
{
	$log->debug("We are authenticating user now");
}
else 
{
	$log->debug("The current user does not have a session.  Going to the login page");	
	$action = "Login";
	$module = "Users";
}


$log->debug($_REQUEST);
$skipHeaders=false;
$skipFooters=false;
$viewAttachment = false;
$skipSecurityCheck= false;

if (isset($action) && isset($module))
{
	$log->info("About to take action ".$action);
	$log->debug("in $action");
	if(ereg("^Save", $action) || ereg("^Delete", $action) || ereg("^Popup", $action) || ereg("^ChangePassword", $action) || ereg("^Authenticate", $action) || ereg("^Logout", $action) || ereg("^Export",$action) || ereg("^add2db", $action) || ereg("^result", $action) || ereg("^LeadConvertToEntities", $action) || ereg("^downloadfile", $action) || ereg("^massdelete", $action) || ereg("^updateLeadDBStatus",$action) || ereg("^AddCustomFieldToDB", $action) || ereg("^updateRole",$action) || ereg("^UserInfoUtil",$action) || ereg("^deleteRole",$action) || ereg("^UpdateComboValues",$action) || ereg("^fieldtypes",$action) || ereg("^app_ins",$action) || ereg("^minical",$action) || ereg("^minitimer",$action) || ereg("^app_del",$action) || ereg("^send_mail",$action) || ereg("^populatetemplate",$action) || ereg("^TemplateMerge",$action) || ereg("^testemailtemplateusage",$action) || ereg("^saveemailtemplate",$action) || ereg("^lookupemailtemplate",$action) || ereg("^deletewordtemplate",$action) || ereg("^deleteemailtemplate",$action) || ereg("^deleteattachments",$action) || ereg("^MassDeleteUsers",$action) || ereg("^UpdateFieldLevelAccess",$action) || ereg("^UpdateDefaultFieldLevelAccess",$action) || ereg("^UpdateProfile",$action)  || ereg("^updateRelations",$action) || ereg("^updateNotificationSchedulers",$action) || ereg("^VendorPopup",$action) || ereg("^Star",$action) || ereg("^addPbProductRelToDB",$action) || ereg("^UpdateListPrice",$action) || ereg("^PriceBookPopup",$action) || ereg("^SalesOrderPopup",$action) || ereg("^CreatePDF",$action) || ereg("^CreateSOPDF",$action) || ereg("^redirect",$action) || ereg("^webmail",$action) || ereg("^left_main",$action) || ereg("^delete_message",$action) || ereg("^mime",$action) || ereg("^move_messages",$action) || ereg("^folders_create",$action) || ereg("^imap_general",$action) || ereg("^mime",$action) || ereg("^download",$action) || ereg("^about_us",$action) || ereg("^SendMailAction",$action) || ereg("^CreateXL",$action))
	{
		$skipHeaders=true;
		if(ereg("^Popup", $action) || ereg("^ChangePassword", $action) || ereg("^Export", $action) || ereg("^downloadfile", $action) || ereg("^fieldtypes",$action) || ereg("^lookupemailtemplate",$action) || ereg("^about_us",$action))
			$skipFooters=true;
		if(ereg("^downloadfile", $action) || ereg("^fieldtypes",$action))
		{
			$viewAttachment = true;
		}
		if(($action == ' Delete ') && (!$entityDel))
		{
			$skipHeaders=false;
		}
	}
	
	if($action == 'BusinessCard' || $action == 'Save')
	{
 	         header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
 	         header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
 	         header( "Cache-Control: no-cache, must-revalidate" );
 	         header( "Pragma: no-cache" );        
 	}

	if ( $action == "Import" &&
                isset($_REQUEST['step']) &&
                $_REQUEST['step'] == '4'  )
	{
		$skipHeaders=true;
		$skipFooters=true;
	}
	if($module == 'Users' || $module == 'Home' || $module == 'Administration' || $module == 'uploads' ||  $module == 'Settings' || $module == 'Calendar')
	{
		$skipSecurityCheck=true;
	}

	$currentModuleFile = 'modules/'.$module.'/'.$action.'.php';
	$currentModule = $module;
}
elseif(isset($module))
{
	$currentModule = $module;
	$currentModuleFile = $moduleDefaultFile[$currentModule];
}
else {
    // use $default_module and $default_action as set in config.php
    // Redirect to the correct module with the correct action.  We need the URI to include these fields.
	header("Location: index.php?action=$default_action&module=$default_module");
	exit();
}

$log->info("current page is $currentModuleFile");	
$log->info("current module is $currentModule ");	

//define default home pages for each module
require_once("modules/Users/TabMenu.php");
$tabData = new TabMenu();

global $permittedModulesList;

$permittedModulesList = fetchPermissionDataForTabList();
$tempList="";
if(!$permittedModulesList == "")
{
     
     foreach ($permittedModulesList as $list) 
     {
       if($tempList=="")
       {
         $tempList = "'".$list."'" ;  
       }
       else
       {
         $tempList .= ",'" . $list."'" ;
       }
       $list="";
     }
}

$moduleList = $tabData->getTabNames($tempList);

foreach ($moduleList as $mod) {
	$moduleDefaultFile[$mod] = "modules/".$currentModule."/index.php";
}

// for printing
$module = (isset($_REQUEST['module'])) ? $_REQUEST['module'] : "";
$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : "";
$record = (isset($_REQUEST['record'])) ? $_REQUEST['record'] : "";
$lang_crm = (isset($_SESSION['authenticated_user_language'])) ? $_SESSION['authenticated_user_language'] : "";
$GLOBALS['request_string'] = "&module=$module&action=$action&record=$record&lang_crm=$lang_crm";

$current_user = new User();
if(isset($_SESSION['authenticated_user_id']))
{
	$result = $current_user->retrieve($_SESSION['authenticated_user_id']);
	if($result == null)
	{
		session_destroy();
	    header("Location: index.php?action=Login&module=Users");
	}
	
	$log->debug('Current user is: '.$current_user->user_name);
}

if(isset($_SESSION['authenticated_user_theme']) && $_SESSION['authenticated_user_theme'] != '')
{
	$theme = $_SESSION['authenticated_user_theme'];
}
else 
{
	$theme = $default_theme;
}
$log->debug('Current theme is: '.$theme);

//Logging instantiation
require_once('vtiger_logger.php');
$vtlog = new vtiger_logger();

//Used for current record focus
$focus = "";

// if the language is not set yet, then set it to the default language.
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '')
{
	$current_language = $_SESSION['authenticated_user_language'];
}
else 
{
	$current_language = $default_language;
}
$log->debug('current_language is: '.$current_language);

//set module and application string arrays based upon selected language
$app_strings = return_application_language($current_language);
$app_list_strings = return_app_list_strings_language($current_language);
$mod_strings = return_module_language($current_language, $currentModule);

//TODO: Clint - this key map needs to be moved out of $app_list_strings since it never gets translated.
//              best to just have an upgrade script that changes the parent_type column from Account to Accounts, etc.
$app_list_strings['record_type_module'] = array('Account' => 'Accounts','Potential' => 'Potentials', 'Case' => 'Cases');

//If DetailView, set focus to record passed in
if($action == "DetailView" || $action == "SalesOrderDetailView" || $action == "VendorDetailView" || $action == "PriceBookDetailView")
{
	if(!isset($_REQUEST['record']))
		die("A record number must be specified to view details.");

	// If we are going to a detail form, load up the record now.
	// Use the record to track the viewing.
	// todo - Have a record of modules and thier primary object names.
	//Getting the actual module
	$actualModule = $currentModule;
	switch($currentModule)
	{
		case 'Leads':
			require_once("modules/$currentModule/Lead.php");
			$focus = new Lead();
			break;
		case 'Contacts':
			require_once("modules/$currentModule/Contact.php");
			$focus = new Contact();
			break;
		case 'Accounts':
			require_once("modules/$currentModule/Account.php");
			$focus = new Account();
			break;
		case 'Potentials':
			require_once("modules/$currentModule/Opportunity.php");
			$focus = new Potential();
			break;
		case 'Activities':
			require_once("modules/$currentModule/Activity.php");
			$focus = new Activity();
			break;
		case 'Notes':
			require_once("modules/$currentModule/Note.php");
			$focus = new Note();
			break;
		case 'Emails':
			require_once("modules/$currentModule/Email.php");
			$focus = new Email();
			break;
		case 'Users':
			require_once("modules/$currentModule/User.php");
			$focus = new User();
			break;
		case 'Products':
			if($action == 'DetailView')
			{
				require_once("modules/$currentModule/Product.php");
				$focus = new Product();
			}
			elseif($action == 'VendorDetailView')
			{
				require_once("modules/$currentModule/Vendor.php");
				$focus = new Vendor();
				$actualModule = 'Vendor';
			}
			elseif($action == 'PriceBookDetailView')
			{
				require_once("modules/$currentModule/PriceBook.php");
				$focus = new PriceBook();
				$actualModule = 'PriceBook';
			}
			break;
			
		case 'HelpDesk':
			require_once("modules/$currentModule/HelpDesk.php");
			$focus = new HelpDesk();
			break;
		case 'Faq':
			require_once("modules/$currentModule/Faq.php");
			$focus = new Faq();
			break;
		case 'Quotes':
			require_once("modules/$currentModule/Quote.php");
			$focus = new Quote();
			break;
		case 'Orders':
			if($action == 'DetailView')
			{
				require_once("modules/$currentModule/Order.php");
				$focus = new Order();
			}
			elseif($action == 'SalesOrderDetailView')
			{
				require_once("modules/$currentModule/SalesOrder.php");
				$focus = new SalesOrder();
				$actualModule = 'SalesOrder';
			}
			break;
		case 'Invoice':
			require_once("modules/$currentModule/Invoice.php");
			$focus = new Invoice();
			break;
		}
	
		
	if(isset($_REQUEST['record']) && $_REQUEST['record']!='')
	{
		// Only track a viewing if the record was retrieved.
		$focus->track_view($current_user->id, $actualModule,$_REQUEST['record']);
	}
}	

//Added to highlight the HelpDesk tab when create, edit or view the FAQ
if($currentModule == 'Faq')
        $currentModule = 'HelpDesk';

// set user, theme and language cookies so that login screen defaults to last values
if (isset($_SESSION['authenticated_user_id'])) {
        $log->debug("setting cookie ck_login_id to ".$_SESSION['authenticated_user_id']);
        setcookie('ck_login_id', $_SESSION['authenticated_user_id']);
}
if (isset($_SESSION['authenticated_user_theme'])) {
        $log->debug("setting cookie ck_login_theme to ".$_SESSION['authenticated_user_theme']);
        setcookie('ck_login_theme', $_SESSION['authenticated_user_theme']);
}
if (isset($_SESSION['authenticated_user_language'])) {
        $log->debug("setting cookie ck_login_language to ".$_SESSION['authenticated_user_language']);
        setcookie('ck_login_language', $_SESSION['authenticated_user_language']);
}

//skip headers for popups, deleting, saving, importing and other actions
if(!$skipHeaders) {
	$log->debug("including headers");
	//include('themes/'.$theme.'/header.php');
	if(isset($_SESSION["authenticated_user_id"]))
	{
		include('themes/'.$theme.'/header.php');
	}
	else 
		include('themes/'.$theme.'/loginheader.php');
	
	if (isset($_SESSION['administrator_error']))
	{
		// only print DB errors once otherwise they will still look broken after they are fixed.
		// Only print the errors for admin users.
		if(is_admin($current_user)) 
			echo $_SESSION['administrator_error'];
		unset($_SESSION['administrator_error']);
	}
	
	echo "<!-- startscrmprint -->";
}
else {
		$log->debug("skipping headers");
}


// Added to get the theme . This is a bad fix as we need to know where the
// problem lies yet
if(isset($_SESSION['authenticated_user_theme']) && $_SESSION['authenticated_user_theme'] != '')
{
	$theme = $_SESSION['authenticated_user_theme'];
}
else 
{
	$theme = $default_theme;
}

// Fetch the permission set from session and search it for the requisite data
if (!$skipSecurityCheck)
{
  fetchPermissionData($module,$action);
}

if ($display == "No")
{
	$display == "";
}
else
{
	include($currentModuleFile);
}

if (!$viewAttachment)
{
	echo "<!-- stopscrmprint -->";
}

if ( !$skipFooters && isset($_SESSION["authenticated_user_id"]))
{
	include('themes/'.$theme.'/footer.php');
}

?>