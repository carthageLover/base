<?php

session_start();
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use src\Entities\Questions;
use src\Entities\Players;
use src\Entities\ApStore;
use src\Entities\OGActions;

require_once (BASE_DIR . '/src/Entities/Questions.php');
require_once (BASE_DIR . '/src/Entities/Players.php');
require_once (BASE_DIR . '/src/Entities/ApStore.php');
require_once (BASE_DIR . '/src/Entities/OGActions.php');
require_once (BASE_DIR . '/src/Entities/src/facebook.php');
require_once (BASE_DIR . '/src/Entities/Utils.php');

$app_id = $_SESSION["app_id"];
$app_secret = $_SESSION["app_secret"]; 
$app_host = $_SESSION["serverBaseUrl"];


$app->get('/', function(Request $request) use($app){

   $rta="hola! ".$request->get('p');
   return new Response($rta, 200);
});

$app->get('/m', function(Request $request) use($app){

   $rta="hola m: ".$request->get('p');
   phpinfo();
   return new Response($rta, 200);
});

$app->get('/xtr1414miBici71hTxChotoGarcha', function(Request $request) use($app){

  // `cd /var/www/base && git pull`;
	
	$output = shell_exec('cd /var/www/base && git pull 2>&1');
	
   //$rta="hola m: ".$request->get('p');
   
   return new Response('{"result":"'.$output.'"}', 200);
});


/*****************************************************************************************************/
/***** GET GAME TOKEN ********************************************************************************/
/*****************************************************************************************************/	
$app->get('/gTk', function(Request $request) use($app){
    
   global $app_secret,$app_id,$xp_const;
   
   $length = 20;
   $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $random_string = "";    
   
   for ($p = 0; $p < $length; $p++) {
		$random_string .= $characters[rand(0, strlen($characters)-1)];
	}
  
   $_SESSION["token"] = $random_string;
   
   $_SESSION["seed"] = "wopidom";
   $seed = $_SESSION["seed"];
   
   $sql="select * from generalSettings order by id";
   $settings =  $app['db']->fetchAll($sql); 
   
   $_SESSION["xp_level_pts"] = setXPLevels($settings[0]["xpMultiplier"]);
  
   $_SESSION["jokerPrice"] =  $settings[0]["jokerPrice"];
   $_SESSION["freezePrice"] = $settings[0]["freezePrice"]; 
   $_SESSION["livesWait"] = $settings[0]["wait"]; 
   $_SESSION["freeCoins"] = $settings[0]["freeCoins"]; 
   $_SESSION["lifeRegTime"] = $settings[0]["lifeRegTime"]; 
   
   $_SESSION["score"] = 0;
   $_SESSION["rightAnswers"] = 0;
   $_SESSION["wrongAnswers"] = 0;
   $_SESSION["rightAnswersList"] = array();
   $_SESSION["rightTimeList"] = array();
	   
   $_SESSION["usedJoker"] = 0;
   $_SESSION["usedFrenzy"] = 0;
   $_SESSION["isHighScore"] = 0;
   $_SESSION["newLevel"] = 0;
   $_SESSION["reachCombo"] = 0;		  
   $_SESSION["gameAnswers"] = array();	
   $_SESSION["rightZonesList"] = array();
   
   
   $ret["token"] = $random_string;
   $ret["general"] =  $settings[0];
   //$random_string = encrypt($ret,$seed);
   //return new Response(encrypt(json_encode($ret),$seed), 200);
   return new Response(json_encode($ret), 200);
});

function setXPLevels($xp_const)
{
	// SET XP THRESHOLDS
	$xp_level_pts[0] = 0;
	$xp_level_pts[1] = 100;
		
	for($i=2;$i<300;$i++)
	  $xp_level_pts[$i] = intval($xp_level_pts[$i-1] * $xp_const); 
	   
	return $xp_level_pts;
}

/*****************************************************************************************************/
/***** GET GAME QUESTIONS ****************************************************************************/
/*****************************************************************************************************/	
$app->get('/gQ.{format}', function(Request $request) use($app){
    
	$seed = $_SESSION["seed"];
	$encoded = $request->get('p');
    $token=decrypt($encoded,$seed);
    
	
    $_SESSION["score"] = 0;
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Questions::getQuestions();			
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);

		for($i=0; $i< sizeof($q); $i++)
		{	
			
			if($q[$i]["answer1"] !="TRUE" && $q[$i]["answer1"] !="FALSE")
			{
				//$q[$i]["answer1"] .= "(X)";
				$t = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
				shuffle($t);
				$right = array_search($q[$i]["answer1"], $t)+1; 
				$q[$i]["answer1"] = $t[0];
				$q[$i]["answer2"] = $t[1];
				$q[$i]["answer3"] = $t[2];
				$q[$i]["answer4"] = $t[3];
				$q[$i]["right"] = $right;
				$_SESSION["answers"][$i] = array($q[$i]["id"],$right,$q[$i]["idZone"]);				
			}
			else
			{
				$right = 1; 
				if($q[$i]["answer1"] == "TRUE")
					$right = 1; 
				else
					$right = 2; 
					
				$q[$i]["answer1"] = true;
				$q[$i]["answer2"] = false;
				$q[$i]["answer3"] = " ";
				$q[$i]["answer4"] = " ";
	
				$q[$i]["right"] = $right;
			    $_SESSION["answers"][$i] = array($q[$i]["id"],$right,$q[$i]["idZone"]);	
			}
			
			
		}
		
		
		return new Response(encrypt(json_encode($q),$seed), 200); 
	}
	else
	{
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);
	}	
    
});



/*****************************************************************************************************/
/***** CONSUME LIKE (AP) *****************************************************************************/
/*****************************************************************************************************/	
$app->get('/consumeLife.{format}', function(Request $request) use($app){

	global $app_id;
	$seed = $_SESSION["seed"];
	$token = decrypt($request->get('p'),$seed);
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
    {
		setNewLife();
	
		$sql = Players::consumeCredit($_SESSION["facebookId"]);	
		$app['db']->exec($sql);
		
		$sql = Players::newGame($_SESSION["facebookId"]);	
		$app['db']->exec($sql);
		
		$get_scores_data = getRankingFriends($_SESSION["access_token"]);
		$get_scores = sortScores($get_scores_data,$app['db'],false);
		$_SESSION["orignal_rank"] = $get_scores; 
		
		return new Response(encrypt(json_encode($_SESSION["orignal_rank"]),$seed), 200); 
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 	
	
});



/*****************************************************************************************************/
/***** GET PLAYER INFO *******************************************************************************/
/*****************************************************************************************************/	
$app->get('/gPi.{format}', function(Request $request) use($app){
    
    global $xp_const;
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 
	$data = explode(",",$decoded);	
	$token = $data[0];
	$idFacebook= $data[1];
	
	
	$_SESSION["facebookId"] = $idFacebook;
	$xp_level_points = $_SESSION["xp_level_pts"];

   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
   {
		
		updateRanking($app['db']);
		
		$sql = Players::getInfo($idFacebook);	
		$q = $app['db']->fetchAll($sql);
		
		date_default_timezone_set('Europe/Paris');
		$now = date("Y-m-d");
		
		$dif = timeDiff($now,$q[0]["lastGame"]);
		
		/// $sql = Players::exists($idFacebook);
		// $exists = $app['db']->fetchAll($sql);
		
		if($dif != 0)
		{	
			$q["freeCoins"] = $_SESSION["freeCoins"];
			$q[0]["coins"] += $_SESSION["freeCoins"];
			$sql = "update players set coins = coins + " .  $_SESSION["freeCoins"] . " where idFacebook='" . $idFacebook  . "'";
		    $app['db']->exec($sql);
		}
		else
			$q["freeCoins"] = 0;
			
		$sql = "update players set tournamentRead=0,lastGame='" . $now . "' where idFacebook='" . $idFacebook  . "'";
		$app['db']->exec($sql);
		
		$_SESSION["playerInfo"]=$q[0];
		$_SESSION["oldScore"] = $_SESSION["playerInfo"]["highScore"];
		$current_xp_level = $q[0]["XP"];
		
		$_SESSION["current_xp_level"] = $current_xp_level;
		$_SESSION["gameSessions"] = $q[0]["gameSessions"];
		
		$current_xp_points = $q[0]["XPpoints"];
				
		$percentXP = number_format(100*$current_xp_points/$xp_level_points[$current_xp_level],0); 
		$q[0]["percentXP"] = $percentXP;
		$_SESSION["percentXP"] = $percentXP;
		
		 $q[0]["items0"] = ApStore::getBags($app['db']);
         $q[0]["items1"] = ApStore::getLives($app['db']);
		
		$q = utf8_converter($q);
		
		$ret = endTournament($app['db']);
		$q["tournament"]["secondsToEnd"] = $ret["secondsToEnd"];// = $secs;//$difference;
		$q["tournament"]["minutesToEnd"] = $ret["minutesToEnd"];// = $mins;//floor($difference/60);
		$q["tournament"]["hoursToEnd"] = $ret["hoursToEnd"];// = $hours;// floor($difference/3600);
		$q["tournament"]["daysToEnd"] = $ret["daysToEnd"];
		$q["tournament"]["end"] = $ret["daysToEnd"];
		
		
		$rank = myRanking($app['db']);
		$q["ranking"] = $rank;
		
		$q[0]["jokerPrice"] = $_SESSION["jokerPrice"];
   		$q[0]["freezePrice"] = $_SESSION["freezePrice"];
		
		$q[0]["levelThreshold"] = $xp_level_points[$current_xp_level];
		$q[0]["levelThreshold -1"] =  $xp_level_points[$current_xp_level-1];	
		
		$sql = "select count(*) newMsg from inbox where isNew=1 and idTo='" . $_SESSION["facebookId"] . "'";
		
		$inbox = $app['db']->fetchAll($sql);
		$q[0]["isNewCount"] = $inbox[0]["newMsg"];
			
		$sql = "select count(*) as newHints from playersHints where isNew=1 and idFacebook='" . $idFacebook  . "'";
		$hints = $app['db']->fetchAll($sql);
		$q[0]["hasNewHints"] = $hints[0]["newHints"];
		$q["wait"] = $_SESSION["livesWait"];
		
		return new Response(encrypt(json_encode($q),$seed), 200); 	
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 		
    
});



/*****************************************************************************************************/
/***** GET GAME TIME SYNCH ***************************************************************************/
/*****************************************************************************************************/	
$app->get('/gT.{format}', function(Request $request) use($app){
   
    $seed = $_SESSION["seed"];
	$encoded = $request->get('p');
    $token = decrypt($encoded,$seed);
  
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		date_default_timezone_set('Europe/Paris');
		$format = 'Y-m-d H:i:s';
		$str = date($format);
		$dt = DateTime::createFromFormat($format, $str);
		$timestamp = $dt->format('U');
		return new Response($timestamp, 200); 
		 
	}
	else
		return new Response(encrypt("Invalid Token",$seed), 200); 

});



/*****************************************************************************************************/
/***** SEND ANSWER ***********************************************************************************/
/*****************************************************************************************************/	
$app->get('sA.{format}', function(Request $request) use($app){
    
	$seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 
	$data = explode(",",$decoded);	
	
	$token = $data[0];
	$idAnswer= $data[1];
    $idQuestion= $data[2];
	$score = $data[3];
    $time= $data[4];

    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		global $totalScore;
		
		$result=0;
		$i=0;
		$coins= 0;
		$stored_right = 0;
		array_push($_SESSION["gameAnswers"],1);
		while($i<sizeof($_SESSION["answers"]) && $result==0)
		{
			if($_SESSION["answers"][$i][0]==$idQuestion)
			{
				if($_SESSION["answers"][$i][1]==$idAnswer)
				{
					$result="1";
					$_SESSION["score"] += $score;
					$_SESSION["rightAnswers"]++;
										
					$stored_right = $_SESSION["answers"][$i][1];
					array_push($_SESSION["rightAnswersList"],$idQuestion);
					array_push($_SESSION["rightTimeList"],number_format($time,2));	
					array_push($_SESSION["rightZonesList"],$_SESSION["answers"][$i][2]);	
					
					$sql = Players::rightAnswer($_SESSION["facebookId"],$idQuestion);
					$app['db']->exec($sql);
				}
				else
				{
					$_SESSION["wrongAnswers"]++;
					array_push($_SESSION["gameAnswers"],0);
				}
			}	
		
			$i++;	
		}
		
	    $ret= "Result: " .  $result . " | Q: " . $idQuestion .  " | A: " . $idAnswer .   " | STORED A: " . $stored_right;
		return new Response(encrypt($ret,$seed), 200);
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 		
     
});



/*****************************************************************************************************/
/***** USE JOKER ********************************************************************************/
/*****************************************************************************************************/	
$app->get('gJ1.{format}', function(Request $request) use($app){
    
    $seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 
	$data = explode(",",$decoded);	
	$token = $data[0];	
	$idQuestion = $data[1];
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Players::useJoker($_SESSION["facebookId"]);
		$app['db']->exec($sql);
		$_SESSION["usedJoker"] = 1; 
		$_SESSION["jokerQuestion"] = Questions::getQuestionById($idQuestion,$app['db']);
		return new Response(encrypt(1,$seed), 200);  
	}	
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 		
	
	 
});

