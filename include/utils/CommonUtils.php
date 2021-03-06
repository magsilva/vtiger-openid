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
 * $Header$
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

  require_once('include/database/PearDatabase.php');
  require_once('include/ComboUtil.php'); //new
  require_once('include/utils/utils.php'); //new
  require_once('include/utils/RecurringType.php');

/**
 * Check if user id belongs to a system admin.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function is_admin($user) {
	global $log;
	$log->debug("Entering is_admin(".$user.") method ...");
	
	if ($user->is_admin == 'on')
	{
		$log->debug("Exiting is_admin method ..."); 
		return true;
	}
	else
	{
		$log->debug("Exiting is_admin method ...");
		 return false;
	}
}

/**
 * THIS FUNCTION IS DEPRECATED AND SHOULD NOT BE USED; USE get_select_options_with_id()
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options (&$option_list, $selected, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options (".$option_list.",".$selected.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options  method ...");
	return get_select_options_with_id($option_list, $selected, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values is an array of the datas 
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options_with_id (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_id_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_array (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_array (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_array  method ...");
        return get_options_array_seperate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The value is an array of data
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_options_array_seperate_key (&$label_list, &$key_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_options_array_seperate_key (".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
	global $app_strings;
	if($advsearch=='true')
	$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	else
	$select_options = "";

	//for setting null selection values to human readable --None--
	$pattern = "/'0?'></";
	$replacement = "''>".$app_strings['LBL_NONE']."<";
	if (!is_array($selected_key)) $selected_key = array($selected_key);

	//create the type dropdown domain and set the selected value if $opp value already exists
	foreach ($key_list as $option_key=>$option_value) {
		$selected_string = '';
		// the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
		// The vtiger_reported bug was only happening with one of the vtiger_users in the drop down.  It was being replaced by none.
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
		{
			$selected_string = 'selected';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
		$options[$html_value]=array($label_list[$option_key]=>$selected_string);
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);

	$log->debug("Exiting get_options_array_seperate_key  method ...");
	return $options;
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */

