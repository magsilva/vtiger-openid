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
global $theme,$mod_strings,$current_language,$currentModule,$current_user;
$theme_path = "themes/".$theme."/";
$image_path = $theme_path."images/";
require_once($theme_path."layout_utils.php");
require_once("modules/Calendar/calendarLayout.php");
require_once('include/utils/utils.php');
require_once("modules/Calendar/Calendar.php");
require_once('include/logging.php');
$cal_log =& LoggerManager::getLogger('calendar');
$cal_log->debug("In CalendarAjax file");
$mysel = $_REQUEST['view'];
if($_REQUEST['file'] == 'OpenListView')
{
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty;
	require_once("modules/Calendar/OpenListView.php");
	$smarty->assign("APP",$app_strings);
	$smarty->assign("IMAGE_PATH",$image_path);
	if($_REQUEST['mode'] == '0')
	{
		$activities[0] = getPendingActivities(0);
		$smarty->assign("ACTIVITIES",$activities);
		$smarty->display("upcomingActivities.tpl");
	}
	else if($_REQUEST['mode'] == '1')
	{
		$activities[1] = getPendingActivities(1);
		$smarty->assign("ACTIVITIES",$activities);
		$smarty->display("pendingActivities.tpl");
	}
	die();
}
$calendar_arr = Array();
$calendar_arr['IMAGE_PATH'] = $image_path;
$date_data = array();
if ( isset($_REQUEST['day']))
{
	$date_data['day'] = $_REQUEST['day'];
}
if ( isset($_REQUEST['month']))
{
	$date_data['month'] = $_REQUEST['month'];
}
if ( isset($_REQUEST['week']))
{
	$date_data['week'] = $_REQUEST['week'];
}
if ( isset($_REQUEST['year']))
{
	if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970)
	{
		print("<font color='red'>Sorry, Year must be between 1970 and 2037</font>");
		exit;
	}
	$date_data['year'] = $_REQUEST['year'];
}

		
if(isset($_REQUEST['type']) && ($_REQUEST['type'] !=''))
{
	$type = $_REQUEST['type'];
	$cal_log->debug("type value is:".$type);
	if($type == 'minical')
	{
		$cal_log->debug("going to get mini calendar");
	        $temp_module = $currentModule;
        	$mod_strings = return_module_language($current_language,'Calendar');
	        $currentModule = 'Calendar';
		$calendar_arr['IMAGE_PATH'] = $image_path;
	        $calendar_arr['calendar'] = new Calendar('month',$date_data);
        	$calendar_arr['view'] = 'month';
	        $calendar_arr['size'] = 'small';
		$calendar_arr['calendar']->add_Activities($current_user);
        	calendar_layout($calendar_arr);
	        $mod_strings = return_module_language($current_language,$temp_module);
        	$currentModule = $_REQUEST['module'];
	}
	elseif($type == 'settings')
	{
		$cal_log->debug("going to get calendar Settings");
		require_once('modules/Calendar/calendar_share.php');	
	}
	else
	{
		$subtab = $_REQUEST['subtab']; 
		if(empty($mysel))
		{
			$mysel = 'day';
		}
		$calendar_arr['calendar'] = new Calendar($mysel,$date_data);
		
		if ($mysel == 'day' || $mysel == 'week' || $mysel == 'month' || $mysel == 'year')
		{
			$calendar_arr['calendar']->add_Activities($current_user);
		}
		$calendar_arr['view'] = $mysel;
		if($calendar_arr['calendar']->view == 'day')
			$start_date = $end_date = $calendar_arr['calendar']->date_time->get_formatted_date();
		elseif($calendar_arr['calendar']->view == 'week')
		{
			$start_date = $calendar_arr['calendar']->slices[0];
			$end_date = $calendar_arr['calendar']->slices[6];
		}
		elseif($calendar_arr['calendar']->view == 'month')
		{
			$start_date = $calendar_arr['calendar']->date_time->getThismonthDaysbyIndex(0);
			$end_date = $calendar_arr['calendar']->date_time->getThismonthDaysbyIndex($calendar_arr['calendar']->date_time->daysinmonth - 1);
			$start_date = $start_date->get_formatted_date();
			$end_date = $end_date->get_formatted_date();
		}
		elseif($calendar_arr['calendar']->view == 'year')
		{
			$start_date = $calendar_arr['calendar']->date_time->getThisyearMonthsbyIndex(0);
			$end_date = $calendar_arr['calendar']->date_time->get_first_day_of_changed_year('increment');
			$start_date = $start_date->get_formatted_date();
			$end_date = $end_date->get_formatted_date();
		}
		else
		{
			die("view:".$calendar_arr['calendar']->view." is not defined");
		}
		
		if($type == 'change_owner' || $type == 'activity_delete' || $type == 'change_status' || $type == 'activity_postpone')
		{
			if($type == 'change_status')
			{
				$return_id = $_REQUEST['record'];
				if(isset($_REQUEST['status']))
				{
					$status = $_REQUEST['status'];
					$activity_type = "Task";
				}
				elseif(isset($_REQUEST['eventstatus']))
				{
					$status = $_REQUEST['eventstatus'];
					$activity_type = "Events";
				}
				ChangeStatus($status,$return_id,$activity_type);
			}
			if($type == 'activity_postpone')
			{
			}
			if(isset($_REQUEST['viewOption']) && $_REQUEST['viewOption'] != null && $subtab == 'event')
			{
				if($_REQUEST['viewOption'] == 'hourview')
				{
					$cal_log->debug("going to get calendar Event HourView");
					if($calendar_arr['view'] == 'day')
					{
						echo getDayViewLayout($calendar_arr);
					}
					elseif($calendar_arr['view'] == 'week')
					{
						echo getWeekViewLayout($calendar_arr);	
					}
					elseif($calendar_arr['view'] == 'month')
					{
						echo getMonthViewLayout($calendar_arr);
					}
					elseif($calendar_arr['view'] == 'year')
					{
						echo getYearViewLayout($calendar_arr);
					}
					else
					{
						die("view:".$view['view']." is not defined");
					}
				}
				elseif($_REQUEST['viewOption'] == 'listview')
				{
					$cal_log->debug("going to get calendar Event ListView");
					//To get Events List
					$activity_list = getEventList($calendar_arr, $start_date, $end_date);
					echo constructEventListView($calendar_arr,$activity_list);
				}
			}
			elseif($subtab == 'todo')
			{
				$cal_log->debug("going to get calendar Todo ListView");
				//To get Todos List
				$todo_list = getTodoList($calendar_arr, $start_date, $end_date);
				echo constructTodoListView($todo_list,$calendar_arr,$subtab);
			}
		}
		elseif($type == 'view')
		{
			require_once('modules/Calendar/'.$_REQUEST['file'].'.php');
		}
		else
		{
			die("View option is not defined");
		}
	}
}
else
{
	require_once('include/Ajax/CommonAjax.php');
	//die("type is not set");
}

?>