/*****************************************************************************************************/
/***** USE FREEZE ************************************************************************************/
/*****************************************************************************************************/	
$app->get('gJ2.{format}', function(Request $request) use($app){
    
   $seed = $_SESSION["seed"];
	
   $param = $request->get('p');
   $token=decrypt($param,$seed);

   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
   {	
	   $sql = Players::useJoker($_SESSION["facebookId"]);
	   $app['db']->exec($sql); 
	   $_SESSION["usedFrenzy"] = sizeof($_SESSION["gameAnswers"]) + 1;
	   return new Response(encrypt(1,$seed), 200); 
   }
   else
   	   return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 	   
    
	 
});



/*****************************************************************************************************/
/***** GET SCORE *************************************************************************************/
/*****************************************************************************************************/	
$app->get('gS.{format}', function(Request $request) use($app){
    
	$seed = $_SESSION["seed"];
	$encoded = $request->get('p');
    $token=decrypt($encoded,$seed);
	
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{		
		$xp_level_points = $_SESSION["xp_level_pts"];
		$score = $_SESSION["score"];
		$bonus = getBonus($_SESSION["current_xp_level"]) * $score;
		$bonusPercent = 100 * getBonus($_SESSION["current_xp_level"]);

		$r = getReward($score+$bonus,$app['db']);
		$coins = $r["coins"];
		$xp_points_earned = $r["xp"];
			
		$final_score = $score+$bonus;
		$high_score=$_SESSION["playerInfo"]["highScore"];
		$new_level = $_SESSION["current_xp_level"];
		$original_xp_points = $_SESSION["playerInfo"]["XPpoints"];
		
		if(($original_xp_points) + $xp_points_earned > ($xp_level_points[$new_level]) )
		{	
			$xp_points_earned = ($original_xp_points + $xp_points_earned) - ($xp_level_points[$new_level]) ;
			$new_level++;
			$_SESSION["newLevel"] = 1;
			$_SESSION["level"] = $new_level;
		}	
		else
		   if($original_xp_points + $xp_points_earned == ($xp_level_points[$new_level]) )
		   {
		    $xp_points_earned = 0;
			$new_level++;
			$_SESSION["newLevel"] = 1;
			$_SESSION["level"] = $new_level;
		   }
		   else
		    $xp_points_earned = $original_xp_points + $xp_points_earned ;
		
		$_SESSION["isHighScore"] = 0;
		
		if($high_score<$final_score)
		{
			$high_score=intval($final_score);
			$_SESSION["isHighScore"] = 1;
		}
		
		$_SESSION["highScore"] = intval($high_score);
			
	$values = array(intval($score),intval($bonus),$coins,100*$xp_points_earned/$xp_level_points[$new_level],$new_level,$_SESSION["isHighScore"],$bonusPercent,$_SESSION["rightAnswers"],$_SESSION["wrongAnswers"]);		 
		$sql = Players::updateData($_SESSION["facebookId"], $coins,$xp_points_earned,$high_score,$new_level);
    	$app['db']->exec($sql);
		
		return new Response(encrypt(json_encode($values),$seed), 200);	
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 		
  
});


/*****************************************************************************************************/
/***** FACEBOOK LOGIN - SERVER ***********************************************************************/
/*****************************************************************************************************/	
$app->get('/loginFB.{format}', function(Request $request) use($app){
   
   session_start();
   $seed = $_SESSION["seed"];
   $param = $request->get('p');
   $decoded=decrypt($param,$seed);
	 
   $data = explode(",",$decoded);	
   $token = $data[0];	
   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
   {  
	   if(isset($data[1]) && strlen($data[1])>0)
	   {
		 $graph_url = "https://graph.facebook.com/me?access_token=" . $data[1];
		  $_SESSION["access_token"] = $data[1];
	   }	 
	   else
		$graph_url = "https://graph.facebook.com/me?access_token=" . $_SESSION["access_token"];
	  
	   $profile_graph = file_get_contents($graph_url);
	   $profile = json_decode($profile_graph,true);
	   
	   $_SESSION["facebookId"] = $profile["id"];
	   $_SESSION["first_name"] = $profile["first_name"];
	   $_SESSION["last_name"] = $profile["last_name"];
	   
	  // echo $helper->config('facebookAppId');
	   if($profile!="")		
		 {
		   $sql = Players::exists($profile["id"]);
		   $exists = $app['db']->fetchAll($sql);
		  
		   if($exists[0]["cant"]=="0")
		   {
		    
			 Players::insertPlayer($profile,$app['db']);
			 return new Response(encrypt($profile_graph,$seed), 200);
		   }
		   else
		   {
		   
			return new Response(encrypt($profile_graph,$seed),200);
		   }
		} 
		
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 			  
   
});


/*****************************************************************************************************/
/***** TROPHIES ROOM DATA ****************************************************************************/
/*****************************************************************************************************/	
$app->get('/trophiesRoom.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	$encoded = $request->get('p');
    $token=decrypt($encoded,$seed);
	
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{	
	
		 $sql = "select * from trophies order by id";
		 $data = $app['db']->fetchAll($sql);
		 $result=array();
		
		 for($i=0; $i< sizeof($data); $i++)
		 {
			 $result[$i]["id"] = $data[$i]["id"];
			 $result[$i]["name"] = $data[$i]["name"];
			  
			 $sql = "select * from playersTrophies,trophies where playersTrophies.idTrophy=trophies.id and idFacebook=" . $_SESSION["facebookId"] . 
			 " and idTrophy=" . $data[$i]["id"] . " order by stars desc";
			 $t = $app['db']->fetchAll($sql);
			 
			 if(isset($t[0]["stars"]))
				$result[$i]["stars"]=$t[0]["stars"];  
			  else 
			   $result[$i]["stars"]= -1;
			 
			 if(isset($t[0]["stars"]) && $t[0]["stars"]>0 && $t[0]["stars"]<5)
			 { 
				 $result[$i]["diff"] = $data[$i]["value" . ($t[0]["stars"]+1)] - $data[$i]["value" . $t[0]["stars"]];
				 $result[$i]["xp"] =  $data[$i]["XP" . ($t[0]["stars"]+1)];
				 $result[$i]["coins"] = $data[$i]["coins" . ($t[0]["stars"]+1)];
				 $result[$i]["lives"] = $data[$i]["lives" . ($t[0]["stars"]+1)];
				
				 if($data[$i]["id"]<=6)
				 {
					 $result[$i]["actual"] = Players::getRightAnswersByZoneId($_SESSION["facebookId"],$t[0]["idZone"],$app['db']) - $data[$i]["value" . $t[0]["stars"]];
					 $result[$i]["percent"] = number_format(100 * $result[$i]["actual"] /  $result[$i]["diff"],2);
					 $result[$i]["next"] = str_replace("XX",($result[$i]["diff"] - $result[$i]["actual"]),$data[$i]["roomText"]);
				 }	 
				 if($data[$i]["id"]==7)
				 {
					 $result[$i]["actual"] = Players::getRightAnswersByZoneId($_SESSION["facebookId"],$t[0]["idZone"],$app['db']) - $data[$i]["value" . $t[0]["stars"]];
					 $result[$i]["percent"] = number_format(100 * $result[$i]["actual"] /  $result[$i]["diff"],2);
					 $text = str_replace("XX",($result[$i]["diff"] - $result[$i]["actual"]),$data[$i]["roomText"]);
					 $text = str_replace("YY",$result[$i]["lives"],$text);
				     $result[$i]["next"] =  $text;
				 }	 
				 
				 if($data[$i]["id"]==8)	 
				 {
					 $result[$i]["actual"] = Players::jokersUsed($_SESSION["facebookId"],$app['db']) - $data[$i]["value" . $t[0]["stars"]];
					 $result[$i]["percent"] = number_format(100 * $result[$i]["actual"] /  $result[$i]["diff"],2);
					 $result[$i]["next"] =   str_replace("XX",($result[$i]["diff"] - $result[$i]["actual"]),$data[$i]["roomText"]); 
				 }
				 
				 if($data[$i]["id"]==9)
					$result[$i]["next"] =  str_replace("XX",$data[$i]["value" . ($t[0]["stars"]+1)],$data[$i]["roomText"]); 
				 
				 if($data[$i]["id"]==11)
					$result[$i]["next"] =   str_replace("XX",$data[$i]["value" . ($t[0]["stars"]+1)],$data[$i]["roomText"]); 
				 
				 if($data[$i]["id"]==12)	
				 { 
					 $result[$i]["actual"] = Players::getHighScore($_SESSION["facebookId"],$app['db']) - $data[$i]["value" . $t[0]["stars"]];
					 $result[$i]["percent"] = number_format(100 * $result[$i]["actual"] /  $result[$i]["diff"],2);
					 $result[$i]["next"] =   str_replace("XX",$data[$i]["value" . ($t[0]["stars"]+1)],$data[$i]["roomText"]); 
				 }
				 if($data[$i]["id"]==15)	
				 { 
					 $result[$i]["actual"] = Players::getGames($_SESSION["facebookId"],$app['db']) - $data[$i]["value" . $t[0]["stars"]];
					 $result[$i]["percent"] = number_format(100 * $result[$i]["actual"] /  $result[$i]["diff"],2);
					 $text =  str_replace("XX",$data[$i]["value" . ($t[0]["stars"]+1)],$data[$i]["roomText"]); 
					 $text =  str_replace("YY",$result[$i]["lives"],$text);
					 $text =  str_replace("ZZ",$result[$i]["coins"],$text);
					 $result[$i]["next"] =  $text;
				 }
				 
				 if($data[$i]["id"]==16)	
				 { 
				   $result[$i]["next"] =   str_replace("XX",$data[$i]["value" . ($t[0]["stars"]+1)],$data[$i]["roomText"]);
				 } 
			 }
			 else
			 if($result[$i]["stars"]== -1)
			 {
				 
				 $result[$i]["diff"] = $data[$i]["value1"];
				 $result[$i]["xp"] =  $data[$i]["XP1"];
				 $result[$i]["coins"] = $data[$i]["coins1"];
				 $result[$i]["lives"] = $data[$i]["lives1"];
				 
				 if($data[$i]["id"]<=6)
					$result[$i]["next"] = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]);
				 
				 if($data[$i]["id"]==7)	 
				 {
					 $text = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]);
					 $text =  str_replace("YY",$data[$i]["lives1"],$text); 
					 $result[$i]["next"] = $text;
				 }	
				
				 if($data[$i]["id"]==8)	 
					$result[$i]["next"] = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]); 
				
				 if($data[$i]["id"]==9)
					$result[$i]["next"] = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]); 
				 
				 if($data[$i]["id"]==11)
					$result[$i]["next"] = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]); 
				 
				 if($data[$i]["id"]==12)	
				 { 
					  $result[$i]["next"] = str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]); 
				 }
				 
				 if($data[$i]["id"]==15)	
				 { 
					 $text =  str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]); 
					 $text =  str_replace("YY",$data[$i]["lives1"],$text);
					 $text =  str_replace("ZZ",$data[$i]["coins1"],$text);
					 $result[$i]["next"] =  $text;
				 }
				 
				 if($data[$i]["id"]==16)	
				 { 
				   $result[$i]["next"] =   str_replace("XX",$data[$i]["value1"],$data[$i]["roomText"]);
				 } 
	
			 }
		}	
		return new Response(encrypt(json_encode($result),$seed), 200);	
	}
	else
		return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 			
	
});



