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
	 * @var object 
	*/
	var $db; 
	/**
	 * Stores the users details encoded with htmlentities()
	 * @var object
	*/
	var $filter;
	/**
	 * stores the user data without any filter
	 * @var object
	*/
	var $data;
	/**
	 * contains the group details about the current user
	 * @var object
	 */
	var $group;

	function __construct($db) {
		$this->db = $db;
		$this->data = new stdClass();
		$this->filter = array();


		if($this->islg()){ // set some vars
			$this->data = $this->grabData($_SESSION['user']);
			
			if($this->data) {

				foreach ($this->data as $k => $v) {
					$this->filter[$k] = htmlentities($v, ENT_QUOTES);
				}

				$this->filter = (object)$this->filter; // we make it an object
			} else // in case the user was deleted
				$this->logout();

		} 



		if(!$this->islg()) { // we need to call it again in case the user was deleted while logged in

			// we set some default values
			// by doing this we won't have to do an extra check to display user or `guest` on the site
			$this->filter = new stdClass();
			$this->data = new stdClass();
			$this->filter->username = "Guest";
			$this->data->userid = 0;
			$this->data->groupid = 1;
			$this->data->banned = 0;
		}

		$this->group = $this->getGroup();
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
	/**
	 * Gets the url to the avatar of the user
	 * @param  int $userid the user id if none given it will take the current user
	 * @return string          url to the image
	 */
	function getAvatar($userid = 0, $size = null) {
		global $set;
		if($size)
			$size = "?s=$size";
		if(!$userid) {
			if($this->data->showavt)
				return "http://www.gravatar.com/avatar/".md5($this->data->email).$size;
			else
				return "$set->url/img/default-avatar.png";
		}
		$u = $this->db->getRow("SELECT `email`, `showavt` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid);
		if($u->showavt)	
			return "http://www.gravatar.com/avatar/".md5($u->email).$size;
		else	
			return "$set->url/img/default-avatar.png";

	}

	/**
	 * get the group details about the user
	 * @param  int $userid the user id if none given it will take the current user
	 * @return object          the object with the group details
	 */
	function getGroup($userid = 0) {

		if(!$userid)
			return $this->db->getRow("SELECT * FROM `".MLS_PREFIX."groups` WHERE `groupid` = ?i", $this->data->groupid);

		$u = $this->db->getRow("SELECT `groupid` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid);
		return $this->db->getRow("SELECT * FROM `".MLS_PREFIX."groups` WHERE `groupid` = ?i", $u->groupid);
	}
	/**
	 * get the ban details about the user
	 * @param  int $userid the user id if none given it will take the current user
	 * @return object          the object with the ban details
	 */
	function getBan($userid = 0) {

		if(!$userid)
			$userid = $this->data->userid;
		return $this->db->getRow("SELECT * FROM `".MLS_PREFIX."banned` WHERE `userid` = ?i", $userid);
	}

	/**
	 * shows the username of the used formated according to the group
	 * @param  integer $userid the user id if none provided it will use the current one
	 * @return string          formated username
	 */
	function showName($userid = 0) {

		if(!$userid)
			if($this->data->banned)
				return "<strike>".$this->filter->display_name."</strike>";
			else	
				return "<font color='".$this->group->color."'>".$this->filter->display_username."</font>";

		$u = $this->db->getRow("SELECT `display_name`,`banned` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid);
		$group = $this->getGroup($userid);
	
		if($u->banned)
			return "<strike>".htmlentities($u->display_name, ENT_QUOTES)."</strike>";		
		else	
			return "<font color='".$group->color."'>".htmlentities($u->display_name, ENT_QUOTES)."</font>";		
	}



	/**
	 * checks if `userid2` has the privilege to act on/over userid
	 * @param  integer  $userid  the user acted on
	 * @param  integer $userid2 the user who wants to act
	 * @return boolean          true if userid2 can
	 */
	function hasPrivilege($userid, $userid2 = 0) {
		
		$group = $this->getGroup($userid);		
		
		if(!$userid2) {
			if(($this->group->type >=3) || ($this->group->type > $group->type) || (($this->group->type == $group->type) && ($this->group->priority > $group->priority)))
				return TRUE;
			return FALSE;
		}

		$group2 = $this->getGroup($userid2);		
		
		if(($group2->type > $group->type) || (($group2->type == $group->type) && ($group2->priority > $group->priority)))
			return TRUE;
		return FALSE;



	}
	/**
	 * checks if the provoded id is valid
	 * @param  integer $userid id to be checked
	 * @return boolean          true if exists
	 */
	function exists($userid) {
		if($this->db->getRow("SELECT `userid` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid))
			return TRUE;
		return FALSE;
	}
	/**
	 * grabs the data about the user
	 * @param  integer $userid the id to grab data for
	 * @return object         data about the specified id
	 */
	function grabData($userid) {
		return $this->db->getRow("SELECT * FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid);
	}
	/**
	 * Checks if a user is admin
	 * @param  integer $userid user to be checked if none provided we take the current user
	 * @return boolean         true if yes
	 */
	function isAdmin($userid = 0) {
		if(!$userid)
			if($this->group->type >= 3)
				return TRUE;
			else
				return FALSE;

		$u = $this->db->getRow("SELECT `username`,`banned` FROM `".MLS_PREFIX."users` WHERE `userid` = ?i", $userid);
		$group = $this->getGroup($userid);
		if($group->type >= 3)
			return TRUE;
		return FALSE;
	}
	/**
	 * logges out the current user
	 * @return void 
	 */
	function logout() {
		global $set;
		session_unset('user');
		$path_info = parse_url($set->url);
		setcookie("user", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete
		setcookie("pass", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete
	}


}



