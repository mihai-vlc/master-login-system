<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */


include 'inc/init.php';


$page->title = "Validate account";



if(isset($_GET['username']) && isset($_GET['key'])) {

	if($u = $db->getRow("SELECT `userid` FROM `".MLS_PREFIX."users` WHERE `username` = ?s AND `validated` = ?s", $_GET['username'], $_GET['key'])) { 
		if($db->query("UPDATE `".MLS_PREFIX."users` SET `validated` = '1' WHERE `userid` = ?i", $u->userid)) {
			$page->success = "Your account was successfully activated !";
			$user = new User($db);
			$_SESSION['user'] = $u->userid;
		} else
			$page->error = "Some error camed up !";
	} else 
		$page->error = "Error ! Incorrect key !";
	


}


include 'header.php';


echo "<div class='container'>";


if(isset($page->error))
  $options->fError($page->error);
else if(isset($page->success))
  $options->success($page->success);

echo "<a class='btn btn-primary' href='$set->url'>Start exploring</a>

</div>";




include 'footer.php';