/*****************************************************************************************************/
/***** CAN BUY (JOKER OR FREEZE) *********************************************************************/
/*****************************************************************************************************/	
$app->get('/canBuy.{format}', function(Request $request) use($app){

 	$seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	$token = $data[0];	
	$idJoker= $data[1];
	
   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
   { 
     $sql = Players::getCoins($_SESSION["facebookId"]);
	 $data = $app['db']->fetchAll($sql);
	 $coins = $data[0]["coins"];
 
	 if($idJoker==1)
	 { 
		if($coins >=  intval($_SESSION["jokerPrice"]))
		{
		  $count = buyJoker(1, $app['db']);
		  return new Response(encrypt($count,$seed), 200);
		}
		else
		  return new Response(encrypt(-1,$seed), 200);
	 }	
	 else
	 {	
		if($coins >= intval($_SESSION["freezePrice"]))
		{
		  $count = buyJoker(2, $app['db']);
		  return new Response(encrypt($count,$seed), 200);
		}  
		else
		  return new Response(encrypt(-1,$seed), 200);
	 }	 
  }
   else
     return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);

});



/*****************************************************************************************************/
/***** RETURN BOOSTS ********************************************************************************/
/*****************************************************************************************************/	
$app->get('/returnBoost.{format}', function(Request $request) use($app){
    
 	$seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 
	$data = explode(",",$decoded);	
	
	$token = $data[0];	
	$idJoker= $data[1];

	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
    { 
		if($idJoker==1)
		  $currencyToReturn = $_SESSION["jokerPrice"];
		else
		  $currencyToReturn =  $_SESSION["freezePrice"];
		
		$sql = Players::returnJoker($_SESSION["facebookId"],$currencyToReturn);
		$result = $app['db']->exec($sql);	
		
		$sql = Players::getCoins($_SESSION["facebookId"]);
		$data = $app['db']->fetchAll($sql);
		$coins = $data[0]["coins"];
		 
		return new Response(encrypt($coins,$seed), 200);
	}
    else
     return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);		
	
});	


/*****************************************************************************************************/
/***** CHECK ACHIEVEMENTS (AND TRIGGER) **************************************************************/
/*****************************************************************************************************/	
$app->get('/checkAchievements.{format}', function(Request $request) use($app){
   
	$seed = $_SESSION["seed"];
	
	$encoded = $request->get('p');
    $token=decrypt($encoded,$seed);
	
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{	

		$sql = Players::getRightAnswersByZone($_SESSION["facebookId"]);
		$data = $app['db']->fetchAll($sql);
		$achData = array();
		$queries= "";
		$j=0;
		
		for($i=0; $i< sizeof($data); $i++)
		{
			$aux = Players::validateAchievement($data[$i]["id"],$data[$i]["answers"],$app['db']);
			$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
			if($aux["stars"]>0 && $won==0)
			{	  
				$achData[$j]["id"] = $aux["idT"]; 
				$achData[$j]["stars"] = $aux["stars"]; 
				$achData[$j]["name"] = $aux["name"]; 
				$achData[$j]["XP"] = $aux["XP"]; 
				$achData[$j]["coins"] = $aux["coins"]; 
				$achData[$j]["lives"] = $aux["lives"]; 
				$achData[$j]["description"] = str_replace("XX",$aux["value"],$aux["description"]);
				$post_res = postAchievement($aux["idT"],$aux["stars"]);
				$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
				$queries.= $sql . ";";
				$j++;
			} 
		}
		
		// THE DUMMY ***************************************************************************************/
		/*unset($aux);
		$won = Players::isDummyWon($_SESSION["facebookId"],$app['db']);
		if($_SESSION["rightAnswers"]==0 && $won==0)
		{   
			$achData[$j]["id"] = 16; 
			$achData[$j]["stars"] = 1; 
			$achData[$j]["name"] = "The Dummy"; 
			$achData[$j]["XP"] = 0; 
			$achData[$j]["coins"] = 0; 
			$achData[$j]["lives"] = 0; 
			
			postAchievement(16,0);
			$sql = Players::insertAchievement($_SESSION["facebookId"],16,1);
			$queries.= $sql . ";";
			//$app['db']->exec($sql);
			$j++;
		}*/
		
		// GOOD GAME ACHIEVEMENT *****************************************************************/
		unset($aux);
		$aux = Players::goodAnswersAchievement($_SESSION["rightAnswers"],$app['db']);
		$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
		if($won==0 && $aux["stars"]>0)
		{
			$achData[$j]["id"] = $aux["idT"]; 
			$achData[$j]["stars"] = $aux["stars"]; 
			$achData[$j]["name"] = $aux["name"]; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =  str_replace("XX",$aux["value"],$aux["description"]); //$aux["description"];
			postAchievement($aux["idT"],$aux["stars"]);
			$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
			$queries.= $sql . ";";
			$j++;
		}
		
		// HIGSCORE ACHIEVEMENT ********************************************************************/
		unset($aux);
		if(isset($_SESSION["highScore"]))
		{
			$aux = Players::highScoreAchievement($_SESSION["highScore"],$app['db']);
			$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
			if($won==0 && $aux["stars"]>0)
			{
				$achData[$j]["id"] = $aux["idT"]; 
				$achData[$j]["stars"] = $aux["stars"]; 
				$achData[$j]["name"] = $aux["name"]; 
				$achData[$j]["XP"] = $aux["XP"]; 
				$achData[$j]["coins"] = $aux["coins"]; 
				$achData[$j]["lives"] = $aux["lives"]; 
				$achData[$j]["description"] =  str_replace("XX",$aux["value"],$aux["description"]); // $aux["description"];
				postAchievement($aux["idT"],$aux["stars"]);
				$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
				$queries.= $sql . ";";
				$j++;
			}	
		}
		
		// THE KING ***************************************************************************************/
		unset($aux);	
		$aux = Players::kingAchievement($app['db']);
		$won = Players::isKingWon($_SESSION["facebookId"],$app['db']);	
		if($_SESSION["wrongAnswers"]==0 && sizeof($_SESSION["gameAnswers"])>0 && $won==0)
		{   
			$achData[$j]["id"] = 14; 
			$achData[$j]["stars"] = 1; 
			$achData[$j]["name"] = "King of Dummies"; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =  $aux["description"];
			postAchievement(14,0);
			$sql = Players::insertAchievement($_SESSION["facebookId"],14,1);
			$queries.= $sql . ";";
			$j++;
		}	
	
		// ADDICT ********************************************************************/	
		unset($aux);
		$aux = Players::addictAchievement($_SESSION["gameSessions"],$app['db']);
		$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
		if($won==0 && $aux["stars"]>0)
		{
			$achData[$j]["id"] = $aux["idT"]; 
			$achData[$j]["stars"] = $aux["stars"]; 
			$achData[$j]["name"] = $aux["name"]; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =   str_replace("XX",$aux["value"],$aux["description"]); // $aux["description"];
			postAchievement($aux["idT"],$aux["stars"]);
			$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
			$queries.= $sql . ";";
			$j++;
		}	
		
		// JOKERS ACHIEVEMENTS ********************************************************************/
		unset($aux);
		$jokersCount = Players::jokersUsed($_SESSION["facebookId"],$app['db']);
		$aux = Players::jokerAchievement($jokersCount,$app['db']);
		$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
		if($won==0 && $aux["stars"]>0)
		{
			$achData[$j]["id"] = $aux["idT"]; 
			$achData[$j]["stars"] = $aux["stars"]; 
			$achData[$j]["name"] = $aux["name"]; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =   str_replace("XX",$aux["value"],$aux["description"]);
			postAchievement($aux["idT"],$aux["stars"]);
			$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
			$queries.= $sql . ";";
			$j++;
		}	
		
		// RESCUER ACHIEVEMENTS ********************************************************************/
		unset($aux);
		$livesSent = Players::livesSent($_SESSION["facebookId"],$app['db']);
		$aux = Players::livesAchievement($livesSent,$app['db']);
		$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
		if($won==0 && $aux["stars"]>0)
		{
			$achData[$j]["id"] = $aux["idT"]; 
			$achData[$j]["stars"] = $aux["stars"]; 
			$achData[$j]["name"] = $aux["name"]; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =   str_replace("XX",$aux["value"],$aux["description"]); // $aux["description"];
			postAchievement($aux["idT"],$aux["stars"]);
			$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
			$queries.= $sql . ";";
			$j++;
		}	
			
			
		// CHAIN ACHIEVEMENT ********************************************************************/
		unset($aux);
		$aux = Players::chainAchievement($_SESSION["gameAnswers"],$app['db']);
		$won = Players::isTrophyWon($_SESSION["facebookId"],$aux["idT"],$aux["stars"],$app['db']);
		if($won==0 && $aux["stars"]>0)
		{
			$achData[$j]["id"] = $aux["idT"]; 
			$achData[$j]["stars"] = $aux["stars"]; 
			$achData[$j]["name"] = $aux["name"]; 
			$achData[$j]["XP"] = $aux["XP"]; 
			$achData[$j]["coins"] = $aux["coins"]; 
			$achData[$j]["lives"] = $aux["lives"]; 
			$achData[$j]["description"] =  str_replace("XX",$aux["value"],$aux["description"]); //  $aux["description"];
			postAchievement($aux["idT"],$aux["stars"]);
			$sql = Players::insertAchievement($_SESSION["facebookId"],$aux["idT"],$aux["stars"]);
			$queries.= $sql . ";";
			$j++;
		}	
		
		if(strlen($queries)>0)
		$app['db']->exec($queries);
		return new Response(encrypt(json_encode($achData),$seed),200);
   }
   else
     return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);	

});



