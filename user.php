<?php
include "inc/init.php";

if(!$user->islg()){
	header("Location: ".$set->url);
	exit;
}

if(isset($_GET['id']) && $user->group->canedit && $user->exists($_GET['id'])) {
	$uid = (int)$_GET['id'];
	$can_edit = 1;
}else{
	$uid = $user->data->userid;
	$can_edit = 0;
}
$u = $db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `userid` = '$uid'");


$page->title = "Edit info of ". $options->html($u->username);

if($_POST) {
	if(isset($_GET['password']) && ($user->data->userid == $u->userid)) {
		$opass = $_POST['oldpass'];
		$npass = $_POST['newpass'];
		$npass2 = $_POST['newpass2'];
		if($db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `userid` = '".$u->userid."' 
				AND `password` = '".sha1($opass)."'")) {

			if(!isset($npass[3]) || isset($npass[30]))
				$page->error = "Password too short or too long !";
			else if($npass != $npass2)
				$page->error = "New passwords don't match !";
			else
				if($db->query("UPDATE `".MUS_PREFIX."users` SET `password` = '".sha1($npass)."' WHERE `userid` = '".$u->userid."'"))
					$page->success = "Password updated successfully !";

		} else 
		  $page->error = 'Invalid password !';

	} else {
      
      	$email = $_POST['email'];


      	$extra = '';
      	if($can_edit) {
	      	$username = $_POST['username'];
	      	$password = $_POST['password'];
	      	if(isset($_POST['groupid']))
		      	$groupid = $_POST['groupid'];


	      	$extra = ", `username` = '".$db->escape($username)."'";

	      	if($user->isAdmin())
	      		$extra .= ", `groupid` = '".$db->escape($groupid)."'";

	      	if(!empty($password))
	      		$extra .= ", `password` = '".sha1($password)."'";

			if(!isset($username[3]) || isset($username[30]))
			    $page->error = "Username too short or too long !";

			if($user->isAdmin() && !$db->get_row("SELECT `groupid` FROM `".MUS_PREFIX."groups` WHERE `groupid` = '".(int)$groupid."'"))
				$page->error = "The group is invalid !";
		}


	  	if(!$options->isValidMail($email)) 
	    	$page->error = "Email address is not valid.";
	  

	  	if(!isset($page->error) && $db->query("UPDATE `".MUS_PREFIX."users` SET `email` = '".$db->escape($email)."' $extra WHERE `userid` = '".$u->userid."'")) {
	  		$page->success = "Info was saved !";
	  		// we make sure we show updated data
			$u = $db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `userid` = '$u->userid'");
	  	}
	}
}

include 'header.php';


echo "
<div class=\"container\"><div class='span6'>";


if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success))
  $options->success($page->success);


if(isset($_GET['password']) && ($user->data->userid == $u->userid)) { 
// we use this option only for personal profile
// because you need to know the old password
	echo "<form class='form-horizontal well' action='#' method='post'>
		        <fieldset>
		            <legend>Change Password</legend>

		            <div class='control-group'>
		              <div class='control-label'>
		                <label>Old Password</label>
		              </div>
		              <div class='controls'>
		                <input type='password' name='oldpass' class='input-large'>
		              </div>
		            </div>
		            <div class='control-group'>
		              <div class='control-label'>
		                <label>New Password</label>
		              </div>
		              <div class='controls'>
		                <input type='password' name='newpass' class='input-large'>
		              </div>
		            </div>
		            <div class='control-group'>
		              <div class='control-label'>
		                <label>New Password Again</label>
		              </div>
		              <div class='controls'>
		                <input type='password' name='newpass2' class='input-large'>
		              </div>
		            </div>

		            <div class='control-group'>
		              <div class='controls'>
		              <button type='submit' id='submit' class='btn btn-primary'>Save</button>
		              </div>
		            </div>
		          </fieldset>
		    </form>
		    <a href='?'>Edit Info</a>";	

} else {

	echo "<form class='form-horizontal well' action='#' method='post'>
		        <fieldset>
		            <legend>Edit info of ".$options->html($u->username)."</legend>";

if($can_edit) {

	$groups = $db->select("SELECT * FROM `".MUS_PREFIX."groups` ORDER BY `type`,`priority`");


	// get the groups available
	$show_groups = '';
	foreach($groups as $group)
		if($group->groupid != 1)
			if($group->groupid == $u->groupid)
				$show_groups .= "<option value='$group->groupid' selected='1'>".$group->name."</option>";
			else
				$show_groups .= "<option value='$group->groupid'>".$group->name."</option>";

	echo "
	    <div class='control-group'>
	      <div class='control-label'>
	        <label>Username</label>
	      </div>
	      <div class='controls'>
	        <input type='text' name='username' class='input-large' value='".$options->html($u->username)."'>
	      </div>
	    </div>

	    <div class='control-group'>
	      <div class='control-label'>
	        <label>Password</label>
	      </div>
	      <div class='controls'>
	        <input type='text' name='password' class='input-large'><br/>
	        <small>Leave blank if you don't want to change</small>
	      </div>
	    </div>

		<div class='control-group'>
		  <label class='control-label' for='selectbasic'>Group: </label>
		  <div class='controls'>
		    <select id='selectbasic' name='groupid' class='input-xlarge' ".($user->isAdmin() ? "" : "disabled='disabled'").">
				$show_groups	      
		    </select>
		  </div>
		</div> 
	";


}



echo "
        <div class='control-group'>
          <div class='control-label'>
            <label>Email</label>
          </div>
          <div class='controls'>
            <input type='text' name='email' class='input-large' value='".$options->html($u->email)."'>
          </div>
        </div>

        <div class='control-group'>
          <div class='controls'>
          <button type='submit' id='submit' class='btn btn-primary'>Save</button>
          </div>
        </div>
      </fieldset>
</form>";
if(!$can_edit)
	echo "<a href='?password=1'>Change Password</a>";


}

echo "</div>
	</div><!-- /container -->";
include 'footer.php';