<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use src\Entities\Comment;
use src\Entities\Questions;
use src\Entities\Players;

require_once (BASE_DIR . '/src/Entities/Comment.php');
require_once (BASE_DIR . '/src/Entities/Questions.php');
require_once (BASE_DIR . '/src/Entities/Players.php');
session_start();



$app->get('/gTk.{format}', function(Request $request) use($app){
    
   $length = 20;
   $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $random_string = "";    
   
   for ($p = 0; $p < $length; $p++) {
		$random_string .= $characters[rand(0, strlen($characters)-1)];
	}
  
   $_SESSION["token"] = $random_string;
   $random_string = encrypt($random_string,"wopidom");
   return new Response($random_string, 200); 
  
   // $a= array("J", "Q", "K", "A");
	//shuffle($a);
  //  return new Response($a[0], 200); 
    
});


$app->get('/gQ.{format}', function(Request $request) use($app){
    
	//$token = $request->get('token');
	$encoded = $request->get('param');
    $token=decrypt($encoded,"wopidom");
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Questions::getQuestions();
		
		//-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
		//   a cada item del array llamado a nuestro método utf8_converter() definido
		//   en src\util.php
			
		$q = $app['db']->fetchAll($sql);
		 
		$q = utf8_converter($q);
		
		//-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
		//return new Response(encrypt(json_encode($q),"wopidom"), 200); 
		//encrypt($q[0]["answer1"]
		for($i=0;$i<sizeof($q);$i++)
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
		}
		
		//return new Response($toMix[0] . " - " . $toMix[1] . " - " .$toMix[2] . " - " . $toMix[3] . "\r" . $t[0] . " - " . $t[1] . " - " .$t[2] . " - " .$t[3] . " - right:" . $right, 200); 
		return new Response(encrypt(json_encode($q),"wopidom"), 200); 
	}
	else
	{
		return new Response("Invalid Token", 200);
	}	
    
});



$app->get('/gPi.{format}', function(Request $request) use($app){
    
    $param = $request->get('param');
    $encoded=decrypt($param,"wopidom");
 
	$data = explode(",",$encoded);	
	$idPlayer= $data[0];
	$token = $data[1];
	


    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$idPlayer = $request->get('idPlayer');
		$sql = Players::getInfo($idPlayer);
		
		//-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
		//   a cada item del array llamado a nuestro método utf8_converter() definido
		//   en src\util.php
		
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);
		
		//-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
		return new Response(encrypt(json_encode($q),"wopidom"), 200); 
	}
	else
		return new Response("Invalid Token", 200); 		
    
});

$app->get('/gT.{format}', function(Request $request) use($app){
   
    $encoded = $request->get('param');
    $token=decrypt($encoded,"wopidom");
  
   // $token = $request->get('token');
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$d = date('Y-m-d H:i:s');
		return new Response(encrypt($d,"wopidom"), 200); 
	}
	else
		return new Response("Invalid Token", 200); 

		
    
});

$app->get('sA.{format}', function(Request $request) use($app){
    
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
      
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    //-- También podemos usar directamente la Injección de dependecias para 
    //   obtener el request del contenedor a diferencia del ejemplo anterior.
	
	$param = $request->get('param');
    $encoded=decrypt($param,"wopidom");
 
	$data = explode(",",$encoded);	
	
	$idAnswer= $data[0];
    $idQuestion= $data[1];
	$token = $data[2];
	
	//$token = $request->get('token');
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$idQuestion = $request->get('idQuestion');
		//$idAnswer = $request->get('idAnswer');
		$result="0";
		for($i=0;$i<sizeof($_SESSION["answers"]);$i++)
		{
		if($_SESSION["answers"][$i][0]==$idQuestion && $_SESSION["answers"][$i][1]==$idAnswer)
		    $result="1";
		}
		
		//return new Response(encrypt("answer:" . $idAnswer . ", q: " . $idQuestion . ", result: " . $result,"wopidom"), 200);
		return new Response(encrypt($result,"wopidom"), 200);
	}
	else
		return new Response("false", 200); 		
 
    
});