/******************************************************************************************************/
/***** SEND SCORE TO FB API ***************************************************************************/
/*****************************************************************************************************/	
$app->get('/sSFB.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];	
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$post_score = https_post('https://graph.facebook.com/' .  $_SESSION["facebookId"] . '/scores' ,'score=' . $_SESSION["highScore"] . '&access_token=' . 
		$_SESSION["access_token"]);
		
		return new Response(encrypt(json_encode($post_score),$seed), 200); 
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);	
	  
});


$app->get('/cronTournament.{format}', function(Request $request) use($app){

  $app_id = "243850462418561"; 
  $app_secret = "76846d38a336909c68fe6b01feb483da";
  
  $p = Players::getAll($app['db']);

	
  for($j=0;$j<sizeof($p);$j++)
  {	
		$sql = "SELECT p2.idFacebook, p2.highScore, p2.name, p2.lastName
		FROM players p, ranking r, players p2
		WHERE p.idFacebook = r.idPlayer
		AND r.idFriend = p2.idFacebook
		AND p2.highScore > 0
		AND p.idFacebook =  '" . $p[$j]["idFacebook"] . "' 
		ORDER BY p2.highScore DESC";

		$data = $app['db']->fetchAll($sql);
		$position=0;
		$score=0;
		
		for($i=0;$i< sizeof($data);$i++)
		{
			echo  $data[$i]["idFacebook"] . " " .  $data[$i]["name"] . " " . $data[$i]["highScore"] . "\n";
			if($data[$i]["idFacebook"] == $p[$j]["idFacebook"])
				{$position = $i+1;$score=$data[$i]["highScore"];}
		}
		echo $p[$j]["name"] . " " . $p[$j]["lastName"] . " - Pos " . $position . "\n";	
		Players::updateTournament($p[$j]["idFacebook"],$score,$position,$app['db']);
		
		echo "\n\n";

  }	
 
  $sql = "update players set highScore=0";
  $app['db']->exec($sql);

  $sql = "update generalSettings set tournamentEnding = DATE_ADD(tournamentEnding,INTERVAL 7 DAY)";
  $app['db']->exec($sql);
  
  $app_token_url = "https://graph.facebook.com/oauth/access_token?"
				. "client_id=" . $app_id
				. "&client_secret=" . $app_secret 
				. "&grant_type=client_credentials";
		
  $app_token = file_get_contents($app_token_url);
  
  $post_score = https_delete('https://graph.facebook.com/' . $app_id . '/scores' ,$app_token);
  		
});



$app->get('/addLife.{format}', function(Request $request) use($app){
    
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];	
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Players::addCredit($_SESSION["facebookId"]);	
		$app['db']->exec($sql);
	
		return new Response(encrypt(1,$seed), 200); 
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);
});



$app->get('/nL.{format}', function(Request $request) use($app){
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];	
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
	
		date_default_timezone_set('Europe/Paris');
		$now = date("Y-m-d H:i:s");
		$next_token_time=""; 
		
		if(!isset($_SESSION["start_credit_time"]))
			$next_token_time = "1970-01-01 10:00:00";
		else
			$next_token_time = $_SESSION["start_credit_time"];
	
		
		$difference = timeDiff($next_token_time,$now);
			
		$interval= $_SESSION["lifeRegTime"] * 60;
		
		$livesDB = Players::getLives($_SESSION["facebookId"],$app['db']);
		$regenLives = floor($difference/$interval);
		$totalLives = $livesDB + $regenLives;
		
		if($livesDB>5)
			$totalLives  = $livesDB;
		else
		{
			$totalLives = $livesDB + $regenLives;
			if($totalLives>5) $totalLives = 5;
		}
	 
		$ret["lives"] = $totalLives;
		$ret["livesDB"] = $livesDB;
		$ret["secondsFromSNL"] = $difference;
		$ret["minutesFromSNL"] = floor($difference/60);
		$ret["newLives"] = floor($difference/$interval);
		$ret["seconds"] = $interval - ($difference % $interval);
		$ret["started"] = $next_token_time;
		
		Players::updateLives($_SESSION["facebookId"],$totalLives,$app['db']);
		
		return new Response(encrypt(json_encode($ret),$seed), 200); 
	}
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);
	  	
});


$app->get('/setCombo.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	$decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];	
	$idQuestion = $data[1];	
		
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		if(!isset($_SESSION["reachCombo"]) || $_SESSION["reachCombo"]!=1)
		{
			$q = Questions::getHintById(urlencode($idQuestion),$app['db']);
		    echo "question: " . $q;
			$_SESSION["hintText"] = $q;
			$_SESSION["newHint"] = 1;
			$_SESSION["hintTopic"] = Questions::getTopicById($idQuestion,$app['db']);
			$sql = "insert into playersHints(idFacebook,idQuestion,isNew) values('" . $_SESSION["facebookId"] . "'," . $idQuestion. ",1)";
			$app['db']->exec($sql);
			
			$comboQuestion = Questions::getQuestionById($idQuestion,$app['db']);
			$_SESSION["comboQuestion"] = $comboQuestion;
		}
		
		$_SESSION["reachCombo"] = 1;
		return new Response(encrypt(1,$seed), 200); 
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);		

});


$app->get('/checkOG.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	$decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{	

		if(sizeof($_SESSION["rightAnswersList"])>0)
		{
			$random = rand(0,sizeof($_SESSION["rightAnswersList"])-1);
			$q = Questions::getQuestionById(urlencode($_SESSION["rightAnswersList"][$random]),$app['db']);
			OGActions::postQuestion(utf8_encode(str_replace('"','\'',$q)),$_SESSION["rightTimeList"][$random],$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
		}
		
		// OG: USE - Joker
		if($_SESSION["usedJoker"] != 0)
			OGActions::postJoker($_SESSION["jokerQuestion"],$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
		
		// OG: USE - Frenzy
		if($_SESSION["usedFrenzy"] != 0)
			OGActions::postFreeze($_SESSION["usedFrenzy"],$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
			
		// OG: BEAT - BEST SCORE
		if($_SESSION["isHighScore"] == 1)
			OGActions::postBestScore($_SESSION["highScore"],$_SESSION["access_token"],$_SESSION["first_name"],$_SESSION["oldScore"],$app['db']);
			
		// OG: INCREASE - LEVEL
		if($_SESSION["newLevel"] == 1)	
			OGActions::postLevel($_SESSION["level"],$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
		
		// OG: FILL - COMBO	
		if($_SESSION["reachCombo"] == 1)		
			OGActions::postCombo($_SESSION["highScore"],$_SESSION["access_token"],$_SESSION["first_name"],$_SESSION["comboQuestion"],$app['db']);	
			
		if($_SESSION["newHint"]==1)
			OGActions::postHint($_SESSION["hintTopic"],$_SESSION["access_token"],$_SESSION["first_name"],$_SESSION["hintText"],$app['db']);	
			
		 // OG: Learn more about DEPRECATED
		 /*
		 $zonesCount = array_count_values($_SESSION["rightZonesList"]);
	 
		 if(!isset($zonesCount[1])) $zonesCount[1]=0;
		 if(!isset($zonesCount[2])) $zonesCount[2]=0;
		 if(!isset($zonesCount[3])) $zonesCount[3]=0;
		 if(!isset($zonesCount[4])) $zonesCount[4]=0;
		 if(!isset($zonesCount[5])) $zonesCount[5]=0;
		 if(!isset($zonesCount[6])) $zonesCount[6]=0;
		
	 
		if($zonesCount[1]+$_SESSION["playerInfo"]["zone1Count"] >= 10){ Players::updateZones($_SESSION["facebookId"],1,$app['db']); OGActions::postLearn("celebrities",$_SESSION["access_token"],$_SESSION["first_name"]); }
		if($zonesCount[2]+$_SESSION["playerInfo"]["zone2Count"] >= 10){ Players::updateZones($_SESSION["facebookId"],2,$app['db']); OGActions::postLearn("movies",$_SESSION["access_token"],$_SESSION["first_name"]); }
		if($zonesCount[3]+$_SESSION["playerInfo"]["zone3Count"] >= 10){ Players::updateZones($_SESSION["facebookId"],3,$app['db']); OGActions::postLearn("general_culture",$_SESSION["access_token"],$_SESSION["first_name"]); }
		if($zonesCount[4]+$_SESSION["playerInfo"]["zone4Count"]>= 10){Players::updateZones($_SESSION["facebookId"],4,$app['db']);OGActions::postLearn("history_geography",$_SESSION["access_token"],$_SESSION["first_name"]);  }
		if($zonesCount[5]+$_SESSION["playerInfo"]["zone5Count"] >= 10){ Players::updateZones($_SESSION["facebookId"],5,$app['db']); OGActions::postLearn("music",$_SESSION["access_token"],$_SESSION["first_name"]); }
		if($zonesCount[6]+$_SESSION["playerInfo"]["zone6Count"] >= 10){ Players::updateZones($_SESSION["facebookId"],6,$app['db']); OGActions::postLearn("sports",$_SESSION["access_token"],$_SESSION["first_name"]); }
	    */
	
		$_SESSION["score"] = 0;
		$_SESSION["rightAnswers"] = 0;
		$_SESSION["wrongAnswers"] = 0;
		$_SESSION["rightAnswersList"] = array();
		$_SESSION["rightTimeList"] = array(); 
		$_SESSION["usedJoker"] = 0;
		$_SESSION["usedFrenzy"] = 0;
		$_SESSION["isHighScore"] = 0;
		$_SESSION["newLevel"] = 0;
		$_SESSION["reachCombo"] = 0;  
		$_SESSION["gameAnswers"] = array();
		$_SESSION["rightZonesList"] = array();

		return new Response(encrypt(1,$seed), 200); 
		
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);	
});


$app->get('/purchaseItems.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $decoded=decrypt($param,$seed);
 
	$data = explode(",",$decoded);	
	
	$token = $data[0];	
	$idItem = $data[1];

	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$items = ApStore::getItems($idItem,$app['db']);
			
		$result = 0;	
		
		if(substr($idItem,0,3) == "bag")
		{
			Players::addCoins($_SESSION["facebookId"],$items["value"],$app['db']);
			$sql = Players::getCoins($_SESSION["facebookId"]);
			$data = $app['db']->fetchAll($sql);
			$result = $data[0]["coins"];
		}	
		else
		{
			Players::addLives($_SESSION["facebookId"],$items["value"],$app['db']);
			$result = Players::getLives($_SESSION["facebookId"],$app['db']);
		}	
		
		return new Response(encrypt($result,$seed), 200); 
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);
	  
});


