<?php


// Master User System v1.0
// initialization file
session_start();

$set = new stdClass(); // stores general settings
$page = new stdClass(); // stores page details(title,... etc.)
$page->navbar = array(); // stores the navbar items

define("MUS_ROOT", dirname(dirname(__FILE__))); // the root path


include "settings.php";

include MUS_ROOT."/lib/mysql.class.php";
include MUS_ROOT."/lib/users.class.php";
include MUS_ROOT."/lib/presets.class.php";
include MUS_ROOT."/lib/options.class.php";



$db = new dbConn($set->db_host, $set->db_user, $set->db_pass, $set->db_name);
// we grab the settings and we merge them into $set
$set = (object)array_merge((array)$set,(array)$db->get_row("SELECT * FROM `".MUS_PREFIX."settings` LIMIT 1"));

$presets = new presets;
$user = new User($db);
$options = new Options;





// we check for cookies to autologin
if(!$user->islg() && isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
	 if(($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `username` = '".$db->escape($_COOKIE['user'])."' AND `password` = '".$db->escape($_COOKIE['pass'])."'")))
	 	$_SESSION['user'] = $usr->userid;
	 	$user = new User($db);

} else {
	if(!isset($_SESSION['last_log']))
		$_SESSION['last_log'] = 0;
	
	$time = time();

	if($_SESSION['last_log'] < $time - 60 * 2){ // we update the db if more then 2 minutes passed since the last update
		$db->query("UPDATE `".MUS_PREFIX."users` SET `lastactive` = '".$time."' WHERE `userid`='".$user->data->userid."'");
		$_SESSION['last_log'] = $time;
	}
}