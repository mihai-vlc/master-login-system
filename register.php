<?php
include "inc/init.php";

if($user->islg()) { // if it's alreadt logged in redirect to the main page 
  header("Location: $set->url");
  exit;
}


$page->title = "Register to ". $set->site_name;


if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token']) && $set->register) {

  // we validate the data

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];


  if(!isset($name[3]) || isset($name[30]))
    $page->error = "Username too short or too long !";

  if(!isset($password[3]) || isset($password[30]))
    $page->error = "Password too short or too long !";

  if(!$options->isValidMail($email)) 
    $page->error = "Email address is not valid.";

  if($db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `username` = '".$db->escape($name)."'"))
    $page->error = "Username already in use !";

  if($db->get_row("SELECT * FROM `".MUS_PREFIX."users` WHERE `email` = '".$db->escape($email)."'"))
    $page->error = "Email already in use !";


  if(!isset($page->error)){
    $user_data = array(
      "username" => $db->escape($name),
      "password" => sha1($password),
      "email" => $db->escape($email),
      "lastactive" => time(),
      "regtime" => time(),
      "validated" => 1
      );

    if($set->email_validation == 1) {

      $user_data["validated"] = $key = sha1(rand());

      $link = $set->url."/validate.php?key=".$key."&username=".urlencode($name);
      
      

      $from ="From: not.reply@".$set->url;
      $sub = "Activate your account !";
      $msg = "Hello ".$options->html($name).",<br> Thank you for choosing to be a member of out community.<br/><br/> To confirm your account <a href='$link'>click here</a>.<br>If you can't access copy this to your browser<br/>$link  <br><br>Regards<br><small>Note: Dont reply to this email. If you got this email by mistake then ignore this email.</small>";
      if(!$options->sendMail($email, $sub, $msg, $from))
        // if we can't send the mail by some reason we automatically activate the account
          $user_data["validated"] = 1;
    }

    if(($id = $db->insert_array(MUS_PREFIX."users", $user_data)) && $db->query("INSERT INTO `".MUS_PREFIX."privacy` SET `userid` = '$id'")) {
      $page->success = 1;
      $_SESSION['user'] = $id; // we automatically login the user
      $user = new User($db);
    } else
      $page->error = $db->err();

  }


} else if($_POST)
    $page->error = "Invalid request !";


include 'header.php';


if(!$set->register) // we check if the registration is enabled
  $options->fError("We are sorry registration is blocked momentarily please try again leater !");


$_SESSION['token'] = sha1(rand()); // random token

$extra_content = ''; // holds success or error message

if(isset($page->error))
  $extra_content = $options->error($page->error);

if(isset($page->success)) {
  
  echo "<div class='container'>
    <div class='span3 hidden-phone'></div>
    <div class='span6 well'>
    <h1>Congratulations !</h1>";
    $options->success("<p><strong>Your account was successfully registered !</strong></p>");
    echo " <a class='btn btn-primary' href='$set->url'>Start exploring</a>
    </div>
  </div>";


} else {



  echo "
  <div class='container'>
    <div class='span3 hidden-phone'></div>
      <div class='span6'>

      ".$extra_content."

      <form action='#' id='contact-form' class='form-horizontal well' method='post'>
        <fieldset>
          <legend>Register Form </legend>

          <div class='control-group'>
            <label class='control-label' for='name'>Username</label>
            <div class='controls'>
              <input type='text' class='input-xlarge' name='name' id='name'>
            </div>
          </div>
          <div class='control-group'>
            <label class='control-label' for='email'>Email Address</label>
            <div class='controls'>
              <input type='text' class='input-xlarge' name='email' id='email'>
            </div>
          </div>
          <div class='control-group'>
            <label class='control-label' for='password'>Password</label>
            <div class='controls'>
              <input type='password' class='input-xlarge' name='password' id='password'>
            </div>
          </div>
          <input type='hidden' name='token' value='".$_SESSION['token']."'>
          <div class='form-actions'>
          <button type='submit' class='btn btn-primary btn-large'>Register</button>
            <button type='reset' class='btn'>Reset</button>
          </div>
        </fieldset>
      </form>
    </div>


  </div>";
}





include "footer.php";