$app->get('/getRanking.{format}', function(Request $request) use($app){

	global $app_id;
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	$decoded=decrypt($param,$seed);
 	
	$data = explode(",",$decoded);	
	$token = $data[0];
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{	
		if(isset($_SESSION["orignal_rank"]))
		{
			$originalRank =  $_SESSION["orignal_rank"]; 
			$originalPosition=0;
			for($i=0;$i< sizeof($originalRank);$i++)
			{
				if($originalRank[$i]["id"] == $_SESSION["facebookId"])
					$originalPosition = $i+1;
			}
		}
		
		$get_scores_data = getRankingFriends($_SESSION["access_token"]);
		$get_scores = sortScores($get_scores_data,$app['db'],false);
		
		$_SESSION["orignal_rank"] = $get_scores; 
		$myData = $get_scores; 
	
		$newRank = $myData;
		$newPosition = 0;
		for($i=0;$i< sizeof($newRank);$i++)
		{
			if($newRank[$i]["id"] == $_SESSION["facebookId"])
				$newPosition = $i+1;
		}
		
		$result=array();
		if($newPosition<$originalPosition)
		{
			$result[0]["position"] = $newPosition;
			$result[0]["name"] = $newRank[$newPosition-1]["player"];
			$result[0]["score"] = $newRank[$newPosition-1]["score"];
			$result[0]["id"] = $newRank[$newPosition-1]["id"];
				
			$result[1]["position"] = $newPosition+1; 
			$result[1]["name"] = $newRank[$newPosition]["player"]; 
			$result[1]["score"] = $newRank[$newPosition]["score"]; 
			$result[1]["id"] = $newRank[$newPosition]["id"];
			
		}	
		
		updateRanking($app['db']);
		
		return new Response(encrypt(json_encode($result),$seed), 200);
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);
	
});


