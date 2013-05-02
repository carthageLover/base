<?php

namespace src\Entities;

/**
 * Clase creada para simular la encapsulación de la creación de la sentencia SQL.
 */
class OGActions 
{

	static public function postQuestion($title,$time,$fbToken,$name,$db)
	{
		global $app_host;
		$action='https://graph.facebook.com/me/quiz_fordummies:answer';
		
		//$p = 'access_token=' .  $fbToken . '&' . 'question=http:' . $app_host . 'og/question.php?title=' . $name . ' has answered ' . $title . ' in ' . $time . ' seconds&text=' . 
		//urlencode('Can you answer it?');// . '&fb:explicitly_shared=true';
		
		$sql = "select text from ogTexts where object = 'question'";
		$data = $db->fetchAll($sql);
		
		$p = 'access_token=' .  $fbToken . '&' . 'question=http:' . $app_host . 'og/question.php?title=' . $title . '&timetext=Time: ' . $time . ' sec&text=' . 
		urlencode($data[0]["text"]);// . '&fb:explicitly_shared=true';

		//echo $action . "\n"; 
		echo $p . "\n"; 
		
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}
	
		
	static public function postJoker($question,$fbToken,$name,$db)
	{
		global $app_host;
		$action='https://graph.facebook.com/me/quiz_fordummies:use';
		
		$sql = "select title,text from ogTexts where object = 'joker'";
		$data = $db->fetchAll($sql);
		
		$p = 'access_token=' .  $fbToken . '&' . 'joker=http:' . $app_host . 'og/joker.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($data[0]["text"]) . '&questiontext=Question: ' . $question;// . '&fb:explicitly_shared=true';
		$res_obj=https_post($action,$p); 
	
		echo $res_obj . "\n";	
	}
	
	static public function postFreeze($title,$fbToken,$name,$db)
	{	
		global $app_host;
		
		$action='https://graph.facebook.com/me/quiz_fordummies:use';
		
		$sql = "select title,text from ogTexts where object = 'freeze'";
		$data = $db->fetchAll($sql);
		
		$p = 'access_token=' .  $fbToken . '&' . 'freeze=http:' . $app_host . 'og/freeze.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($data[0]["text"]);// . '&fb:explicitly_shared=true';
		$res_obj=https_post($action,$p); 
	
		echo $res_obj . "\n";	
	}
		
	static public function postBestScore($score,$fbToken,$name,$oldscore,$db)
	{
		global $app_host;
		$action='https://graph.facebook.com/me/quiz_fordummies:reach';
		
		$sql = "select title,text from ogTexts where object = 'score'";
		$data = $db->fetchAll($sql);
	
		$text = str_replace("XX",$name,$data[0]["text"]);
		$text = str_replace("YY",$score,$text);
		
		$p = 'access_token=' .  $fbToken . '&' . 'highscore=http:' . $app_host . 'og/high_score.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($text) . '&score=Previous highscore: ' . $oldscore;// . '&fb:explicitly_shared=true';
		$res_obj=https_post($action,$p); 
	    echo $p . "\n";
		echo $res_obj . "\n";	
	}
	
	static public function postLevel($level,$fbToken,$name,$db)
	{
		global $app_host;	
		$action='https://graph.facebook.com/me/quiz_fordummies:reach';
		
		$sql = "select title,text from ogTexts where object = 'level'";
		$data = $db->fetchAll($sql);
		
		$p = 'access_token=' .  $fbToken . '&' . 'level=http:' . $app_host . 'og/level.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($data[0]["text"]) . '&score=Level: ' . $level;// . '&fb:explicitly_shared=true';
		$res_obj=https_post($action,$p); 
	
		echo $res_obj . "\n";	
	}
	
	static public function postCombo($points,$fbToken,$name,$question,$db)
	{
		global $app_host;		
		$action='https://graph.facebook.com/me/quiz_fordummies:fill';
		
		$sql = "select title,text from ogTexts where object = 'combo'";
		$data = $db->fetchAll($sql);
	
		$text = str_replace("XX",$name,$data[0]["text"]);
		$text = str_replace("YY",$points,$text);
		
		$p = 'access_token=' .  $fbToken . '&' . 'combo_bar=http:' . $app_host . 'og/combo_bar.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($text) . '&textq=Question: ' . $question;// . '&fb:explicitly_shared=true';
	
	
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}
	
