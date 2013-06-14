<?php

// user profile

include 'inc/init.php';


$uid = (int)$_GET["u"];


if(!($u = $db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `userid`='$uid'"))){
	$page->error = "User doesn't exists or it was deleted !";
	$u = new stdClass();
	$u->username = 'Guest';
}

$page->title = "Profile of ". $u->username;


include 'header.php';


if(isset($page->error)) 
  $options->fError($page->error);

echo "<div class='container'>
	<h3>Profile of ".$options->html($u->username)."</h3>
	<hr>
		
		<div class='thumbnail span'>
			<img src='".$user->getAvatar($u->userid)."' alt='".$options->html($u->username)."'>
			<div style='text-align:center;'><b>".$options->html($u->username)."</b></div>
		</div>
		<span style='margin:10px;'> Last seen: ".$options->tsince($u->lastactive)."</span>

</div>";


include 'footer.php';