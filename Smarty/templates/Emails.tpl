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
<!--  USER  SETTINGS PAGE STARTS HERE -->
<script language="javascript">
function ShowFolders(folderid)
{ldelim}
	gselectedrowid = 0;
	$("status").style.display="inline";
	gFolderid = folderid;
	getObj('search_text').value = '';
	switch(folderid)
	{ldelim}
		case 1:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_ALLMAILS}</b>';
			break;
		case 2:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_TO_CONTACTS}</b>';
			break;
		case 3:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_TO_ACCOUNTS}</b>';
			break;
		case 4:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_TO_LEADS}</b>';
			break;
		case 5:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_TO_USERS}</b>';
			break;
		case 6:
			getObj('mail_fldrname').innerHTML = '<b>{$MOD.LBL_QUAL_CONTACT}</b>';
	{rdelim}
	
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                method: 'post',
                postBody: 'module=Emails&ajax=true&action=EmailsAjax&file=ListView&folderid='+folderid,
                onComplete: function(response) {ldelim}
                                        $("status").style.display="none";
                                        if(gFolderid != folderid)
                                        {ldelim}
                                                gselectedrowid = 0;
                                                $("email_con").innerHTML=response.responseText;
                                                execJS($('email_con'));
                                        {rdelim}
                                        else
                                        {ldelim}
                                                $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top:10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                                $("subjectsetter").innerHTML='';
                                                $("email_con").innerHTML=response.responseText;
                                                execJS($('email_con'));
                                        {rdelim}
                                {rdelim}
                        {rdelim}
	);

{rdelim}
</script>
		{include file='Buttons_List.tpl'}
