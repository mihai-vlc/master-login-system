<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */



include "inc/init.php";


if(!$user->islg()) {
	header("Location: $set->url");
	exit;
}


$page->title = "Privacy Settings";



if($_POST) {

	$data = $db->getRow("SELECT * FROM `".MLS_PREFIX."privacy` WHERE `userid` = ?i", $user->data->userid);

	$columns = get_object_vars($data);
	
	$sql = "UPDATE `".MLS_PREFIX."privacy` SET ";
	foreach ($columns as $k => $v)
		if(($k != 'userid') && in_array($_POST[$k], array(1,0))) // we make sure the received value is 0 or 1
			$sql .= $db->parse(" ?n = ?s,", $k, $_POST[$k]);

	$sql = trim($sql,",").$db->parse(" WHERE `userid` = ?i", $user->data->userid);

	if($db->query(" ?p",$sql))
		$page->success = "Settings saved !";
	else
		$page->error = "Some error camed up ! ";

}




include 'header.php';


echo "<div class='container'>

<form class='form-horizontal' method='post' action='?'>
<fieldset>

<legend>Privacy Settings</legend>";

if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success))
  $options->success($page->success);


$data = $db->getRow("SELECT * FROM `".MLS_PREFIX."privacy` WHERE `userid` = ?i", $user->data->userid);

$columns = get_object_vars($data);





foreach($columns as $k => $v)
	if($k != 'userid')
		echo "<div class='control-group'>
		  <label class='control-label' for='".$options->html($k)."'>".$options->prettyPrint($options->html($k))."</label>
		  <div class='controls'>
		    <select id='".$options->html($k)."' name='".$options->html($k)."' class='input-xlarge'>
		      <option value='0' ".($v == 0 ? "selected='1'" : "").">Private</option>
		      <option value='1' ".($v == 1 ? "selected='1'" : "").">Public</option>
		    </select>
		  </div>
		</div>";




echo "

<div class='control-group'>
  <div class='controls'>
    <button type='submit' class='btn btn-primary'>Save</button>
  </div>
</div>



</fieldset>
</form>




</div>";



include 'footer.php';