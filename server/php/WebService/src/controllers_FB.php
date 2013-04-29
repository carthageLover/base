<?php
session_start();
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use src\Entities\Comment;
use src\Entities\Questions;
use src\Entities\Players;
//use src\Entities\FacebookWS;

require_once (BASE_DIR . '/src/Entities/Comment.php');
require_once (BASE_DIR . '/src/Entities/Questions.php');
require_once (BASE_DIR . '/src/Entities/Players.php');
//require_once (BASE_DIR . '/src/Entities/FacebookWS.php');
require_once (BASE_DIR . '/src/Entities/src/facebook.php');

	$seed="wopidom";
	
	$app_id = '481002115284282';
	$app_secret = '346a073d5580d0992437caf3345897d6';
	$app_namespace = 'crowsgame';
	$app_url = 'http://www.facebook.com/appcenter/crowsgame'; //'http://apps.facebook.com/' . $app_namespace . '/';
	$scope = 'email,publish_actions,user_games_activity';
	$facebook=""; 

	function fbCurlRequest($graphObject, $post = false) 
	{
		//echo "curlin!!<br>";
		$my_curl_url="https://graph.facebook.com/".$graphObject;
		echo $my_curl_url . "<br><br><br>"; 
		$ch = curl_init();curl_setopt($ch, CURLOPT_URL,$my_curl_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-length: 0'));
		if($post == true)
		  curl_setopt($ch, CURLOPT_POST, true);
		$data = curl_exec($ch);
		curl_close($ch);
		return json_decode($data, true);
	}
	
	
$app->get('/gTk.{format}', function(Request $request) use($app){
    
   global $seed;
   $length = 20;
   $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $random_string = "";    
   
   for ($p = 0; $p < $length; $p++) {
		$random_string .= $characters[rand(0, strlen($characters)-1)];
	}
  
   $_SESSION["token"] = $random_string;
   $_SESSION["score"] = 0;
   $random_string = /*encrypt(*/$random_string/*,$seed)*/;
   return new Response($random_string, 200); 
  
   // $a= array("J", "Q", "K", "A");
   //shuffle($a);
   //  return new Response($a[0], 200); 
    
});

$app->get('/gFB.{format}', function(Request $request) use($app){
   
   global $app_id,$app_secret,$app_url,$scope,$facebook;
  
   $facebook = new Facebook(array(
		  'appId'  => $app_id,
		  'secret' => $app_secret,
		));
   $user = $facebook->getUser();
   $_SESSION["user"] =	$user;
   // If the user has not installed the app, redirect them to the Auth Dialog
   if (!$user) 
   {
     $loginUrl = $facebook->getLoginUrl(array(
	   'scope' => $scope,
		'redirect_uri' => $app_url,
	 ));
	 /* print('<script> top.location.href=\'' . $app_url . '\'</script>');*/
     header("location: " .  $app_url);
   }
   else
   {
     $access_token = $facebook->getAccessToken();
	 $_SESSION["access_token"] = $access_token;
     $profile = $facebook->api('/me','GET');
     return new Response(json_encode($profile) , 200); 
   }
   
});



$app->get('/gRFB.{format}', function(Request $request) use($app){

	global $app_id;
	$url = 'https://graph.facebook.com/'.$app_id.'/scores?access_token='.$_SESSION["access_token"];
	$rank = file_get_contents($url);
	return new Response($rank, 200); 

});

$app->get('/sSFB.{format}', function(Request $request) use($app){

	$total_points = "999"; ///rand(0,1000);
	//Use the Curl function to post the score
	$post_score = fbCurlRequest($_SESSION["user"] ."/scores?score=". $total_points ."&access_token=". $_SESSION["access_token"], true);
	return new Response(json_encode($post_score), 200); 

});

$app->get('/gFBP.{format}', function(Request $request) use($app){
   
  global $facebook;// $app_id,$app_secret,$app_url,$scope;
  $user_profile =  $_SESSION["fb"]->api('/me','GET');
   
  return new Response($user_profile["last_name"], 200); 
    
});




$app->get('/gQ.{format}', function(Request $request) use($app){
    
	global $seed;
	$encoded = $request->get('param');
    $token=/*decrypt(*/$encoded/*,"wopidom")*/;
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Questions::getQuestions();			
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);

		for($i=0; $i< sizeof($q); $i++)
		{	
			//$toMix = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
			$t = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
			shuffle($t);
			$right = array_search($q[$i]["answer1"], $t)+1; 
			$q[$i]["answer1"] = $t[0];
			$q[$i]["answer2"] = $t[1];
			$q[$i]["answer3"] = $t[2];
			$q[$i]["answer4"] = $t[3];

			$_SESSION["answers"][$i] = array($q[$i]["id"],$right);
			//$_SESSION["answers"][$q[$i]["id"]] = $right;
		}
		
		//return new Response($toMix[0] . " - " . $toMix[1] . " - " .$toMix[2] . " - " . $toMix[3] . "\r" . $t[0] . " - " . $t[1] . " - " .$t[2] . " - " .$t[3] . " - right:" . $right, 200); 
		return new Response(/*encrypt(*/json_encode($q)/*,$seed)*/, 200); 
	}
	else
	{
		return new Response("Invalid Token", 200);
	}	
    
});



