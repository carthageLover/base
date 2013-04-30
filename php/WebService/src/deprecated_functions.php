<?php


function postAchievement($id)
{

	//echo "ID: " . $id;
	
	global $app_id,$app_token;
	// Register an Achievement for the app
	$achievement[0] = "";
	$achievement[1] = 'http://frozen-garden-4287.herokuapp.com/achievements/celebrities2.php';
	$achievement[2] = 'http://frozen-garden-4287.herokuapp.com/achievements/movies2.php';
	$achievement[3] = 'http://frozen-garden-4287.herokuapp.com/achievements/general2.php';
	$achievement[4] = 'http://frozen-garden-4287.herokuapp.com/achievements/history_geography2.php';
	$achievement[5] = 'http://frozen-garden-4287.herokuapp.com/achievements/music2.php';
	$achievement[6] = 'http://frozen-garden-4287.herokuapp.com/achievements/sports2.php';

	
  	$achievement_display_order = 1;
	
  	// Register an Achievement for the app
  //	$achievement_registration_URL = 'https://graph.facebook.com/' . $app_id . '/achievements';
  //	$achievement_registration_result=https_post($achievement_registration_URL,'achievement=' . $achievement[$id]  . '&display_order=' . $achievement_display_order . '&' . $_SESSION["app_access_token"]);
	
	//echo "achievement_registration_result: " . $achievement_registration_result . "<br>";
	
	// POST a user achievement
	//print('Publish a User Achievement<br/>');



	// ESTO ESTA OK
	$achievement_URL = 'https://graph.facebook.com/' . $_SESSION["facebookId"] . '/achievements';
	$achievement_result = https_post($achievement_URL,'achievement=' . $achievement[$id] . '&' .  $app_token);// $_SESSION["app_access_token"]);
	echo "achievement_result: " .  $achievement_result  . "<br>";
	
	
	
	
	
	
    //  return new Response(json_encode($achievement_result),200);*/

}

$app->get('/regAchievements.{format}', function(Request $request) use($app){

    global $app_id,$app_token;
	
	// Register an Achievement for the app
	//$achievement[0] = "";
	$achievement[0] = ""; 
	$achievement[1] = 'http://frozen-garden-4287.herokuapp.com/achievements/celebrities.php';
	$achievement[2] = 'http://frozen-garden-4287.herokuapp.com/achievements/movies.php';
	$achievement[3] = 'http://frozen-garden-4287.herokuapp.com/achievements/general.php';
	$achievement[4] = 'http://frozen-garden-4287.herokuapp.com/achievements/history_geography.php';
	$achievement[5] = 'http://frozen-garden-4287.herokuapp.com/achievements/music.php';
	$achievement[6] = 'http://frozen-garden-4287.herokuapp.com/achievements/sports.php';
	$achievement[7] = ' ';
	$achievement[8] = ' ';
	$achievement[9] = ' ';
	$achievement[10] = ' ';
	$achievement[11] = 'http://frozen-garden-4287.herokuapp.com/achievements/good_level' . $stars . '.php';
	
  	$achievement_display_order = 1;
	
	$urlList = array();
	for($i=0;$i<=5;$i++)	
  	{
	// Register an Achievement for the app
  	//$achievement_registration_URL = 'https://graph.facebook.com/' . $app_id . '/achievements/?';
   // $params = $achievement_registration_URL . 'achievement=' . $achievement[$i]  . '&display_order=' . $achievement_display_order . '&' . $_SESSION["app_access_token"];
	//	echo $params . " - ";
	$achievement_registration_URL = 'https://graph.facebook.com/' . $app_id . '/achievements';
  	$achievement_registration_result=https_post($achievement_registration_URL,'achievement=' . $achievement[$i]  . '&display_order=' . $achievement_display_order . '&' . $app_token);// $_SESSION["app_access_token"]);

	//array_push($urlList,$params);
	}
 //   var_dump($urlList);
//	echo muti_thread_request($urlList); 
	
		
});


/*

************************** DEPRECATED FUNCTIONS **************************

$app->get('/setS.{format}', function(Request $request) use($app){
 
   global $seed,$xp_level_pts,$xp_const;
   
 echo $_SESSION["score"] . "\n";
   echo $_SESSION["rightAnswers"] . "\n";
   echo $_SESSION["wrongAnswers"] . "\n";
   var_dump($_SESSION["rightAnswersList"]);
   var_dump($_SESSION["rightTimeList"]);
   
   echo $_SESSION["usedJoker"] . "\n";
   echo $_SESSION["usedFrenzy"] . "\n";
   echo $_SESSION["isHighScore"] . "\n";
   echo  $_SESSION["newLevel"] . "\n";
   echo $_SESSION["reachCombo"] . "\n";
      
   var_dump($_SESSION["gameAnswers"]);
   var_dump($_SESSION["gameAnswers"]);
   var_dump($_SESSION["rightZonesList"]);
   
   echo "xp points \n";
   
    $_SESSION["token"] = $random_string;

  
   
  // var_dump($xp_level_pts);

});*/

/*

*/