$app->get('/getItems.{format}', function(Request $request) use($app){
  
  	$seed = $_SESSION["seed"];
	$param = $request->get('p');
    $encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	
	$token = $data[0];	
	$type = $data[1];
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{  
		$items="";
		if($type==0)
		  $items = ApStore::getBags($app['db']);
		else
		  $items = ApStore::getLives($app['db']);
		   
		return new Response(encrypt(json_encode($items),$seed), 200);
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 
});



$app->get('/removeApp.{format}', function(Request $req) use($app){

	global $app_secret;

	// Validate request is from Facebook and parse contents for use.
	$request = parse_signed_request($req->get('signed_request'), $app_secret);
    
	error_log('POST Request = ' . print_r($request, true), 3, './deauthorize.log');

});



$app->post('/pay.{format}', function(Request $req) use($app){

	//global $app_secret;
	$app_secret = "76846d38a336909c68fe6b01feb483da"; 
	// Validate request is from Facebook and parse contents for use.
	$request = parse_signed_request($req->get('signed_request'), $app_secret);

	// Get request type.
	// Two types:
	//   1. payments_get_items.
	//   2. payments_status_update.
	$request_type = $req->get('method');

	// Setup response.
	$response = '';

	if ($request_type == 'payments_get_items') {
	  // Get order info from Pay Dialog's order_info.
	  // Assumes order_info is a JSON encoded string.
	  $order_info = json_decode($request['credits']['order_info'], true);

	  // Get item id.
	  $item_id = $order_info['item_id'];

	  // Simulutates item lookup based on Pay Dialog's order_info.
	  
	   $bags = ApStore::getItems($item_id,$app['db']);
	   $item = array(
	      'title' => $bags["title"],
	      'description' => $bags["description"],
	      // Price must be denominated in credits.
	      'price' => ($bags["price"]*10),
	      'image_url' => $bags["image"],
	    );
		 
	  
	  // Construct response.
      $response = array(
                  'content' => array(
                                 0 => $item,
                               ),
                  'method' => $request_type,
                );
      // Response must be JSON encoded.
      $response = json_encode($response);

	} else if ($request_type == "payments_status_update") {
	  // Get order details.
	  $order_details = json_decode($request['credits']['order_details'], true);

	  // Determine if this is an earned currency order.
	  $item_data = json_decode($order_details['items'][0]['data'], true);
	  $earned_currency_order = (isset($item_data['modified'])) ?
	                             $item_data['modified'] : null;

	  // Get order status.
	  $current_order_status = $order_details['status'];

	  if ($current_order_status == 'placed') {
	    // Fulfill order based on $order_details unless...

	    if ($earned_currency_order) {
	      // Fulfill order based on the information below...
	      // URL to the application's currency webpage.
	      $product = $earned_currency_order['product'];
	      // Title of the application currency webpage.
	      $product_title = $earned_currency_order['product_title'];
	      // Amount of application currency to deposit.
	      $product_amount = $earned_currency_order['product_amount'];
	      // If the order is settled, the developer will receive this
	      // amount of credits as payment.
	      $credits_amount = $earned_currency_order['credits_amount'];
	    }

	    $next_order_status = 'settled';

	    // Construct response.
	    $response = array(
	                  'content' => array(
	                                 'status' => $next_order_status,
	                                 'order_id' => $order_details['order_id'],
	                               ),
	                  'method' => $request_type,
	                );
	    // Response must be JSON encoded.
	    $response = json_encode($response);

	  } else if ($current_order_status == 'disputed') {
	    // 1. Track disputed item orders.
	    // 2. Investigate user's dispute and resolve by settling or refunding the order.
	    // 3. Update the order status asychronously using Graph API.

	  } else if ($current_order_status == 'refunded') {
	    // Track refunded item orders initiated by Facebook. No need to respond.

	  } else if ($current_order_status == 'settled') {
	    
	    // Verify that the order ID corresponds to a purchase you've fulfilled, then…
	    
	    // Get order details.
	    $order_details = json_decode($request['credits']['order_details'], true);

	    // Construct response.
	    $response = array(
	                  'content' => array(
	                                 'status' => 'settled',
	                                 'order_id' => $order_details['order_id'],
	                               ),
	                  'method' => $request_type,
	                );
	    // Response must be JSON encoded.
	    $response = json_encode($response);
	    
	  } else {
	    // Track other order statuses.

	  }
	} else {
		$response=array('content' => 'nada ... no hay POST VARS');
		$response = json_encode($response);
	}

	// Send response.
	//echo $response;
    return new Response($response,201);

});


function postAchievement($id,$stars)
{

	global $app_id,$app_token,$app_host;
	
	$achievement[0] = "";
	$achievement[1] = "http:" . $app_host . 'achievements/history_geography' . $stars . '.php';
	$achievement[2] = "http:" . $app_host . 'achievements/movies' . $stars . '.php';
	$achievement[3] = "http:" . $app_host . 'achievements/music' . $stars . '.php';
	$achievement[4] = "http:" . $app_host . 'achievements/celebrities' . $stars . '.php';
	$achievement[5] = "http:" . $app_host . 'achievements/sports' . $stars . '.php';
	$achievement[6] = "http:" . $app_host . 'achievements/general' . $stars . '.php';
	$achievement[7] = "http:" . $app_host . 'achievements/rescuer' . $stars . '.php';
	$achievement[8] = "http:" . $app_host . 'achievements/boosts' . $stars . '.php';
	$achievement[9] = "http:" . $app_host . 'achievements/chain' . $stars . '.php';
	$achievement[10] = ' ';
	$achievement[11] = "http:" . $app_host . 'achievements/good_level' . $stars . '.php';
	$achievement[12] = "http:" . $app_host . 'achievements/high_score' . $stars . '.php';
	$achievement[16] = "http:" . $app_host . 'achievements/the_dummy.php';	
	$achievement[14] = "http:" . $app_host . 'achievements/king.php';	
	$achievement[15] = "http:" . $app_host . 'achievements/addict' . $stars . '.php';
		
  	$achievement_display_order = 1;
	
	$achievement_URL = 'https://graph.facebook.com/' . $_SESSION["facebookId"] . '/achievements';
	$achievement_result = https_post($achievement_URL,'achievement=' . $achievement[$id] . '&' .  $_SESSION["app_access_token"]); //$_SESSION["app_access_token"]);

}


function updateRanking($db)
{

    global $app_id;
	
	$p = Players::getAll($db);

	//$get_scores = file_get_contents('https://graph.facebook.com/'.$app_id.'/scores?access_token=' . $_SESSION["access_token"]);
	$get_scores = getRankingFriends($_SESSION["access_token"]);
	
	//$myData = json_decode($get_scores, true);
    $myData = $get_scores;
	//$rank = $myData["data"];
	$rank = $myData;
	
	$position=0;
	$score=0;
	$friends="";
	
	$sql = "delete from ranking where idPlayer ='" . $_SESSION["facebookId"] . "'";
	$db->exec($sql);
	
	for($i=0;$i< sizeof($rank);$i++)
	{
		$sql = "insert into ranking(idPlayer,idFriend) values('%s','%s')";
        $sql = sprintf($sql,$_SESSION["facebookId"],$rank[$i]["id"]);
        //echo $sql;
		$db->exec($sql);
	}	

}


function getReward($score,$db)
{
	$sql = "select coins,xp from rewards where low <= " . $score . " and " . $score . " < high";
    // $sql = sprintf($sqld);
	//echo $sql;
    $data = $db->fetchAll($sql);
    return $data[0];
}


function buyJoker($idJoker,$db){
   
 	$seed = $_SESSION["seed"];
	
     $currencyToConsume="";
	 
	 if($idJoker==1)
	 	$currencyToConsume =  $_SESSION["jokerPrice"];
	 else
	 	$currencyToConsume = $_SESSION["freezePrice"];
	 	
	 $sql = Players::buyJoker($_SESSION["facebookId"],$currencyToConsume);
	 $result = $db->exec($sql);	
	 
	 $sql = Players::getCoins($_SESSION["facebookId"]);
	 $data = $db->fetchAll($sql);
	 $coins = $data[0]["coins"];
	 
	 return $coins;
}


function endTournament($db)
{
	date_default_timezone_set('Europe/Paris');
	$now = date("Y-m-d H:i:s");
    
	$sql = "select day(tournamentEnding) as d,month(tournamentEnding) as m,year(tournamentEnding) as y,hour(tournamentEnding) as h,minute(tournamentEnding) as mt,second(tournamentEnding) as s  from generalSettings";
    // $sql = sprintf($sqld);
	//echo $sql;
    $data = $db->fetchAll($sql);
	
	$d="";
	$m="";
	$y="";
	$h="";
	$mt="";
	$s="";
		
	if($data[0]["d"]<10) $d = "0" . $data[0]["d"]; else  $d = $data[0]["d"];
	if($data[0]["m"]<10) $m = "0" . $data[0]["m"]; else  $m = $data[0]["m"];
	$y = $data[0]["y"];
	
	if($data[0]["h"]<10)  $h = "0" .  $data[0]["h"]; else  $h = $data[0]["h"];
	if($data[0]["mt"]<10) $mt = "0" . $data[0]["mt"]; else  $mt = $data[0]["m"];
	if($data[0]["s"]<10)  $s = "0" .  $data[0]["s"]; else  $s = $data[0]["s"];
	
	
	$_SESSION["end_tournament"] = $y . "-" . $m . "-" . $d . " " . $h . ":" . $mt . ":" . $s;  //"2013-03-01 13:00:00";
	
	//echo $_SESSION["end_tournament"];
	
	$difference = timeDiff($now,$_SESSION["end_tournament"]);
	
	$years = abs(floor($difference / 31536000));
	$days = abs(floor(($difference-($years * 31536000))/86400));
	$hours = abs(floor(($difference-($years * 31536000)-($days * 86400))/3600));
	$mins = abs(floor(($difference-($years * 31536000)-($days * 86400)-($hours * 3600))/60));
	$secs= abs(floor(($difference-($years * 31536000)-($days * 86400)-($hours * 3600)-($mins * 60))));

	$ret["secondsToEnd"] = $secs;//$difference;
	$ret["minutesToEnd"] = $mins;//floor($difference/60);
	$ret["hoursToEnd"] = $hours;// floor($difference/3600);
	$ret["daysToEnd"] = $days;//floor($difference/3600/24);
	
 
	$ret["end"] = $_SESSION["end_tournament"];// $_SESSION["start_credit_time"];

	return $ret;
}


function setNewLife(){

	date_default_timezone_set('Europe/Paris');
	$now = date("Y-m-d H:i:s");
	$_SESSION["start_credit_time"] = $now; //date("H:i:s", strtotime($now)+(6*60));
	return new Response($_SESSION["start_credit_time"], 200); 

}	


function myRanking($db)
{
    global $app_id;
	
	$get_scores = getRankingFriends($_SESSION["access_token"]);
	
	$sorted =  sortScores($get_scores,$db,true);
	
	for($i=0;$i<sizeof($sorted);$i++)
	{
		$sql = "select sent from inbox where type='accept' and idFrom ='" . $_SESSION["facebookId"]  . "' and idTo='" . $sorted[$i]["id"] . "' order by id desc limit 1";

		$data = $db->fetchAll($sql);
		
		date_default_timezone_set('Europe/Paris');
		$now = date("Y-m-d H:i:s");
		$difference = abs(timeDiff($data[0]["sent"],$now));
		
		if(!isset($data[0]["sent"]) || $difference > $_SESSION["livesWait"] || $data[0]["sent"]=="1970-01-01 10:00:00")
			$sorted[$i]["wait"] = 0;
		else
			$sorted[$i]["wait"] = $difference; // . " - " . $data[0]["sent"] . " - " . $now;
	}
	
	return $sorted;
	
}	


function sortScores($get_scores,$db,$format)
{

	$rank = $get_scores; 

	$ids="";
	$result=array();
	
	for($i=0;$i< sizeof($rank);$i++)
	{
		$sql = "select highScore, XP from players where idFacebook ='" . $rank[$i]["id"] . "'";
		$data = $db->fetchAll($sql);
		$result[$i]["xp"] = $data[0]["XP"];

		$result[$i]["score"] = $data[0]["highScore"];	
		$result[$i]["player"] = $rank[$i]["name"];
		$result[$i]["id"] = $rank[$i]["id"];
		$result[$i]["position"] = $i+1;
		
	}	
	
	$sortArray = array(); 

	foreach($result as $r){ 
		foreach($r as $key=>$value){ 
			if(!isset($sortArray[$key])){ 
				$sortArray[$key] = array(); 
			} 
			$sortArray[$key][] = $value; 
		} 
	} 

	$orderby = "score";
	
	array_multisort($sortArray[$orderby],SORT_DESC,$result); 
   
    for($i=0;$i< sizeof($result);$i++)
	{
		$result[$i]["position"] = $i+1;
		if($result[$i]["score"] == 0) 
		{
			if($format)
				$result[$i]["score"] = "No score";
		}
		else
		{
			if($format)
				$result[$i]["score"] = number_format($result[$i]["score"],0,".",",");
		}		
				
	}
	
	return $result;


}


/********************************************************************************************/
/******* DEBUGGING FUNCTIONS ****************************************************************/
/********************************************************************************************/

$app->get('/postQ.{format}', function(Request $request) use($app){

	$title = $request->get('title');
	$time = $request->get('time');
		
	OGActions::postQuestion($title,$time,$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);

});	

$app->get('/rankingTest.{format}', function(Request $request) use($app){

    global $app_id;
		
	$get_scores = getRankingFriends($_SESSION["access_token"]);
	$sorted =  sortScores($get_scores,$app['db'],true);
	
	for($i=0;$i<sizeof($sorted);$i++)
	{
		$sql = "select sent from inbox where type='accept' and idFrom ='" . $_SESSION["facebookId"]  . "' and idTo='" . $sorted[$i]["id"] . "' order by id desc limit 1";
		echo $sql . "\n";
		$data = $app['db']->fetchAll($sql);
		
		date_default_timezone_set('Europe/Paris');
		$now = date("Y-m-d H:i:s");
		$difference = abs(timeDiff($data[0]["sent"],$now));
		
		if(!isset($data[0]["sent"]) || $difference > $_SESSION["livesWait"] || $data[0]["sent"]=="1970-01-01 10:00:00")
			$sorted[$i]["wait"] = 0;
		else
			$sorted[$i]["wait"] = $difference; // . " - " . $data[0]["sent"] . " - " . $now;
	
	}
	
	var_dump($sorted);
	
});


$app->get('/postJoker.{format}', function(Request $request) use($app){

	$title = $request->get('title');	
	OGActions::postJoker($title,$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
	
});	


$app->get('/postFreeze.{format}', function(Request $request) use($app){

	$title = $request->get('title');	
	OGActions::postFreeze($title,$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
		
});	


$app->get('/postBest.{format}', function(Request $request) use($app){

	$score = $request->get('score');
	OGActions::postBestScore($score,$_SESSION["access_token"],$_SESSION["first_name"],20,$app['db']);
	
});	


$app->get('/postLevel.{format}', function(Request $request) use($app){

	$level = $request->get('level');
	OGActions::postLevel($level,$_SESSION["access_token"],$_SESSION["first_name"],$app['db']);
	
});	


$app->get('/postCombo.{format}', function(Request $request) use($app){

	$points = $request->get('points');
	$q = $request->get('q');
	OGActions::postCombo($points,$_SESSION["access_token"],$_SESSION["first_name"],$q,$app['db']);	
	
});	

$app->get('/postPass.{format}', function(Request $request) use($app){

	global $app_host;
	$seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	
	$token = $data[0];
	$points = $data[1];
	$tag = $data[2];
    $name = Players::getName($tag,$app['db']);
	
	OGActions::postPass($points,$tag,$_SESSION["access_token"],$name,$app['db']);

});	


$app->get('/postEarn.{format}', function(Request $request) use($app){

	$seed = $_SESSION["seed"];
	
	$param = $request->get('p');
    $encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	$token = $data[0];	
	$position = $data[1];
	
	OGActions::postEarn($position,$_SESSION["access_token"],$_SESSION["first_name"],$_SESSION["playerInfo"]["lastScore"],$app['db']);
	
});	


$app->get('/postHint.{format}', function(Request $request) use($app){

	global $app_host;
	
	$topic = $request->get('topic');
	$_SESSION["access_token"] = $request->get('tk');
	$_SESSION["first_name"] = $request->get('name');
	$_SESSION["hintText"] = $request->get('hint');
	OGActions::postHint($topic,$_SESSION["access_token"],$_SESSION["first_name"],$_SESSION["hintText"],$app['db']);
	
});	


$app->get('/resetScore.{format}', function(Request $request) use($app){

	
	$param = $request->get('param');
    $encoded=/*decrypt(*/$param/*,"wopidom")*/;
 	
	$data = explode(",",$encoded);	
	$user = $data[0];	
	$score = $data[1];	
	
	$post_score = https_post('https://graph.facebook.com/' . $user . '/scores' ,'score=' . $score . '&access_token=' . $_SESSION["access_token"]);
	
	return new Response(json_encode($post_score), 200); 

});


$app->get('regId.{format}', function(Request $request) use($app){
   
	global $app_id,$app_token,$app_host;
	
	$param = $request->get('params');
    $encoded=/*decrypt(*/$param/*,"wopidom")*/;
 
	$data = explode(",",$encoded);	
	
	$id= $data[0];
	$stars = $data[1];
	$token = $data[2];
	
		$achievement[0] = "aa";
		$achievement[1] = "http:" . $app_host . 'achievements/history_geography' . $stars . '.php';
		$achievement[2] = "http:" . $app_host . 'achievements/movies' . $stars . '.php';
		$achievement[3] = "http:" . $app_host . 'achievements/music' . $stars . '.php';
		$achievement[4] = "http:" . $app_host . 'achievements/celebrities' . $stars . '.php';
		$achievement[5] = "http:" . $app_host . 'achievements/sports' . $stars . '.php';
		$achievement[6] = "http:" . $app_host . 'achievements/general' . $stars . '.php';
		$achievement[7] = "http:" . $app_host . 'achievements/rescuer' . $stars . '.php';
		$achievement[8] = "http:" . $app_host . 'achievements/boosts' . $stars . '.php';
		$achievement[9] = "http:" . $app_host . 'achievements/chain' . $stars . '.php';
		$achievement[10] = ' ';
		$achievement[11] = "http:" . $app_host . 'achievements/good_level' . $stars . '.php';
		$achievement[12] = "http:" . $app_host . 'achievements/high_score' . $stars . '.php';
		$achievement[16] = "http:" . $app_host . 'achievements/the_dummy.php';	
		$achievement[14] = "http:" . $app_host . 'achievements/king.php';	
		$achievement[15] = "http:" . $app_host . 'achievements/addict' . $stars . '.php';
	
	// Register an Achievement for the app
	//$achievement = 'http://frozen-garden-4287.herokuapp.com/achievements/sports.php';
  	$achievement_display_order = 1;
	
  	// Register an Achievement for the app
    $achievement_registration_URL = 'https://graph.facebook.com/' . $app_id . '/achievements';
  	$achievement_registration_result=https_post($achievement_registration_URL,'achievement=' . $achievement[$id]  . '&display_order=' . $achievement_display_order . 
	'&access_token=243850462418561|oIcd3WqPgL3S-kmMx5WTfTipMEU');// . $_SESSION["app_access_token"]);
	//echo "\n <br> REG: " . $achievement_registration_URL . 'achievement=' . $achievement[$id]  . '&display_order=' . $achievement_display_order . '&access_token=243850462418561|oIcd3WqPgL3S-kmMx5WTfTipMEU';
	// . $_SESSION["app_access_token"]; //$_SESSION["app_access_token"] . "\n";
	
    return new Response(json_encode($achievement_registration_result),200);
		
});


$app->get('/deleteScores.{format}', function(Request $request) use($app){

    global $app_id;
	
	$post_score = https_delete('https://graph.facebook.com/' . $app_id . '/scores' ,'access_token=243850462418561|-I1Tg5y4BZaONdmTKEF_iTa6Z0E');

	return new Response(json_encode($post_score), 200); 

});


$app->get('/updateRanking.{format}', function(Request $request) use($app){

	updateRanking($app['db']);
	
});


$app->get('/deleteAchievement.{format}', function(Request $request) use($app){

    global $app_id,$app_token,$app_host;
	
	$param = $request->get('p');
    $encoded=/*decrypt(*/$param/*,"wopidom")*/;
 
	$data = explode(",",$encoded);	
	
	$token = $data[0];
    $id= $data[1];
	$stars = $data[2];
	
	
	for($stars=1;$stars<6;$stars++)
	{
		$achievement[0] = "aa";
		$achievement[1] = "http:" . $app_host . 'achievements/history_geography' . $stars . '.php';
		$achievement[2] = "http:" . $app_host . 'achievements/movies' . $stars . '.php';
		$achievement[3] = "http:" . $app_host . 'achievements/music' . $stars . '.php';
		$achievement[4] = "http:" . $app_host . 'achievements/celebrities' . $stars . '.php';
		$achievement[5] = "http:" . $app_host . 'achievements/sports' . $stars . '.php';
		$achievement[6] = "http:" . $app_host . 'achievements/general' . $stars . '.php';
		$achievement[7] = "http:" . $app_host . 'achievements/rescuer' . $stars . '.php';
		$achievement[8] = "http:" . $app_host . 'achievements/boosts' . $stars . '.php';
		$achievement[9] = "http:" . $app_host . '/achievements/chain' . $stars . '.php';
		$achievement[10] = ' ';
		$achievement[11] = "http:" . $app_host . 'achievements/good_level' . $stars . '.php';
		$achievement[12] = "http:" . $app_host . 'achievements/high_score' . $stars . '.php';
		$achievement[16] = "http:" . $app_host . 'achievements/the_dummy.php';	
		$achievement[14] = "http:" . $app_host . 'achievements/king.php';	
		$achievement[15] = "http:" . $app_host . 'achievements/addict' . $stars . '.php';	
		  
		$achievement_URL = 'https://graph.facebook.com/' . $_SESSION["facebookId"] . '/achievements';
		$achievement_result = https_delete($achievement_URL,'achievement=' . $achievement[$id] . '&' . $_SESSION["app_access_token"]);// $_SESSION["app_access_token"]);
	}
	
    return new Response(json_encode($achievement_result),200);
    
});


$app->get('/achievementId.{format}', function(Request $request) use($app){
   
	global $app_id,$app_token,$app_host;
	$seed = $_SESSION["seed"];
	
	$param = $request->get('params');
    $encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	
	$id= $data[0];
	$stars = $data[1];
	$token = $data[2];
	
		$achievement[0] = "aa";
		$achievement[1] = "http:" . $app_host . 'achievements/history_geography' . $stars . '.php';
		$achievement[2] = "http:" . $app_host . 'achievements/movies' . $stars . '.php';
		$achievement[3] = "http:" . $app_host . 'achievements/music' . $stars . '.php';
		$achievement[4] = "http:" . $app_host . 'achievements/celebrities' . $stars . '.php';
		$achievement[5] = "http:" . $app_host . 'achievements/sports' . $stars . '.php';
		$achievement[6] = "http:" . $app_host . 'achievements/general' . $stars . '.php';
		$achievement[7] = "http:" . $app_host . 'achievements/rescuer' . $stars . '.php';
		$achievement[8] = "http:" . $app_host . 'achievements/boosts' . $stars . '.php';
		$achievement[9] = "http:" . $app_host . 'achievements/chain' . $stars . '.php';
		$achievement[10] = ' ';
		$achievement[11] = "http:" . $app_host . 'achievements/good_level' . $stars . '.php';
		$achievement[12] = "http:" . $app_host . 'achievements/high_score' . $stars . '.php';
		$achievement[16] = "http:" . $app_host . 'achievements/the_dummy.php';	
		$achievement[14] = "http:" . $app_host . 'achievements/king.php';	
		$achievement[15] = "http:" . $app_host . 'achievements/addict' . $stars . '.php';
		
	
	// Register an Achievement for the app
	//$achievement = 'http://frozen-garden-4287.herokuapp.com/achievements/sports.php';
  	$achievement_display_order = 1;
	
	$achievement_URL = 'https://graph.facebook.com/' . $_SESSION["facebookId"] . '/achievements';
	$achievement_result = https_post($achievement_URL,'achievement=' . $achievement[$id] . '&' .  $_SESSION["app_access_token"]); //$_SESSION["app_access_token"]);
	//echo "POST: " . $achievement_URL . 'achievement=' . $achievement[$id] . '&' .  $_SESSION["app_access_token"]; //$_SESSION["app_access_token"] . "<br>";
    return new Response(json_encode($achievement_result),200);
		
});

$app->get('/getHints.{format}', function(Request $request) use($app){


	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	$token=decrypt($param,$seed);
 	
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql="select id,zone from zones order by id";
		$zones = $app['db']->fetchAll($sql); 
		$result= array();

		for($i=0;$i<sizeof($zones);$i++)
		{
	
			$result[$i]["id"] = $zones[$i]["id"];
			$result[$i]["zone"] = $zones[$i]["zone"];	
			$result[$i]["hasNew"] = 0;
			$sql="select z.id,z.zone,q.factoid,ph.isNew, ph.id from playersHints ph, questions q, zones z
			where ph.idQuestion=q.id and
			q.idZone = z.id and
			ph.idFacebook='" .  $_SESSION["facebookId"] . "' and q.factoid is not null 
			and z.id=" . $zones[$i]["id"] . " order by ph.id desc";
			//echo $sql;
			$hints = $app['db']->fetchAll($sql); 
		   	
			for($j=0;$j<sizeof($hints);$j++)
			{
				if($hints[$j]["isNew"] == 1)
					$result[$i]["hasNew"] = 1;
				$result[$i]["hints"][$j] = 	$hints[$j]["factoid"];	
			}
			
		}
		
		$sql = "update playersHints set isNew=0 where idFacebook='" . $_SESSION["facebookId"] . "'";
		$app['db']->exec($sql);
		
		return new Response(encrypt(json_encode($result),$seed),200);
	}	
    else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 	

});
$app->get('/getInbox.{format}', function(Request $request) use($app){
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	$token=decrypt($param,$seed);
	
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sqlHelp = "select p.idFacebook, concat(p.name,' ',p.lastName) name,i.idRequest,'Send help' description,i.isReply from inbox i ,players p
		where
		idFrom = p.idFacebook and
		i.idTo = '" . $_SESSION["facebookId"] . "' and
		i.type = 'help' and i.isNew=1";
		$helpData = $app['db']->fetchAll($sqlHelp); 
		
		$result=array();
		
		$result[0]=array();
			
		
		for($i=0;$i<sizeof($helpData);$i++)
		{
			$result[0][$i]["idFacebook"] = $helpData[$i]["idFacebook"];
			$result[0][$i]["name"] = $helpData[$i]["name"];
			$result[0][$i]["idRequest"] = $helpData[$i]["idRequest"];
			$result[0][$i]["description"] = $helpData[$i]["description"];
			$result[0][$i]["isReply"] = $helpData[$i]["isReply"];
			
			
		}
		
		
		// texto sin Can you send me one cuando es reply=1
		$sqlAccept = "select p.idFacebook, concat(p.name,' ',p.lastName) name,i.idRequest,'Here is a life for you. Can you send me one?' description,i.isReply  from inbox i ,players p
		where 
		i.idTo = '" . $_SESSION["facebookId"] . "' and
		idFrom =  p.idFacebook and
		i.type = 'accept' and i.isNew=1";
		
		$acceptData = $app['db']->fetchAll($sqlAccept); 
		
		//$result=array();
		$result[1]=array();
		
		for($i=0;$i<sizeof($acceptData);$i++)
		{
			$result[1][$i]["idFacebook"] = $acceptData[$i]["idFacebook"];
			$result[1][$i]["name"] = $acceptData[$i]["name"];
			$result[1][$i]["idRequest"] = $acceptData[$i]["idRequest"];
			if($acceptData[$i]["isReply"] == 1 ||$acceptData[$i]["isReply"] == "1" )
				$result[1][$i]["description"] = "Here is a life for you.";// $acceptData[$i]["description"];
			else
				$result[1][$i]["description"] = "Here is a life for you. Can you send me one?";
			$result[1][$i]["isReply"] = $acceptData[$i]["isReply"];
			
		}

		return new Response(encrypt(json_encode($result),$seed),200);
	
	}
	else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200); 	
	  
});


