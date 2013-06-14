<?php
include "inc/init.php";

if($user->islg()) { // if it's alreadt logged in redirect to the main page 
  header("Location: $set->url");
  exit;
}


$page->title = "Register to ". $set->site_name;


if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) {

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
      "username" => $name,
      "password" => sha1($password),
      "email" => $email
      );

    if($id = $db->insert_array(MUS_PREFIX."users", $user_data)) {
      $page->success = 1;
      $_SESSION['user'] = $id; // we automatically login the user
      $user = new User($db);
    }

  }


} else if($_POST)
    $page->error = "Invalid request !";


include 'header.php';

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
            <button type='reset' class='btn'>Cancel</button>
          </div>
        </fieldset>
      </form>
    </div>


  </div>";
}





include "footer.php";