{*<!--

	/*********************************************************************************
	 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
	 * ("License"); You may not use this file except in compliance with the License
	 * The Original Code is:  vtiger CRM Open Source
	 * The Initial Developer of the Original Code is vtiger.
	 * Portions created by vtiger are Copyright (C) vtiger.
	 * All Rights Reserved.
	 *
	 ********************************************************************************/

-->*}
<br>
<script language="JavaScript" type="text/javascript" src="modules/Reports/Report.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
{$BLOCKJS}


<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
    <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	
	
	
<table class="small reportGenHdr mailClient mailClientBg" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<form name="NewReport" action="index.php" method="POST">
    <input type="hidden" name="booleanoperator" value="5"/>
    <input type="hidden" name="record" value="{$REPORTID}"/>
    <input type="hidden" name="reload" value=""/>    
    <input type="hidden" name="module" value="Reports"/>
    <input type="hidden" name="action" value="SaveAndRun"/>
    <input type="hidden" name="dlgType" value="saveAs"/>
    <input type="hidden" name="reportName"/>
    <input type="hidden" name="folderid" value="{$FOLDERID}"/>
    <input type="hidden" name="reportDesc"/>
    <input type="hidden" name="folder"/>

	<tbody>
	<tr>
	<td style="padding: 10px; text-align: left;" width="70%">
		<span class="moduleName">
		{if $MOD.$REPORTNAME neq ''}
			{$MOD.$REPORTNAME}
		{else}
			{$REPORTNAME}
		{/if}
		</span>&nbsp;&nbsp;
		
		<input type="button" name="custReport" value="{$MOD.LBL_CUSTOMIZE_REPORT}" class="crmButton small edit" onClick="editReport('{$REPORTID}');">
		<br>
		<a href="index.php?module=Reports&action=ListView" class="reportMnu" style="border-bottom: 0px solid rgb(0, 0, 0);">&lt;{$MOD.LBL_BACK_TO_REPORTS}</a>
	</td>
	<td style="border-left: 2px dotted rgb(109, 109, 109); padding: 10px;" width="30%">
		<b>{$MOD.LBL_SELECT_ANOTHER_REPORT} : </b><br>
		<select name="another_report" class="detailedViewTextBox" onChange="selectReport()">
		{foreach key=report_in_fld_id item=report_in_fld_name from=$REPINFOLDER}
		{if $MOD.$report_in_fld_name neq ''} 
			{if $report_in_fld_id neq $REPORTID}
				<option value={$report_in_fld_id}>{$MOD.$report_in_fld_name}</option>
			{else}	
				<option value={$report_in_fld_id} "selected">{$MOD.$report_in_fld_name}</option>
			{/if}
		{else}
			{if $report_in_fld_id neq $REPORTID}
				<option value={$report_in_fld_id}>{$report_in_fld_name}</option>
			{else}	
				<option value={$report_in_fld_id} "selected">{$report_in_fld_name}</option>
			{/if}
		{/if}
		{/foreach}
		</select>&nbsp;&nbsp;
	</td>
	</tr>
	</tbody>
</table>

<!-- Generate Report UI Filter -->
<table class="small reportGenerateTable" align="center" cellpadding="5" cellspacing="0" width="95%" border=0>
<tr>
	<td align=center class=small>
	
	<table border=0 cellspacing=0 cellpadding=0 width=80%>
		<tr>
			<td align=left class=small><b>{$MOD.LBL_SELECT_COLUMN} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SELECT_TIME} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SF_STARTDATE} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SF_ENDDATE} </b>
		</tr>
		<tr>
			<td align="left" width="30%">
			<select name="stdDateFilterField" class="small" style="width:98%">
			{$BLOCK1}
			</select>
			</td>
			<td class=small>&nbsp;</td>			
			<td align=left width="30%">
			<select name="stdDateFilter" class="small" onchange='showDateRange( this.options[ this.selectedIndex ].value )' style="width:98%">
			{$BLOCKCRITERIA}
			</select>
			</td>
			<td class=small>&nbsp;</td>
			<td align=left width="20%">
				<table border=0 cellspacing=0 cellpadding=2>
				<tr>
					<td align=left><input name="startdate" id="jscal_field_date_start" type="text" size="10" class="importBox" style="width:70px;" value="{$STARTDATE}"></td>
					<td valign=absmiddle align=left><img src="{$IMAGE_PATH}calendar.gif" id="jscal_trigger_date_start"></td>
				</tr>
				</table>
			</td>
			<td align=left class=small>&nbsp;</td>
			<td align=left width=20%>
				<table border=0 cellspacing=0 cellpadding=2>
				<tr>
					<td align=left><input name="enddate" id="jscal_field_date_end" type="text" size="10" class="importBox" style="width:70px;" value="{$ENDDATE}"></td>
					<td valign=absmiddle align=left><img src="{$IMAGE_PATH}calendar.gif" id="jscal_trigger_date_end"></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="8" style="padding:5px"><input name="generatenw" value=" {$MOD.LBL_GENERATE_NOW} " class="crmbutton small create" type="button" onClick="generateReport({$REPORTID});"></td>
		</tr>
	</table>
	
	</td>
