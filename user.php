<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */




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
$u = $db->getRow("SELECT * FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $uid);


$page->title = "Edit info of ". $options->html($u->username);

if($_POST) {
	if(isset($_GET['password']) && ($user->data->userid == $u->userid)) {
		$opass = $_POST['oldpass'];
		$npass = $_POST['newpass'];
		$npass2 = $_POST['newpass2'];
		if($db->getRow("SELECT `userid` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i AND `password` = ?s", $u->userid, sha1($opass))) {

			if(!isset($npass[3]) || isset($npass[30]))
				$page->error = "Password too short or too long !";
			else if($npass != $npass2)
				$page->error = "New passwords don't match !";
			else
				if($db->query("UPDATE `".MLS_PREFIX."users` SET `password` = ?s WHERE `userid` = ?i", sha1($npass), $u->userid))
					$page->success = "Password updated successfully !";

		} else 
		  $page->error = 'Invalid password !';

	} else {
      
      	$email = $_POST['email'];
      	$display_name = $_POST['display_name'];


      	$extra = '';
      	if($can_edit) {
	      	$username = $_POST['username'];
	      	$password = $_POST['password'];
	      	if(isset($_POST['groupid']))
		      	$groupid = $_POST['groupid'];

	      	$extra = $db->parse(", `username` = ?s", $username);

	      	if($user->isAdmin())
	      		$extra .= $db->parse(", `groupid` = ?i", $groupid);

	      	if(!empty($password))
	      		$extra .= $db->parse(", `password` = ?s", sha1($password));

			if(!isset($username[3]) || isset($username[30]))
			    $page->error = "Username too short or too long !";

			if(!$options->validUsername($username))
				$page->error = "Invalid username !";

			if($user->isAdmin() && !$db->getRow("SELECT `groupid` FROM `".MLS_PREFIX."groups` WHERE `groupid` = ?i", $groupid))
				$page->error = "The group is invalid !";
		}


	  	if(!$options->isValidMail($email)) 
	    	$page->error = "Email address is not valid.";
	    
	    if(!isset($display_name[3]) || isset($display_name[50]))
		    $page->error = "Display name too short or too long !";	  

	  	if(!isset($page->error) && $db->query("UPDATE `".MLS_PREFIX."users` SET `email` = ?s, `display_name` = ?s ?p WHERE `userid` = ?i", $email, $display_name, $extra, $u->userid)) {
	  		$page->success = "Info was saved !";
	  		// we make sure we show updated data
			$u = $db->getRow("SELECT * FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $u->userid);
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

	$groups = $db->getAll("SELECT * FROM `".MLS_PREFIX."groups` ORDER BY `type`,`priority`");


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
            <label>Display name</label>
          </div>
          <div class='controls'>
            <input type='text' name='display_name' class='input-large' value='".$options->html($u->display_name)."'>
          </div>
        </div>

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