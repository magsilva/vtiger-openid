/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var gFolderid = 1;
var gselectedrowid = 0;
function gotoWebmail()
{
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
                	method: 'post',
			postBody: "module=Webmails&action=WebmailsAjax&config_chk=true",
			onComplete: function(response) {
				if(response.responseText != 'SUCESS')
					$('mailconfchk').style.display = 'block';
				else
					window.location.href = "index.php?module=Webmails&action=index&parenttab=My Home Page";
			}
		}
	);

}

function setSubject(subject)
{
	document.getElementById("subjectsetter").innerHTML=subject
}

function getEmailContents(id)
{
	$("status").style.display="inline";
	var rowid = 'row_'+id;
	getObj(rowid).className = 'emailSelected';
	if(gselectedrowid != 0 && gselectedrowid != id)
	{
		var prev_selected_rowid = 'row_'+gselectedrowid;
		getObj(prev_selected_rowid).className = 'prvPrfHoverOff';
	}
	gselectedrowid = id;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Emails&action=EmailsAjax&file=DetailView&mode=ajax&record='+id,
			onComplete: function(response) {
						$("status").style.display="none";
						$("EmailDetails").innerHTML = response.responseText;
					}
			}
		);
}

function getListViewEntries_js(module,url)
{
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
                	method: 'post',
			postBody: "module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&"+url,
			onComplete: function(response) {
				$("status").style.display="none";
				$("email_con").innerHTML=response.responseText;
				execJS(document.getElementById('email_con'));
			}
		}
	);

}

function massDelete()
{
		var delete_selected_row = false;
        x = document.massdelete.selected_id.length;
        idstring = "";
        if ( x == undefined)
        {

                if (document.massdelete.selected_id.checked)
                {
					if(document.massdelete.selected_id.value == gselectedrowid)
					{
						gselectedrowid = 0;
						delete_selected_row = true;						
					}
                        idstring = document.massdelete.selected_id.value;
						xx = 1;
                }
                else
                {
                        alert("Please select at least one entity");
                        return false;
                }
        }
        else
        {
                xx = 0;
                for(i = 0; i < x ; i++)
                {
                        if(document.massdelete.selected_id[i].checked)
						{
							if(document.massdelete.selected_id[i].value == gselectedrowid)
							{
								gselectedrowid = 0;
								delete_selected_row = true;						
							}
							idstring = document.massdelete.selected_id[i].value +";"+idstring
							xx++
						}
                }
                if (xx != 0)
                {
                        document.massdelete.idlist.value=idstring;
                }
                else
                {
                        alert("Please select at least one entity");
                        return false;
                }
        }
		if(confirm("Are you sure you want to delete the selected "+xx+" records ?"))
		{	
			getObj('search_text').value = '';
			show("status");
			if(!delete_selected_row)
			{
				new Ajax.Request(
						'index.php',
						{queue: {position: 'end', scope: 'command'},
						method: 'post',
						postBody: "module=Users&action=massdelete&folderid="+gFolderid+"&return_module=Emails&idlist="+idstring,
						onComplete: function(response) {
						$("status").style.display="none";
						$("email_con").innerHTML=response.responseText;
						execJS(document.getElementById('email_con'));
						}
						}
						);
			}
			else	
			{
				new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: "module=Users&action=massdelete&folderid="+gFolderid+"&return_module=Emails&idlist="+idstring,
                                onComplete: function(response) {
                                                $("status").style.display="none";
                                                $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                                $("subjectsetter").innerHTML='';
                                                $("email_con").innerHTML=response.responseText;
                                                execJS($('email_con'));
                                }
                        }
                );
			}
		}
		else
		{
			return false;
		}
}

function DeleteEmail(id)
{
	if(confirm("Are you sure you want to delete ?"))
	{	
		getObj('search_text').value = '';
		gselectedrowid = 0;
		$("status").style.display="inline";
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: "module=Users&action=massdelete&return_module=Emails&folderid="+gFolderid+"&idlist="+id,
                                onComplete: function(response) {
                                                $("status").style.display="none";
                                                $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                                $("subjectsetter").innerHTML='';
                                                $("email_con").innerHTML=response.responseText;
                                                execJS($('email_con'));
                                }
                        }
                );
	}
	else
	{
		return false;
	}
}
function Searchfn()
{
	gselectedrowid = 0;
	var osearch_field = document.getElementById('search_field');
	var search_field = osearch_field.options[osearch_field.options.selectedIndex].value;
	var search_text = document.getElementById('search_text').value;
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Emails&action=EmailsAjax&ajax=true&file=ListView&folderid="+gFolderid+"&search=true&search_field="+search_field+"&search_text="+search_text,
                        onComplete: function(response) {
                                        $("status").style.display="none";
                                        $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                        $("subjectsetter").innerHTML='';
                                        $("email_con").innerHTML=response.responseText;
                                        execJS($('email_con'));
                        }
                }
        );
}

