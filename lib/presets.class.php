
<?php
/**
 * Presets class
 * generates some presets for different portions of the site.
 */
 
class presets {
  
  var $active = '';


  function GenerateNavbar() {
      global $set, $user;
      $var = array();
      $var[] = array("item" ,
                      array("href" => $set->url,
                            "name" => "Home",
                            "class" => $this->isActive("home")),
                      "id" => "home");


      $var[] = array("item",
                      array("href" => $set->url."/users_list.php",
                            "name" => "User List",
                            "class" => $this->isActive("userslist")),
                      "id" => "userslist");
      $var[] = array("item",
                      array("href" => $set->url."/contact.php",
                            "name" => "Contact",
                            "class" => $this->isActive("contact")),
                      "id" => "contact");

      $var[] = array("dropdown",
                      array(  0 => array("href" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=T9HU2KAF54EBE&lc=RO&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted",
                                       "name" => "Donate",
                                       "class" => 0),

                              1 => array("href" => "https://github.com/ionutvmi",
                                         "name" => "Fork Me On Github",
                                         "class" => 0),
                          ),
                      "class" => 0,
                      "style" => 0,
                      "name" => "Extra",
                      "id" => "extra");      

      // keep this always the last one or edit hrader.php:8
      $var[] = array("dropdown",
                      array(  0 => array("href" => $set->url."/profile.php?u=".$user->data->userid,
                                       "name" => "<i class=\"icon-user\"></i> My Profile",
                                       "class" => 0),
                              1 => array("href" => $set->url."/user.php",
                                       "name" => "<i class=\"icon-cog\"></i> Edit info",
                                       "class" => 0),

                              2 => array("href" => $set->url."/logout.php",
                                         "name" => "LogOut",
                                         "class" => 0),
                          ),
                      "class" => 0,
                      "style" => 0,
                      "name" => $user->filter->username,
                      "id" => "user");



          

      return $var;
  }

  function setActive($id) {
    $this->active = $id;
  }

  function isActive($id) {
    if($id == $this->active)
      return "active";
    return 0;
  }

}