<?php

include "inc/init.php";
include 'lib/pagination.class.php';


$page->title = "Users of ". $set->site_name;

$presets->setActive("userslist"); // we highlight the home link


$content = ''; // will store the html code for users list

if(!isset($_GET['page']))
  $_GET['page'] = 1;


$sort_name = array("id", "name");

// sorting
if(!isset($_GET['sort']) || !in_array($_GET['sort'], array(0,1)))  // check if it's a valid sort option
  $sort = 0;
else
  $sort = (int)$_GET['sort'];

if(!isset($_GET['sort_type']) || !in_array($_GET['sort_type'], array(0,1)))
  $sort_type = 0;
else
  $sort_type = (int)$_GET['sort_type'];


if($sort == 1) {
  $order_by = "`username` ". (!$sort_type ? "ASC" : "DESC");
} else {
  $order_by = "`userid` ". (!$sort_type ? "ASC" : "DESC");
}

$show_sort_options = '';
foreach ($sort_name as $k => $v) {
  if($k != $sort)
    $show_sort_options .= "<li><a href='?sort=$k'>Sort by $v</a></li>";
}



// pagination
$page_number = (int)$_GET['page'] <= 0 ? 1 : (int)$_GET['page']; // grab the page number

$perpage = 20; // number of elements perpage

$total_results = $db->count("SELECT * FROM `".MUS_PREFIX."users`");

if($page_number > ceil($total_results/$perpage))
  $page_number = ceil($total_results/$perpage);


$start = ($page_number - 1) * $perpage;

$data = $db->select("SELECT * FROM `".MUS_PREFIX."users` ORDER BY $order_by LIMIT $start,$perpage");

$pagination = new pagination($total_results, $page_number, $perpage);






foreach($data as $u) {
	$content .= "<li class='span5 clearfix'>
  <div class='thumbnail clearfix'>
	<a href='$set->url/profile.php?u=$u->userid'><img src='".$user->getAvatar($u->userid)."' width='80' alt='".$options->html($u->username)."' class='pull-left clearfix' style='margin-right:10px'>
    <div class='caption' class='pull-left'>
      <h4>      
	      <a href='$set->url/profile.php?u=$u->userid'>".$user->showName($u->userid)."</a>
      </h4>
      <small><b>Last seen: </b> ".$options->tsince($u->lastactive)."</small>
      
      </div>
    </div>
  </li>";
}





include 'header.php';


echo "
<div class='container'>

  <h3 class='pull-left'>Users on ".$set->site_name."</h3>
  <div class='btn-group pull-right'>
    <a class='btn btn' href='?sort=$sort&sort_type=".(!$sort_type ? 1 : 0)."'><i class='icon-chevron-".(!$sort_type ? 'up' : 'down')."'></i> Sort by ".$sort_name[$sort]."</a>
    <a class='btn btn dropdown-toggle' data-toggle='dropdown' href='#'><span class='caret'></span></a>
    <ul class='dropdown-menu'>
      $show_sort_options
    </ul>
  </div>
  <div class='clearfix'></div>
  <small>Showing ".($start+1)."-".($start+count($data))." out of ".$total_results."</small>
  <hr>

	<ul class='thumbnails'>
		$content
	</ul>
$pagination->pages
</div>";


include 'footer.php';