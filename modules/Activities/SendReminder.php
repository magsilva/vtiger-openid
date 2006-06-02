<?php
////////////////////////////////////////////////////
// PHPMailer - PHP email class
//
// Class for sending email using either
// sendmail, PHP mail(), or SMTP.  Methods are
// based upon the standard AspEmail(tm) classes.
//
// Copyright (C) 2001 - 2003  Brent R. Matzelle
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * PHPMailer - PHP email transport class
 * @package PHPMailer
 * @author Brent R. Matzelle
 * @copyright 2001 - 2003 Brent R. Matzelle
 */


//file modified by shankar

require("modules/Emails/class.phpmailer.php");
require("include/database/PearDatabase.php");
require("config.php");

// Get the list of activity for which reminder needs to be sent

global $adb;

// Select the events with reminder
//$query="select crmentity.crmid,activity.*,activity_reminder.* from activity inner join crmentity on crmentity.crmid=activity.activityid inner join activity_reminder on activity.activityid=activity_reminder.activity_id where ".$adb->getDBDateString("activity.date_start")." >= '".date('Y-m-d')."' and crmentity.crmid != 0 and activity.eventstatus = 'Planned' and activity_reminder.reminder_sent = 0;";


$query="select crmentity.crmid,activity.*,activity_reminder.*,recurringevents.activityid,recurringevents.recurringdate,recurringevents.recurringtype from activity inner join crmentity on crmentity.crmid=activity.activityid inner join activity_reminder on activity.activityid=activity_reminder.activity_id left join recurringevents on activity.activityid=recurringevents.activityid where '".date('Y-m-d')."' between ".$adb->getDBDateString("activity.date_start")." and ". $adb->getDBDateString("activity.due_date") ." and crmentity.crmid != 0 and activity.eventstatus = 'Planned' and activity_reminder.reminder_sent = 0;";

$result = $adb->query($query);

if($adb->num_rows($result) >= 1)
{
	while($result_set = $adb->fetch_array($result))
	{
		$date_start = $result_set['date_start'];
		$time_start = $result_set['time_start'];
		$reminder_time = $result_set['reminder_time'];
	        $curr_time = strtotime(date("Y-m-d H:i"))/60;
		$activity_id = $result_set['activityid'];
		$activitymode = ($result_set['activitytype'] == "Task")?"Task":"Events";
		$to_addr='';

		$recur_id = $result_set['recurringid'];
		$current_date=date('Y-m-d');
		
		if($recur_id == 0)
		{
			//echo "<h1>if $recur_id</h1>";
			$date_start = $result_set['date_start'];
		}
		elseif( ($recur_id !=0) )
		{
			//echo "<h1>else $recur_id</h1>";
			$date_start = $result_set['recurringdate'];
			$st=explode("-",$date_start);	
			$dateDiff = mktime(0,0,0,$st[1],$st[2],$st[0]) - mktime(0,0,0,'m','d','Y');
			$days = floor($dateDiff/60/60/24)+1; //to calculate no of. days
			
			if($days != 0)
				break;
			
		}

	        $activity_time = strtotime(date("$date_start $time_start"))/60;
	
		if (($activity_time - $curr_time) > 0 && ($activity_time - $curr_time) == $reminder_time)
		{
			$query_user="SELECT users.email1,salesmanactivityrel.smid FROM salesmanactivityrel inner join users on users.id=salesmanactivityrel.smid where salesmanactivityrel.activityid =".$activity_id." and users.deleted=0"; 
			$user_result = $adb->query($query_user);		
			if($adb->num_rows($user_result)>=1)
			{
				while($user_result_row = $adb->fetch_array($user_result))
				{
					if($user_result_row['email1']!='' || $user_result_row['email1'] !=NULL)
					{
						$to_addr[] = $user_result_row['email1'];
					}
				}
			}
		
			$query_cnt="SELECT contactdetails.email,cntactivityrel.contactid,crmentity.crmid FROM cntactivityrel inner join contactdetails on contactdetails.contactid=cntactivityrel.contactid inner join crmentity on crmentity.crmid=cntactivityrel.contactid where cntactivityrel.activityid =".$activity_id." and crmentity.deleted=0"; 
			$cnt_result = $adb->query($query_cnt);
			if($adb->num_rows($cnt_result)>=1)
			{
				while($cnt_result_row = $adb->fetch_array($cnt_result))
				{
					if($cnt_result_row['email']!='' || $cnt_result_row['email'] !=NULL)
					{
						$to_addr[] = $cnt_result_row['email'];
					}
				}
			}
			
			// Set the preferred email id
			$from ="reminders@localserver.com";
			
			// Retriving the Subject and message from reminder table		
			$sql = "select active,notificationsubject,notificationbody from notificationscheduler where schedulednotificationid=1";
			$result_main = $adb->query($sql);

			$subject = "[Reminder:".$result_set['activitytype']." @ ".$result_set['date_start']." ".$result_set['time_start']."] ".$adb->query_result($result_main,0,'notificationsubject');

			//Set the mail body/contents here
			#$contents ="Hi,\n\n This a activity reminder mail. Kindly visit the link for more details of the activity <a href='".$site_URL."/index.php?action=DetailView&module=Activities&record=".$activity_id."&activity_mode=".$activitymode."'>Click here</a>\n\n Regards,\n Reminder Manager";
			$contents = nl2br($adb->query_result($result_main,0,'notificationbody')) ."\n\n Kindly visit the link for more details on the activity <a href='".$site_URL."/index.php?action=DetailView&module=Activities&record=".$activity_id."&activity_mode=".$activitymode."'>Click here</a>";

			if(count($to_addr) >=1)
			{
				send_mail($to_addr,$from,$subject,$contents,$mail_server,$mail_server_username,$mail_server_password);
				$upd_query = "UPDATE activity_reminder SET reminder_sent=1 where activity_id=".$activity_id;
				$adb->query($upd_query);
				
			}
		//$parentmailid = getParentMailId($_REQUEST['return_module'],$_REQUEST['parent_id']);
		}
	}
}

