<?php
/**
 * MASTER LOGIN SYSTEM
 * @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
 * June 2013
 *
 */


include 'lib/mysql.class.php';
include 'lib/options.class.php';
$options = new Options;
$page = new stdClass();

if($_POST) {
  // we first check the settings file
  if(!is_writable('inc/settings.php'))
    chmod('inc/settings.php', 0666);


  // we make the db connection
  $db = new SafeMySQL(array(
  'host'  => $_POST['dbhost'], 
  'user'  => $_POST['dbuser'], 
  'pass'  => $_POST['dbpass'], 
  'db'    => $_POST['dbname']));


  // once that is done we write the details in the settings file
  $host = str_replace("'", "\'", $_POST['dbhost']);
  $user = str_replace("'", "\'", $_POST['dbuser']);
  $pass = str_replace("'", "\'", $_POST['dbpass']);
  $name = str_replace("'", "\'", $_POST['dbname']);
  $prefix = str_replace("'", "\'", $_POST['tbprefix']);

$data =<<<EEE
<?php

// Master Login System
// Mihai Ionut Vilcu (ionutvmi@gmail.com)
// configuration file


// database details
\$set->db_host = '$host'; // database host
\$set->db_user = '$user'; // database user
\$set->db_pass = '$pass'; // database password
\$set->db_name = '$name'; // database name

define('MLS_PREFIX', '$prefix');  

EEE;

  // add the data to the file
  if(!file_put_contents('inc/settings.php', $data))
    $page->error = "There is an error with inc/settings.php make sure it is writable.";



  $sqls[] = "
  CREATE TABLE IF NOT EXISTS `".$prefix."banned` (
  `userid` int(11) NOT NULL,
  `until` int(11) NOT NULL,
  `by` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";


  $sqls[] = "
  CREATE TABLE IF NOT EXISTS `".$prefix."groups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `canban` int(11) NOT NULL,
  `canhideavt` int(11) NOT NULL,
  `canedit` int(11) NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";

  $sqls[] = "
  INSERT INTO `".$prefix."groups` (`groupid`, `name`, `type`, `priority`, `color`, `canban`, `canhideavt`, `canedit`) VALUES
(1, 'Guest', 0, 1, '', 0, 0, 0),
(2, 'Member', 1, 1, '#08c', 0, 0, 0),
(3, 'Moderator', 2, 1, 'green', 1, 1, 0),
(4, 'Administrator', 3, 1, '#F0A02D', 1, 1, 1);";

  $sqls[] = "
  CREATE TABLE IF NOT EXISTS `".$prefix."privacy` (
  `userid` int(11) NOT NULL,
  `email` int(11) NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

  $sqls[] = "
  INSERT INTO `".$prefix."privacy` (`userid`, `email`) VALUES (1, 0);";
  $sqls[] = "
  CREATE TABLE IF NOT EXISTS `".$prefix."settings` (
  `site_name` varchar(255) NOT NULL DEFAULT 'Demo Site',
  `url` varchar(300) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `max_ban_period` int(11) NOT NULL DEFAULT '10',
  `register` int(11) NOT NULL DEFAULT '1',
  `email_validation` int(11) NOT NULL DEFAULT '0',
  `captcha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

  $sqls[] = $db->parse("
  INSERT INTO `".$prefix."settings` (`site_name`, `url`, `admin_email`, `max_ban_period`, `register`, `email_validation`, `captcha`) VALUES
(?s, ?s, 'nor.reply@gmail.com', 10, 1, 0, 1);", $_POST['sitename'], $_POST['siteurl']);

  $sqls[] = "
  CREATE TABLE IF NOT EXISTS `".$prefix."users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `key` varchar(50) NOT NULL,
  `validated` varchar(100) NOT NULL,
  `groupid` int(11) NOT NULL DEFAULT '2',
  `lastactive` int(11) NOT NULL,
  `showavt` int(11) NOT NULL DEFAULT '1',
  `banned` int(11) NOT NULL,
  `regtime` int(11) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

  $sqls[] = "
  INSERT INTO `".$prefix."users` (`userid`, `username`, `display_name`, `password`, `email`, `key`, `validated`, `groupid`, `lastactive`, `showavt`, `banned`, `regtime`) VALUES
(1, 'admin', 'Admin', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'admin@gmail.com', '', '1', 4, ".time().", 1, 0, ".time().");";



  foreach($sqls as $sql)
    if(!isset($page->error) && (!$db->query("?p",$sql)))
      $page->error = "There was a problem while executing <code>$sql</code>";



  if(!isset($page->error)) {
    $page->success = "The installation was successful ! Thank you for using master loging system and we hope you enjo it ! Have fun ! <br/><br/>
    <a class='btn btn-success' href='./index.php'>Start exploring</a>
    <br/><br/>

    <h3>USER: admin <br/> PASSWORD: 1234</h3>";
  }

}



?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Installer</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="./css/bootstrap.min.css">
        <!-- join the dark side :) -->
        <!-- <link rel="stylesheet" href="./css/darkstrap.min.css">-->
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>
        <link rel="stylesheet" href="./css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="./css/main.css">

        <script src="./js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        

    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]--> 

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="?">MASTER LOGIN SYSTEM INSTALLER</a>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
<div class="container">
        

<?php

if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success)) {
  $options->success($page->success);
  exit;
}

?>

    <div class='span3 hidden-phone'></div>



<form class="form-horizontal well span6" action="?" method="post">
<fieldset>

    <legend>Install form</legend>


    <div class="control-group">
      <label class="control-label" for="sitename">Site Name</label>
      <div class="controls">
        <input id="sitename" name="sitename" type="text" value="Demo Site" class="input-xlarge">
        <p class="help-block">The name of the site will be used in the top left corner.</p>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="siteurl">Site Url</label>
      <div class="controls">
        <input id="siteurl" name="siteurl" type="text" value="http://<?php echo $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);?>" class="input-xlarge">
        <p class="help-block">The url of your site(no end /).</p>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="dbhost">Database Host</label>
      <div class="controls">
        <input id="dbhost" name="dbhost" type="text" value="localhost" class="input-xlarge">
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="dbuser">Database Username</label>
      <div class="controls">
        <input id="dbuser" name="dbuser" type="text" value="root" class="input-xlarge">
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="dbpass">Database Password</label>
      <div class="controls">
        <input id="dbpass" name="dbpass" type="password" value="" class="input-xlarge">
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="dbname">Database Name</label>
      <div class="controls">
        <input id="dbname" name="dbname" type="text" value="mls" class="input-xlarge">
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="tbprefix">Tables Prefix</label>
      <div class="controls">
        <input id="tbprefix" name="tbprefix" type="text" value="mls_" class="input-xlarge">
      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        <input type='submit' value='Install' class='btn btn-success'>
      </div>
    </div>

</fieldset>
</form>







</div> <!-- /container -->
   

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="./js/vendor/jquery-1.9.1.min.js"><\/script>')</script>

<script src="./js/vendor/bootstrap.min.js"></script>

</body>
</html>