function get_select_options_with_id_separate_key(&$label_list, &$key_list, $selected_key, $advsearch='false')
{
	global $log;
    $log->debug("Entering get_select_options_with_id_separate_key(".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
    global $app_strings;
    if($advsearch=='true')
    $select_options = "\n<OPTION value=''>--NA--</OPTION>";
    else
    $select_options = "";

    $pattern = "/'0?'></";
    $replacement = "''>".$app_strings['LBL_NONE']."<";
    if (!is_array($selected_key)) $selected_key = array($selected_key);

    foreach ($key_list as $option_key=>$option_value) {
        $selected_string = '';
        if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
        {
            $selected_string = 'selected ';
        }

        $html_value = $option_key;

        $select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
    }
    $select_options = preg_replace($pattern, $replacement, $select_options);
    $log->debug("Exiting get_select_options_with_id_separate_key method ...");
    return $select_options;

}

/**
 * Converts localized date format string to jscalendar format
 * Example: $array = array_csort($array,'town','age',SORT_DESC,'name');
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function parse_calendardate($local_format) {
	global $log;
	$log->debug("Entering parse_calendardate(".$local_format.") method ...");
	global $current_user;
	if($current_user->date_format == 'dd-mm-yyyy')
	{
		$dt_popup_fmt = "%d-%m-%Y";
	}
	elseif($current_user->date_format == 'mm-dd-yyyy')
	{
		$dt_popup_fmt = "%m-%d-%Y";
	}
	elseif($current_user->date_format == 'yyyy-mm-dd')
	{
		$dt_popup_fmt = "%Y-%m-%d";
	}
	$log->debug("Exiting parse_calendardate method ...");
	return $dt_popup_fmt;
	//return "%Y-%m-%d";
}

/**
 * Decodes the given set of special character 
 * input values $string - string to be converted, $encode - flag to decode
 * returns the decoded value in string fromat
 */

function from_html($string, $encode=true){
	global $log;
	$log->debug("Entering from_html(".$string.",".$encode.") method ...");
        global $toHtml;
        //if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
        if($encode && is_string($string)){
                $string = str_replace(array_values($toHtml), array_keys($toHtml), $string);
        }
	$log->debug("Exiting from_html method ...");
        return $string;
}

/**
 *	Function used to decodes the given single quote and double quote only. This function used for popup selection 
 *	@param string $string - string to be converted, $encode - flag to decode
 *	@return string $string - the decoded value in string fromat where as only single and double quotes will be decoded
 */

function popup_from_html($string, $encode=true)
{
	global $log;
	$log->debug("Entering popup_from_html(".$string.",".$encode.") method ...");

	$popup_toHtml = array(
        			'"' => '&quot;',
			        "'" =>  '&#039;',
			     );

        //if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
        if($encode && is_string($string))
	{
                $string = addslashes(str_replace(array_values($popup_toHtml), array_keys($popup_toHtml), $string));
        }

	$log->debug("Exiting popup_from_html method ...");
        return $string;
}


/** To get the Currency of the specified user
  * @param $id -- The user Id:: Type integer
  * @returns  vtiger_currencyid :: Type integer
 */
function fetchCurrency($id)
{
	global $log;
	$log->debug("Entering fetchCurrency(".$id.") method ...");
        global $adb;
        $sql = "select currency_id from vtiger_users where id=" .$id;
        $result = $adb->query($sql);
        $currencyid=  $adb->query_result($result,0,"currency_id");
	$log->debug("Exiting fetchCurrency method ...");
        return $currencyid;
}

/** Function to get the Currency name from the vtiger_currency_info
  * @param $currencyid -- vtiger_currencyid:: Type integer
  * @returns $currencyname -- Currency Name:: Type varchar
  *
 */
function getCurrencyName($currencyid)
{
	global $log;
	$log->debug("Entering getCurrencyName(".$currencyid.") method ...");
        global $adb;
        $sql1 = "select * from vtiger_currency_info where id=".$currencyid;
        $result = $adb->query($sql1);
        $currencyname = $adb->query_result($result,0,"currency_name");
        $curr_symbol = $adb->query_result($result,0,"currency_symbol");
	$log->debug("Exiting getCurrencyName method ...");
        return $currencyname.' : '.$curr_symbol;
}


/**
 * Function to fetch the list of vtiger_groups from group vtiger_table 
 * Takes no value as input 
 * returns the query result set object
 */

function get_group_options()
{
	global $log;
	$log->debug("Entering get_group_options() method ...");
	global $adb,$noof_group_rows;;
	$sql = "select groupname,groupid from vtiger_groups";
	$result = $adb->query($sql);
	$noof_group_rows=$adb->num_rows($result);
	$log->debug("Exiting get_group_options method ...");
	return $result;
}

/**
 * Function to get the tabid 
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getTabid($module)
{
	global $log;
	$log->debug("Entering getTabid(".$module.") method ...");

	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) 
	{
		include('tabdata.php');
		$tabid= $tab_info_array[$module];
	}
	else
	{	

        $log->info("module  is ".$module);
        global $adb;
	$sql = "select tabid from vtiger_tab where name='".$module."'";
	$result = $adb->query($sql);
	$tabid=  $adb->query_result($result,0,"tabid");
	}
	$log->debug("Exiting getTabid method ...");
	return $tabid;

}

/**
 * Function to get the tabid 
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getSalesEntityType($crmid)
{
	global $log;
	$log->debug("Entering getSalesEntityType(".$crmid.") method ...");
	$log->info("in getSalesEntityType ".$crmid);
	global $adb;
	$sql = "select * from vtiger_crmentity where crmid=".$crmid;
        $result = $adb->query($sql);
	$parent_module = $adb->query_result($result,0,"setype");
	$log->debug("Exiting getSalesEntityType method ...");
	return $parent_module;
}

/**
 * Function to get the AccountName when a vtiger_account id is given 
 * Takes the input as $acount_id - vtiger_account id
 * returns the vtiger_account name in string format.
 */

function getAccountName($account_id)
{
	global $log;
	$log->debug("Entering getAccountName(".$account_id.") method ...");
	$log->info("in getAccountName ".$account_id);

	global $adb;
	if($account_id != '')
	{
		$sql = "select accountname from vtiger_account where accountid=".$account_id;
        	$result = $adb->query($sql);
		$accountname = $adb->query_result($result,0,"accountname");
	}
	$log->debug("Exiting getAccountName method ...");
	return $accountname;
}

/**
 * Function to get the ProductName when a product id is given 
 * Takes the input as $product_id - product id
 * returns the product name in string format.
 */

function getProductName($product_id)
{
	global $log;
	$log->debug("Entering getProductName(".$product_id.") method ...");

	$log->info("in getproductname ".$product_id);

	global $adb;
	$sql = "select productname from vtiger_products where productid=".$product_id;
        $result = $adb->query($sql);
	$productname = $adb->query_result($result,0,"productname");
	$log->debug("Exiting getProductName method ...");
	return $productname;
}

/**
 * Function to get the Potentail Name when a vtiger_potential id is given 
 * Takes the input as $potential_id - vtiger_potential id
 * returns the vtiger_potential name in string format.
 */

function getPotentialName($potential_id)
{
	global $log;
	$log->debug("Entering getPotentialName(".$potential_id.") method ...");
	$log->info("in getPotentialName ".$potential_id);

	global $adb;
	$potentialname = '';
	if($potential_id != '')
	{
		$sql = "select potentialname from vtiger_potential where potentialid=".$potential_id;
        	$result = $adb->query($sql);
		$potentialname = $adb->query_result($result,0,"potentialname");
	}
	$log->debug("Exiting getPotentialName method ...");
	return $potentialname;
}

/**
 * Function to get the Contact Name when a contact id is given 
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */

function getContactName($contact_id)
{
	global $log;
	$log->debug("Entering getContactName(".$contact_id.") method ...");
	$log->info("in getContactName ".$contact_id);

        global $adb;
        $sql = "select * from vtiger_contactdetails where contactid=".$contact_id;
        $result = $adb->query($sql);
        $firstname = $adb->query_result($result,0,"firstname");
        $lastname = $adb->query_result($result,0,"lastname");
        $contact_name = $lastname.' '.$firstname;
	$log->debug("Exiting getContactName method ...");
        return $contact_name;
}

/**
 * Function to get the Campaign Name when a campaign id is given
 * Takes the input as $campaign_id - campaign id
 * returns the Campaign Name in string format.
 */

function getCampaignName($campaign_id)
{
	global $log;
	$log->debug("Entering getCampaignName(".$campaign_id.") method ...");
	$log->info("in getCampaignName ".$campaign_id);

	global $adb;
	$sql = "select * from vtiger_campaign where campaignid=".$campaign_id;
	$result = $adb->query($sql);
	$campaign_name = $adb->query_result($result,0,"campaignname");
	$log->debug("Exiting getCampaignName method ...");
	return $campaign_name;
}


/**
 * Function to get the Vendor Name when a vtiger_vendor id is given 
 * Takes the input as $vendor_id - vtiger_vendor id
 * returns the Vendor Name in string format.
 */

function getVendorName($vendor_id)
{
	global $log;
	$log->debug("Entering getVendorName(".$vendor_id.") method ...");
	$log->info("in getVendorName ".$vendor_id);
        global $adb;
        $sql = "select * from vtiger_vendor where vendorid=".$vendor_id;
        $result = $adb->query($sql);
        $vendor_name = $adb->query_result($result,0,"vendorname");
	$log->debug("Exiting getVendorName method ...");
        return $vendor_name;
}

/**
 * Function to get the Quote Name when a vtiger_vendor id is given 
 * Takes the input as $quote_id - quote id
 * returns the Quote Name in string format.
 */

function getQuoteName($quote_id)
{
	global $log;
	$log->debug("Entering getQuoteName(".$quote_id.") method ...");
	$log->info("in getQuoteName ".$quote_id);
        global $adb;
	if($quote_id != NULL && $quote_id != '')
	{
        	$sql = "select * from vtiger_quotes where quoteid=".$quote_id;
        	$result = $adb->query($sql);
        	$quote_name = $adb->query_result($result,0,"subject");
	}
	else
	{
		$log->debug("Quote Id is empty.");
		$quote_name = '';
	}
	$log->debug("Exiting getQuoteName method ...");
        return $quote_name;
}

/**
 * Function to get the PriceBook Name when a vtiger_pricebook id is given 
 * Takes the input as $pricebook_id - vtiger_pricebook id
 * returns the PriceBook Name in string format.
 */

function getPriceBookName($pricebookid)
{
	global $log;
	$log->debug("Entering getPriceBookName(".$pricebookid.") method ...");
	$log->info("in getPriceBookName ".$pricebookid);
        global $adb;
        $sql = "select * from vtiger_pricebook where pricebookid=".$pricebookid;
        $result = $adb->query($sql);
        $pricebook_name = $adb->query_result($result,0,"bookname");
	$log->debug("Exiting getPriceBookName method ...");
        return $pricebook_name;
}

/** This Function returns the  Purchase Order Name.
  * The following is the input parameter for the function
  *  $po_id --> Purchase Order Id, Type:Integer
  */
function getPoName($po_id)
{
	global $log;
	$log->debug("Entering getPoName(".$po_id.") method ...");
        $log->info("in getPoName ".$po_id);
        global $adb;
        $sql = "select * from vtiger_purchaseorder where purchaseorderid=".$po_id;
        $result = $adb->query($sql);
        $po_name = $adb->query_result($result,0,"subject");
	$log->debug("Exiting getPoName method ...");
        return $po_name;
}
/**
 * Function to get the Sales Order Name when a vtiger_salesorder id is given 
 * Takes the input as $salesorder_id - vtiger_salesorder id
 * returns the Salesorder Name in string format.
 */

function getSoName($so_id)
{
	global $log;
	$log->debug("Entering getSoName(".$so_id.") method ...");
	$log->info("in getSoName ".$so_id);
	global $adb;
        $sql = "select * from vtiger_salesorder where salesorderid=".$so_id;
        $result = $adb->query($sql);
        $so_name = $adb->query_result($result,0,"subject");
	$log->debug("Exiting getSoName method ...");
        return $so_name;
}

/**
 * Function to get the Group Information for a given groupid  
 * Takes the input $id - group id and $module - module name
 * returns the group information in an array format.
 */

function getGroupName($id, $module)
{
	global $log;
	$log->debug("Entering getGroupName(".$id.",".$module.") method ...");
	$group_info = Array();
        $log->info("in getGroupName, entityid is ".$id.'  module is    '.$module);
        global $adb;
        if($module == 'Leads')
        {
               $sql = "select vtiger_leadgrouprelation.groupname,vtiger_groups.groupid from vtiger_leadgrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_leadgrouprelation.groupname where vtiger_leadgrouprelation.leadid=".$id;
        }
        elseif($module == 'Accounts')
        {
               $sql = "select vtiger_accountgrouprelation.groupname,vtiger_groups.groupid from vtiger_accountgrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_accountgrouprelation.groupname where vtiger_accountgrouprelation.accountid=".$id;
        }
        elseif($module == 'Contacts')
        {
               $sql = "select vtiger_contactgrouprelation.groupname,vtiger_groups.groupid from vtiger_contactgrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_contactgrouprelation.groupname where vtiger_contactgrouprelation.contactid=".$id;
        }
	elseif($module == 'Potentials')
        {
               $sql = "select vtiger_potentialgrouprelation.groupname,vtiger_groups.groupid from vtiger_potentialgrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_potentialgrouprelation.groupname where vtiger_potentialgrouprelation.potentialid=".$id;
        }
	elseif($module == 'Quotes')
        {
               $sql = "select vtiger_quotegrouprelation.groupname,vtiger_groups.groupid from vtiger_quotegrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_quotegrouprelation.groupname where vtiger_quotegrouprelation.quoteid=".$id;
        }
	elseif($module == 'SalesOrder')
        {
               $sql = "select vtiger_sogrouprelation.groupname,vtiger_groups.groupid from vtiger_sogrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_sogrouprelation.groupname where vtiger_sogrouprelation.salesorderid=".$id;
        }
	elseif($module == 'Invoice')
        {
               $sql = "select vtiger_invoicegrouprelation.groupname,vtiger_groups.groupid from vtiger_invoicegrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_invoicegrouprelation.groupname where vtiger_invoicegrouprelation.invoiceid=".$id;
        }
	elseif($module == 'PurchaseOrder')
        {
               $sql = "select vtiger_pogrouprelation.groupname,vtiger_groups.groupid from vtiger_pogrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_pogrouprelation.groupname where vtiger_pogrouprelation.purchaseorderid=".$id;
        }
        elseif($module == 'HelpDesk')
        {
               $sql = "select vtiger_ticketgrouprelation.groupname,vtiger_groups.groupid from vtiger_ticketgrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_ticketgrouprelation.groupname where vtiger_ticketgrouprelation.ticketid=".$id;
        }
	elseif($module == 'Campaigns')
	{
	       $sql = "select vtiger_campaigngrouprelation.groupname,vtiger_groups.groupid from vtiger_campaigngrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_campaigngrouprelation.groupname where vtiger_campaigngrouprelation.campaignid=".$id;
        }
        elseif($module == 'Calendar' || $module == 'Emails' || $module == 'Events')
        {
               $sql = "select vtiger_activitygrouprelation.groupname,vtiger_groups.groupid from vtiger_activitygrouprelation inner join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_activitygrouprelation.activityid=".$id;
	}
	$result = $adb->query($sql);
        $group_info[] = $adb->query_result($result,0,"groupname");
        $group_info[] = $adb->query_result($result,0,"groupid");
	$log->debug("Exiting getGroupName method ...");
        return $group_info;

}

/**
 * Get the username by giving the user id.   This method expects the user id
 */
     
function getUserName($userid)
{
	global $log;
	$log->debug("Entering getUserName(".$userid.") method ...");
	$log->info("in getUserName ".$userid);

	global $adb;
	if($userid != '')
	{
		$sql = "select user_name from vtiger_users where id=".$userid;
		$result = $adb->query($sql);
		$user_name = $adb->query_result($result,0,"user_name");
	}
	$log->debug("Exiting getUserName method ...");
	return $user_name;	
}

/**
* Get the user full name by giving the user id.   This method expects the user id
* DG 30 Aug 2006
*/

function getUserFullName($userid)
{
	global $log;
	$log->debug("Entering getUserFullName(".$userid.") method ...");
	$log->info("in getUserFullName ".$userid);
	global $adb;
	if($userid != '')
	{
		$sql = "select first_name, last_name from vtiger_users where id=".$userid;
		$result = $adb->query($sql);
		$first_name = $adb->query_result($result,0,"first_name");
		$last_name = $adb->query_result($result,0,"last_name");
		$user_name = $first_name." ".$last_name;
	}
        $log->debug("Exiting getUserFullName method ...");
        return $user_name;
}

/**
 * Creates and returns database query. To be used for search and other text links.   This method expects the module object.
 * param $focus - the module object contains the column vtiger_fields
 */
   
function getURLstring($focus)
{
	global $log;
	$log->debug("Entering getURLstring(".$focus.") method ...");
	$qry = "";
	foreach($focus->column_fields as $fldname=>$val)
	{
		if(isset($_REQUEST[$fldname]) && $_REQUEST[$fldname] != '')
		{
			if($qry == '')
			$qry = "&".$fldname."=".$_REQUEST[$fldname];
			else
			$qry .="&".$fldname."=".$_REQUEST[$fldname];
		}
	}
	if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] !='')
	{
		$qry .="&current_user_only=".$_REQUEST['current_user_only'];
	}
	if(isset($_REQUEST['advanced']) && $_REQUEST['advanced'] =='true')
	{
		$qry .="&advanced=true";
	}

	if($qry !='')
	{
		$qry .="&query=true";
	}
	$log->debug("Exiting getURLstring method ...");
	return $qry;

}