function send_mail($to,$from,$subject,$contents,$mail_server,$mail_server_username,$mail_server_password)
{
	global $adb;
	global $root_directory;

	$mail = new PHPMailer();


	$mail->Subject = $subject;
	$mail->Body    = nl2br($contents);//"This is the HTML message body <b>in bold!</b>";


	$mail->IsSMTP();                                      // set mailer to use SMTP
	//$mail->Host = "smtp1.example.com;smtp2.example.com";  // specify main and backup server

	if($mail_server=='')
	{
		$mailserverresult=$adb->query("select * from systems where server_type='email'");
		$mail_server=$adb->query_result($mailserverresult,0,'server');
		$_REQUEST['server']=$mail_server;
	}	

	$mail->Host = $mail_server;  // specify main and backup server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = $mail_server_username ;//$smtp_username;  // SMTP username
	$mail->Password = $mail_server_password ;//$smtp_password; // SMTP password
	$mail->From = $from;
	$mail->FromName = $initialfrom;
	
	foreach($to as $pos=>$addr)
	{
		$mail->AddAddress($addr);                  // name is optional
	}
	//$mail->AddReplyTo($from);
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters

	$mail->IsHTML(true);                                  // set email format to HTML
	
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	$flag = MailSend($mail);

}

function MailSend($mail)
{
        if(!$mail->Send())
        {
$vtlog->logthis("error in sending mail ",'fatal');  	
           $msg = $mail->ErrorInfo;
           //header("Location: index.php?action=$returnaction&module=".$_REQUEST['return_module']."&parent_id=$parent_id&record=".$_REQUEST['return_id']."&filename=$filename&message=$msg");
        }
	else 
	{
			$vtlog->logthis("mail sent successfully! ",'info');  	
			return true;
	}
}

function getParentMailId($returnmodule,$parentid)
{
	global $adb;
        if($returnmodule == 'Leads')
        {
                $tablename = 'leaddetails';
                $idname = 'leadid';
        }
        if($returnmodule == 'Contacts' || $returnmodule == 'HelpDesk')
        {
		if($returnmodule == 'HelpDesk')
			$parentid = $_REQUEST['contact_id'];
                $tablename = 'contactdetails';
                $idname = 'contactid';
        }
	if($parentid != '')
	{
	        $query = 'select * from '.$tablename.' where '.$idname.' = '.$parentid;
	        $mailid = $adb->query_result($adb->query($query),0,'email');
	}
        if($mailid == '' && $returnmodule =='Contacts')
        {
                $mailid = $adb->query_result($adb->query($query),0,'otheremail');
                if($mailid == '')
                        $mailid = $adb->query_result($adb->query($query),0,'yahooid');
        }

$vtlog->logthis("mailid is  ".$mailid,'debug');  	
	return $mailid;
}

?>
