<?php
include "inc/init.php";



$page->title = "Contact to ". $set->site_name;


$page->navbar = $presets->GenerateNavbar();

$page->navbar[1][1]['class'] = 'active'; // we highlith the contact link


if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) {

	  $email = $_POST['email'];
	  $message = $_POST['message'];

	  if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) 
	    $page->error = "Email address is not valid.";
	  else if(!isset($message[10]))
	    $page->error = "Message was too short !";
	  else {
            $from = 'MIME-Version: 1.0' . "\r\n";
            $from .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $from .="From: ".$email;
            $sub = "Contact Admin $set->site_name !";
            if(mail($email, $sub, $message,$from))
                $page->success = "Your message was sent !";

	  }
} else if($_POST)
    $page->error = "Invalid request !";


include 'header.php';

$_SESSION['token'] = sha1(rand()); // random token

echo "<div class='container'>
    <div class='span3 hidden-phone'></div>
	<div class='span6'>	";


if(isset($page->error))
  echo "<div class=\"alert alert-error\">".$page->error."</div>";
else if(isset($page->success))
  echo "<div class=\"alert alert-success\">".$page->success."</div>";

	echo "<form class='form-horizontal well' action='#' method='post'>
		        <fieldset>
		            <legend>Contact Admin</legend>

		            <div class='control-group'>
		              <div class='control-label'>
		                <label>Your Email</label>
		              </div>
		              <div class='controls'>
		                <input type='text' name='email' class='input-large' value='".($user->islg() ? $user->filter->email : "")."'>
		              </div>
		            </div>

		            <div class='control-group'>
		              <div class='control-label'>
		                <label>Message</label>
		              </div>
		              <div class='controls'>
		                <textarea name='message' rows='5' class='input-large'></textarea>
		              </div>
		            </div>

           			<input type='hidden' name='token' value='".$_SESSION['token']."'>

		            <div class='control-group'>
		              <div class='controls'>
		              <button type='submit' id='submit' class='btn btn-primary'>Send</button>
		              </div>
		            </div>
		          </fieldset>
		    </form>
		    
		    </div>
	</div><!-- /container -->";

include 'footer.php';