/** This function returns the date in user specified format.
  * param $cur_date_val - the default date format
 */
    
function getDisplayDate($cur_date_val)
{
	global $log;
	$log->debug("Entering getDisplayDate(".$cur_date_val.") method ...");
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '')
	{
		$dat_fmt = 'dd-mm-yyyy';
	}

		$date_value = explode(' ',$cur_date_val);
		list($y,$m,$d) = split('-',$date_value[0]);
		if($dat_fmt == 'dd-mm-yyyy')
		{
			$display_date = $d.'-'.$m.'-'.$y;
		}
		elseif($dat_fmt == 'mm-dd-yyyy')
		{

			$display_date = $m.'-'.$d.'-'.$y;
		}
		elseif($dat_fmt == 'yyyy-mm-dd')
		{

			$display_date = $y.'-'.$m.'-'.$d;
		}

		if($date_value[1] != '')
		{
			$display_date = $display_date.' '.$date_value[1];
		}
	$log->debug("Exiting getDisplayDate method ...");
	return $display_date;
 			
}

/** This function returns the date in user specified format.
  * Takes no param, receives the date format from current user object
  */
    
function getNewDisplayDate()
{
	global $log;
	$log->debug("Entering getNewDisplayDate() method ...");
        $log->info("in getNewDisplayDate ");

	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '')
        {
                $dat_fmt = 'dd-mm-yyyy';
        }
	//echo $dat_fmt;
	//echo '<BR>';
	$display_date='';
	if($dat_fmt == 'dd-mm-yyyy')
	{
		$display_date = date('d-m-Y');
	}
	elseif($dat_fmt == 'mm-dd-yyyy')
	{
		$display_date = date('m-d-Y');
	}
	elseif($dat_fmt == 'yyyy-mm-dd')
	{
		$display_date = date('Y-m-d');
	}
		
	//echo $display_date;
	$log->debug("Exiting getNewDisplayDate method ...");
	return $display_date;
}

/** This function returns the default vtiger_currency information.
  * Takes no param, return type array.
    */
    
function getDisplayCurrency()
{
	global $log;
	global $adb;
	$log->debug("Entering getDisplayCurrency() method ...");
        $curr_array = Array();
        $sql1 = "select * from vtiger_currency_info where currency_status='Active'";
        $result = $adb->query($sql1);
        $num_rows=$adb->num_rows($result);
        for($i=0; $i<$num_rows;$i++)
        {
                $curr_id = $adb->query_result($result,$i,"id");
                $curr_name = $adb->query_result($result,$i,"currency_name");
                $curr_symbol = $adb->query_result($result,$i,"currency_symbol");
                $curr_array[$curr_id] = $curr_name.' : '.$curr_symbol;
        }
	$log->debug("Exiting getDisplayCurrency method ...");
        return $curr_array;
}

/** This function returns the amount converted to dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
      
function convertToDollar($amount,$crate){
	global $log;
	$log->debug("Entering convertToDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertToDollar method ...");
        return $amount / $crate;
}

/** This function returns the amount converted from dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
function convertFromDollar($amount,$crate){
	global $log;
	$log->debug("Entering convertFromDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertFromDollar method ...");
        return $amount * $crate;
}

/** This function returns the amount converted from master currency.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
function convertFromMasterCurrency($amount,$crate){
	global $log;
	$log->debug("Entering convertFromDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertFromDollar method ...");
        return $amount * $crate;
}

/** This function returns the conversion rate and vtiger_currency symbol
  * in array format for a given id.
  * param $id - vtiger_currency id.
  */
      
function getCurrencySymbolandCRate($id)
{
	global $log;
	$log->debug("Entering getCurrencySymbolandCRate(".$id.") method ...");
        global $adb;
        $sql1 = "select conversion_rate,currency_symbol from vtiger_currency_info where id=".$id;
        $result = $adb->query($sql1);
	$rate_symbol['rate'] = $adb->query_result($result,0,"conversion_rate");
	$rate_symbol['symbol'] = $adb->query_result($result,0,"currency_symbol");
	$log->debug("Exiting getCurrencySymbolandCRate method ...");
	return $rate_symbol;
}

/** This function returns the terms and condition from the database.
  * Takes no param and the return type is text.
  */
	    
function getTermsandConditions()
{
	global $log;
	$log->debug("Entering getTermsandConditions() method ...");
        global $adb;
        $sql1 = "select * from vtiger_inventory_tandc";
        $result = $adb->query($sql1);
        $tandc = $adb->query_result($result,0,"tandc");
	$log->debug("Exiting getTermsandConditions method ...");
        return $tandc;
}

/**
 * Create select options in a dropdown list.  To be used inside
  *  a reminder select statement in a vtiger_activity form. 
   * param $start - start value
   * param $end - end value
   * param $fldname - vtiger_field name 
   * param $selvalue - selected value 
   */
    
function getReminderSelectOption($start,$end,$fldname,$selvalue='')
{
	global $log;
	$log->debug("Entering getReminderSelectOption(".$start.",".$end.",".$fldname.",".$selvalue=''.") method ...");
	global $mod_strings;
	global $app_strings;
	
	$def_sel ="";
	$OPTION_FLD = "<SELECT name=".$fldname.">";
	for($i=$start;$i<=$end;$i++)
	{
		if($i==$selvalue)
		$def_sel = "SELECTED";
		$OPTION_FLD .= "<OPTION VALUE=".$i." ".$def_sel.">".$i."</OPTION>\n";
		$def_sel = "";
	}
	$OPTION_FLD .="</SELECT>";
	$log->debug("Exiting getReminderSelectOption method ...");
	return $OPTION_FLD;
}

/** This function returns the List price of a given product in a given price book.
  * param $productid - product id.
  * param $pbid - vtiger_pricebook id.
  */
  
function getListPrice($productid,$pbid)
{
	global $log;
	$log->debug("Entering getListPrice(".$productid.",".$pbid.") method ...");
        $log->info("in getListPrice productid ".$productid);

	global $adb;
	$query = "select listprice from vtiger_pricebookproductrel where pricebookid=".$pbid." and productid=".$productid;
	$result = $adb->query($query);
	$lp = $adb->query_result($result,0,'listprice');
	$log->debug("Exiting getListPrice method ...");
	return $lp;
}

/** This function returns a string with removed new line character, single quote, and back slash double quoute.
  * param $str - string to be converted.
  */
      
function br2nl($str) {
   global $log;
   $log->debug("Entering br2nl(".$str.") method ...");
   $str = preg_replace("/(\r\n)/", " ", $str);
   $str = preg_replace("/'/", " ", $str);
   $str = preg_replace("/\"/", " ", $str);
   $log->debug("Exiting br2nl method ...");
   return $str;
}

/** This function returns a text, which escapes the html encode for link tag/ a href tag
*param $text - string/text
*/

