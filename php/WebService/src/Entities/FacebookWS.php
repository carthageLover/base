<?php

namespace src\Entities;
require_once (BASE_DIR . '/src/Entities/src/facebook.php');

/**
 * Clase creada para simular la encapsulación de la creación de la sentencia SQL.
 */
class FacebookWS 
{
 	private $app_id = '481002115284282';
    private $app_secret = '346a073d5580d0992437caf3345897d6';
    private $app_namespace = 'crowsgame';
    private $app_url = 'http://www.facebook.com/appcenter/crowsgame'; //'http://apps.facebook.com/' . $app_namespace . '/';
    private $scope = 'email,publish_actions,user_games_activity';
	
	public function init()
	{
		 $facebook = new Facebook(array(
		  'appId'  => $app_id,
		  'secret' => $app_secret,
		));
	   /*
		// Get the current user
		$user = $facebook->getUser();
	
		// If the user has not installed the app, redirect them to the Auth Dialog
		if (!$user) 
		{
		  $loginUrl = $facebook->getLoginUrl(array(
			'scope' => $scope,
			'redirect_uri' => $app_url,
		  ));
	
		  print('<script> top.location.href=\'' . $app_url . '\'</script>');
		}
	    return $user;*/
		
		return BASE_DIR . '/src/Entities/src/facebook.php';
		
	}


}



   