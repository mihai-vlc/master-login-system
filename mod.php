<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */



include "inc/init.php";


if(!isset($_GET['act']) || !isset($_GET['id']) || (!$user->exists($_GET['id'])) || !($user->hasPrivilege($_GET['id']))) {
	header("Location: ". $set->url);
	exit;
}

$u = $user->grabData($_GET['id']);


$page->title = "Moderator Panel";


$act = $_GET['act'];

$show_content = '';




if(($act == 'ban') && $user->group->canban && ($user->data->userid != $u->userid)) { 

	if($_POST) {
		$period = $_POST['period'];
		$reason = $_POST['reason'];
		if(($period > 0 && $period <= $set->max_ban_period) && isset($reason[5])) {
			$period *= 3600*24; // convert it into seconds 
			$db->query("UPDATE `".MLS_PREFIX."users` SET `banned` = '1' WHERE `userid` = '$u->userid'");
			$db->query("INSERT INTO `".MLS_PREFIX."banned` SET `userid` = ?i, `by` = ?i, `until` = ?i, `reason` = ?s", $u->userid, $user->data->userid, time()+$period, $reason);
			$page->success = "User has been banned successfully for ".(int)$_POST['period']." day(s) ! ";
		} else {
			$page->error = "Invalid period or reason !";
		}

	} else {
		$ban_options = '';
		for($i = 1; $i <= $set->max_ban_period; $i++)
			$ban_options .= "<option value='$i'>$i day".($i == 1 ? '' : 's')."</option>";

		$show_content = "
			<form class='well form-horizontal' action='#' method='post'>
			<fieldset>

			<!-- Form Name -->
			<legend>Ban ".$options->html($u->username)."</legend>

			<!-- Select Basic -->
			<div class='control-group'>
			  <label class='control-label' for='period'>Period</label>
			  <div class='controls'>
			    <select id='period' name='period' class='input-xlarge'>
			    	
			    	$ban_options

			    </select>
			  </div>
			</div>

			<div class='control-group'>
			  <label class='control-label' for='reason'>Reason</label>
			  <div class='controls'>
			    <input type='text' id='reason' name='reason'>
			  </div>
			</div>

			<!-- Button -->
			<div class='control-group'>
			  <label class='control-label' for='submit'></label>
			  <div class='controls'>
			    <button id='submit' name='submit' class='btn btn-primary'>Ban</button>
			  </div>
			</div>

			</fieldset>
			</form>


		";

		// if he is already banned we show the unban option
		if($u->banned) {
			$banned = $user->getBan($u->userid);
			$show_content = "
			<form class='well form-horizontal' action='?act=unban&id=$u->userid' method='post'>
			<fieldset>

			<!-- Form Name -->
			<legend>UnBan ".$options->html($u->username)."</legend>
			".$options->info("This user was banned by <a href='$set->url/profile.php?u=$banned->by'>".$user->showName($banned->by)."</a> for `<i>".$options->html($banned->reason)."</i>`.",1)."
			<!-- Button -->
			<div class='control-group'>
			  <label class='control-label' for='submit'></label>
			  <div class='controls'>
			    <button id='submit' name='submit' class='btn btn-primary'>UnBan</button>
			  </div>
			</div>

			</fieldset>
			</form>
			";
		}



	}
} else if(($act == 'unban') && $user->group->canban) {
	$db->query("UPDATE `".MLS_PREFIX."users` SET `banned` = '0' WHERE `userid` = ?i", $u->userid);
	$db->query("DELETE FROM `".MLS_PREFIX."banned` WHERE `userid` = ?i", $u->userid);
	header("Location: ". $set->url."/profile.php?u=$u->userid");
	exit;
} else if(($act == 'avt') && $user->group->canhideavt) {
	if($u->showavt == 0){
		if($db->query("UPDATE `".MLS_PREFIX."users` SET `showavt` = '1' WHERE `userid` = ?i", $u->userid))
			$_SESSION['success'] = 'Avatar showed successfully !';
	} else
		if($db->query("UPDATE `".MLS_PREFIX."users` SET `showavt` = '0' WHERE `userid` = ?i", $u->userid))
			$_SESSION['success'] = 'Avatar hidden successfully !';

	header("Location: ". $set->url."/profile.php?u=$u->userid");
	exit;
} else if(($act == 'del') && $user->isAdmin() && ($user->data->userid != $u->userid)) {

	if($_POST) { // we make sure that the users is deleted from all tables
		$db->query("DELETE FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $u->userid);
		$db->query("DELETE FROM `".MLS_PREFIX."privacy` WHERE `userid` = ?i", $u->userid);

		$page->success = "You have deleted the user ".$options->html($u->username);

	} else {
		$show_content = "
			<form class='well form-horizontal' action='?act=del&id=$u->userid' method='post'>
			<fieldset>


			<legend>Delete ".$options->html($u->username)."</legend>
			".$options->error("You are about to DELETE ".$user->showName($u->userid).". Are you sure ?",1)."



			<div class='control-group'>
			  <label class='control-label' for='submit'></label>
			  <div class='controls'>
			    <button id='submit' name='submit' class='btn btn-primary'>Yes DELETE</button> <a href='$set->url/profile.php?u=$u->userid' class='btn'>Cancel</a>
			  </div>
			</div>

			</fieldset>
			</form>";
	}

} else {
	header("Location: ". $set->url."/profile.php?u=$u->userid");
	exit;
}



include 'header.php';


echo "
<div class='container'>
<h3>Moderator Panel</h3>
<hr>
";

if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success))
  $options->success($page->success);

echo "
$show_content
<br/> <a href='$set->url/profile.php?u=$u->userid' class='btn btn-primary'>Back to profile</a>
</div>";



include 'footer.php';