function make_clickable($text)
{
   global $log;
   $log->debug("Entering make_clickable(".$text.") method ...");
   $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
   // pad it with a space so we can match things at the start of the 1st line.
   $ret = ' ' . $text;

   // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
   // xxxx can only be alpha characters.
   // yyyy is anything up to the first space, newline, comma, double quote or <
   $ret = preg_replace("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
   // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
   // zzzz is optional.. will contain everything up to the first space, newline,
   // comma, double quote or <.
   $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches an email@domain type address at the start of a line, or after a space.
   // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
   $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

   // Remove our padding..
   $ret = substr($ret, 1);

   //remove comma, fullstop at the end of url
   $ret = preg_replace("#,\"|\.\"|\)\"|\)\.\"|\.\)\"#", "\"", $ret);

   $log->debug("Exiting make_clickable method ...");
   return($ret);
}
/**
 * This function returns the vtiger_blocks and its related information for given module.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create),$mode - edit, $col_fields - * column vtiger_fields/
 * This function returns an array
 */

function getBlocks($module,$disp_view,$mode,$col_fields='',$info_type='')
{
	global $log;
	$log->debug("Entering getBlocks(".$module.",".$disp_view.",".$mode.",".$col_fields.",".$info_type.") method ...");
        global $adb,$current_user;
        global $mod_strings;
        $tabid = getTabid($module);
        $block_detail = Array();
        $getBlockinfo = "";
        $query="select blockid,blocklabel,show_title from vtiger_blocks where tabid=$tabid and $disp_view=0 and visible = 0 order by sequence";

        $result = $adb->query($query);
        $noofrows = $adb->num_rows($result);
        $prev_header = "";
	$blockid_list ='(';
	for($i=0; $i<$noofrows; $i++)
	{
		$blockid = $adb->query_result($result,$i,"blockid");
		if($i != 0)
			$blockid_list .= ', ';
		$blockid_list .= $blockid;
		$block_label[$blockid] = $adb->query_result($result,$i,"blocklabel");
	}
	$blockid_list .= ')';
	if($mode == 'edit')	
	{
		$display_type_check = 'vtiger_field.displaytype = 1';
	}else
	{
		$display_type_check = 'vtiger_field.displaytype in (1,4)';
	}
	//retreive the vtiger_profileList from database
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($disp_view == "detail_view")
	{
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users")
  		{
 			$sql = "SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN $blockid_list AND vtiger_field.displaytype IN (1,2,4) ORDER BY block,sequence";
  		}
  		else
  		{
  			$profileList = getCurrentUserProfileList();
 			$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list." AND vtiger_field.displaytype IN (1,2,4) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ".$profileList." GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
 			//Postgres 8 fixes
 			if( $adb->dbType == "pgsql")
 			    $sql = fixPostgresQuery( $sql, $log, 0);
  		}
		$result = $adb->query($sql);
		$getBlockInfo=getDetailBlockInformation($module,$result,$col_fields,$tabid,$block_label);
	}
	else
	{
		if ($info_type != '')
		{
			if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2]== 0 || $module == 'Users')
  			{
 				$sql = "SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list ." AND ".$display_type_check." AND info_type = '".$info_type."' ORDER BY block,sequence";
  			}
  			else
  			{
  				$profileList = getCurrentUserProfileList();
 				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list." AND ".$display_type_check." AND info_type = '".$info_type."' AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ".$profileList.=" GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
 				//Postgres 8 fixes
 				if( $adb->dbType == "pgsql")
 				    $sql = fixPostgresQuery( $sql, $log, 0);
  			}
		}
		else
		{
		if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == 'Users')
  			{
 				$sql = "SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list." AND ".$display_type_check." ORDER BY block,sequence";
  			}
  			else
  			{
  				$profileList = getCurrentUserProfileList();
 				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list." AND ".$display_type_check." AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ".$profileList.=" GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
 				//Postgres 8 fixes
 				if( $adb->dbType == "pgsql")
 				    $sql = fixPostgresQuery( $sql, $log, 0);
  			}	
		}
		$result = $adb->query($sql);
                $getBlockInfo=getBlockInformation($module,$result,$col_fields,$tabid,$block_label,$mode);	
	}
	$log->debug("Exiting getBlocks method ...");
	$index_count =1;
	$max_index =0;
	foreach($getBlockInfo as $label=>$contents)
	{
			$no_rows = count($contents);	
			$index_count = $max_index+1;
		foreach($contents as $block_row => $elements)
		{
			$max_index= $no_rows+$index_count;
			
			for($i=0;$i<count($elements);$i++)
			{	
				if(sizeof($getBlockInfo[$label][$block_row][$i])!=0)
				{
					if($i==0)
					$getBlockInfo[$label][$block_row][$i][]=array($index_count);
					else
					$getBlockInfo[$label][$block_row][$i][]=array($max_index);
				}
			}
			$index_count++;
			
		}
	}
	return $getBlockInfo;
}	
/**
 * This function is used to get the display type.
 * Takes the input parameter as $mode - edit  (mostly)
 * This returns string type value
 */

function getView($mode)
{
	global $log;
	$log->debug("Entering getView(".$mode.") method ...");
        if($mode=="edit")
	        $disp_view = "edit_view";
        else
	        $disp_view = "create_view";
	$log->debug("Exiting getView method ...");
        return $disp_view;
}
/**
 * This function is used to get the blockid of the customblock for a given module.
 * Takes the input parameter as $tabid - module tabid and $label - custom label
 * This returns string type value
 */

function getBlockId($tabid,$label)
{
	global $log;
	$log->debug("Entering getBlockId(".$tabid.",".$label.") method ...");
        global $adb;
        $blockid = '';
        $query = "select blockid from vtiger_blocks where tabid=$tabid and blocklabel = '$label'";
        $result = $adb->query($query);
        $noofrows = $adb->num_rows($result);
        if($noofrows == 1)
        {
                $blockid = $adb->query_result($result,0,"blockid");
        }
	$log->debug("Exiting getBlockId method ...");
        return $blockid;
}

/**
 * This function is used to get the Parent and Child vtiger_tab relation array.
 * Takes no parameter and get the data from parent_tabdata.php and vtiger_tabdata.php
 * This returns array type value
 */

function getHeaderArray()
{
	global $log;
	$log->debug("Entering getHeaderArray() method ...");
	global $adb;
	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	include('parent_tabdata.php');
	include('tabdata.php');
	$noofrows = count($parent_tab_info_array);
	foreach($parent_tab_info_array as $parid=>$parval)
	{
		$subtabs = Array();
		$tablist=$parent_child_tab_rel_array[$parid];
		$noofsubtabs = count($tablist);

		foreach($tablist as $childTabId)
		{
			$module = array_search($childTabId,$tab_info_array);
			
			if($is_admin)
			{
				$subtabs[] = $module;
			}	
			elseif($profileGlobalPermission[2]==0 ||$profileGlobalPermission[1]==0 || $profileTabsPermission[$childTabId]==0) 
			{
				$subtabs[] = $module;
			}	
		}

		$parenttab = getParentTabName($parid);
		if($parenttab == 'Settings' && $is_admin)
                {
                        $subtabs[] = 'Settings';
                }

		if($parenttab != 'Settings' ||($parenttab == 'Settings' && $is_admin))
		{
			if(!empty($subtabs))
				$relatedtabs[$parenttab] = $subtabs;
		}
	}
	$log->debug("Exiting getHeaderArray method ...");
	return $relatedtabs;
}

/**
 * This function is used to get the Parent Tab name for a given parent vtiger_tab id.
 * Takes the input parameter as $parenttabid - Parent vtiger_tab id
 * This returns value string type 
 */

function getParentTabName($parenttabid)
{
	global $log;
	$log->debug("Entering getParentTabName(".$parenttabid.") method ...");
	global $adb;
	if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
	{
		include('parent_tabdata.php');
		$parent_tabname= $parent_tab_info_array[$parenttabid];
	}
	else
	{
		$sql = "select parenttab_label from vtiger_parenttab where parenttabid=".$parenttabid;
		$result = $adb->query($sql);
		$parent_tabname=  $adb->query_result($result,0,"parenttab_label");
	}
	$log->debug("Exiting getParentTabName method ...");
	return $parent_tabname;
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes the input parameter as $module - module name
 * This returns value string type 
 */


function getParentTabFromModule($module)
{
	global $log;
	$log->debug("Entering getParentTabFromModule(".$module.") method ...");
	global $adb;
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0) && file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
	{
		include('tabdata.php');
		include('parent_tabdata.php');
		$tabid=$tab_info_array[$module];
		foreach($parent_child_tab_rel_array as $parid=>$childArr)
		{
			if(in_array($tabid,$childArr))
			{
				$parent_tabname= $parent_tab_info_array[$parid];
			}
		}
		$log->debug("Exiting getParentTabFromModule method ...");
		return $parent_tabname;
	}
	else
	{
		$sql = "select vtiger_parenttab.* from vtiger_parenttab inner join vtiger_parenttabrel on vtiger_parenttabrel.parenttabid=vtiger_parenttab.parenttabid inner join vtiger_tab on vtiger_tab.tabid=vtiger_parenttabrel.tabid where vtiger_tab.name='".$module."'";
		$result = $adb->query($sql);
		$tab =  $adb->query_result($result,0,"parenttab_label");
		$log->debug("Exiting getParentTabFromModule method ...");
		return $tab;
	}
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes no parameter but gets the vtiger_parenttab value from form request
 * This returns value string type 
 */

function getParentTab()
{
    global $log;	
    $log->debug("Entering getParentTab() method ...");
    if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] !='')
    {
     	       $log->debug("Exiting getParentTab method ...");
               return $_REQUEST['parenttab'];
    }
    else
    {
		$log->debug("Exiting getParentTab method ...");
                return getParentTabFromModule($_REQUEST['module']);
    }

}
/**
 * This function is used to get the days in between the current time and the modified time of an entity .
 * Takes the input parameter as $id - crmid  it will calculate the number of days in between the
 * the current time and the modified time from the vtiger_crmentity vtiger_table and return the result as a string.
 * The return format is updated <No of Days> day ago <(date when updated)>
 */

function updateInfo($id)
{
    global $log;
    $log->debug("Entering updateInfo(".$id.") method ...");

    global $adb;
    global $app_strings;
    $query='select modifiedtime from vtiger_crmentity where crmid ='.$id ;
    $result = $adb->query($query);
    $modifiedtime = $adb->query_result($result,0,'modifiedtime');
    $values=explode(' ',$modifiedtime);
    $date_info=explode('-',$values[0]);
    $time_info=explode(':',$values[1]);
    $date = $date_info[2].' '.$app_strings[date("M", mktime(0, 0, 0, $date_info[1], $date_info[2],$date_info[0]))].' '.$date_info[0];
    $time_modified = mktime($time_info[0], $time_info[1], $time_info[2], $date_info[1], $date_info[2],$date_info[0]);
    $time_now = time();
    $days_diff = (int)(($time_now - $time_modified) / (60 * 60 * 24));
    if($days_diff == 0)
        $update_info = $app_strings['LBL_UPDATED_TODAY']." (".$date.")";
    elseif($days_diff == 1)
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAY_AGO']." (".$date.")";
    else
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAYS_AGO']." (".$date.")";

    $log->debug("Exiting updateInfo method ...");
    return $update_info;
}


/**
 * This function is used to get the Product Images for the given Product  .
 * It accepts the product id as argument and returns the Images with the script for 
 * rotating the product Images
 */

function getProductImages($id)
{
	global $log;
	$log->debug("Entering getProductImages(".$id.") method ...");
	global $adb;
	$image_lists=array();
	$script_images=array();
	$script = '<script>var ProductImages = new Array(';
   	$i=0;
	$query='select imagename from vtiger_products where productid='.$id;
	$result = $adb->query($query);
	$imagename=$adb->query_result($result,0,'imagename');
	$image_lists=explode('###',$imagename);
	for($i=0;$i<count($image_lists);$i++)
	{
		$script_images[] = '"'.$image_lists[$i].'"';
	}
	$script .=implode(',',$script_images).');</script>';
	if($imagename != '')
	{
		$log->debug("Exiting getProductImages method ...");
		return $script;
	}
}	

