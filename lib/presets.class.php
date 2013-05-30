<?php
/**
 * Presets class
 * generates some presets for different portions of the site.
 */
 
class presets {
  function GenerateNavbar() {
      global $set, $user;
      $var = array();
      $var[0] = array("item" ,
                      array("href" => $set->url,
                            "name" => "Home",
                            "class" => 0));


      $var[1] = array("item",
                      array("href" => $set->url."/contact.php",
                            "name" => "Contact",
                            "class" => 0));


      $var[2] = array("dropdown",
                      array(  0 => array("href" => $set->url."/user.php",
                                       "name" => "Edit info",
                                       "class" => 0),

                              1 => array("href" => $set->url."/logout.php",
                                         "name" => "LogOut",
                                         "class" => 0),
                          ),
                      "class" => 0,
                      "style" => "float:right;",
                      "name" => $user->filter->username);


      $var[3] = array("dropdown",
                      array(  0 => array("href" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=T9HU2KAF54EBE&lc=RO&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted",
                                       "name" => "Donate",
                                       "class" => 0),

                              1 => array("href" => "https://github.com/ionutvmi",
                                         "name" => "Fork Me On Github",
                                         "class" => 0),
                          ),
                      "class" => 0,
                      "style" => 0,
                      "name" => "Extra");
          

      return $var;
  }
}