$app->get('/preguntas.{format}', function(Request $request) use($app){

        $sql = Questions::getQuestions();			
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);

		for($i=0; $i< sizeof($q); $i++)
		{	
			//$toMix = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
			$t = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
			shuffle($t);
			$right = array_search($q[$i]["answer1"], $t)+1; 
			$q[$i]["answer1"] = $t[0];
			$q[$i]["answer2"] = $t[1];
			$q[$i]["answer3"] = $t[2];
			$q[$i]["answer4"] = $t[3];

			$_SESSION["answers"][$i] = array($q[$i]["id"],$right);
			//$_SESSION["answers"][$q[$i]["id"]] = $right;
		}
		
		//return new Response($toMix[0] . " - " . $toMix[1] . " - " .$toMix[2] . " - " . $toMix[3] . "\r" . $t[0] . " - " . $t[1] . " - " .$t[2] . " - " .$t[3] . " - right:" . $right, 200); 
		return new Response(/*encrypt(*/json_encode($q)/*,$seed)*/, 200); 

});

$app->get('/query.{format}', function(Request $request) use($app){

//$url=parse_url(getenv("mysql://b590635813a535:fadf6557@us-cdbr-east-02.cleardb.com/heroku_7c00e617d8000bf"));

    $server = "us-cdbr-east-02.cleardb.com";// $url["host"];
    $username = "b590635813a535";// $url["user"];
    $password = "fadf6557"; // $url["pass"];
    $db = "heroku_7c00e617d8000bf"; // substr($url["path"],1);

      $link = mysql_connect($server, $username, $password);         
    mysql_select_db($db);
	$a="";
	$result= mysql_query("select * from table1",$link);
    while($row = mysql_fetch_array($result))
	{
	   $a .= $row["id"] . "<br>";
	}
   //  return new Response($server . " - " . $username . " - " . $password . " - " .  $db, 200); 	
     return new Response($a, 200); 	
    
});

$app->get('/gPi.{format}', function(Request $request) use($app){
    
    global $seed;
	$param = $request->get('param');
    $encoded=/*decrypt(*/$param/*,"wopidom")*/;
 
	$data = explode(",",$encoded);	
	$idPlayer= $data[0];
	$token = $data[1];

    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$idPlayer = $request->get('idPlayer');
		$sql = Players::getInfo($idPlayer);
				
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);
		
		//-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
		return new Response(/*encrypt(*/json_encode($q)/*,$seed)*/, 200); 
	}
	else
		return new Response("Invalid Token", 200); 		
    
});

$app->get('/gT.{format}', function(Request $request) use($app){
   
    global $seed;
	$encoded = $request->get('param');
    $token=/*decrypt(*/$encoded/*,"wopidom")*/;
  
   // $token = $request->get('token');
   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$d = date('Y-m-d H:i:s');
		date_default_timezone_set('UTC');
		$format = 'Y-m-d H:i:s';
		$str = date($format);
		//echo $str . "<br>";
		$dt = DateTime::createFromFormat($format, $str);
		$timestamp = $dt->format('U');
		//echo $timestamp;
		return new Response(/*encrypt(*/$timestamp/*,$seed)*/, 200); 
		//return new Response("hola", 200); 
	}
	else
		return new Response("Invalid Token", 200); 
    
});

