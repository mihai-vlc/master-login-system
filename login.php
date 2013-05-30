<?php
include "inc/init.php";

if($user->islg()) { // if it's alreadt logged in redirect to the main page 
  header("Location: $set->url");
  exit;
}


$page->title = "Login to ". $set->site_name;


if($_POST) {
    // we validate the data
    if(isset($_GET['forget'])) {
    
        $email = $_POST['email'];
        
        if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) 
            $page->error = "Email address is not valid.";   
        
        if(!($usr = $db->get_row("SELECT `userid` FROM `$set->users_table` WHERE `email` = '".$db->escape($email)."'")))
            $page->error = "This email address doesn't exist in our database !";


        if(!isset($page->error)) {
            $key = sha1(rand());
           
            $db->query("UPDATE `$set->users_table` SET `key` = '$key' WHERE `userid` = '$usr->userid'");
           
            $link = $set->url."/login.php?key=".$key;
            $from = 'MIME-Version: 1.0' . "\r\n";
            $from .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $from .="From: not.reply@".$set->url;
            $sub = "New Password !";
            $msg = "Hello,<br> You requested for a new password. To confirm <a href='$link'>click here</a>.<br>If you can't access copy this to your browser<br/>$link  <br><br>Regards<br><small>Note: Dont reply to this email. If you got this email by mistake then ignore this email.</small>";
            if(mail($email, $sub, $msg,$from))
                $page->success = "An email with instructions was sent !";
        }

    } else if(isset($_GET['key'])) {
        if($_GET['key'] == '0') {
            header("Location: $set->url");
            exit;
        }
        if($usr = $db->get_row("SELECT `userid` FROM `$set->users_table` WHERE `key` = '".$db->escape($_GET['key'])."'")) {
            if($db->query("UPDATE `$set->users_table` SET `password` = '".sha1($_POST['password'])."' WHERE `userid` = '$usr->userid'")) {
                $db->query("UPDATE `$set->users_table` SET `key` = '0' WHERE `userid` = '$usr->userid'");
                $page->success = "Password was updated !";
            }

        }

    } else {
        $name = $_POST['name'];
        $password = $_POST['password'];


        if(!($usr = $db->get_row("SELECT `userid` FROM `$set->users_table` WHERE `username` = '".$db->escape($name)."' AND `password` = '".sha1($password)."'")))
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
}


include 'header.php';


  echo "<div class='container'>";


if(isset($page->error))
  echo "<div class=\"alert alert-error\">".$page->error."</div>";
else if(isset($page->success))
  echo "<div class=\"alert alert-success\">".$page->success."</div>";

echo "<div class='row'>
      <div class='span3 hidden-phone'></div>
      <div class='span6' id='form-login'>
        ";
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
    if($usr = $db->get_row("SELECT `userid` FROM `$set->users_table` WHERE `key` = '".$db->escape($_GET['key'])."'")) {
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