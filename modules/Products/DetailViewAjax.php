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

require_once('include/logging.php');
require_once('modules/Products/Products.php');
require_once('include/database/PearDatabase.php');
global $adb,$current_user;

$local_log =& LoggerManager::getLogger('ProductsAjax');

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($crmid != "")
	{
		$modObj = new Products();
		$modObj->retrieve_entity_info($crmid,"Products");
		$modObj->column_fields[$fieldname] = $fieldvalue;
		if($fieldname == 'unit_price')//unit price converted to dollar value while saving
		{
			$modObj->column_fields[$fieldname] = getConvertedPrice($fieldvalue);	
		}
		$modObj->id = $crmid;
		$modObj->mode = "edit";
		$modObj->save("Products");
		if($modObj->id != "")
		{
			echo ":#:SUCCESS";
		}else
		{
			echo ":#:FAILURE";
		}   
	}else
	{
		echo ":#:FAILURE";
	}
}
?>