/**
 * This function is used to save the Images .
 * It acceps the File lists,modulename,id and the mode as arguments  
 * It returns the array details of the upload
 */

function SaveImage($_FILES,$module,$id,$mode)
{
	global $log;
	$log->debug("Entering SaveImage(".$_FILES.",".$module.",".$id.",".$mode.") method ...");
	global $adb;
	$uploaddir = $root_directory."test/".$module."/" ;//set this to which location you need to give the contact image
	$log->info("The Location to Save the Contact Image is ".$uploaddir);
	$file_path_name = $_FILES['imagename']['name'];
	$image_error="false";
	$saveimage="true";
	$file_name = basename($file_path_name);
	if($file_name!="")
	{

		$log->debug("Contact Image is given for uploading");
		$image_name_val=file_exist_fn($file_name,0);

		$encode_field_values="";
		$errormessage="";

		$move_upload_status=move_uploaded_file($_FILES["imagename"]["tmp_name"],$uploaddir.$image_name_val);
		$image_error="false";

		//if there is an error in the uploading of image

		$filetype= $_FILES['imagename']['type'];
		$filesize = $_FILES['imagename']['size'];

		$filetype_array=explode("/",$filetype);

		$file_type_val_image=strtolower($filetype_array[0]);
		$file_type_val=strtolower($filetype_array[1]);
		$log->info("The File type of the Contact Image is :: ".$file_type_val);
		//checking the uploaded image is if an image type or not
		if(!$move_upload_status) //if any error during file uploading
		{
			$log->debug("Error is present in uploading Contact Image.");
			$errorCode =  $_FILES['imagename']['error'];
			if($errorCode == 4)
			{
				$errorcode="no-image";
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 2)
			{
				$errormessage = 2;
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 3 )
			{
				$errormessage = 3;
				$saveimage="false";
				$image_error="true";
			}
		}
		else
		{
			$log->debug("Successfully uploaded the Contact Image.");
			if($filesize != 0)
			{
				if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) || ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") || ($file_type_val == "gif") ) //Checking whether the file is an image or not
				{
					$saveimage="true";
					$image_error="false";
				}
				else
				{
					$savelogo="false";
					$image_error="true";
					$errormessage = "image";
				}
			}
			else
			{       
				$savelogo="false";
				$image_error="true";
				$errormessage = "invalid";
			}

		}
	}
	else //if image is not given
	{
		$log->debug("Contact Image is not given for uploading.");
		if($mode=="edit" && $image_error=="false" )
		{
			if($module='contact')
			$image_name_val=getContactImageName($id);
			elseif($module='user')
			$image_name_val=getUserImageName($id);
			$saveimage="true";
		}
		else
		{
			$image_name_val="";
		}
	}
	$return_value=array('imagename'=>$image_name_val,
	'imageerror'=>$image_error,
	'errormessage'=>$errormessage,
	'saveimage'=>$saveimage,
	'mode'=>$mode);
	$log->debug("Exiting SaveImage method ...");
	return $return_value;
}

 /**
 * This function is used to generate file name if more than one image with same name is added to a given Product.
 * Param $filename - product file name
 * Param $exist - number time the file name is repeated.
 */

function file_exist_fn($filename,$exist)
{
	global $log;
	$log->debug("Entering file_exist_fn(".$filename.",".$exist.") method ...");
	global $uploaddir;

	if(!isset($exist))
	{
		$exist=0;
	}
	$filename_path=$uploaddir.$filename;
	if (file_exists($filename_path)) //Checking if the file name already exists in the directory
	{
		if($exist!=0)
		{
			$previous=$exist-1;
			$next=$exist+1;
			$explode_name=explode("_",$filename);
			$implode_array=array();
			for($j=0;$j<count($explode_name); $j++)
			{
				if($j!=0)
				{
					$implode_array[]=$explode_name[$j];
				}
			}
			$implode_name=implode("_", $implode_array);
			$test_name=$implode_name;
		}
		else
		{
			$implode_name=$filename;
		}
		$exist++;
		$filename_val=$exist."_".$implode_name;
		$testfilename = file_exist_fn($filename_val,$exist);
		if($testfilename!="")
		{
			$log->debug("Exiting file_exist_fn method ...");
			return $testfilename;
		}
	}	
	else
	{
		$log->debug("Exiting file_exist_fn method ...");
		return $filename;
	}
}

/**
 * This function is used get the User Count.
 * It returns the array which has the total vtiger_users ,admin vtiger_users,and the non admin vtiger_users 
 */

function UserCount()
{
	global $log;
	$log->debug("Entering UserCount() method ...");
	global $adb;
	$result=$adb->query("select * from vtiger_users where deleted =0;");
	$user_count=$adb->num_rows($result);
	$result=$adb->query("select * from vtiger_users where deleted =0 AND is_admin != 'on';");
	$nonadmin_count = $adb->num_rows($result);
	$admin_count = $user_count-$nonadmin_count;
	$count=array('user'=>$user_count,'admin'=>$admin_count,'nonadmin'=>$nonadmin_count);
	$log->debug("Exiting UserCount method ...");
	return $count;
}

/**
 * This function is used to create folders recursively.
 * Param $dir - directory name
 * Param $mode - directory access mode
 * Param $recursive - create directory recursive, default true
 */

function mkdirs($dir, $mode = 0777, $recursive = true)
{
	global $log;
	$log->debug("Entering mkdirs(".$dir.",".$mode.",".$recursive.") method ...");
	if( is_null($dir) || $dir === "" ){
		$log->debug("Exiting mkdirs method ...");
		return FALSE;
	}
	if( is_dir($dir) || $dir === "/" ){
		$log->debug("Exiting mkdirs method ...");
		return TRUE;
	}
	if( mkdirs(dirname($dir), $mode, $recursive) ){
		$log->debug("Exiting mkdirs method ...");
		return mkdir($dir, $mode);
	}
	$log->debug("Exiting mkdirs method ...");
	return FALSE;
}

/**This function returns the module name which has been set as default home view for a given user.
 * Takes no parameter, but uses the user object $current_user.
 */
function DefHomeView()
{
		global $log;
		$log->debug("Entering DefHomeView() method ...");
		global $adb;
		global $current_user;
		$query="select defhomeview from vtiger_users where id = ".$current_user->id;
		$result=$adb->query($query);
		$defaultview=$adb->query_result($result,0,'defhomeview');
		$log->debug("Exiting DefHomeView method ...");
		return $defaultview;

}


/**
 * This function is used to set the Object values from the REQUEST values.
 * @param  object reference $focus - reference of the object
 */
function setObjectValuesFromRequest($focus)
{
	global $log;
	$log->debug("Entering setObjectValuesFromRequest(".$focus.") method ...");
	global $current_user;
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	if(isset($_REQUEST['record']))
	{
		$focus->id = $_REQUEST['record'];
	}
	if(isset($_REQUEST['mode']))
	{
		$focus->mode = $_REQUEST['mode'];
	}
	foreach($focus->column_fields as $fieldname => $val)
	{
		if(isset($_REQUEST[$fieldname]))
		{
			$value = $_REQUEST[$fieldname];
			$focus->column_fields[$fieldname] = $value;
		}
		if(isset($_REQUEST['txtTax']))
		{
			$value = convertToDollar($_REQUEST['txtTax'],$rate);
			$focus->column_fields['txtTax'] = $value;
		}
		if(isset($_REQUEST['txtAdjustment']))
		{
			$value = convertToDollar($_REQUEST['txtAdjustment'],$rate);
			$focus->column_fields['txtAdjustment'] = $value;
		}
		if(isset($_REQUEST['hdnSubTotal']))
		{
			$value = convertToDollar($_REQUEST['hdnSubTotal'],$rate);
			$focus->column_fields['hdnSubTotal'] = $value;
		}
		if(isset($_REQUEST['hdnGrandTotal']))
		{
			$value = convertToDollar($_REQUEST['hdnGrandTotal'],$rate);
			$focus->column_fields['hdnGrandTotal'] = $value;
		}

	}
	$log->debug("Exiting setObjectValuesFromRequest method ...");
}

 /**
 * Function to write the tabid and name to a flat file vtiger_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */

