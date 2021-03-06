<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/install/5createTables.php,v 1.58 2005/04/19 16:57:08 ray Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/
set_time_limit(600);

if (isset($_REQUEST['db_name'])) $db_name  				= $_REQUEST['db_name'];
if (isset($_REQUEST['db_drop_tables'])) $db_drop_tables 	= $_REQUEST['db_drop_tables'];
if (isset($_REQUEST['db_create'])) $db_create 			= $_REQUEST['db_create'];
if (isset($_REQUEST['db_populate'])) $db_populate		= $_REQUEST['db_populate'];
if (isset($_REQUEST['admin_email'])) $admin_email		= $_REQUEST['admin_email'];
if (isset($_REQUEST['admin_password'])) $admin_password	= $_REQUEST['admin_password'];
if (isset($_REQUEST['currency_name'])) $currency_name	= $_REQUEST['currency_name'];
if (isset($_REQUEST['currency_code'])) $currency_code	= $_REQUEST['currency_code'];
if (isset($_REQUEST['currency_symbol'])) $currency_symbol	= $_REQUEST['currency_symbol'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>vtiger CRM 5 - Configuration Wizard - Finish</title>


<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br><br><br>
	<!-- Table for cfgwiz starts -->

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="Configuration Wizard" hspace="20" title="Configuration Wizard"></td>
		<td class="cwHeadBg" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
			<tr>
				<td width=20% valign=top>

				<!-- Left side tabs -->
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Welcome</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Installation Check</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">System Configuration</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Confirm Settings</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Config File Creation</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Database Generation</div></td></tr>
					<tr><td class="small cwSelectedTab" align=right><div align="left"><b>Finish</b></div></td></tr>
					</table>
					
				</td>
				<td width=80% valign=top class="cwContentDisplay" align=left>
				<!-- Right side tabs -->
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
					<tr><td class=small align=left><img src="include/install/images/confWizFinish.gif" alt="Configuration Completed" title="Configuration Completed"><br>
					  <hr noshade size=1></td></tr>

					<tr>
					<td align=center class="small" style="height:250px;"> 

<?php

	// Output html instead of plain text for the web
	$useHtmlEntities = true;

	require_once('install/5createTables.inc.php');

	
//populating forums data

//this is to rename the installation file and folder so that no one destroys the setup
$renamefile = uniqid(rand(), true);

//@rename("install.php", $renamefile."install.php.txt");
if(!rename("install.php", $renamefile."install.php.txt"))
{
	if (copy ("install.php", $renamefile."install.php.txt"))
       	{
        	 unlink($renamefile."install.php.txt");
     	}
}

//@rename("install/", $renamefile."install/");
if(!rename("install/", $renamefile."install/"))
{
	if (copy ("install/", $renamefile."install/"))
       	{
        	 unlink($renamefile."install/");
     	}
}
//populate Calendar data


?>
		<table border=0 cellspacing=0 cellpadding=5 align="center" width=75% style="background-color:#E1E1FD;border:1px dashed #111111;">
		<tr>
			<td align=center class=small>
			<b>vtigercrm-5.0.2 is all set to go!</b>
			<hr noshade size=1>
			<div style="width:100%;padding:10px; "align=left>
			<ul>
			<li>Your install.php file has been renamed to <?echo $renamefile;?>install.php.txt.
			<li>Your install folder too has been renamed to <?echo $renamefile;?>install/.  
			<li>Please log in using the "admin" user name and the password you entered in step 2.
			</ul>
			</div>

			</td>
		</tr>
		</table>
		<br>	
		<table border=0 cellspacing=0 cellpadding=10 width=100%>
		<tr><td colspan=2 align="center">
				 <form action="index.php" method="post" name="form" id="form">
				 <input type="hidden" name="default_user_name" value="admin">
			 	 <input  type="image" src="include/install/images/cwBtnFinish.gif" name="next" title="Finish" value="Finish" />
				 </form>
		</td></tr>
		</table>		
		</td>

		</tr>
		</table>
		<!-- Master display stops -->
		
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>

		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
	</table>
    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>

      <tr>
        <td class=small align=center> <a href="#">www.vtiger.com</a></td>
      </tr>
    </table>
</body>
</html>	
