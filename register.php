<?php
include "inc/init.php";

if($user->islg()) { // if it's alreadt logged in redirect to the main page 
  header("Location: $set->url");
  exit;
}


$page->title = "Register to ". $set->site_name;


if($_POST) {
  // we validate the data

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];


  if(!isset($name[3]) || isset($name[30]))
    $page->error = "Username too short or too long !";

  if(!isset($password[3]) || isset($password[30]))
    $page->error = "Password too short or too long !";

  if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) 
    $page->error = "Email address is not valid.";

  if($db->get_row("SELECT * FROM `$set->users_table` WHERE `username` = '".$db->escape($name)."'"))
    $page->error = "Username already in use !";

  if($db->get_row("SELECT * FROM `$set->users_table` WHERE `email` = '".$db->escape($email)."'"))
    $page->error = "Email already in use !";


  if(!isset($page->error)){
    $user_data = array(
      "username" => $name,
      "password" => sha1($password),
      "email" => $email
      );

    if($id = $db->insert_array($set->users_table, $user_data)) {
      $page->success = 1;
      $_SESSION['user'] = $id; // we automatically login the user
    }

  }


}


include 'header.php';



if(isset($page->error))
  echo "<div class=\"alert alert-error\">".$page->error."</div>";

if(isset($page->success)) {

  echo "<div class=\"alert alert-success\"><p><strong>Your account was successfully registered !</strong></p></div>
  <div class='content'> <a class='btn btn-primary' href='$set->url'>Start exploring</a>
  ";


} else {
  echo "<div class='container'>
  <form action='#' id='contact-form' class='form-horizontal' method='post'>
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

  <div class='form-actions'>
  <button type='submit' class='btn btn-primary btn-large'>Register</button>
    <button type='reset' class='btn'>Cancel</button>
  </div>
    </fieldset>
  </form>


  </div>";
}





include "footer.php";