$app->get('/ver-comentarios.{format}', function(Request $request) use($app){
    
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

$app->get('/gZ.{format}', function(Request $request) use($app){

 var_dump($_SESSION["rightZonesList"]);
 
 $zonesCount = array_count_values($_SESSION["rightZonesList"]);
 
 if(!isset($zonesCount[1])) $zonesCount[1]=0;
 if(!isset($zonesCount[2])) $zonesCount[2]=0;
 if(!isset($zonesCount[3])) $zonesCount[3]=0;
 if(!isset($zonesCount[4])) $zonesCount[4]=0;
 if(!isset($zonesCount[5])) $zonesCount[5]=0;
 if(!isset($zonesCount[6])) $zonesCount[6]=0;
 
 echo "1: " . $zonesCount[1] . "\n";
 echo "2: " . $zonesCount[2] . "\n";
 echo "3: " . $zonesCount[3] . "\n";
 echo "4: " . $zonesCount[4] . "\n";
 echo "5: " . $zonesCount[5] . "\n";
 echo "6: " . $zonesCount[6] . "\n";
 
 echo "*********************************************************** \n";
 echo "1: " . $_SESSION["playerInfo"]["zone1Count"] . "\n";
 echo "2: " . $_SESSION["playerInfo"]["zone2Count"] . "\n";
 echo "3: " . $_SESSION["playerInfo"]["zone3Count"] . "\n";
 echo "4: " . $_SESSION["playerInfo"]["zone4Count"] . "\n";
 echo "5: " . $_SESSION["playerInfo"]["zone5Count"] . "\n";
 echo "6: " . $_SESSION["playerInfo"]["zone6Count"] . "\n";
 
if($zonesCount[1]+$_SESSION["playerInfo"]["zone1Count"] >= 10){ echo "won 1 \n"; Players::updateZones($_SESSION["facebookId"],1,$app['db']); OGActions::postLearn("celebrities"); }
if($zonesCount[2]+$_SESSION["playerInfo"]["zone2Count"] >= 10){ echo "won 2 \n"; Players::updateZones($_SESSION["facebookId"],2,$app['db']); OGActions::postLearn("movies"); }
if($zonesCount[3]+$_SESSION["playerInfo"]["zone3Count"] >= 10){ echo "won 3 \n"; Players::updateZones($_SESSION["facebookId"],3,$app['db']); OGActions::postLearn("general_culture"); }
if($zonesCount[4]+$_SESSION["playerInfo"]["zone4Count"] >= 10){ echo "won 4 \n"; Players::updateZones($_SESSION["facebookId"],4,$app['db']); OGActions::postLearn("history_geography");  }
if($zonesCount[5]+$_SESSION["playerInfo"]["zone5Count"] >= 10){ echo "won 5 \n"; Players::updateZones($_SESSION["facebookId"],5,$app['db']); OGActions::postLearn("music"); }
if($zonesCount[6]+$_SESSION["playerInfo"]["zone6Count"] >= 10){ echo "won 6 \n"; Players::updateZones($_SESSION["facebookId"],6,$app['db']); OGActions::postLearn("sports"); }
	
 

});


$app->post('/pay.{format}', function(Request $req) use($app){

	//////////////////////////////
	// facebook example: https://developers.facebook.com/docs/payments/callback/#php_example 
	////////////////////////////
/*
 * segun este ejemplo: https://developers.facebook.com/docs/reference/dialogs/pay/
 * (ver DirectURL example)
 * se puede hacer sin js:
	https://www.facebook.com/dialog/pay?app_id=481002115284282&
                                    redirect_uri=http://frozen-garden-4287.herokuapp.com/server/web/pay.json&
                                    action=buy_item&
                                    order_info={"item_id":"1a"}&
                                    dev_purchase_params={"oscif":true}

	https://www.facebook.com/dialog/pay?app_id=481002115284282&redirect_uri=http://frozen-garden-4287.herokuapp.com/server/web/pay.json&action=buy_item&order_info={"item_id":"1a"}&dev_purchase_params={"oscif":true}
*/

	//$app_secret = 'APP_SECRET';
    global $app_secret;

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

	  // TODO: RETRIEVE FROM DATABASE items_id:
    //  $bags = ApStore::getBags($app['db']);
	  
	/*  if ($item_id == 'bag1') { 
	    $item = array(
	      'title' => '1,000 coins',
	      'description' => 'Use coins to buy boosts.',
	      // Price must be denominated in credits.
	      'price' => 5,
	      'image_url' => 'http://www.facebook.com/images/gifts/21.png',
	    );
		
	  }*/
	  
	   $bags = ApStore::getItems($item_id,$app['db']);
	   $item = array(
	      'title' => $bags["title"],
	      'description' => $bags["description"],
	      // Price must be denominated in credits.
	      'price' => ($bags["price"]*10),
	      'image_url' => $bags["image"],
	    );
		 
	   
	 /* if ($item_id == $bags[0]["itemId"]) { 
	    $item = array(
	      'title' => $bags[0]["title"],
	      'description' => $bags[0]["description"],
	      // Price must be denominated in credits.
	      'price' => ($bags[0]["prices"]*10),
	      'image_url' => $bags[0]["image"],
	    );
		
	  } 	  

	  else if ($item_id == 'bag2') {
	    $item = array(
	      'title' => '10,000 coins',
	      'description' => 'Use coins to buy boosts.',
	      // Price must be denominated in credits.
	      'price' => 8,
	      'image_url' => 'http://www.facebook.com/images/gifts/21.png',
	    );
	  } else if ($item_id == 'bag3') {
	    $item = array(
	      'title' => '25,000 coins',
	      'description' => 'Use coins to buy boosts.',
	      // Price must be denominated in credits.
	      'price' => 20,
	      'image_url' => 'http://www.facebook.com/images/gifts/21.png',
	    );
	  } else if ($item_id == 'bag4') {
	    $item = array(
	      'title' => '50,000 coins',
	      'description' => 'Use coins to buy boosts.',
	      // Price must be denominated in credits.
	      'price' => 40,
	      'image_url' => 'http://www.facebook.com/images/gifts/21.png',
	    );
	  } else if ($item_id == 'bag5') {
	    $item = array(
	      'title' => '100,000 coins',
	      'description' => 'Use coins to buy boosts.',
	      // Price must be denominated in credits.
	      'price' => 70,
	      'image_url' => 'http://www.facebook.com/images/gifts/21.png',
	    );
	  }*/

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

$app->get('/getS.{format}', function(Request $request) use($app){
 
   global $xp_level_pts,$xp_const;
   
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
          
   $xp_level_pts = $_SESSION["xp_level_pts"];
   var_dump($xp_level_pts);

    $bags = ApStore::getItems($item_id,$app['db']);
   var_dump($bags);

});

return $app;