$app->get('/sendLife.{format}', function(Request $request) use($app){
	
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	//$token=decrypt($param,$seed);
	$decoded=decrypt($param,$seed);
 
	$data = explode(";",$decoded);	
	
	$token= $data[0];
	$info =  $data[1];
	//echo $info;	
	$infoArray = json_decode($info,true);
 	//var_dump($infoArray);
	

	$requestId = $infoArray["ids"];
	$to = $_SESSION["facebookId"];
	
	for($i=0;$i<sizeof($requestId);$i++)
	{
	
		$url = "https://graph.facebook.com/" . $requestId[$i] . "?" . $_SESSION["app_access_token"];
		$reqResult = file_get_contents($url);
		$reqResult = json_decode($reqResult,true);
		
		
		$sql = "update inbox set isNew=0 where idFrom='" .  $reqResult["from"]["id"]  . "' and idRequest='" . $requestId[$i] . "'";
		//echo $sql . "\n";
		$app['db']->exec($sql);
		
		$deleteResult = https_delete("https://graph.facebook.com/" . $requestId[$i] .  "_"  . $to , $_SESSION["app_access_token"]);
		//echo "delete: " . $deleteResult;
	}
	
});	


$app->get('/acceptLife.{format}', function(Request $request) use($app){
	
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	//$token=decrypt($param,$seed);
	$decoded=decrypt($param,$seed);
 
	$data = explode(";",$decoded);	
	
	$token= $data[0];
	$info =  $data[1];
	//echo $info;	
	$infoArray = json_decode($info,true);
 	//var_dump($infoArray);
	

	$requestId = $infoArray["ids"];// $info; //"2147483647"; //$infoArray["request"]; //"47727128233
	$to = $_SESSION["facebookId"];
	
	for($i=0;$i<sizeof($requestId);$i++)
	{
		
		$sql = "update players set lives = lives + 1 where idFacebook='" . $to  . "'";
		$app['db']->exec($sql);
		
		$sql = "update inbox set isNew=0 where idTo='" . $to  . "' and idRequest='" . $requestId[$i] . "'";
		$app['db']->exec($sql);
					
		$deleteResult = https_delete("https://graph.facebook.com/" . $requestId[$i] .  "_"  . $to, $_SESSION["app_access_token"]);
	}
	
	
	$sql = "select lives from players where idFacebook='" . $to  . "'";
	$lives = $app['db']->fetchAll($sql);
	
	return new Response(encrypt($lives[0]["lives"],$seed),200);
	
	
});
	
