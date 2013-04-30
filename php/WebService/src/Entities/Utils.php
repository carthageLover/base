<?php

	function https_post($uri, $postdata) 
	{
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
				
		return $result;
	}	
    
	function https_post_og($uri, $attachment) 
	{
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
		$result = curl_exec($ch);
		curl_close ($ch);
				
		return $result;
	}
		
	function https_delete($url, $postdata)
	{
	
		//$url =$this->__url.$path;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		return $result;
	}


	function multiple_threads_request($nodes)
	{ 
        $mh = curl_multi_init(); 
        $curl_array = array(); 
        foreach($nodes as $i => $url) 
        { 
            $curl_array[$i] = curl_init($url); 
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true); 
            curl_multi_add_handle($mh, $curl_array[$i]); 
        } 
        $running = NULL; 
        do { 
            usleep(10000); 
            curl_multi_exec($mh,$running); 
        } while($running > 0); 
        
        $res = array(); 
        foreach($nodes as $i => $url) 
        { 
            $res[$url] = curl_multi_getcontent($curl_array[$i]); 
        } 
        
        foreach($nodes as $i => $url){ 
            curl_multi_remove_handle($mh, $curl_array[$i]); 
        } 
        curl_multi_close($mh);        
        return $res; 
	} 


 
	function multiRequest($data, $options = array()) {
	 
	  // array of curl handles
	  $curly = array();
	  // data to be returned
	  $result = array();
	 
	  // multi handle
	  $mh = curl_multi_init();
	 
	  // loop through $data and create curl handles
	  // then add them to the multi-handle
	  foreach ($data as $id => $d) {
	 
		$curly[$id] = curl_init();
	 
		$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($curly[$id], CURLOPT_URL,            $url);
		curl_setopt($curly[$id], CURLOPT_HEADER,         0);
		curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
	 
		// post?
		if (is_array($d)) {
		  if (!empty($d['post'])) {
			curl_setopt($curly[$id], CURLOPT_POST,       1);
			curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
		  }
		}
	 
		// extra options?
		if (!empty($options)) {
		  curl_setopt_array($curly[$id], $options);
		}
	 
		curl_multi_add_handle($mh, $curly[$id]);
	  }
	 
	  // execute the handles
	  $running = null;
	  do {
		curl_multi_exec($mh, $running);
	  } while($running > 0);
	 
	 
	  // get content and remove handles
	  foreach($curly as $id => $c) {
		$result[$id] = curl_multi_getcontent($c);
		curl_multi_remove_handle($mh, $c);
	  }
	 
	  // all done
	  curl_multi_close($mh);
	 
	  return $result;
	}
	


	function getBonus($i)
	{
	
		$bonus1=0;
		$bonus_max=0.35;
		$xp_calage=20;
		$bonus_calage=0.25;
		
		
			$b = $bonus1 + ($bonus_max - $bonus1) * (
													 1 - pow( 
															($bonus_max - $bonus_calage)/($bonus_max - $bonus1),
															(1-$i)/(1-$xp_calage)
															) 
													);
		//echo $b;
		return $b;
	}
	
	function timeDiff($firstTime,$lastTime)
	{
  
	   // convert to unix timestamps
	   $firstTime=strtotime($firstTime);
	   $lastTime=strtotime($lastTime);
	
	   // perform subtraction to get the difference (in seconds) between times
	   $timeDiff=$lastTime-$firstTime;
	
	   // return the difference
	   return $timeDiff;
	}
	
	
	// These methods are documented here:
	// https://developers.facebook.com/docs/authentication/signed_request/
	function parse_signed_request($signed_request, $secret) {
	  list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	
	  // decode the data
	  $sig = base64_url_decode($encoded_sig);
	  $data = json_decode(base64_url_decode($payload), true);
	
	  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		error_log('Unknown algorithm. Expected HMAC-SHA256');
		return null;
	  }
	
	  // check sig
	  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	  if ($sig !== $expected_sig) {
		error_log('Bad Signed JSON signature!');
		return null;
	  }
	
	  return $data;
	}
	
	function base64_url_decode($input) {
	  return base64_decode(strtr($input, '-_', '+/'));
	}
	
	function utf8_converter($array)
	{
		array_walk_recursive($array, function(&$item, $key){
			$item = utf8_encode($item);
		});
		
		return $array;
	}

	function encrypt($string,$key){
		$returnString = "";
	  // $charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ");
		$charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo");
		$charsLength = count($charsArray);
		$stringArray = str_split($string);
		$keyArray = str_split(md5($key));
		$randomKeyArray = array();
		while(count($randomKeyArray) < $charsLength){
			$randomKeyArray[] = $charsArray[rand(0, $charsLength-1)];
		}
		for ($a = 0; $a < count($stringArray); $a++){
			$numeric = ord($stringArray[$a]) + ord($randomKeyArray[$a%$charsLength]);
			$returnString .= $charsArray[floor($numeric/$charsLength)];
			$returnString .= $charsArray[$numeric%$charsLength];
		}
		$randomKeyEnc = '';
		for ($a = 0; $a < $charsLength; $a++){
			$numeric = ord($randomKeyArray[$a]) + ord($keyArray[$a%count($keyArray)]);
			$randomKeyEnc .= $charsArray[floor($numeric/$charsLength)];
			$randomKeyEnc .= $charsArray[$numeric%$charsLength];
		}
		return $randomKeyEnc.md5($string).$returnString;
		
		//return $string;
		
	};


	function decrypt($string,$key){
		$returnString = "";
		//$charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ");
		$charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo");
		$charsLength = count($charsArray);
		$keyArray = str_split(md5($key));
		$stringArray = str_split(substr($string,($charsLength*2)+32));
		$md5 = substr($string,($charsLength*2),32);
		$randomKeyArray = str_split(substr($string,0,$charsLength*2));
		$randomKeyDec = array();
		for ($a = 0; $a < $charsLength*2; $a+=2){
			$numeric = array_search($randomKeyArray[$a],$charsArray) * $charsLength;
			$numeric += array_search($randomKeyArray[$a+1],$charsArray);
			$numeric -= ord($keyArray[floor($a/2)%count($keyArray)]);
			$randomKeyDec[] = chr($numeric);
		}
		for ($a = 0; $a < count($stringArray); $a+=2){
			$numeric = array_search($stringArray[$a],$charsArray) * $charsLength;
			$numeric += array_search($stringArray[$a+1],$charsArray);
			$numeric -= ord($randomKeyDec[floor($a/2)%$charsLength]);
			$returnString .= chr($numeric);
		}
		if(md5($returnString) != $md5){
			return false;
		}else{
			return $returnString;
		}
		//return $string;
	};		
 
?>