	/*static public function postLearn($object,$fbToken,$name,$db)
	{	
		global $app_host;	
		
		$texts["history_geography"] = 'History and Geography';
		$texts["music"] = 'Music';
		$texts["movies"] = 'Movies';
		$texts["celebrities"] = 'Celebrities';
		$texts["general_culture"] = 'General Culture';
		$texts["sports"] = 'Sports';
		
		$sql = "select title,text from ogTexts where object = 'learn'";
		$data = $db->fetchAll($sql);
	
		$title = str_replace("XX",$name,$data[0]["title"]);
		$title = str_replace("YY",$texts[$object],$title);
	
		$action='https://graph.facebook.com/me/quiz_fordummies:learn_more_about';
		$p = 'access_token=' .  $fbToken . '&' . $object .'=http:' . $app_host . 'og/' . $object . '.php?title=' . $title . '&text=' . 
		urlencode($data[0]["text"]) . '&fb:explicitly_shared=true';
		
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}*/
	
	static public function postPass($points,$tag,$fbToken,$name,$db)
	{
		global $app_host;	
		
		$action='https://graph.facebook.com/me/quiz_fordummies:pass';
	//	$p = 'access_token=' .  $fbToken . '&' . 'profile=http:' . $app_host . 'og/pass_profile.php?title=' . $name . '&text=' . 
		//urlencode('If you want to get your revenge, you will need to score ' . $points . ' more points.') . '&tags=' . $tag . '&fb:explicitly_shared=true';
		
		$sql = "select title,text from ogTexts where object = 'pass'";
		$data = $db->fetchAll($sql);
		
		$text = str_replace("XX",$points,$data[0]["text"]);	
		
		$p = 'access_token=' .  $fbToken . '&' . 'profile=http:' . $app_host . 'og/pass_profile.php?title=' . $name . '&text=' . 
		urlencode($text) . '&fb:explicitly_shared=true';
		
		
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}
	
	static public function postHint($topic,$fbToken,$name,$hintText,$db)
	{	
		global $app_host;	
		
		$action='https://graph.facebook.com/me/quiz_fordummies:get';
		
		$sql = "select title,text from ogTexts where object = 'hint'";
		$data = $db->fetchAll($sql);
	
		$text = str_replace("XX",$name,$data[0]["text"]);
		$text = str_replace("YY",$topic,$text);
		
		
		$p = 'access_token=' .  $fbToken . '&new_hint=http:' . $app_host . '/og/new_hint.php?title=' . $data[0]["title"] . '&text=' . 
		urlencode($text) . '&hinttext=Hint: ' . $hintText;// . '&fb:explicitly_shared=true';
		echo $p . "\n";
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}
	
	static public function postEarn($position,$fbToken,$name,$score,$db)
	{		
		global $app_host;	
		
		//$texts[1] = 'Gold Medal';
		//$texts[2] = 'Silver Medal';
		//$texts[3] = 'Bronze Medal';
		
		$object[1] = "gold_medal";
		$object[2] = "silver_medal";
		$object[3] = "bronze_medal";
		
		$sql = "select title,text from ogTexts where object = 'gold_medal'";
		$data = $db->fetchAll($sql);
		$msg[1]["text"] = str_replace("XX",$name,$data[0]["text"]);
		$msg[1]["title"] = $data[0]["title"];
		
		$sql = "select title,text from ogTexts where object = 'silver_medal'";
		$data = $db->fetchAll($sql);
		$msg[2]["text"] = str_replace("XX",$name,$data[0]["text"]);
		$msg[2]["title"] = $data[0]["title"];
		
		$sql = "select title,text from ogTexts where object = 'bronze_medal'";
		$data = $db->fetchAll($sql);
		$msg[3]["text"] = $data[0]["text"];
		$msg[3]["title"] = $data[0]["title"];
		
		//$msg[1] = $name . " outperformed all the Dummies last week and earned the Gold medal! It's almost too easy for him.";
		//$msg[2] = $name . " has almost done better than all the Dummies last week and finished second in the weekly tournament. ";
		//$msg[3] = "There were too much dummies in the tournament!";
		
		$action='https://graph.facebook.com/me/quiz_fordummies:earn';
		$p = 'access_token=' .  $fbToken . '&' . $object[$position] .'=http:' . $app_host . 'og/' . $object[$position] . '.php?title=' . $msg[$position]["title"] . '&text=' . 
		urlencode($msg[$position]["text"]) . '&textscore=Score: ' . $score;// . '&fb:explicitly_shared=true';
		
		$res_obj=https_post($action,$p); 
		echo $res_obj . "\n";	
	}	
}	

?>