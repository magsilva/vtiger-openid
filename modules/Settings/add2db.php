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
require_once('include/utils/utils.php');


	$uploaddir = $root_directory ."/test/logo/" ;// set this to wherever
	$saveflag="true";
	$nologo_specified="true";
	$error_flag ="";
	$nologo_specified="false";
	$binFile = $_FILES['binFile']['name'];
	$filename = basename($binFile);
	$filetype= $_FILES['binFile']['type'];
	$filesize = $_FILES['binFile']['size'];
	
	$filetype_array=explode("/",$filetype); 

	$file_type_val=strtolower($filetype_array[1]);

	if($filesize != 0)
	{
		if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) ||  ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") ) //Checking whether the file is an image or not
		{
			if(stristr($binFile, '.gif') != FALSE)
			{
				$savelogo="false";
				$error_flag ="1";
			}		
			else if($result!=false)
			{
				$savelogo="true";
			}
		}
		else
		{
			$savelogo="false";
			$error_flag ="1";
		}
		
	}
	else
	{
		$savelogo="false";
		if($filename != "") 
			$error_flag ="2";
	}
		
	$errorCode =  $_FILES['binFile']['error'];
	if($errorCode == 4)
	{
	  	$savelogo="false";	    	
		$errorcode="";
		$error_flag="5";
		$nologo_specified="true";
	}
	else if($errorCode == 2)
	{
	   	$error_flag ="3";
	   	$savelogo="false";	    	
		$nologo_specified="false";
	}
	else if($errorCode == 3 )
	{
		$error_flag ="4";
  		$savelogo="false";
		$nologo_specified="false";
	}
	if($savelogo=="true")
	{	
		move_uploaded_file($_FILES["binFile"]["tmp_name"],$uploaddir.$_FILES["binFile"]["name"]); 
	}	

	if($saveflag=="true")
	{
		$organization_name=$_REQUEST['organization_name'];
		$org_name=$_REQUEST['org_name'];
		$organization_address=$_REQUEST['organization_address'];
		$organization_city=$_REQUEST['organization_city'];
		$organization_state=$_REQUEST['organization_state'];
		$organization_code=$_REQUEST['organization_code'];
		$organization_country=$_REQUEST['organization_country'];
		$organization_phone=$_REQUEST['organization_phone'];
		$organization_fax=$_REQUEST['organization_fax'];
		$organization_website=$_REQUEST['organization_website'];
		$organization_logo=$_REQUEST['organization_logo'];

		$organization_logoname=$filename;
		if(!isset($organization_logoname))
			$organization_logoname="";

		$sql="SELECT * FROM vtiger_organizationdetails WHERE organizationname = ".$adb->quote($org_name);
		$result = $adb->query($sql);
		$org_name = from_html($adb->query_result($result,0,'organizationname'));
		$org_logo = $adb->query_result($result,0,'logoname'); 


		if($org_name=='')
		{
			$sql="INSERT INTO vtiger_organizationdetails
				(organizationname, address, city, state, code, country, phone, fax, website, logoname)
				VALUES (".$adb->quote($organization_name).
					",".$adb->quote($organization_address).
					",".$adb->quote($organization_city).
					",".$adb->quote($organization_state).
					",".$adb->quote($organization_code).
					",".$adb->quote($organization_country).
					",".$adb->quote($organization_phone).
					",".$adb->quote($organization_fax).
					",".$adb->quote($organization_website).
					",".$adb->quote($organization_logoname).
				")";
		}
		else
		{
			if($savelogo=="true")
			{
				$organization_logoname=$filename;
			}
			elseif($savelogo=="false" && $error_flag=="")
			{
				$savelogo="true";
				$organization_logoname=$_REQUEST['PREV_FILE'];
			}
			else
			{
				$organization_logoname=$_REQUEST['PREV_FILE'];
			}	
			if($nologo_specified=="true")
			{
				$savelogo="true";
				$organization_logoname=$org_logo;
			}

			$sql = "UPDATE vtiger_organizationdetails
				SET organizationname = ".$adb->quote($organization_name).",
					address = ".$adb->quote($organization_address).",
					city = ".$adb->quote($organization_city).",
					state = ".$adb->quote($organization_state).",
					code = ".$adb->quote($organization_code).",
					country = ".$adb->quote($organization_country)." ,
					phone = ".$adb->quote($organization_phone)." ,
					fax = ".$adb->quote($organization_fax).",
					website = ".$adb->quote($organization_website).",
					logoname = ".$adb->quote($organization_logoname)."
				WHERE organizationname = ".$adb->quote($org_name);
		}
		$adb->query($sql);

		if($savelogo=="true")
		{
			header("Location: index.php?parenttab=Settings&module=Settings&action=OrganizationConfig");
		}
		elseif($savelogo=="false")
		{
		
    		header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=".$error_flag);
		}
	

	}	
?>

