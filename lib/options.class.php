<?php

/**
* Provides some options to check validate or filter vars
*/
class Options
{
	/**
	 * Checks if an email is valid
	 * @param  string  $mail the value to be checked
	 * @return boolean       true if it's valid
	 */
	function isValidMail($mail) {
		if(!filter_var($mail, FILTER_VALIDATE_EMAIL))
			return FALSE;

		if(!checkdnsrr("gmail.com", "MX")) // if we have no internet access on the server
			return TRUE;

		list($username, $maildomain) = explode("@", $mail);
		if(checkdnsrr($maildomain, "MX"))
			return TRUE;

		return FALSE;
	}


	function sendMail($to, $subject, $message, $from = 'From: no.reply@dot.com', $isHtml = true) {
	    
	    $from .= "\r\n"; // we make sure we have an endline
		if($isHtml) {
			$from .= "MIME-Version: 1.0 \r\n";
	        $from .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}
		$from .= 'X-Mailer: PHP/'.phpversion()."\r\n";
	    
		return mail($to, $subject, $message, $from);
	    
	}

	/**
	 * Encodes the string with htmlentities
	 * @param  string $string the string to be encoded
	 * @return string         encoded string
	 */
	function html($string) {
		return htmlentities($string, ENT_QUOTES);
	}

}