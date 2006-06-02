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
require_once('include/database/PearDatabase.php');
require_once('XTemplate/xtpl.php');
require_once('include/utils.php');
require_once('modules/Products/Product.php');
require_once('include/uifromdbutil.php');

$focus = new Product();

if (isset($_REQUEST['record'])) {
  $focus->retrieve_entity_info($_REQUEST['record'], "Products");
  $focus->id = $_REQUEST['record'];
  $focus->name=$focus->column_fields['productname'];		
}

if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
        $focus->id = "";
}

global $app_strings;
global $app_list_strings;
global $mod_strings;
global $current_user;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$xtpl=new XTemplate ('modules/Products/DetailView.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
if (isset($focus->name)) $xtpl->assign("NAME", $focus->name);
else $xtpl->assign("NAME", "");
$block_1 = getDetailBlockInformation("Products",1,$focus->column_fields);
$xtpl->assign("BLOCK1", $block_1);
$block_2 = getDetailBlockInformation("Products",2,$focus->column_fields);
$xtpl->assign("BLOCK2", $block_2);
$block_3 = getDetailBlockInformation("Products",3,$focus->column_fields);
$xtpl->assign("BLOCK3", $block_3);
$block_4 = getDetailBlockInformation("Products",4,$focus->column_fields);
$xtpl->assign("BLOCK4", $block_4);
$block_6 = getDetailBlockInformation("Products",6,$focus->column_fields);
$xtpl->assign("BLOCK6", $block_6);
$block_1_header = getBlockTableHeader("LBL_PRODUCT_INFORMATION");
$block_2_header = getBlockTableHeader("LBL_PRICING_INFORMATION");
$block_3_header = getBlockTableHeader("LBL_STOCK_INFORMATION");
$block_4_header = getBlockTableHeader("LBL_DESCRIPTION_INFORMATION");
$block_6_header = getBlockTableHeader("LBL_IMAGE_INFORMATION");
$xtpl->assign("BLOCK1_HEADER", $block_1_header);
$xtpl->assign("BLOCK2_HEADER", $block_2_header);
$xtpl->assign("BLOCK3_HEADER", $block_3_header);
$xtpl->assign("BLOCK4_HEADER", $block_4_header);
$xtpl->assign("BLOCK6_HEADER", $block_6_header);

$block_5 = getDetailBlockInformation("Products",5,$focus->column_fields);
if(trim($block_5) != '')
{
        $cust_fld = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formOuterBorder">';
        $cust_fld .=  '<tr><td>';
        $block_5_header = getBlockTableHeader("LBL_CUSTOM_INFORMATION");
        $cust_fld .= $block_5_header;
        $cust_fld .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $cust_fld .= $block_5;
        $cust_fld .= '</table>';
        $cust_fld .= '</td></tr></table>';
        $cust_fld .= '<BR>';

}

$xtpl->assign("CUSTOMFIELD", $cust_fld);


$permissionData = $_SESSION['action_permission_set'];
if(isPermitted("Products",1,$_REQUEST['record']) == 'yes')
{
	$xtpl->assign("EDITBUTTON","<td><input title=\"$app_strings[LBL_EDIT_BUTTON_TITLE]\" accessKey=\"$app_strings[LBL_EDIT_BUTTON_KEY]\" class=\"button\" onclick=\"this.form.return_module.value='Products'; this.form.return_action.value='DetailView'; this.form.return_id.value='".$_REQUEST['record']."'; this.form.action.value='EditView'\" type=\"submit\" name=\"Edit\" value=\"$app_strings[LBL_EDIT_BUTTON_LABEL]\"></td>");


	$xtpl->assign("DUPLICATEBUTTON","<td><input title=\"$app_strings[LBL_DUPLICATE_BUTTON_TITLE]\" accessKey=\"$app_strings[LBL_DUPLICATE_BUTTON_KEY]\" class=\"button\" onclick=\"this.form.return_module.value='Products'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true'; this.form.action.value='EditView'\" type=\"submit\" name=\"Duplicate\" value=\"$app_strings[LBL_DUPLICATE_BUTTON_LABEL]\"></td>");
}


if(isPermitted("Products",2,$_REQUEST['record']) == 'yes')
{
	$xtpl->assign("DELETEBUTTON","<td><input title=\"$app_strings[LBL_DELETE_BUTTON_TITLE]\" accessKey=\"$app_strings[LBL_DELETE_BUTTON_KEY]\" class=\"button\" onclick=\"this.form.return_module.value='Products'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('$app_strings[NTC_DELETE_CONFIRMATION]')\" type=\"submit\" name=\"Delete\" value=\"$app_strings[LBL_DELETE_BUTTON_LABEL]\"></td>");
}

$xtpl->assign("IMAGE_PATH", $image_path);
$xtpl->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$xtpl->assign("ID", $_REQUEST['record']);
$xtpl->parse("main");
$xtpl->out("main");

//Security check for related list
global $profile_id;
$tab_per_Data = getAllTabsPermission($profile_id);
$permissionData = $_SESSION['action_permission_set'];
getRelatedLists("Products", $focus);

?>
