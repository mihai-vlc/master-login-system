<?php

/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */


session_start();

$set = new stdClass(); // stores general settings
$page = new stdClass(); // stores page details(title,... etc.)
$page->navbar = array(); // stores the navbar items

define("MLS_ROOT", dirname(dirname(__FILE__))); // the root path


include "settings.php";

include MLS_ROOT."/lib/mysql.class.php";
include MLS_ROOT."/lib/users.class.php";
include MLS_ROOT."/lib/presets.class.php";
include MLS_ROOT."/lib/options.class.php";


$db = new SafeMySQL(array(
	'host' 	=> $set->db_host, 
	'user'	=> $set->db_user, 
	'pass'	=> $set->db_pass, 
	'db'=> $set->db_name));

if(!($db_set = $db->getRow("SELECT * FROM `".MLS_PREFIX."settings` LIMIT 1"))) { // if we have no data in db we need to run the install.php
	header("Location: install.php");
	exit;
}

// we grab the settings and we merge them into $set
$set = (object)array_merge((array)$set,(array)$db_set);

$presets = new presets;
$user = new User($db);
$options = new Options;





// we check for cookies to autologin
if(!$user->islg() && isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
	 if($usr = $db->getRow("SELECT `userid` FROM `".MLS_PREFIX."users` WHERE `username` = ?s AND `password` = ?s", $_COOKIE['user'], $_COOKIE['pass'])) {
	 	$_SESSION['user'] = $usr->userid;
	 	$user = new User($db);
	}

} else {
	
	$time = time();
	
	if(!isset($_SESSION['last_log']))
		$_SESSION['last_log'] = 0;
	

	if($_SESSION['last_log'] < $time - 60 * 2){ // we update the db if more then 2 minutes passed since the last update
		$db->query("UPDATE `".MLS_PREFIX."users` SET `lastactive` = '".$time."' WHERE `userid`='".$user->data->userid."'");
		$_SESSION['last_log'] = $time;
	}
}