function create_tab_data_file()
{
	global $log;
	$log->debug("Entering create_tab_data_file() method ...");
        $log->info("creating vtiger_tabdata file");
        global $adb;
        $sql = "select * from vtiger_tab";
        $result = $adb->query($sql);
        $num_rows=$adb->num_rows($result);
        $result_array=Array();
	$seq_array=Array();
        for($i=0;$i<$num_rows;$i++)
        {
                $tabid=$adb->query_result($result,$i,'tabid');
                $tabname=$adb->query_result($result,$i,'name');
		$presence=$adb->query_result($result,$i,'presence');
                $result_array[$tabname]=$tabid;
		$seq_array[$tabid]=$presence;

        }

	//Constructing the actionname=>actionid array
	$actionid_array=Array();
	$sql1="select * from vtiger_actionmapping";
	$result1=$adb->query($sql1);
	$num_seq1=$adb->num_rows($result1);
	for($i=0;$i<$num_seq1;$i++)
	{
		$actionname=$adb->query_result($result1,$i,'actionname');
		$actionid=$adb->query_result($result1,$i,'actionid');
		$actionid_array[$actionname]=$actionid;
	}		

	//Constructing the actionid=>actionname array with securitycheck=0
	$actionname_array=Array();
	$sql2="select * from vtiger_actionmapping where securitycheck=0";
	$result2=$adb->query($sql2);
	$num_seq2=$adb->num_rows($result2);
	for($i=0;$i<$num_seq2;$i++)
	{
		$actionname=$adb->query_result($result2,$i,'actionname');
		$actionid=$adb->query_result($result2,$i,'actionid');
		$actionname_array[$actionid]=$actionname;
	}

        $filename = 'tabdata.php';
	
	
if (file_exists($filename)) {

        if (is_writable($filename))
        {

                if (!$handle = fopen($filename, 'w+')) {
                        echo "Cannot open file ($filename)";
                        exit;
                }
	require_once('modules/Users/CreateUserPrivilegeFile.php');
                $newbuf='';
                $newbuf .="<?php\n\n";
                $newbuf .="\n";
                $newbuf .= "//This file contains the commonly used variables \n";
                $newbuf .= "\n";
                $newbuf .= "\$tab_info_array=".constructArray($result_array).";\n";
                $newbuf .= "\n";
                $newbuf .= "\$tab_seq_array=".constructArray($seq_array).";\n";
		$newbuf .= "\n";
                $newbuf .= "\$action_id_array=".constructSingleStringKeyAndValueArray($actionid_array).";\n";
		$newbuf .= "\n";
                $newbuf .= "\$action_name_array=".constructSingleStringValueArray($actionname_array).";\n";
                $newbuf .= "?>";
                fputs($handle, $newbuf);
                fclose($handle);

        }
        else
        {
                echo "The file $filename is not writable";
        }

}
else
{
	echo "The file $filename does not exist";
	$log->debug("Exiting create_tab_data_file method ...");
	return;
}
}


 /**
 * Function to write the vtiger_parenttabid and name to a flat file parent_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */

function create_parenttab_data_file()
{
	global $log;
	$log->debug("Entering create_parenttab_data_file() method ...");
	$log->info("creating parent_tabdata file");
	global $adb;
	$sql = "select parenttabid,parenttab_label from vtiger_parenttab order by sequence";
	$result = $adb->query($sql);
	$num_rows=$adb->num_rows($result);
	$result_array=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$parenttabid=$adb->query_result($result,$i,'parenttabid');
		$parenttab_label=$adb->query_result($result,$i,'parenttab_label');
		$result_array[$parenttabid]=$parenttab_label;

	}

	$filename = 'parent_tabdata.php';


	if (file_exists($filename)) {

		if (is_writable($filename))
		{

			if (!$handle = fopen($filename, 'w+'))
			{
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			$newbuf='';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$parent_tab_info_array=".constructSingleStringValueArray($result_array).";\n";
			$newbuf .="\n";
			

			$parChildTabRelArray=Array();

			foreach($result_array as $parid=>$parvalue)
			{
				$childArray=Array();
				$sql = "select * from vtiger_parenttabrel where parenttabid=".$parid." order by sequence";
				$result = $adb->query($sql);
				$num_rows=$adb->num_rows($result);
				$result_array=Array();
				for($i=0;$i<$num_rows;$i++)
				{
					$tabid=$adb->query_result($result,$i,'tabid');
					$childArray[]=$tabid;
				}
				$parChildTabRelArray[$parid]=$childArray;

			}
			$newbuf .= "\n";
			$newbuf .= "\$parent_child_tab_rel_array=".constructTwoDimensionalValueArray($parChildTabRelArray).";\n";
			$newbuf .="\n";
			 $newbuf .="\n";
                        $newbuf .="\n";
                        $newbuf .= "?>";
                        fputs($handle, $newbuf);
                        fclose($handle);

		}
		else
		{
			echo "The file $filename is not writable";
		}

	}
	else
	{
		echo "The file $filename does not exist";
		$log->debug("Exiting create_parenttab_data_file method ...");
		return;
	}
}

/**
 * This function is used to get the all the modules that have Quick Create Feature.
 * Returns Tab Name and Tablabel.
 */

function getQuickCreateModules()
{
	global $log;
	$log->debug("Entering getQuickCreateModules() method ...");
         global $adb;
         global $mod_strings;


	$new_label=Array('Leads'=>'LNK_NEW_LEAD',
			 'Accounts'=>'LNK_NEW_ACCOUNT',
			 'Calendar'=>'LNK_NEW_TASK',
			 'Campaigns'=>'LNK_NEW_CAMPAIGN',
			 'Emails'=>'LNK_NEW_EMAIL',
			 'Events'=>'LNK_NEW_EVENT',
			 'HelpDesk'=>'LNK_NEW_HDESK',
			 'Notes'=>'LNK_NEW_NOTE',
			 'Potentials'=>'LNK_NEW_OPPORTUNITY',
			 'PriceBooks'=>'LNK_NEW_PRICEBOOK',
			 'Products'=>'LNK_NEW_PRODUCT',
			 'Contacts'=>'LNK_NEW_CONTACT',
			 'Vendors'=>'LNK_NEW_VENDOR'); 	

$qc_query = "select distinct vtiger_tab.tablabel,vtiger_tab.name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where quickcreate=0 order by vtiger_tab.tablabel";
$result = $adb->query($qc_query);
$noofrows = $adb->num_rows($result);
$return_qcmodule = Array();
for($i = 0; $i < $noofrows; $i++)
{
         $tablabel = $adb->query_result($result,$i,'tablabel');

         $tabname = $adb->query_result($result,$i,'name');
	 $tablabel = $new_label[$tabname];
	 if(isPermitted($tabname,'EditView','') == 'yes')
	 {
         	$return_qcmodule[] = $tablabel;
	        $return_qcmodule[] = $tabname;
	}	
}
	if(sizeof($return_qcmodule >0))
        {
        	$return_qcmodule = array_chunk($return_qcmodule,2);
	}
	
	$log->debug("Exiting getQuickCreateModules method ...");
        return $return_qcmodule;
}
																					   
/**
 * This function is used to get the Quick create form vtiger_field parameters for a given module.
 * Param $module - module name 
 * returns the value in array format
 */


function QuickCreate($module)
{
	global $log;
	$log->debug("Entering QuickCreate(".$module.") method ...");
    global $adb;
    global $current_user;
    global $mod_strings;

$tabid = getTabid($module);

//Adding Security Check
require('user_privileges/user_privileges_'.$current_user->id.'.php');
           if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
           {
                 $quickcreate_query = "select * from vtiger_field where quickcreate=0 and tabid = ".$tabid." order by quickcreatesequence";
           }
           else
           {
                $profileList = getCurrentUserProfileList();
 		$quickcreate_query = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=".$tabid." AND quickcreate=0 AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid IN ".$profileList." ORDER BY quickcreatesequence";
 		//Postgres 8 fixes
 		if( $adb->dbType == "pgsql")
 		    $quickcreate_query = fixPostgresQuery( $quickcreate_query, $log, 0); 
           }
																					     
$category = getParentTab();
$result = $adb->query($quickcreate_query);
$noofrows = $adb->num_rows($result);
$fieldName_array = Array();
for($i=0; $i<$noofrows; $i++)
{
      $fieldtablename = $adb->query_result($result,$i,'tablename');
      $uitype = $adb->query_result($result,$i,"uitype");
      $fieldname = $adb->query_result($result,$i,"fieldname");
      $fieldlabel = $adb->query_result($result,$i,"fieldlabel");
      $maxlength = $adb->query_result($result,$i,"maximumlength");
      $generatedtype = $adb->query_result($result,$i,"generatedtype");
      $typeofdata = $adb->query_result($result,$i,"typeofdata");

      //to get validationdata
      $fldLabel_array = Array();
      $fldLabel_array[$fieldlabel] = $typeofdata;
      $fieldName_array[$fieldname] = $fldLabel_array;
      $custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module);
      $qcreate_arr[]=$custfld;
}
for ($i=0,$j=0;$i<count($qcreate_arr);$i=$i+2,$j++)
{
       $key1=$qcreate_arr[$i];
       if(is_array($qcreate_arr[$i+1]))
       {
               $key2=$qcreate_arr[$i+1];
       }
       else
       {
                $key2 =array();
       }
                $return_data[$j]=array(0 => $key1,1 => $key2);
}
	$form_data['form'] = $return_data;
	$form_data['data'] = $fieldName_array;
	$log->debug("Exiting QuickCreate method ...");
	return $form_data;
}

