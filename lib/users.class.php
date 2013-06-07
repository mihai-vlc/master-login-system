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
	 * Stores the users table name.
	*/
	var $table;
	/**
	 * The name of the auto_increment column in users table, it will be stored in session to identify the user
	*/
	var $auto_increment;
	/**
	 * Stores the users details encoded with htmlentities()
	*/
	var $filter;
	/**
	 * stores the user data without any filter
	*/
	var $data;

	function __construct($db, $table = 'users', $auto_increment = 'userid') {
		$this->db = $db;
		$this->table = $table;
		$this->auto_increment = $auto_increment;
		$this->data = new stdClass();
		$this->filter = array();


		if($this->islg()){ // set some vars
			$this->data =$db->get_row("SELECT * FROM `$table` WHERE `$this->auto_increment` = '".$db->escape($_SESSION['user'])."'");
	
			foreach ($this->data as $k => $v) {
				$this->filter[$k] = htmlentities($v, ENT_QUOTES);
			}

			$this->filter = (object)$this->filter; // we make it an object

			

		}else {
			// we set some default values
			// by doing this we won't have to do an extra check to display user or `guest` on the site
			$this->filter = new stdClass();
			$this->filter->username = "Guest";
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



}