/*
$app->get('rewards.{format}', function(Request $request) use($app){

$r = getReward(500,$app['db']);
echo "500: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

$r = getReward(3000,$app['db']);
echo "3000: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

$r = getReward(14000,$app['db']);
echo "14000: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

$r = getReward(70000,$app['db']);
echo "70000: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

$r = getReward(220000,$app['db']);
echo "220000: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

$r = getReward(800000,$app['db']);
echo "800000: c" . $r["coins"] . " xp" . $r["xp"] . "\n";

});

/*
$app->get('/fb.{format}', function(Request $request) use($app){

    global $facebook,$seed,$app_secret,$app_id,$app_host;
	$app->post('https://graph.facebook.com/?id=' . $app_host . '/question.php?title=' . $title . '&scrape=true');
	
	$config = array(
    'appId' => $app_id,
    'secret' => $app_secret,
    );

    $facebook = new Facebook($config);
	
	$u = $facebook->getUser();
	
	$title = $request->get('title');
	
	$params = array('question'=> $app_host . '/question.php?title=' . $title,'access_token'=>$facebook->getAccessToken());
    $out = $facebook->api('/me/crowsgame:answer','post',$params);
	
	return new Response($out, 200); 
	
});

$app->get('/getAnswers.{format}', function(Request $request) use($app){

	return new Response(json_encode($_SESSION["gameAnswers"]), 200);
});

$app->get('/gR.{format}', function(Request $request) use($app){

	return new Response(json_encode($_SESSION["myQuestions"]), 200); 

});

$app->get('/achievement.{format}', function(Request $request) use($app){
   
	global $app_id,$app_token,$app_host;
	// Register an Achievement for the app
	$achievement = $app_host . '/achievements/music2.php';
  	$achievement_display_order = 1;
	
  	// Register an Achievement for the app
  	$achievement_registration_URL = 'https://graph.facebook.com/' . $app_id . '/achievements';
  	$achievement_registration_result=https_post($achievement_registration_URL,'achievement=' . $achievement  . '&display_order=' . $achievement_display_order . '&' . $_SESSION["app_access_token"]);// $_SESSION["app_access_token"]);
		
	// POST a user achievement
	//print('Publish a User Achievement<br/>');
	$achievement_URL = 'https://graph.facebook.com/' . $_SESSION["facebookId"] . '/achievements';
	$achievement_result = https_post($achievement_URL,'achievement=' . $achievement . '&' . $_SESSION["app_access_token"]);// $_SESSION["app_access_token"]);
    return new Response(json_encode($achievement_result),200);
		
});

$app->get('/token.{format}', function(Request $request) use($app){
  
    session_start();
	$param = $request->get('token');
	$_SESSION["access_token"] = $param;
	return new Response($_SESSION["access_token"], 200); 
	
});

$app->get('/ogA.{format}', function(Request $request) use($app){

    global $app_host;
	$action='https://graph.facebook.com/me/crowsgame:win';
	
	$title = $request->get('title');
	$action='https://graph.facebook.com/me/crowsgame:answer';
	
	$p = 'access_token=' .  $_SESSION["access_token"] . '&' . 'question=' . $app_host . '/question.php?title=' . $title . '&fb:explicitly_shared=true';
	$res_obj=https_post($action,$p); 
	//echo $action . " " . $p;
	echo $res_obj . "\n";
		
});	

$app->get('/buyJoker.{format}', function(Request $request) use($app){
   
 	global $seed;
	
	$param = $request->get('p');
    $encoded=/*decrypt(*///$param/*,"wopidom")*/;
 
/*	$data = explode(",",$encoded);	
	$token = $data[0];	
	$idJoker= $data[1];// 1 joker, 2 frenzy


   //$token = $request->get('token');
   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
   {
     $currencyToConsume="";
	 
	 if($idJoker==1)
	 	$currencyToConsume =  $_SESSION["jokerPrice"];
	 else
	 	$currencyToConsume =  $_SESSION["freezePrice"];
	 	
	 $sql = Players::buyJoker($_SESSION["facebookId"],$currencyToConsume);
	 $result = $app['db']->exec($sql);	
	 
	 $sql = Players::getCoins($_SESSION["facebookId"]);
	 $data = $app['db']->fetchAll($sql);
	 $coins = $data[0]["coins"];
	 
	 return new Response( $coins  , 200);
	 
   }
   else
     return new Response("Invalid token", 200);
	 
});

$app->get('/regOGA2.{format}', function(Request $request) use($app){

	global $app_host;
	$title = $request->get('title');
	$res_obj=https_post('https://graph.facebook.com','id=' . $app_host . '/question.php?title=' . $title . '&scrape=true'); 
	echo $res_obj . "\n";	
	
});	

$app->get('/sNL.{format}', function(Request $request) use($app){

	date_default_timezone_set('America/Argentina/Buenos_Aires');
	$now = date("Y-m-d H:i:s");
	$_SESSION["start_credit_time"] = $now; //date("H:i:s", strtotime($now)+(6*60));
	return new Response($_SESSION["start_credit_time"], 200); 

});


*/
?>
