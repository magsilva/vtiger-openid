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
?>
<html>
<body>
<script>
if (document.layers || (!document.all && document.getElementById))
{
	document.write("This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.");
	document.write("<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page");
}
else if(document.all)
{
	document.write("<OBJECT Name='vtigerCRM' codebase='modules/Settings/vtigerCRM.CAB#version=1,5,0,0' id='objMMPage' classid='clsid:0FC436C2-2E62-46EF-A3FB-E68E94705126' width=0 height=0></object>");
}
</script>
<?php

require_once('include/database/PearDatabase.php');
require_once('config.php');

$templateid = $_REQUEST['mergefile'];

if($templateid == "")
{
	die("Select Mail Merge Template");
}
//get the particular file from db and store it in the local hard disk.
//store the path to the location where the file is stored and pass it  as parameter to the method 
$sql = "select filename,data,filesize from vtiger_wordtemplates where templateid=".$templateid;

$result = $adb->query($sql);
$temparray = $adb->fetch_array($result);

$fileContent = $temparray['data'];
$filename=$temparray['filename'];
$filesize=$temparray['filesize'];
$wordtemplatedownloadpath =$root_directory ."/test/wordtemplatedownload/";

$handle = fopen($wordtemplatedownloadpath .$temparray['filename'],"wb");
fwrite($handle,base64_decode($fileContent),$filesize);
fclose($handle);

//<<<<<<<<<<<<<<<<<<<<<<<<<<<for mass merge>>>>>>>>>>>>>>>>>>>>>>>>>>>
$mass_merge = $_REQUEST['idlist'];
$single_record = $_REQUEST['record'];

if($mass_merge != "")
{	
	$mass_merge = explode(";",$mass_merge);
	$temp_mass_merge = $mass_merge;
	if(array_pop($temp_mass_merge)=="")
		array_pop($mass_merge);
	$mass_merge = implode(",",$mass_merge);
}
else if($single_record != "")
{
	$mass_merge = $single_record;	
}
else
{
	die("Record Id is not found, cannot merge the document");
}

//<<<<<<<<<<<<<<<<header for csv and select columns for query>>>>>>>>>>>>>>>>>>>>>>>>
$query1="select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (13,4,6) and vtiger_field.uitype <> 61 and (vtiger_field.tablename <>'CustomerDetails' and block <> 6 and block <> 75) and block <> 30 order by vtiger_field.tablename";

$result = $adb->query($query1);
$y=$adb->num_rows($result);
	
for ($x=0; $x<$y; $x++)
{ 
	$tablename = $adb->query_result($result,$x,"tablename");
	$columnname = $adb->query_result($result,$x,"columnname");
	$modulename = $adb->query_result($result,$x,"name");

	$column_name = $tablename.".".$columnname;

	if($columnname == "parent_id")
	{
		$column_name = "case crmentityRelHelpDesk.setype when 'Accounts' then accountRelHelpDesk.accountname when 'Contacts' then concat(contactdetailsRelHelpDesk.firstname,' ',contactdetailsRelHelpDesk.lastname) End";
	}
	if($columnname == "product_id")
	{
		$column_name = "productsRel.productname";
	}
	if($tablename == "vtiger_crmentity")
	{
		if($modulename == "Contacts")
		{
			$tablename = "crmentityContacts";
			$column_name = $tablename.".".$columnname;
		}
		if($modulename == "Accounts")
		{
			$tablename = "crmentityAccounts";
			$column_name = $tablename.".".$columnname;
		}

	}

	if($columnname == "smownerid")
	{
		if($modulename == "Accounts")
		{
			$column_name = "concat(usersAccounts.last_name,' ',usersAccounts.first_name) as username";
		}
		if($modulename == "Contacts")
		{
			$column_name = "concat(usersContacts.last_name,' ',usersContacts.first_name) as usercname";
		}
		if($modulename == "HelpDesk")
		{
			$column_name = "concat(vtiger_users.last_name,' ',vtiger_users.first_name) as userhelpname,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.yahoo_id,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
		}
	}
	if($columnname == "parentid")
	{
		$column_name = "accountAccount.accountname";
	}
	if($columnname == "accountid")
	{
		$column_name = "accountContacts.accountname";
	}
	if($columnname == "reportsto")
	{
		$column_name = "contactdetailsContacts.lastname";
	}

	$querycolumns[$x] = $column_name;

	if($modulename == "Accounts")
	{
		$field_label[$x] = "ACCOUNT_".strtoupper(str_replace(" ","",$adb->query_result($result,$x,"fieldlabel")));
	}
	if($modulename == "Contacts")
	{
		$field_label[$x] = "CONTACT_".strtoupper(str_replace(" ","",$adb->query_result($result,$x,"fieldlabel")));
	}
	if($modulename == "HelpDesk")
	{
		$field_label[$x] = "TICKET_".strtoupper(str_replace(" ","",$adb->query_result($result,$x,"fieldlabel")));
		if($columnname == "smownerid")
		{
			$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_YAHOOID,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
		}
	}

}
$csvheader = implode(",",$field_label);
//<<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>