$app->get('/rejectLife.{format}', function(Request $request) use($app){
	
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	//$token=decrypt($param,$seed);
	$decoded=decrypt($param,$seed);
 
	$data = explode(";",$decoded);	
	
	$token= $data[0];
	$info =  $data[1];

	$infoArray = json_decode($info,true);
	

	$requestId = $infoArray["ids"];
	$to = $_SESSION["facebookId"];
	
	
	//var_dump($requestId);
	
	for($i=0;$i<sizeof($requestId);$i++)
	{
	
	
		$sql = "update inbox set isNew=0 where idTo='" . $to  . "' and idRequest='" . $requestId[$i] . "'";
		echo $sql . "\n";
		$app['db']->exec($sql);
			
		$deleteResult = https_delete("https://graph.facebook.com/" . $requestId[$i] .  "_"  . $to, $_SESSION["app_access_token"]);
		//echo "delete: " . $deleteResult;
	}
	
});

$app->get('/insertInbox.{format}', function(Request $request) use($app){
	
	$seed = $_SESSION["seed"];
	$param = $request->get('p');
	//$token=decrypt($param,$seed);
	$decoded=decrypt($param,$seed);
 
	$data = explode(";",$decoded);	
	
	$token= $data[0];
	$info =  $data[1];
	$type =  $data[2];
	$isReply="0";
	if(isset($data[3]) && $data[3]=="1")
		$isReply="1";

	$infoArray = json_decode($info,true);

	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
	
		$requestId=$infoArray["request"];
		$idList = $infoArray["to"];
		for($i=0;$i<sizeof($idList);$i++)
		{
			$from = $_SESSION["facebookId"];// $reqResult["from"]["id"];
			$to = $idList[$i];

			date_default_timezone_set('Europe/Paris');
			$now = date("Y-m-d H:i:s",time()+$_SESSION["livesWait"]); // date("Y-m-d H:i:s");
			
			$sql = "insert into inbox(idRequest,idFrom,idTo,type,isReply,sent) values('" . $requestId . "','" . $from . "','" . $to . "','" . $type . "','" . $isReply . "','" . $now . "')";
			echo $sql . "\n";
			$app['db']->exec($sql);
		}
	}
	else
      return new Response(encrypt(json_encode("Invalid Token"),$seed), 200);

});

$app->get('/fql.{format}', function(Request $request) use($app){

  $access_token = $request->get('p');//"243850462418561|oIcd3WqPgL3S-kmMx5WTfTipMEU";
  
  $fql_query_url = 'https://graph.facebook.com/'
    . 'fql?q=SELECT+uid,+first_name,+last_name,+mutual_friend_count+FROM+user+WHERE+uid+IN+(SELECT+uid2+FROM+friend+WHERE+uid1+=+me())+ORDER+BY+mutual_friend_count+DESC+LIMIT+0,+49'
    . '&access_token=' . $access_token;
  echo  $fql_query_url;
  $fql_query_result = file_get_contents($fql_query_url);
  $fql_query_obj = json_decode($fql_query_result, true);
  
  $data = $fql_query_obj["data"];
  
  var_dump($fql_query_obj);
  $f=array();
  for($i=0;$i<sizeof($data);$i++)
  {
	  $f[$i]["id"] = $data[$i]["uid"];
	  $f[$i]["name"] = $data[$i]["first_name"] . " " . $data[$i]["last_name"];   
  }
  
  var_dump($f);
  
  
});  

$app->get('/getFriends.{format}', function(Request $request) use($app){

    $seed = $_SESSION["seed"];
	$param = $request->get('p');
	$encoded=decrypt($param,$seed);
 
	$data = explode(",",$encoded);	
	$token = $data[0];	
	$type = $data[1];
	$selected = $data[2];
	
	  $fbToken = "";

	  $fql_query_url = 'https://graph.facebook.com/'
    . 'fql?q=SELECT+uid,+first_name,+last_name,+mutual_friend_count+FROM+user+WHERE+uid+IN+(SELECT+uid2+FROM+friend+WHERE+uid1+=+me())+ORDER+BY+mutual_friend_count+DESC+LIMIT+0,+48'
    . '&access_token=' . $_SESSION["access_token"];
	  //  echo  $fql_query_url;
	  $fql_query_result = file_get_contents($fql_query_url);
	  $fql_query_obj = json_decode($fql_query_result, true);
	  
	  $data = $fql_query_obj["data"];
	  
	  $f=array();
	  $selectedExists = 0;
	  $x=0;
	  for($i=0;$i<sizeof($data);$i++)
	  {  
		  if($type == "lives" )
		  {
				$sql = "select sent from inbox where type='accept' and idFrom ='" . $_SESSION["facebookId"]  . "' and idTo='" . $data[$i]["uid"] . "' order by id desc limit 1";
				$sent = $app['db']->fetchAll($sql);
				
				date_default_timezone_set('Europe/Paris');
				$now = date("Y-m-d H:i:s");
				$difference = abs(timeDiff($sent[0]["sent"],$now));
							
				if(!isset($sent[0]["sent"]) || $difference > $_SESSION["livesWait"] || $sent[0]["sent"]=="1970-01-01 10:00:00")
				{
					$f[$x]["id"] = $data[$i]["uid"];
		  		 	$f[$x]["name"] = $data[$i]["first_name"] . " " . $data[$i]["last_name"]; 
					$x++;
				}	

		    }
			else
			{
				 $f[$i]["id"] = $data[$i]["uid"];
		  		 $f[$i]["name"] = $data[$i]["first_name"] . " " . $data[$i]["last_name"];   
			}		
	  }
	 
	   
	  return new Response(encrypt(json_encode($f),$seed),200);

});


$app->post('/backEnd.{format}', function(Request $request) use($app){

$param = $request->post('p');

echo $param;

});


function getRankingFriends($param)
{
	$friends = file_get_contents('https://graph.facebook.com/me/friends?fields=installed,first_name&access_token=' . $param);
   	$friends2 = json_decode($friends,true);
   		
	$lives = array();
	$x=0;
	
	for($i=0;$i<sizeof($friends2["data"]);$i++)
	{	
		if(isset($friends2["data"][$i]["installed"]))	
		{
			$lives[$x]["id"] = $friends2["data"][$i]["id"];
			$lives[$x]["name"] = $friends2["data"][$i]["first_name"];
				
			$x++;
		}
	}
	
	$lives[$x]["id"] = $_SESSION["facebookId"];
	$lives[$x]["name"] = $_SESSION["first_name"];
	
	return $lives;

}

return $app;