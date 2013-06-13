<?php

/**
* User Class
* Contains function related to users
* @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
* 11 - May - 2013
* 
*/

class User {

	/**
	 * Stores the object of the mysql class 
	*/
	var $db; 
	/**
	 * Stores the users details encoded with htmlentities()
	*/
	var $filter;
	/**
	 * stores the user data without any filter
	*/
	var $data;

	function __construct($db) {
		$this->db = $db;
		$this->data = new stdClass();
		$this->filter = array();


		if($this->islg()){ // set some vars
			$this->data =$db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `userid` = '".$db->escape($_SESSION['user'])."'");
	
			foreach ($this->data as $k => $v) {
				$this->filter[$k] = htmlentities($v, ENT_QUOTES);
			}

			$this->filter = (object)$this->filter; // we make it an object

			

		}else {
			// we set some default values
			// by doing this we won't have to do an extra check to display user or `guest` on the site
			$this->filter = new stdClass();
			$this->filter->username = "Guest";
			$this->data->userid = 0;
		}
	}
	/**
	 * Checks if user is logged in
	 * @return bool
	*/
	function islg() {
		if(isset($_SESSION['user']))
			return true;
		return false;
	}

	function getAvatar($userid = false) {
		if(!$userid) 
			return "http://www.gravatar.com/avatar/".md5($this->data->email);
		
		$u = $this->db->get_row("SELECT `email` FROM `".MUS_PREFIX."users` WHERE `userid` = '".(int)$userid."'");
		return "http://www.gravatar.com/avatar/".md5($u->email);

	}

}