if(count($querycolumns) > 0)
{
	$selectcolumns = implode($querycolumns,",");

	$query ="select ".$selectcolumns." from vtiger_troubletickets
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
			inner join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
			left join vtiger_crmentity as crmentityRelHelpDesk on crmentityRelHelpDesk.crmid = vtiger_troubletickets.parent_id
			left join vtiger_account as accountRelHelpDesk on accountRelHelpDesk.accountid=crmentityRelHelpDesk.crmid
			left join vtiger_contactdetails as contactdetailsRelHelpDesk on contactdetailsRelHelpDesk.contactid= crmentityRelHelpDesk.crmid
			left join vtiger_products as productsRel on productsRel.productid = vtiger_troubletickets.product_id
			left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
			left join vtiger_account on vtiger_account.accountid = vtiger_troubletickets.parent_id               
			left join vtiger_crmentity as crmentityAccounts on crmentityAccounts.crmid = vtiger_account.accountid 
			left join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid
			left join vtiger_accountshipads on vtiger_accountshipads.accountaddressid = vtiger_account.accountid
			left join vtiger_accountscf on vtiger_accountbillads.accountaddressid = vtiger_accountscf.accountid 
			left join vtiger_account as accountAccount on accountAccount.accountid = vtiger_troubletickets.parent_id
			left join vtiger_users as usersAccounts on usersAccounts.id = crmentityAccounts.smownerid
			left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id               
			left join vtiger_crmentity as crmentityContacts on crmentityContacts.crmid = vtiger_contactdetails.contactid 
			left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
			left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
			left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
			left join vtiger_contactdetails as contactdetailsContacts on contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
			left join vtiger_account as accountContacts on accountContacts.accountid = vtiger_contactdetails.accountid 
			left join vtiger_users as usersContacts on usersContacts.id = crmentityContacts.smownerid
			where vtiger_crmentity.deleted=0 and ((crmentityContacts.deleted=0 || crmentityContacts.deleted is null)||(crmentityAccounts.deleted=0 || crmentityAccounts.deleted is null)) 
			and vtiger_troubletickets.ticketid in (".$mass_merge.")";

	$result = $adb->query($query);

	while($columnValues = $adb->fetch_array($result))
	{
		$y=$adb->num_fields($result);
		for($x=0; $x<$y; $x++)
		{
			$value = $columnValues[$x];
			//<<<<<<<<<<<<<<<for blank vtiger_fields>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			if($value == "0")
			{
				$value = "";
			}
			if(trim($value) == "--None--" || trim($value) == "--none--")
			{
				$value = "";
			}
			//<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$actual_values[$x] = $value;
			$actual_values[$x] = str_replace('"'," ",$actual_values[$x]);
			//if value contains any line feed or carriage return replace the value with ".value."
			if (preg_match ("/(\r\n)/", $actual_values[$x])) 
			{
				$actual_values[$x] = '"'.$actual_values[$x].'"';
			}
			$actual_values[$x] = str_replace(","," ",$actual_values[$x]);
		}
		$mergevalue[] = implode($actual_values,",");  	
	}
	$csvdata = implode($mergevalue,"###");
}
else
{
	die("No fields to do Merge");
}	

$handle = fopen($wordtemplatedownloadpath."datasrc.csv","wb");
fwrite($handle,$csvheader."\r\n");
fwrite($handle,str_replace("###","\r\n",$csvdata));
fclose($handle);

?>
<script>
if (window.ActiveXObject)
{
	try 
	{
  		ovtigerVM = eval("new ActiveXObject('vtigerCRM.ActiveX');");
  		if(ovtigerVM)
		{
			var filename = "<?php echo $filename?>";
			if(filename != "")
			{
				if(objMMPage.bDLTempDoc("<?php echo $site_URL?>/test/wordtemplatedownload/<?php echo $filename?>","MMTemplate.doc"))
				{
					try
					{
						if(objMMPage.Init())
						{
							objMMPage.vLTemplateDoc();
							objMMPage.bBulkHDSrc("<?php echo $site_URL;?>/test/wordtemplatedownload/datasrc.csv");
							objMMPage.vBulkOpenDoc();
							objMMPage.UnInit()
								window.history.back();
						}		
					}catch(errorObject)
					{
						document.write("Error while processing mail merge operation");
					}
				}
				else
				{
					document.write("Cannot get template document");
				}
			}
		}
	}
	catch(e)
	{
		document.write("Requires to download ActiveX Control from vtigerCRM. Please, ensure that you have administration privilage");
	}
}
</script>
</body>
</html>
