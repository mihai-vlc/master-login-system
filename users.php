<?php

include "inc/init.php";

$page->title = "Users of ". $set->site_name;

$presets->setActive("userslist"); // we highlith the home link


$content = ''; // will store the html code for users list

$data = $db->select("SELECT * FROM `".MUS_PREFIX."users`");
foreach($data as $u) {
	$content .= "<li class='span5 clearfix'>
  <div class='thumbnail clearfix'>
	<a href='$set->url/profile.php?u=$u->userid'><img src='".$user->getAvatar($u->userid)."' alt='".$options->html($u->username)."' class='pull-left clearfix' style='margin-right:10px'>
    <div class='caption' class='pull-left'>
      <h4>      
	      <a href='$set->url/profile.php?u=$u->userid'>".$options->html($u->username)."</a>
      </h4>
      <small><b>Last seen: </b> 3 sec ago</small>
        
      
    </div>
  </div>
</li>";
}


include 'header.php';


echo "
<div class='container'>
	<ul class='thumbnails'>
		$content
	</ul>
</div>";


include 'footer.php';