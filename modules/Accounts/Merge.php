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
if (document.layers)
{
	document.write("This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.");
	document.write("<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page");
}	
else if (document.layers || (!document.all && document.getElementById))
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


$handle = fopen($wordtemplatedownloadpath.$temparray['filename'],"wb");
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
}else if($single_record != "")
{
	$mass_merge = $single_record;	
}else
{
	die("Record Id is not found, cannot merge the document");
}

//echo $mass_merge;
//die;
//for setting vtiger_accountid=0 for the contacts which are deleted
$ct_query = "select crmid from vtiger_crmentity where setype='Contacts' and deleted=1";
$result = $adb->query($ct_query);

while($row = $adb->fetch_array($result))
{
	$deleted_id[] = $row['crmid'];
}

if(count($deleted_id) > 0)
{
	$deleted_id = implode(",",$deleted_id);
	$update_query = "update vtiger_contactdetails set accountid = 0 where contactid in (".$deleted_id.")";
	$result = $adb->query($update_query);
}
//End setting vtiger_accountid=0 for the contacts which are deleted

//<<<<<<<<<<<<<<<<header for csv and select columns for query>>>>>>>>>>>>>>>>>>>>>>>>
$query1="select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (4,6) and vtiger_field.block <> 6 and vtiger_field.block <> 75 order by vtiger_field.tablename";

$result = $adb->query($query1);
$y=$adb->num_rows($result);
	
for ($x=0; $x<$y; $x++)
{ 
  $tablename = $adb->query_result($result,$x,"tablename");
  $columnname = $adb->query_result($result,$x,"columnname");
  $modulename = $adb->query_result($result,$x,"name");
  
  if($tablename == "crmentity")
  {
  	if($modulename == "Contacts")
  	{
  		$tablename = "vtiger_crmentityContacts";
  	}
  }
  $querycolumns[$x] = $tablename.".".$columnname;
	if($columnname == "smownerid")
  {
      if($modulename == "Accounts")
      {
  			$querycolumns[$x] = "concat(vtiger_users.last_name,' ',vtiger_users.first_name) as userjoinname,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.yahoo_id,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
      }
  		if($modulename == "Contacts")
      {
      	$querycolumns[$x] = "concat(vtiger_usersContacts.last_name,' ',vtiger_usersContacts.first_name) as userjoincname";
      }
  }
	if($columnname == "parentid")
	{
		$querycolumns[$x] = "vtiger_accountAccount.accountname";
	}
	if($columnname == "accountid")
	{
		$querycolumns[$x] = "vtiger_accountContacts.accountname";
	}
	if($columnname == "reportsto")
	{
		$querycolumns[$x] = "vtiger_contactdetailsContacts.lastname";
	}

	if($modulename == "Accounts")
  {
    	$field_label[$x] = "ACCOUNT_".strtoupper(str_replace(" ","",$adb->query_result($result,$x,"fieldlabel")));
  		if($columnname == "smownerid")
  		{
  		$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_YAHOOID,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
  		}
	}
	
	if($modulename == "Contacts")
  {
  	$field_label[$x] = "CONTACT_".strtoupper(str_replace(" ","",$adb->query_result($result,$x,"fieldlabel")));
  }
  
}

$csvheader = implode(",",$field_label);
//<<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>
	
if(count($querycolumns) > 0)
{
	$selectcolumns = implode($querycolumns,",");

$query = "select  ".$selectcolumns." from vtiger_account 
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid
				where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null) and vtiger_account.accountid in(".$mass_merge.")";
//echo $query;
//die;	
$result = $adb->query($query);
	
while($columnValues = $adb->fetch_array($result))
{
	$y=$adb->num_fields($result);
  for($x=0; $x<$y; $x++)
  {
  	$value = $columnValues[$x];
  	//<<<<<<<<<<<<<<<for modifing default values>>>>>>>>>>>>>>>>>>>>>>>>>>>>
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
}else
{
	die("No fields to do Merge");
}

$handle = fopen($wordtemplatedownloadpath."datasrc.csv","wb");
fwrite($handle,$csvheader."\r\n");
fwrite($handle,str_replace("###","\r\n",$csvdata));
fclose($handle);
?>
<script>
if (window.ActiveXObject){
	try 
	{
  		ovtigerVM = eval("new ActiveXObject('vtigerCRM.ActiveX');");
  		if(ovtigerVM)
  		{
        	var filename = "<?php echo $filename?>";
        	if(filename != "")
        	{
        		if(objMMPage.bDLTempDoc("<?php echo $site_URL;?>/test/wordtemplatedownload/<?php echo $filename; ?>","MMTemplate.doc"))
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
        		}else
        		{
        			document.write("Cannot get template document");
        		}
        	}
  		}
		}
	catch(e) {
		document.write("Requires to download ActiveX Control from vtigerCRM. Please, ensure that you have administration privilage");
	}
}
</script>
</body>
</html>
