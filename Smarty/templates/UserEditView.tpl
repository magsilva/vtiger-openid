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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/ColorPicker2.js"></script>
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<script language="JavaScript" type="text/javascript">

 	var cp2 = new ColorPicker('window');
	
function pickColor(color)
{ldelim}
	ColorPicker_targetInput.value = color;
        ColorPicker_targetInput.style.backgroundColor = color;
{rdelim}	

function openPopup(){ldelim}
		window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no");
	{rdelim}	
</script>	

<script language="javascript">
function check_duplicate()
{ldelim}
	var user_name = window.document.EditView.user_name.value;
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=Save&ajax=true&dup_check=true&userName='+user_name,
                        onComplete: function(response) {ldelim}
				if(response.responseText == 'SUCCESS')
			                document.EditView.submit();
       				else
			                alert(response.responseText);
                        {rdelim}
                {rdelim}
        );

{rdelim}

</script>


<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
	{if $PARENTTAB eq 'Settings'}
		{include file='SetMenu.tpl'}
	{/if}

		<form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data">
		<input type="hidden" name="module" value="Users">
		<input type="hidden" name="record" value="{$ID}">
		<input type="hidden" name="mode" value="{$MODE}">
		<input type='hidden' name='parenttab' value='{$PARENTTAB}'>
		<input type="hidden" name="activity_mode" value="{$ACTIVITYMODE}">
		<input type="hidden" name="action">
		<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
		<input type="hidden" name="return_id" value="{$RETURN_ID}">
		<input type="hidden" name="return_action" value="{$RETURN_ACTION}">			
		<input type="hidden" name="tz" value="Europe/Berlin">			
		<input type="hidden" name="holidays" value="de,en_uk,fr,it,us,">			
		<input type="hidden" name="workdays" value="0,1,2,3,4,5,6,">			
		<input type="hidden" name="namedays" value="">			
		<input type="hidden" name="weekstart" value="1">

	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="settingsSelUITopLine">
	<tr><td align="left">
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2"><img src="{$IMAGE_PATH}ico-users.gif" align="absmiddle"></td>
			<td>	
				<span class="lvtHeaderText">
				{if $PARENTTAB neq ''}	
				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> &gt; <a href="index.php?module=Administration&action=index&parenttab=Settings">{$MOD.LBL_USERS}</a> &gt; 
					{if $MODE eq 'edit'}
						{$UMOD.LBL_EDITING} "{$USERNAME}" 
					{else}
						{$UMOD.LBL_CREATE_NEW_USER}
					{/if}
					</b></span>
				{else}
                                <span class="lvtHeaderText">
                                <b>{$APP.LBL_MY_PREFERENCES}</b>
                                </span>
                                {/if}
			</td>
			<td rowspan="2" nowrap>&nbsp;
			</td>
	 	</tr>
		<tr>
			{if $MODE eq 'edit'}
				<td><b class="small">{$UMOD.LBL_EDIT_VIEW} "{$USERNAME}"</b>
			{else}
				<td><b class="small">{$UMOD.LBL_CREATE_NEW_USER}</b>
			{/if}
			</td>
                </tr>
		</table>
	</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap align="right">
				<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='Save'; return verify_data(EditView)" style="width: 70px;" type="button" />
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
						
		</td>
	</tr>
	<tr><td class="padTab" align="left">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">

		<tr><td colspan="2">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
			<tr>
			    <td align="left" valign="top">
			             <table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td align="left">
						{foreach key=header name=blockforeach item=data from=$BLOCKS}
						<br>
		                                <table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
                		                <tr>
                                		    {strip}
		                                     <td class="big">
                		                        <strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong>
                                		     </td>
		                                     <td class="small" align="right">&nbsp;</td>
		                                  {/strip}
                		              	</tr>
                                		</table>
		                                <table border="0" cellpadding="5" cellspacing="0" width="100%">
						<!-- Handle the ui types display -->
							{include file="DisplayFields.tpl"}
						</table>
					   	{/foreach}
				<br>
			    	<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
			    	<tr>
				     <td class="big">	
					<strong>5. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
				     </td>
				     <td class="small" align="right">&nbsp;</td>	
			        </tr>
			    	</table>
			    	<table border="0" cellpadding="5" cellspacing="0" width="100%">
				{foreach item=homeitems key=values from=$HOMEORDER}
					<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.$values}</td>
					    {if $homeitems neq ''}
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="{$values}" checked type="radio"></td><td class="dvtCellInfo" align="left" width="20%">{$UMOD.LBL_SHOW}</td> 		
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td> 		
					    {else}	
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="{$values}" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOW}</td> 		
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="" checked type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td> 		
					    {/if}	
					</tr>			
				{/foreach}
			    	</table>	
				<br>
				<tr><td colspan=4>&nbsp;</td></tr>
							
					        <tr>
					       		<td colspan=4 align="right">
							<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='Save'; return verify_data(EditView)" style="width: 70px;" type="button" />
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
							</td>
						</tr>
					    </table>
					 </td></tr>
					</table>
			  	   </td></tr>
				   </table>
				 <br>
				  </td></tr>
				<tr><td class="small"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>
				</table>
			</td>
			</tr>
			</table>
			</form>	
</td>
</tr>
</table>
</td></tr></table>
<br>
{$JAVASCRIPT}