</tr>
</table>


<div style="display: block;" id="Generate" align="center">
	{include file="ReportRunContents.tpl"}
</div>
<br>

</td>
<td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
</tr>
</table>



{literal}

<SCRIPT LANGUAGE=JavaScript>
function CrearEnlace(tipo,id){
	return "index.php?module=Reports&action="+tipo+"&record="+id+"&stdDateFilterField="+document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value+"&stdDateFilter="+document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value+"&startdate="+document.NewReport.startdate.value+"&enddate="+document.NewReport.enddate.value;

}
function goToURL( url )
{
	document.location.href = url;
}
					
var filter = getObj('stdDateFilter').options[document.NewReport.stdDateFilter.selectedIndex].value
    if( filter != "custom" )
    {
        showDateRange( filter );
    }

Calendar.setup ({inputField : "jscal_field_date_start", ifFormat : "%Y-%m-%d", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1});
    Calendar.setup ({inputField : "jscal_field_date_end", ifFormat : "%Y-%m-%d", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1});
function generateReport(id)
{
	var stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value;
	var stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value;
	var startdatevalue = document.NewReport.startdate.value;
	var enddatevalue = document.NewReport.enddate.value;
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&record='+id+'&stdDateFilterField='+stdDateFilterFieldvalue+'&stdDateFilter='+stdDateFiltervalue+'&startdate='+startdatevalue+'&enddate='+enddatevalue,
                        onComplete: function(response) {
				getObj('Generate').innerHTML = response.responseText;
				setTimeout("ReportInfor()",1);
                        }
                }
        );
}
function selectReport()
{
	var id = document.NewReport.another_report.options  [document.NewReport.another_report.selectedIndex].value;
	var folderid = getObj('folderid').value;
	url ='index.php?action=ReportsAjax&file=SaveAndRun&module=Reports&record='+id+'&folderid='+folderid;
	goToURL(url);
}
function ReportInfor()
{
	var stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].text;
    var stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].text;
	var startdatevalue = document.NewReport.startdate.value;
	var enddatevalue = document.NewReport.enddate.value;
	
	if(startdatevalue != '' && enddatevalue=='')
	{
		var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"   (from  '+startdatevalue+' )';
	}else if(startdatevalue == '' && enddatevalue !='')
	{
		var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"   (  till  '+enddatevalue+')';
	}else if(startdatevalue == '' && enddatevalue =='')
	{
		var reportinfr = 'No filter Selected';
	}else if(startdatevalue != '' && enddatevalue !='')
	{
	var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"  of  "'+stdDateFiltervalue+'"  ( '+startdatevalue+'  to  '+enddatevalue+' )';
	}
	getObj('report_info').innerHTML = reportinfr;
}
ReportInfor();

{/literal}
function goToPrintReport(id) 
{ldelim}
	window.open("index.php?module=Reports&action=ReportsAjax&file=PrintReport&record="+id+"&stdDateFilterField="+document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value+"&stdDateFilter="+document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value+"&startdate="+document.NewReport.startdate.value+"&enddate="+document.NewReport.enddate.value,"{$MOD.LBL_Print_REPORT}","width=750,height=800,resizable=0,scrollbars=1,left=100");
{rdelim}
</SCRIPT>
