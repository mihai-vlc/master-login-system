<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */


include "inc/init.php";
include 'lib/pagination.class.php';


$page->title = "Users of ". $set->site_name;

$presets->setActive("userslist"); // we highlight the home link


$content = ''; // will store the html code for users list

if(!isset($_GET['page']))
  $page_number = 1;


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



// search

$where = '';

if(isset($_GET['q'])) 
  $where = $db->parse("WHERE `username` LIKE ?s", '%'.$_GET['q'].'%');


if($total_results = $db->getRow("SELECT COUNT(*) as count FROM `".MLS_PREFIX."users` ?p", $where)->count) {

    // pagination
    if(!isset($page_number))
      $page_number = (int)$_GET['page'] <= 0 ? 1 : (int)$_GET['page']; // grab the page number

    $perpage = 10; // number of elements perpage


    if($page_number > ceil($total_results/$perpage))
      $page_number = ceil($total_results/$perpage);


    $start = ($page_number - 1) * $perpage;

    $data = $db->getAll("SELECT * FROM `".MLS_PREFIX."users` ?p ORDER BY ?p LIMIT ?i,?i", $where, $order_by, $start, $perpage);


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
} else
  $page->error = "No results were found !";




include 'header.php';



echo "
<div class='container'>

  <h3 class='pull-left'>Users on ".$set->site_name."</h3>

  <form class='form-search' action='?'>
    <div class='input-append pull-right'>
      <input class='span2 search-query' name='q' type='text' ".( isset($_GET['q']) ? "value='".$options->html($_GET['q'])."'" : "" )." placeholder='Search...'/>
      <button type='submit' class='btn'><i class='icon-search'></i></button>

      ".$options->queryString("hidden", array("q","page"))."
    </div>
  </form>
  <div class='clearfix'></div>

  <div class='btn-group pull-right'>
    <a class='btn btn' href='?sort=$sort&sort_type=".(!$sort_type ? 1 : 0)."'><i class='icon-chevron-".(!$sort_type ? 'up' : 'down')."'></i> Sort by ".$sort_name[$sort]."</a>
    <a class='btn btn dropdown-toggle' data-toggle='dropdown' href='#'><span class='caret'></span></a>
    <ul class='dropdown-menu'>
      $show_sort_options
    </ul>
  </div>
  <div class='clearfix'></div>";

  if(isset($data))
    echo "<small>Showing ".($start+1)."-".($start+count($data))." out of ".$total_results."</small><hr>";
  else
    echo "<hr>";


if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success))
  $options->success($page->success);


echo "
  <ul class='thumbnails'>
		$content
	</ul>
".(isset($pagination) ? $pagination->pages : "" )."
</div>";


include 'footer.php';



