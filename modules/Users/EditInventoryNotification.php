<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$smarty = new vtigerCRM_Smarty;
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') 
{
    $id = $_REQUEST['record'];
	$sql="select * from vtiger_inventorynotification where notificationid = ".$id;
	$result = $adb->query($sql);
	if($adb->num_rows($result) ==1);
	{
		$label = $mod_strings[$adb->query_result($result,0,'notificationname')];
		$notification_subject = $adb->query_result($result,0,'notificationsubject');
		$notification_body = $adb->query_result($result,0,'notificationbody');
		$notification_id = $adb->query_result($result,0,'notificationid');

		$notification = Array();
		$notification['label'] = $label;
		$notification['subject'] = $notification_subject;
		$notification['body'] = $notification_body;
		$notification['id'] = $notification_id;
	}
	
	$smarty->assign("NOTIFY_DETAILS",$notification);
	$smarty->assign("MOD", return_module_language($current_language,'Settings'));
	$smarty->assign("IMAGE_PATH",$image_path);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("CMOD", $mod_strings);
	$smarty->display("Settings/EditInventoryNotify.tpl");
}
else
{
	header("Location:index.php?module=Users&action=listnotificationschedulers&directmode=ajax");
}
?>
