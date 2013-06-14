<?php

include "inc/init.php";
include 'lib/pagination.class.php';


$page->title = "Users of ". $set->site_name;

$presets->setActive("userslist"); // we highlith the home link


$content = ''; // will store the html code for users list

if(!isset($_GET['page']))
  $_GET['page'] = 1;


$page_number = (int)$_GET['page'] <= 0 ? 1 : (int)$_GET['page']; // grab the page number
$perpage = 20;

$total_results = $db->count("SELECT * FROM `".MUS_PREFIX."users`");

$start = ($page_number - 1) * $perpage;

$data = $db->select("SELECT * FROM `".MUS_PREFIX."users` LIMIT $start,$perpage");


$pagination = new pagination($total_results, $page_number, $perpage);


foreach($data as $u) {
	$content .= "<li class='span5 clearfix'>
  <div class='thumbnail clearfix'>
	<a href='$set->url/profile.php?u=$u->userid'><img src='".$user->getAvatar($u->userid)."' alt='".$options->html($u->username)."' class='pull-left clearfix' style='margin-right:10px'>
    <div class='caption' class='pull-left'>
      <h4>      
	      <a href='$set->url/profile.php?u=$u->userid'>".$options->html($u->username)."</a>
      </h4>
      <small><b>Last seen: </b> ".$options->tsince($u->lastactive)."</small>
      
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
$pagination->pages
</div>";


include 'footer.php';