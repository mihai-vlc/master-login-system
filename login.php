<?php
include "inc/init.php";

if($user->islg()) { // if it's alreadt logged in redirect to the main page 
  header("Location: $set->url");
  exit;
}


$page->title = "Login to ". $set->site_name;


if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) {
    // we validate the data
    if(isset($_GET['forget'])) {
    
        $email = $_POST['email'];
        
        if(!$options->isValidMail($email)) 
            $page->error = "Email address is not valid.";   
        
        if(!isset($page->error) && !($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `email` = '".$db->escape($email)."'")))
            $page->error = "This email address doesn't exist in our database !";


        if(!isset($page->error)) {
            $key = sha1(rand());
           
            $db->query("UPDATE `".MUS_PREFIX."users` SET `key` = '$key' WHERE `userid` = '$usr->userid'");
           
            $link = $set->url."/login.php?key=".$key."&userid=".$usr->userid;

            $from ="From: not.reply@".$set->url;
            $sub = "New Password !";
            $msg = "Hello,<br> You requested for a new password. To confirm <a href='$link'>click here</a>.<br>If you can't access copy this to your browser<br/>$link  <br><br>Regards<br><small>Note: Dont reply to this email. If you got this email by mistake then ignore this email.</small>";
            if($options->sendMail($email, $sub, $msg, $from))
                $page->success = "An email with instructions was sent !";
        }

    } else if(isset($_GET['key'])) {
        if($_GET['key'] == '0') {
            header("Location: $set->url");
            exit;
        }
        if($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `key` = '".$db->escape($_GET['key'])."'")) {
            if($db->query("UPDATE `".MUS_PREFIX."users` SET `password` = '".sha1($_POST['password'])."' WHERE `userid` = '$usr->userid'")) {
                $db->query("UPDATE `".MUS_PREFIX."users` SET `key` = '0' WHERE `userid` = '$usr->userid'");
                $page->success = "Password was updated !";
            }

        }

    } else {
        $name = $_POST['name'];
        $password = $_POST['password'];


        if(!($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `username` = '".$db->escape($name)."' AND `password` = '".sha1($password)."'")))
            $page->error = "Username or password are wrong !";
        else {
            if($_POST['r'] == 1){
                $path_info = parse_url($set->url);
                setcookie("user", $name, time() + 3600 * 24 * 30, $path_info['path']); // set
                setcookie("pass", sha1($password), time() + 3600 * 24 * 30, $path_info['path']); // set
            }
            $_SESSION['user'] = $usr->userid;
            header("Location: $set->url");
            exit;
        }
    }
} else if($_POST)
    $page->error = "Invalid request !";


include 'header.php';


$_SESSION['token'] = sha1(rand()); // random token

  echo "<div class='container'>
  <div class='row'>
    <div class='span3 hidden-phone'></div>
      <div class='span6' id='form-login'>";


if(isset($page->error))
  $options->error($page->error);
else if(isset($page->success))
  $options->success($page->success);


if(isset($_GET['forget'])) {
    
    echo "<form class='form-horizontal well' action='#' method='post'>
        <fieldset>
            <legend>Recover</legend>
            <div class='control-group'>
              <div class='control-label'>
                <label>Email</label>
              </div>
              <div class='controls'>
                <input type='text' placeholder='john.doe@domain.com' name='email' class='input-large'>
              </div>
            </div>
            
            <input type='hidden' name='token' value='".$_SESSION['token']."'>

            <div class='control-group'>
              <div class='controls'>
              <button type='submit' id='submit' class='btn btn-primary'>Recover</button>
              </div>
            </div>
          </fieldset>";

} else if(isset($_GET['key']) && !isset($page->success)) { 
    if($_GET['key'] == '0') {
        echo "<div class=\"alert alert-error\">Error !</div>";
        exit;
    }
    if($usr = $db->get_row("SELECT `userid` FROM `".MUS_PREFIX."users` WHERE `key` = '".$db->escape($_GET['key'])."' AND `userid` = '".(int)$_GET['userid']."'")) {
    echo "<form class='form-horizontal well' action='#' method='post'>
        <fieldset>
            <legend>Reset</legend>
            <div class='control-group'>
              <div class='control-label'>
                <label>New password</label>
              </div>
              <div class='controls'>
                <input type='password' name='password' class='input-large'>
              </div>
            </div>

            <input type='hidden' name='token' value='".$_SESSION['token']."'>

            <div class='control-group'>
              <div class='controls'>
              <button type='submit' id='submit' class='btn btn-primary'>Save</button>
              </div>
            </div>
          </fieldset>";


    } else {
        echo "<div class=\"alert alert-error\">Error bad key !</div>";
    }

}else {
    echo "<form class='form-horizontal well' action='?' method='post'>
        <fieldset>
            <legend>Login Form</legend>
            <div class='control-group'>
              <div class='control-label'>
                <label>Username</label>
              </div>
              <div class='controls'>
                <input type='text' placeholder='john.doe' name='name' class='input-large'>
              </div>
            </div>

            <div class='control-group'>
              <div class='control-label'>
                <label>Password</label>
              </div>
              <div class='controls'>
                <input type='password' placeholder='type your password' name='password' class='input-large'>

                <!-- Help-block example -->
                <!-- <span class='help-block'>Example block-level help text here.</span> -->
              </div>
              

            </div>
            <div class='control-group'>            
              <div class='control-label'>
                <label for='r'>Remember Me</label>
              </div>
              <div class='controls'>              
                <input type='checkbox' name='r' value='1' id='r'>
              </div>
            </div>

            <input type='hidden' name='token' value='".$_SESSION['token']."'>

            <div class='control-group'>
              <div class='controls'>

              <button type='submit' id='submit' class='btn btn-primary'>Sign in</button>

              <a href='?forget=1' class='btn btn-secondary'>Forgot Password</a>

              </div>
            </div>
          </fieldset>";
}          
echo "  </form>
      </div>
</div>";


include "footer.php";