/**	Function to send the Notification mail to the assigned to owner about the entity creation or updation
  *	@param string $module -- module name
  *	@param object $focus  -- reference of the object
**/
function sendNotificationToOwner($module,$focus)
{
	global $log;
	$log->debug("Entering sendNotificationToOwner(".$module.",".$focus.") method ...");
	require_once("modules/Emails/mail.php");
	global $current_user;

	$ownername = getUserName($focus->column_fields['assigned_user_id']);
	$ownermailid = getUserEmailId('id',$focus->column_fields['assigned_user_id']);

	if($module == 'Contacts')
	{
		$objectname = $focus->column_fields['lastname'].' '.$focus->column_fields['firstname'];
		$mod_name = 'Contact';
		$object_column_fields = array(
						'lastname'=>'Last Name',
						'firstname'=>'First Name',
						'leadsource'=>'Lead Source',
						'department'=>'Department',
						'description'=>'Description',
					     );
	}
	if($module == 'Accounts')
	{
		$objectname = $focus->column_fields['accountname'];
		$mod_name = 'Account';
		$object_column_fields = array(
						'accountname'=>'Account Name',
						'rating'=>'Rating',
						'industry'=>'Industry',
						'accounttype'=>'Account Type',
						'description'=>'Description',
					     );
	}
	if($module == 'Potentials')
	{
		$objectname = $focus->column_fields['potentialname'];
		$mod_name = 'Potential';
		$object_column_fields = array(
						'potentialname'=>'Potential Name',
						'amount'=>'Amount',
						'closingdate'=>'Expected Close Date',
						'opportunity_type'=>'Opportunity Type',
						'description'=>'Description',
			      		     );
	}	
	
	$description = 'Dear '.$ownername.',<br><br>';

	if($focus->mode == 'edit')
	{
		$subject = 'Regarding '.$mod_name.' updation - '.$objectname;
		$description .= 'The '.$mod_name.' has been updated.';
	}
	else
	{
		$subject = 'Regarding '.$mod_name.' assignment - '.$objectname;
		$description .= 'The '.$mod_name.' has been assigned to you.';
	}
	$description .= '<br>The '.$mod_name.' details are:<br><br>';
	$description .= $mod_name.' Id : '.$focus->id.'<br>';

	foreach($object_column_fields as $fieldname => $fieldlabel)
	{
		//Get the translated string
		$temp_label = isset($app_strings[$fieldlabel])?$app_strings[$fieldlabel]:(isset($mod_strings[$fieldlabel])?$mod_strings[$fieldlabel]:$fieldlabel);

		$description .= $temp_label.' : <b>'.$focus->column_fields[$fieldname].'</b><br>';
	}

	$description .= '<br><br>Thank You <br>';
	$status = send_mail($module,$ownermailid,$current_user->user_name,'',$subject,$description);

	$log->debug("Exiting sendNotificationToOwner method ...");
	return $status;
}
function getUserslist()
{
	global $log;
	$log->debug("Entering getUserslist() method ...");
	global $adb;
	$result=$adb->query("select * from vtiger_users");
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
	       $useridlist[$i]=$adb->query_result($result,$i,'id');
	       $usernamelist[$useridlist[$i]]=$adb->query_result($result,$i,'user_name');
	}
	$change_owner = get_select_options_with_id($usernamelist,'admin');
	$log->debug("Exiting getUserslist method ...");
	return $change_owner;
}


function getGroupslist()
{
	global $log;
	$log->debug("Entering getGroupslist() method ...");
	global $adb;
	$result=$adb->query("select * from vtiger_groups");
	
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
	       $groupidlist[$i]=$adb->query_result($result,$i,'groupid');
	       $groupnamelist[$groupidlist[$i]]=$adb->query_result($result,$i,'groupname');
	       
	}
	$change_groups_owner = get_select_options_with_id($groupnamelist,'');
	$log->debug("Exiting getGroupslist method ...");

	return $change_groups_owner;
}


/**
  *	Function to Check for Security whether the Buttons are permitted in List/Edit/Detail View of all Modules
  *	@param string $module -- module name
  *	Returns an array with permission as Yes or No
**/
function Button_Check($module)
{
	global $log;
	$log->debug("Entering Button_Check(".$module.") method ...");
        $permit_arr = array ('EditView' => '',
                             'index' => '',
                             'Import' => '',
                             'Export' => '' );

          foreach($permit_arr as $action => $perr)
          {
                 $tempPer=isPermitted($module,$action,'');
                 $permit_arr[$action] = $tempPer;
          }
	  $permit_arr["Calendar"] = isPermitted("Calendar","index",'');

	$log->debug("Exiting Button_Check method ...");
	  return $permit_arr;

}

/**
  *	Function to Check whether the User is allowed to delete a particular record from listview of each module using   
  *	mass delete button.
  *	@param string $module -- module name
  *	@param array $ids_list -- Record id 
  *	Returns the Record Names of each module that is not permitted to delete
**/
function getEntityName($module, $ids_list)
{
	$list = implode(",",$ids_list);
	global $adb;
	global $log;
	$log->debug("Entering getEntityName(".$module.") method ...");
		
	if($module != '')
	{
		 $query = "select fieldname,tablename,entityidfield from vtiger_entityname where modulename = '$module'";
		 $result = $adb->query($query);
		 $fieldsname = $adb->query_result($result,0,'fieldname');
		 $tablename = $adb->query_result($result,0,'tablename'); 
		 $entityidfield = $adb->query_result($result,0,'entityidfield'); 
		 if(!(strpos($fieldsname,',') === false))
		 {
			 $fieldlists = explode(',',$fieldsname);
			 $fieldsname = "concat(";
			 $fieldsname = $fieldsname.implode(",' ',",$fieldlists);
			 $fieldsname = $fieldsname.")";
		 }	
		 $query1 = "select $fieldsname as entityname from $tablename where $entityidfield in (".$list.")"; 
		 $result = $adb->query($query1);
		 $numrows = $adb->num_rows($result);
	  	 $account_name = array();
		 for ($i = 0; $i < $numrows; $i++)
		 {
			$entity_id = $ids_list[$i];
			$entity_info[$entity_id] = $adb->query_result($result,$i,'entityname');
		 }
		 return $entity_info;
	}
	$log->debug("Exiting getEntityName method ...");
}

/**Function to get all permitted modules for a user with their parent
*/

function getAllParenttabmoduleslist()
{
        global $adb;
	global $current_user;
        $resultant_array = Array();
        $query = 'select name,tablabel,parenttab_label,vtiger_tab.tabid from vtiger_parenttabrel inner join vtiger_tab on vtiger_parenttabrel.tabid = vtiger_tab.tabid inner join vtiger_parenttab on vtiger_parenttabrel.parenttabid = vtiger_parenttab.parenttabid order by vtiger_parenttab.sequence, vtiger_parenttabrel.sequence';
        $result = $adb->query($query);
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
        for($i=0;$i<$adb->num_rows($result);$i++)
        {
                $parenttabname = $adb->query_result($result,$i,'parenttab_label');
                $modulename = $adb->query_result($result,$i,'name');
                $tablabel = $adb->query_result($result,$i,'tablabel');
		$tabid = $adb->query_result($result,$i,'tabid');
		if($is_admin)
		{
			$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
		}	
		elseif($profileGlobalPermission[2]==0 || $profileGlobalPermission[1]==0 || $profileTabsPermission[$tabid]==0)		     {
                	$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
		}
        }
	
	if($is_admin)
	{
               	$resultant_array['Settings'][] = Array('Settings','Settings');
	}			

	        return $resultant_array;
}

/**
 * 	This function is used to decide the File Storage Path in where we will upload the file in the server.
 * 	return string $filepath  - filepath inwhere the file should be stored in the server will be return
*/
function decideFilePath()
{
	global $log, $adb;
	$log->debug("Entering into decideFilePath() method ...");

	$filepath = 'storage/';

	$year  = date('Y');
	$month = date('F');
	$day  = date('j');
	$week   = '';

	if(!is_dir($filepath.$year))
	{
		//create new folder
		mkdir($filepath.$year);
	}

	if(!is_dir($filepath.$year."/".$month))
	{
		//create new folder
		mkdir($filepath."$year/$month");
	}

	if($day > 0 && $day <= 7)
		$week = 'week1';
	elseif($day > 7 && $day <= 14)
		$week = 'week2';
	elseif($day > 14 && $day <= 21)
		$week = 'week3';
	elseif($day > 21 && $day <= 28 )
		$week = 'week4';
	else
		$week = 'week5';

	if(!is_dir($filepath.$year."/".$month."/".$week))
	{
		//create new folder
		mkdir($filepath."$year/$month/$week");
	}

	$filepath = $filepath.$year."/".$month."/".$week."/";

	$log->debug("Year=$year & Month=$month & week=$week && filepath=\"$filepath\"");
	$log->debug("Exiting from decideFilePath() method ...");
	
	return $filepath;
}

/**
 * 	This function is used to get the Path in where we store the vtiger_files based on the module.
 *	@param string $module   - module name
 * 	return string $storage_path - path inwhere the file will be uploaded (also where it was stored) will be return based on the module
*/
function getModuleFileStoragePath($module)
{
	global $log;
	$log->debug("Entering into getModuleFileStoragePath($module) method ...");
	
	$storage_path = "test/";

	if($module == 'Products')
	{
		$storage_path .= 'product/';
	}
	if($module == 'Contacts')
	{
		$storage_path .= 'contact/';
	}

	$log->debug("Exiting from getModuleFileStoragePath($module) method. return storage_path = \"$storage_path\"");
	return $storage_path;
}

/**
 * 	This function is used to check whether the attached file is a image file or not
 *	@param string $file_details  - vtiger_files array which contains all the uploaded file details
 * 	return string $save_image - true or false. if the image can be uploaded then true will return otherwise false.
*/
function validateImageFile($file_details)
{
	global $adb, $log;
	$log->debug("Entering into validateImageFile($file_details) method.");
	
	$savefile = 'true';
	$file_type_details = explode("/",$file_details['type']);
	$filetype = $file_type_details['1'];

	if (($filetype == "jpeg" ) || ($filetype == "png") || ($filetype == "jpg" ) || ($filetype == "pjpeg" ) || ($filetype == "x-png") || ($filetype == "gif") )
	{
		$saveimage = 'true';
	}
	else
	{
		$saveimage = 'false';
		$_SESSION['image_type_error'] .= "<br> &nbsp;&nbsp;<b>$file_details[name]</b> is not uploaded. Allowed file types - jpeg, png, jpg, pjpeg, x-png or gif.";
		$log->debug("Invalid Image type == $filetype");
	}

	$log->debug("Exiting from validateImageFile($file_details) method. return saveimage=$saveimage");
	return $saveimage;
}

/**
 * 	This function is used to get the Email Template Details like subject and content for particular template. 
 *	@param integer $templateid  - Template Id for an Email Template
 * 	return array $returndata - Returns Subject, Body of Template of the the particular email template.
*/

