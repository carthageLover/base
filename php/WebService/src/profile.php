<?php
require 'src/facebook.php';

$user_profile =  $_SESSION["facebook"]->api('/me','GET');
		echo "<b>My profile</b><br>";
		echo "<pre>";
		var_dump($user_profile);
		
		
?>		