<?php
include "inc/init.php";

if(!$user->islg())
	header("Location: ".$set->url);

$page->title = "Edit info to ". $set->site_name;

if($_POST) {
	if(isset($_GET['password'])) {
		$opass = $_POST['oldpass'];
		$npass = $_POST['newpass'];
		if($db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `userid` = '".$_SESSION['user']."' 
				AND `password` = '".sha1($opass)."'")) {
			if(!isset($npass[3]) || isset($npass[30]))
				$page->error = "Password too short or too long !";
			else
				if($db->query("UPDATE `".MUS_PREFIX."users` SET `password` = '".sha1($npass)."' WHERE `userid` = '".$_SESSION['user']."'"))
					$page->success = "Password updated successfully !";

		} else 
		  $page->error = 'Invalid password !';

	} else {
      
      $email = $_POST['email'];
	  
	  if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) 
	    $page->error = "Email address is not valid.";
	  else 
	  	if($db->query("UPDATE `".MUS_PREFIX."users` SET `email` = '".$db->escape($email)."' WHERE `userid` = '".$_SESSION['user']."'")) {
	  		$page->success = "Your info was saved !";
	  		$user->filter->email = htmlentities($email, ENT_QUOTES);
	  	}
	}
}

include 'header.php';


echo "
<div class=\"container\"><div class='span6'>";


if(isset($page->error))
  echo "<div class=\"alert alert-error\">".$page->error."</div>";
else if(isset($page->success))
  echo "<div class=\"alert alert-success\">".$page->success."</div>";


if(isset($_GET['password'])) {
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
		            <legend>Edit Info</legend>

		            <div class='control-group'>
		              <div class='control-label'>
		                <label>Email</label>
		              </div>
		              <div class='controls'>
		                <input type='text' name='email' class='input-large' value='".$user->filter->email."'>
		              </div>
		            </div>

		            <div class='control-group'>
		              <div class='controls'>
		              <button type='submit' id='submit' class='btn btn-primary'>Save</button>
		              </div>
		            </div>
		          </fieldset>
		    </form>
		    <a href='?password=1'>Change Password</a>
		    ";
}

echo "</div>
	</div><!-- /container -->";
include 'footer.php';