$app->get('sA.{format}', function(Request $request) use($app){
    
    global $seed;
	
	$param = $request->get('param');
    $encoded=/*decrypt(*/$param/*,"wopidom")*/;
 
	$data = explode(",",$encoded);	
	
	$idAnswer= $data[0];
    $idQuestion= $data[1];
	$token = $data[2];
	$score = $data[3];
	
	//$token = $request->get('token');
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		global $totalScore;
		//$idQuestion = $request->get('idQuestion');
		//$idAnswer = $request->get('idAnswer');
		$result=0;
		$i=0;
		$coins= 0;
		while($i<sizeof($_SESSION["answers"]) && $result==0)
		{
			if($_SESSION["answers"][$i][0]==$idQuestion && $_SESSION["answers"][$i][1]==$idAnswer)
			{
				$result="1";
				$_SESSION["score"] += $score;
			//	unset($_SESSION["answers"][$i]);
			}	
			$i++;	
		}
		
		//return new Response(encrypt("answer:" . $idAnswer . ", q: " . $idQuestion . ", result: " . $result,"wopidom"), 200);
		return new Response(/*encrypt(*/$result/*,$seed)*/, 200);
	}
	else
		return new Response("false", 200); 		
 
    
});

$app->get('gS.{format}', function(Request $request) use($app){
    
	global $seed;
	$encoded = $request->get('param');
    $token=/*decrypt(*/$encoded/*,"wopidom")*/;
	
	//$token = $request->get('token');
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$coins = $_SESSION["score"] / 5;
		
		$values = array($_SESSION["score"],$coins);
		
		return new Response(/*encrypt(*/json_encode($values)/*,$seed)*/, 200);
	}
	else
		return new Response("false", 200); 		
  
});


$app->get('/ver-comentarios.{format}', function(Request $request) use($app){
    
    global $seed;
	
	$sql = Comment::findAll();
    
    //-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
    //   a cada item del array llamado a nuestro método utf8_converter() definido
    //   en src\util.php
    $comentarios = $app['db']->fetchAll($sql);
    $comentarios = utf8_converter($comentarios);
    
    //-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
    return new Response(json_encode($comentarios), 200); 
    
});

$app->post('/crear-comentario.{format}', function(Request $request) use($app){
    
    global $seed;
	//-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    if (!$comment = $request->get('comment'))
    {
        return new Response('Parametros insuficientes', 400);
    }

    //-- Utilizamos como ejemplo un objeto Comentario para delegar la creación 
    //   del SQL utilizando el método PDO::quote() para no tener problemas con 
    //   SQL Injection.
    $c = new Comment();
    $c->author = $app['db']->quote($comment['author']);
    $c->email = $app['db']->quote($comment['email']);
    $c->content = $app['db']->quote($comment['content']);
    
    $sql = $c->getInsertSQL();
    
    //-- Ejecutamos la sentencia
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 201 - Creado
    return new Response('Comentario creado', 201);
    
});

$app->put('actualizar-comentario/{id}.{format}', function($id) use($app){
    
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    //-- También podemos usar directamente la Injección de dependecias para 
    //   obtener el request del contenedor a diferencia del ejemplo anterior.
    
    if (!$comment = $app['request']->get('comment'))
    {
        return new Response('Parametros insuficientes', 400);
    }
    
    //-- Obtenemos el select para encontrar un comentario de acuerdo al $id y
    //   comprobar que lo que vamos a modificar realmente exista.
    $sql = Comment::find($id);
    
    $comentario = $app['db']->fetchAll($sql);
    
    //-- En caso de no existir el comentario a modificar retornamos un código
    //   HTTP 404 - No encontrado
    if(empty($comentario))
    {
        return new Response('Comentario no encontrado.', 404);
    }
    
    //-- Si existe el comentario a modificar obtenemos el SQL para el update y
    //   lo ejecutamos
    $content = $app['db']->quote($comment['content']);
    $sql = Comment::getUpdateSQL($id, $content);
    
    //-- Ejecutamos la sentencia
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 200 - OK
    return new Response("Comentario con ID: {$id} actualizado", 200);
    
});

$app->delete('eliminar-comentario/{id}.{format}', function($id) use($app){
    
    //-- Obtenemos el select para encontrar un comentario de acuerdo al $id y
    //   comprobar que lo que vamos a eliminar realmente exista.
    $sql = Comment::find($id);
    
    $comentario = $app['db']->fetchAll($sql);
    
    //-- En caso de no existir el comentario a eliminar retornamos un código
    //   HTTP 404 - No encontrado
    if(empty($comentario))
    {
        return new Response('Comentario no encontrado.', 404);
    }
    
    //-- Obtenemos el SQL para eliminar el comentario y ejecutamos la sentencia
    $sql = Comment::getDeleteSQL($id);
    
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 200 - OK
    return new Response("Comentario con ID: {$id} eliminado", 200);
    
}); 

return $app;