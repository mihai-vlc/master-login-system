<?php


// Master User System v1.0
// initialization file
session_start();

$set = new stdClass(); // stores general settings
$page = new stdClass(); // stores page details(title,... etc.)
$page->navbar = array(); // stores the navbar items

define("MUS_ROOT", dirname(dirname(__FILE__))); // the root path


include "settings.php";
define("MUS_PREFIX", "");

include MUS_ROOT."/lib/mysql.class.php";
include MUS_ROOT."/lib/users.class.php";
include MUS_ROOT."/lib/presets.class.php";
include MUS_ROOT."/lib/options.class.php";



$presets = new presets;
$db = new dbConn($set->db_host, $set->db_user, $set->db_pass, $set->db_name);
$user = new User($db);
$options = new Options;

// we check for cookies to autologin
if(!$user->islg() && isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
	 if(($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `username` = '".$db->escape($_COOKIE['user'])."' AND `password` = '".$db->escape($_COOKIE['pass'])."'")))
	 	$_SESSION['user'] = $usr->userid;
	 	$user = new User($db);

}