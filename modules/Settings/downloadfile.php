<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
* 
 ********************************************************************************/

require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $fileId;

$fileid = $_REQUEST['fileid'];

//$dbQuery = "SELECT * from vtiger_seattachmentsrel where crmid = '" .$fileid ."'";
//$attachmentsid = $adb->query_result($adb->query($dbQuery),0,'attachmentsid');
$attachmentsid = $fileid;

$returnmodule=$_REQUEST['return_module'];

if($_REQUEST['activity_type']=='Settings')
	$attachmentsid=$fileid;

$dbQuery = "SELECT * FROM vtiger_organizationdetails ";

$result = $adb->query($dbQuery) or die("Couldn't get file list");
if($adb->num_rows($result) == 1)
{
$name = @$adb->query_result($result, 0, "logoname");
	//echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa ' .$fileType;
$fileContent = @$adb->query_result($result, 0, "logo");
header("Cache-Control: private");
header("Content-Disposition: attachment; filename=$name");
header("Content-Description: PHP Generated Data");
echo base64_decode($fileContent);
}
else
{
echo "Record doesn't exist.";
}
?>

