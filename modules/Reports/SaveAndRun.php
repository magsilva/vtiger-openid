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
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once ($theme_path."layout_utils.php");
require_once("config.php");
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once("modules/Reports/ReportRun.php");
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

global $adb;
global $mod_strings;

$reportid = $_REQUEST["record"];
$folderid = $_REQUEST["folderid"];
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
$startdate = $_REQUEST["startdate"];
$enddate = $_REQUEST["enddate"];

global $primarymodule;
global $secondarymodule;
global $orderbylistsql;
global $orderbylistcolumns;
global $ogReport;

$ogReport = new Reports($reportid);
$primarymodule = $ogReport->primodule;
$secondarymodule = $ogReport->secmodule;
$oReportRun = new ReportRun($reportid);
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);
$sshtml = $oReportRun->GenerateReport("HTML",$filterlist);
$totalhtml = $oReportRun->GenerateReport("TOTALHTML",$filterlist);
if(isPermitted($primarymodule,'index') == "yes" && (isPermitted($secondarymodule,'index')== "yes"))
{

	$list_report_form = new vtigerCRM_Smarty;
	$ogReport->getSelectedStandardCriteria($reportid);
	//commented to omit dashboards for vtiger_reports
	//require_once('modules/Dashboard/ReportsCharts.php');
	//$image = get_graph_by_type('Report','Report',$primarymodule,'',$sshtml[2]);
	//$list_report_form->assign("GRAPH", $image);

	$BLOCK1 = getPrimaryStdFilterHTML($ogReport->primodule,$ogReport->stdselectedcolumn);
	$BLOCK1 .= getSecondaryStdFilterHTML($ogReport->secmodule,$ogReport->stdselectedcolumn);
	$list_report_form->assign("BLOCK1",$BLOCK1);
	$BLOCKJS = $ogReport->getCriteriaJS();
	$list_report_form->assign("BLOCKJS",$BLOCKJS);

	$BLOCKCRITERIA = $ogReport->getSelectedStdFilterCriteria($ogReport->stdselectedfilter);
	$list_report_form->assign("BLOCKCRITERIA",$BLOCKCRITERIA);

	$startdate = $ogReport->startdate;
	$list_report_form->assign("STARTDATE",$startdate);	

	$enddate = $ogReport->enddate;
	$list_report_form->assign("ENDDATE",$enddate);

	$list_report_form->assign("MOD", $mod_strings);
	$list_report_form->assign("APP", $app_strings);
	$list_report_form->assign("IMAGE_PATH", $image_path);
	$list_report_form->assign("REPORTID", $reportid);
	$list_report_form->assign("REPORTNAME", $ogReport->reportname);
	$list_report_form->assign("REPORTHTML", $sshtml);
	$list_report_form->assign("REPORTTOTHTML", $totalhtml);
	$list_report_form->assign("FOLDERID", $folderid);
	if($_REQUEST['mode'] != 'ajax')
	{
		$list_report_form->assign("REPINFOLDER", getReportsinFolder($folderid));
		include('themes/'.$theme.'/header.php');
		$list_report_form->display('ReportRun.tpl');
	}
	else
	{
		$list_report_form->display('ReportRunContents.tpl');
	}
}
else
{
	if($_REQUEST['mode'] != 'ajax')
	{
		include('themes/'.$theme.'/header.php');
	}	
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='themes/$theme/images/denied.gif' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_PERMISSION']." ".$primarymodule." ".$secondarymodule."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   		     </td>
		</tr>
		</tbody></table> 
		</div>";
	echo "</td></tr></table>";
}

	/** Function to get the StdfilterHTML strings for the given  primary module 
	 *  @ param $module : Type String
	 *  @ param $selected : Type String(optional)	
	 *  This Generates the HTML Combo strings for the standard filter for the given reports module  
	 *  This Returns a HTML sring
	 */
function getPrimaryStdFilterHTML($module,$selected="")
{
	global $app_list_strings;
	global $ogReport;
	global $current_language;

        $mod_strings = return_module_language($current_language,$module);

	$result = $ogReport->getStdCriteriaByModule($module);
	
	if(isset($result))
	{
		foreach($result as $key=>$value)
		{
			if(isset($mod_strings[$value]))
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".$app_list_strings['moduleList'][$module]." - ".$mod_strings[$value]."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".$app_list_strings['moduleList'][$module]." - ".$mod_strings[$value]."</option>";
				}
			}else
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".$app_list_strings['moduleList'][$module]." - ".$value."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".$app_list_strings['moduleList'][$module]." - ".$value."</option>";
				}
			}
		}
	}
	
	return $shtml;
}

	/** Function to get the StdfilterHTML strings for the given secondary module 
	 *  @ param $module : Type String
	 *  @ param $selected : Type String(optional)	
	 *  This Generates the HTML Combo strings for the standard filter for the given reports module  
	 *  This Returns a HTML sring
	 */
function getSecondaryStdFilterHTML($module,$selected="")
{
	global $app_list_strings;
	global $ogReport;
	global $current_language;

	if($module != "")
        {
        	$secmodule = explode(":",$module);
        	for($i=0;$i < count($secmodule) ;$i++)
        	{
			$result = $ogReport->getStdCriteriaByModule($secmodule[$i]);
			$mod_strings = return_module_language($current_language,$secmodule[$i]);
        		if(isset($result))
        		{
                		foreach($result as $key=>$value)
                		{
                        		if(isset($mod_strings[$value]))
                                        {
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".$app_list_strings['moduleList'][$secmodule[$i]]." - ".$mod_strings[$value]."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".$app_list_strings['moduleList'][$secmodule[$i]]." - ".$mod_strings[$value]."</option>";
						}
					}else
					{
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".$app_list_strings['moduleList'][$secmodule[$i]]." - ".$value."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".$app_list_strings['moduleList'][$secmodule[$i]]." - ".$value."</option>";
						}
					}
                		}
        		}
		
		}
	}
	return $shtml;
}
	/** Function to get the reports under a report folder 
	 *  @ param $folderid : Type Integer 
	 *  This Returns $reports_array in the following format 
	 *  		$reports_array = array ($reportid=>$reportname,$reportid=>$reportname1,.............,$reportidn=>$reportname)
	 */
function getReportsinFolder($folderid)
{
	global $adb;
	$query = 'select reportid,reportname from vtiger_report where folderid='.$folderid;
	$result = $adb->query($query);
	$reports_array = Array();
	for($i=0;$i < $adb->num_rows($result);$i++)	
	{
		$reportid = $adb->query_result($result,$i,'reportid');
		$reportname = $adb->query_result($result,$i,'reportname');
		$reports_array[$reportid] = $reportname; 
	}
	if(count($reports_array) > 0)
		return $reports_array;
	else
		return false;
}
?>