<script language="JavaScript" type="text/javascript" src="modules/Emails/Email.js"></script>
<div id="mailconfchk" class="small" style="position:absolute;display:none;left:350px;top:160px;height:27px;white-space:nowrap;z-index:10000007px;"><font color='red'><b>{$MOD.LBL_CONFIGURE_MAIL_SETTINGS}.<br> {$APP.LBL_PLEASE_CLICK} <a href="index.php?module=Settings&action=AddMailAccount&record={$USERID}">{$APP.LBL_HERE}</a> {$APP.LBL_TO_CONFIGURE}</b></font></div>
<!-- Shadow starts here -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
	<tr>
		<td valign=top align=right><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>

		<td class="showPanelBg" valign="top" width="95%" align=center >
		<!-- Email Client starts here -->
			<br>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mailClient">
				<tr>
					<td class="mailClientBg" width="7">&nbsp;</td>
					<td class="mailClientBg">
					<form name="massdelete" method="POST">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
							<!-- Compose, Settings and Name image -->
							<tr>
								<td colspan="3" style="vertical-align:middle;">
									<table border=0 cellspacing=0 cellpadding=0 width=100%>
									<tr>
									<td align=left>
									
										<table cellpadding="5" cellspacing="0" border="0">
											<tr>
												<td nowrap style="padding-left:20px;padding-right:20px" class=small>
												<img src="{$IMAGE_PATH}compose.gif" align="absmiddle" />
							&nbsp;<a href="javascript:;" onClick="OpenCompose('','create');" >{$MOD.LBL_COMPOSE}</a>
												</td>
												<td nowrap style="padding-left:20px;padding-right:20px" class=small>
												<img src="{$IMAGE_PATH}webmail_settings.gif" align="absmiddle" />
							&nbsp;<a href="index.php?module=Settings&action=AddMailAccount&record={$USERID}" >{$MOD.LBL_SETTINGS}</a>
												</td>
											</tr>
											</table>
										</td>
										<td align=right>
											<table >
											<tr>
												<td class="componentName" align=right>vtiger Email Client <!-- <img src="{$IMAGE_PATH}titleMailClient.gif" align="right"/> --></td>
											</tr>
											</table>
									</td>
									</tr>
									</table>
									
								</td>
							</tr>
							<!-- Columns -->
							<tr>
							<td width="25%" class="big mailSubHeader" ><b>{$MOD.LBL_EMAIL_FOLDERS}</b></td>
							<td width="2%">&nbsp;</td>
							<td width="73%" class="big mailSubHeader" align="left"><span id="mail_fldrname"><b>{$MOD.LBL_ALLMAILS}</b></span></td>
							</tr>
							
							<tr>
								<td rowspan="6" class="hdrNameBg" valign="top" bgcolor="#FFFFFF" style="padding:5px; " align="left" >
								<!-- Mailbox Tree -->
								<!-- Inbox -->
								<img src="{$IMAGE_PATH}webmail_root.gif" align="absmiddle" />&nbsp;<b class="txtGreen">{$MOD.LBL_INBOX}</b>
								<ul style="list-style-type:none;margin-left:10px;margin-top:5px;padding:2px">
									<li><img src="{$IMAGE_PATH}webmail_downarrow.gif" align="absmiddle" />&nbsp;&nbsp;
										<a href="javascript:;" onClick="ShowFolders(6)" class="webMnu">{$MOD.LBL_QUAL_CONTACT}</a>&nbsp;<b></b>
									</li>
									<li><img src="{$IMAGE_PATH}webmail_downarrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="gotoWebmail();" class="webMnu">{$MOD.LBL_MY_MAILS}</a>&nbsp;<b></b>
									</li>
								</ul>
								<!-- Sent mail -->
								<img src="{$IMAGE_PATH}webmail_root.gif" align="absmiddle" />&nbsp;<b class="txtGreen">{$MOD.LBL_SENT_MAILS}</b>
								<ul style="list-style-type:none;margin-left:10px;margin-top:5px;padding:2px">
									<li><img src="{$IMAGE_PATH}webmail_uparrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="ShowFolders(1)" class="webMnu">{$MOD.LBL_ALLMAILS}</a>&nbsp;<b></b>
									<li><img src="{$IMAGE_PATH}webmail_uparrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="ShowFolders(2)" class="webMnu">{$MOD.LBL_TO_CONTACTS}</a>&nbsp;<b></b>
									</li>
									<li><img src="{$IMAGE_PATH}webmail_uparrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="ShowFolders(3)" class="webMnu">{$MOD.LBL_TO_ACCOUNTS}</a>&nbsp;<b></b>
									</li>
									<li><img src="{$IMAGE_PATH}webmail_uparrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="ShowFolders(4)" class="webMnu">{$MOD.LBL_TO_LEADS}</a>&nbsp;
									</li>
									<li><img src="{$IMAGE_PATH}webmail_uparrow.gif" align="absmiddle" />&nbsp;&nbsp;
									<a href="javascript:;" onClick="ShowFolders(5)" class="webMnu">{$MOD.LBL_TO_USERS}</a>&nbsp;
									</li>
								</ul>
								</td>
								<td>&nbsp;</td>
								<!-- All mails pane -->
								<td class="hdrNameBg">
									<!-- Command Buttons and Search Email -->
									<table width="100%"  border="0" cellspacing="0" cellpadding="2">
									<input name="idlist" type="hidden">
										<tr>
											<td width="25%" align="left"><input type="button" name="Button2" value=" {$APP.LBL_DELETE_BUTTON}"  class="crmbutton small delete" onClick="return massDelete();"/> &nbsp;</td>
											<td width="50%" align="right" class="small">
												<font color="#000000">{$APP.LBL_SEARCH}</font>&nbsp;<input type="text" name="search_text" id="search_text" class="importBox" onkeyUp="Searchfn();">&nbsp;
											</td>
											<td width="25%" align=left class="small">
												<select name="search_field" id="search_field" onChange="Searchfn();" class="importBox">
												<option value='subject'>{$MOD.LBL_IN_SUBJECT}</option>
												<option value='user_name'>{$MOD.LBL_IN_SENDER}</option>
												<option value='join'>{$MOD.LBL_IN_SUBJECT_OR_SENDER}</option>
												</select>&nbsp;
											</td>
										</tr>
									</table>
									
								</td>
							</tr>
							<!-- Mail Subject Headers list -->
							<tr>
								<td>&nbsp;</td>
								<td align="left">
									<div id="email_con">
									{include file="EmailContents.tpl"}
									</div>
								</td>
							</tr>
							
							<tr>
								<td>&nbsp;</td>
								<td height="5"></td>
							</tr>
							
							<tr>
								<td>&nbsp;</td>
								<td class="mailSubHeader" id="subjectsetter" align=left><b>&nbsp;</b></td>
							</tr>
							
							<tr>
								<td>&nbsp;</td>	
								<td valign="top">
									<div id="EmailDetails" > 
									{include file="EmailDetails.tpl"}
									</div>
								</td>
							</tr>
						</table>
						</form>
						<br>
					</td>
					<td class="mailClientBg" width="7">&nbsp;</td>
				</tr>
				<!-- <tr>
					<td width="7" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}bottom_left.jpg" align="bottom"  /></td>
					<td bgcolor="#ECECEC" height="8" style="font-size:1px;" ></td>
					<td width="8" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}bottom_right.jpg" align="bottom" /></td>
				</tr>-->
			</table><br/>
		</td>
		<td valign=top><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
		
		
	</tr>
</table>
<!-- END -->