function getTemplateDetails($templateid)
{
        global $adb,$log;
        $log->debug("Entering into getTemplateDetails($templateid) method ...");
        $returndata =  Array();
        $result = $adb->query("select * from vtiger_emailtemplates where templateid=$templateid");
        $returndata[] = $templateid;
        $returndata[] = $adb->query_result($result,0,'body');
        $returndata[] = $adb->query_result($result,0,'subject');
        $log->debug("Exiting from getTemplateDetails($templateid) method ...");
        return $returndata;
}
/**
 * 	This function is used to merge the Template Details with the email description  
 *  @param string $description  -body of the mail(ie template)
 *	@param integer $tid  - Id of the entity
 *  @param string $parent_type - module of the entity
 * 	return string $description - Returns description, merged with the input template.
*/
									
function getMergedDescription($description,$id,$parent_type)
{
	global $adb,$log;
    $log->debug("Entering getMergedDescription ...");
	$token_data_pair = explode('$',$description);
	$fields = Array();
	for($i=1;$i < count($token_data_pair);$i+=2)
	{

		$module = explode('-',$token_data_pair[$i]);
		$fields[$module[0]][] = $module[1];
		
	}
	//replace the tokens with the values for the selected parent
	switch($parent_type)
	{
		case 'Accounts':
			if(is_array($fields["accounts"]))
			{
				$columnfields = implode(',',$fields["accounts"]);
				$query = 'select '.$columnfields.' from vtiger_account where accountid ='.$id;
				$result = $adb->query($query);
				foreach($fields["accounts"] as $columnname)			
				{
					$token_data = '$accounts-'.$columnname.'$';
					$description = str_replace($token_data,$adb->query_result($result,0,$columnname),$description);
				}
			}
			break;
		case 'Contacts':
			if(is_array($fields["contacts"]))
			{
				//Checking for salutation type and checking the table column to be queried
				$key = array_search('salutationtype',$fields["contacts"]);
				if(isset($key) && $key !='')
				{
					$fields["contacts"][$key]='salutation';
				}	
				
				$columnfields = implode(',',$fields["contacts"]);
				$query = 'select '.$columnfields.' from vtiger_contactdetails where contactid='.$id;
				$result = $adb->query($query);
				foreach($fields["contacts"] as $columnname)
				{
					$token_data = '$contacts-'.$columnname.'$';
					$description = str_replace($token_data,$adb->query_result($result,0,$columnname),$description);
				}
			}
			break;
		case 'Leads':	
			if(is_array($fields["leads"]))
			{
				//Checking for salutation type and checking the table column to be queried
				$key = array_search('salutationtype',$fields["contacts"]);
				if(isset($key) && $key !='')
				{
					$fields["contacts"][$key]='salutation';
				}
				$columnfields = implode(',',$fields["leads"]);
				$query = 'select '.$columnfields.' from vtiger_leaddetails where leadid='.$id;
				$result = $adb->query($query);
				foreach($fields["leads"] as $columnname)
				{
					$token_data = '$leads-'.$columnname.'$';
					$description = str_replace($token_data,$adb->query_result($result,0,$columnname),$description);
				}
			}
			break;
		case 'Users':	
			if(is_array($fields["users"]))
			{
				$columnfields = implode(',',$fields["users"]);
				$query = 'select '.$columnfields.' from vtiger_users where id='.$id;
				$result = $adb->query($query);
				foreach($fields["users"] as $columnname)
				{
					$token_data = '$users-'.$columnname.'$';
					$description = str_replace($token_data,$adb->query_result($result,0,$columnname),$description);
				}
			}
			break;
	}
	//replace the unwanted tokens by null
	$token_data_pair = explode('$',$description);
    for($i=1;$i < count($token_data_pair);$i+=2)
	{
		$token_data = '$'.$token_data_pair[$i].'$';
		$description = str_replace($token_data,'',$description);
	}
    $log->debug("Exiting from getMergedDescription ...");
	return $description;
}

/**	Function used to retrieve a single field value from database
 *	@param string $tablename - tablename from which we will retrieve the field value
 *	@param string $fieldname - fieldname to which we want to get the value from database
 *	@param string $idname	 - idname which is the name of the entity id in the table like, inoviceid, quoteid, etc.,
 *	@param int    $id	 - entity id
 *	return string $fieldval  - field value of the needed fieldname from database will be returned
 */
function getSingleFieldValue($tablename, $fieldname, $idname, $id)
{
	global $log, $adb;
	$log->debug("Entering into function getSingleFieldValue($tablename, $fieldname, $idname, $id)");

	$fieldval = $adb->query_result($adb->query("select $fieldname from $tablename where $idname = $id"),0,$fieldname);

	$log->debug("Exit from function getSingleFieldValue. return value ==> \"$fieldval\"");

	return $fieldval;
}

/**	Function used to retrieve the announcements from database
 *	The function accepts no argument and returns the announcements
 *	return string $announcement  - List of announments for the CRM users 
 */

function get_announcements()
{
	global $adb;
	$sql="select * from vtiger_announcement order by time";
	$result=$adb->query($sql);
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
		$announce = getUserName($adb->query_result($result,$i,'creatorid')).' :  '.$adb->query_result($result,$i,'announcement').'   ';
		if($adb->query_result($result,$i,'announcement')!='')
			$announcement.=$announce;
	}
	return $announcement;
}

/**	Function used to retrieve the rate converted into dollar tobe saved into database
 *	The function accepts the price in the current currency
 *	return integer $conv_price  - 
 */
 function getConvertedPrice($price) 
 {
	 global $current_user;
	 $currencyid=fetchCurrency($current_user->id);
	 $rate_symbol = getCurrencySymbolandCRate($currencyid);
	 $conv_price = convertToDollar($price,$rate_symbol['rate']);
	 return $conv_price;
 }


/**	Function used to get the converted amount from dollar which will be showed to the user
 *	@param float $price - amount in dollor which we want to convert to the user configured amount
 *	@return float $conv_price  - amount in user configured currency
 */
function getConvertedPriceFromDollar($price) 
{
	global $current_user;
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$conv_price = convertFromDollar($price,$rate_symbol['rate']);
	return $conv_price;
}


/**
 *  Function to get recurring info depending on the recurring type
 *  return  $recurObj       - Object of class RecurringType
 */
 
function getrecurringObjValue()
{
	$recurring_data = array();
	if(isset($_REQUEST['recurringtype']) && $_REQUEST['recurringtype'] != null &&  $_REQUEST['recurringtype'] != '--None--' )
	{
		if(isset($_REQUEST['date_start']) && $_REQUEST['date_start'] != null)
		{
			$recurring_data['startdate'] = $_REQUEST['date_start'];
		}
		if(isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != null)
		{
			$recurring_data['enddate'] = $_REQUEST['due_date'];
		}
		$recurring_data['type'] = $_REQUEST['recurringtype'];
		if($_REQUEST['recurringtype'] == 'Weekly')
		{
			if(isset($_REQUEST['sun_flag']) && $_REQUEST['sun_flag'] != null)
				$recurring_data['sun_flag'] = true;
			if(isset($_REQUEST['mon_flag']) && $_REQUEST['mon_flag'] != null)
				$recurring_data['mon_flag'] = true;
			if(isset($_REQUEST['tue_flag']) && $_REQUEST['tue_flag'] != null)
				$recurring_data['tue_flag'] = true;
			if(isset($_REQUEST['wed_flag']) && $_REQUEST['wed_flag'] != null)
				$recurring_data['wed_flag'] = true;
			if(isset($_REQUEST['thu_flag']) && $_REQUEST['thu_flag'] != null)
				$recurring_data['thu_flag'] = true;
			if(isset($_REQUEST['fri_flag']) && $_REQUEST['fri_flag'] != null)
				$recurring_data['fri_flag'] = true;
			if(isset($_REQUEST['sat_flag']) && $_REQUEST['sat_flag'] != null)
				$recurring_data['sat_flag'] = true;
		}
		elseif($_REQUEST['recurringtype'] == 'Monthly')
		{
			if(isset($_REQUEST['repeatMonth']) && $_REQUEST['repeatMonth'] != null)
				$recurring_data['repeatmonth_type'] = $_REQUEST['repeatMonth'];
			if($recurring_data['repeatmonth_type'] == 'date')
			{
				if(isset($_REQUEST['repeatMonth_date']) && $_REQUEST['repeatMonth_date'] != null)
					$recurring_data['repeatmonth_date'] = $_REQUEST['repeatMonth_date'];
				else
					$recurring_data['repeatmonth_date'] = 1;
			}
			elseif($recurring_data['repeatmonth_type'] == 'day')
			{
				$recurring_data['repeatmonth_daytype'] = $_REQUEST['repeatMonth_daytype'];
				switch($_REQUEST['repeatMonth_day'])
				{
					case 0 :
						$recurring_data['sun_flag'] = true;
						break;
					case 1 :
						$recurring_data['mon_flag'] = true;
						break;
					case 2 :
						$recurring_data['tue_flag'] = true;
						break;
					case 3 :
						$recurring_data['wed_flag'] = true;
						break;
					case 4 :
						$recurring_data['thu_flag'] = true;
						break;
					case 5 :
						$recurring_data['fri_flag'] = true;
						break;
					case 6 :
						$recurring_data['sat_flag'] = true;
						break;
				}
			}
		}
		if(isset($_REQUEST['repeat_frequency']) && $_REQUEST['repeat_frequency'] != null)
			$recurring_data['repeat_frequency'] = $_REQUEST['repeat_frequency'];
		//echo '<pre>';print_r($recurring_data);echo '</pre>';die;
		$recurObj = new RecurringType($recurring_data);
		//echo '<pre>';print_r($recurObj);echo '</pre>';die;
		return $recurObj;
	}
	
}
?>
