<?php

namespace src\Entities;

class Players 
{
       
    public function getInsertSQL()
    {
        $sql = "INSERT INTO comments(
                author, 
                email, 
                content, 
                created_at
            ) 
            VALUES (%s, %s, %s, '%s')";
        
        $sql = sprintf(
            $sql, 
            $this->author,
            $this->email,
            $this->content,
            date('Y-m-d H:i:s')
        );
        
        return $sql;
    }
    
    public static function find($id)
    {
        $sql = "select * 
                from comments
                where id = %d";
        $sql = sprintf($sql, $id);
        
        return $sql;
    }
	
	
    public static function getInfo($id)
    {
        /*$sql = "select * 
                from players
                where idFacebook = %s";*/
				
		$sql = "select * 
                from players
                where idFacebook = %s";		
        $sql = sprintf($sql, $id);
        
        return $sql;
    }
	    
    public static function findAll()
    {
        $sql = "select * 
                from comments";
        
        return $sql;
    }
	
    public static function exists($id)
    {
        $sql = "select count(*) as cant from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
    }
	
    public static function consumeCredit($id)
    {
        $sql = "update players set lives = lives - 1 where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
    }
		
    public static function newGame($id)
    {
        $sql = "update players set gameSessions = gameSessions + 1 where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
    }	
	
    public static function getLives($id,$db)
    {
        $sql = "select lives from players where idFacebook = '%s'";
		
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["lives"];
    }	
	
    public static function getAll($db)
    {
        $sql = "select * from players";
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data;
    }	
		
    public static function jokersUsed($id,$db)
    {
        $sql = "select jokersUsed from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["jokersUsed"];
    }
	
    public static function livesSent($id,$db)
    {
        $sql = "select count(*) c from inbox where type='accept' and idFrom = '%s'";
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["c"];
    }
			
    public static function getHighScore($id,$db)
    {
        $sql = "select highScore from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["highScore"];
    }
		
    public static function getName($id,$db)
    {
        $sql = "select name,lastName from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["name"] . " " . $data[0]["lastName"] ;
    }	
	
    public static function getGames($id,$db)
    {
        $sql = "select gameSessions from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
		//echo $sql;
        $data = $db->fetchAll($sql);
        return $data[0]["gameSessions"];
    }	
				
    public static function updateLives($idFacebook,$lives,$db)
    {
        $sql = "update players set lives = '%s' where idFacebook = '%s'";
        $sql = sprintf($sql,$lives,$idFacebook);
        $data = $db->exec($sql);
		
        return $data;
    }
	
	public static function updateTournament($id,$score,$position,$db)
	{
	    $tournamentRead=1;
		$lives=0;
		$coins=0;
		
		if($position==0) $tournamentRead=0;
		
		$sql="select * from tournamentRewards where position=" . $position;
		$data = $db->fetchAll($sql);
        
		if(sizeof($data)>0)
		{
			$lives = $data[0]["lives"];
			$coins = $data[0]["coins"];
		}
		
		$sql = "update players set lastScore='%s',lastPosition='%s',tournamentRead=" . $tournamentRead . ",coinsEarned=" . 
		$coins . ",livesEarned=" . $lives . ", coins = coins + " . $coins . ", lives = lives + " . $lives . " where idFacebook = '%s'";
		
        $sql = sprintf($sql,$score,$position,$id);
        $data = $db->exec($sql);
		
		echo $sql . "\n";
	}
		
    public static function updateZones($idFacebook,$zone,$db)
    {
        $sql = "update players set zone" . $zone . "Count = 0 where idFacebook = '%s'";
        $sql = sprintf($sql,$idFacebook);
        $data = $db->exec($sql);
		echo $sql . "\n";
        return $data;
    }	
		
	public static function addCredit($id)
    {
        $sql = "update players set lives = lives + 1 where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
    }	

	public static function addCoins($id,$coins,$db)
    {
        $sql = "update players set coins = coins + %s where idFacebook = '%s'";
        $sql = sprintf($sql,$coins,$id);
        //echo $sql;
		$db->exec($sql);
        return $sql;
    }
	
	public static function addLives($id,$lives,$db)
    {
        $sql = "update players set lives = lives + %s where idFacebook = '%s'";
        $sql = sprintf($sql,$lives,$id);
        // echo $sql;
		$db->exec($sql);
        return $sql;
    }
	
	
	public static function insertAchievement($idFacebook,$idTrophy,$stars)	
	{
        $sql = "insert into playersTrophies(idFacebook,idTrophy,stars) values('%s','%s','%s')";
        $sql = sprintf($sql,$idFacebook,$idTrophy,$stars);
        // echo $sql;
        return $sql;	
	
	}
	
	public static function getAchievement($data)
    {
       // $sql = "select * from trophies where id = " . $id;
        
		//$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		
		//$data = $db->fetchAll($sql);
		$result=array();
		
		if($value >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; }//echo "5 stars";
		else	
 		if($value >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; } //echo "4 stars";
		else	
		if($value >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; }  //echo "3 stars";
		else	
		if($value >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"]; }  //echo "2 stars";
		else
		if($value >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"]; }  //echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];
			     
        return $result;
    }
	
    public static function validateAchievement($idZone,$answersCount,$db)
    {
        $sql = "select * from trophies where idZone = '%s'";
        
		$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		$data = $db->fetchAll($sql);
		//closeCursor()
		$result=array();
		
		if($answersCount >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; $result["value"] = $data[0]["value5"]; } //echo "5 stars";
		else	
 		if($answersCount >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; $result["value"] = $data[0]["value4"];  } //echo "4 stars";
		else	
		if($answersCount >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; $result["value"] = $data[0]["value3"];  } //echo "3 stars";
		else	
		if($answersCount >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"]; $result["value"] = $data[0]["value2"];  } //echo "2 stars";
		else
		if($answersCount >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"]; $result["value"] = $data[0]["value1"];  } //echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["answers"] = $answersCount;		
		$result["zone"] = $idZone;		
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];
		
		
			     
        return $result;
    }
	
		
	public static function goodAnswersAchievement($answersCount,$db)
    {
        $sql = "select * from trophies where id = 11";
        
		//$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		$data = $db->fetchAll($sql);
		$result=array();
		
		if($answersCount >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; $result["value"] = $data[0]["value5"];  } //echo "5 stars";
		else	
 		if($answersCount >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; $result["value"] = $data[0]["value4"];  } //echo "4 stars";
		else	
		if($answersCount >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; $result["value"] = $data[0]["value3"];  } //echo "3 stars";
		else	
		if($answersCount >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"]; $result["value"] = $data[0]["value2"];  }//echo "2 stars";
		else
		if($answersCount >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"]; $result["value"] = $data[0]["value1"];  }//echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["answers"] = $answersCount;		
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];     
        
		return $result;
    }
	
	public static function livesAchievement($livesCount,$db)
    {
        $sql = "select * from trophies where id = 7";
        
		//$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		$data = $db->fetchAll($sql);
		$result=array();
		
		if($livesCount >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; $result["value"] = $data[0]["value5"];  } //echo "5 stars";
		else	
 		if($livesCount >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; $result["value"] = $data[0]["value4"];  } //echo "4 stars";
		else	
		if($livesCount >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; $result["value"] = $data[0]["value3"];  } //echo "3 stars";
		else	
		if($livesCount >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"]; $result["value"] = $data[0]["value2"];  }//echo "2 stars";
		else
		if($livesCount >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"]; $result["value"] = $data[0]["value1"];  }//echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		//$result["lives"] = $livesCount;		
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];     
        
		return $result;
    }
	
	public static function highScoreAchievement($score,$db)
    {
        $sql = "select * from trophies where id = 12";
        
		//$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		$data = $db->fetchAll($sql);
		$result=array();
		
		if($score >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; $result["value"] = $data[0]["value5"];  }//echo "5 stars";
		else	
 		if($score >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; $result["value"] = $data[0]["value4"];  } //echo "4 stars";
		else	
		if($score >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; $result["value"] = $data[0]["value3"];  }  //echo "3 stars";
		else	
		if($score >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"]; $result["value"] = $data[0]["value2"];  }  //echo "2 stars";
		else
		if($score >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"]; $result["value"] = $data[0]["value1"];  }  //echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];
			     
        return $result;
    }
	
	public static function kingAchievement($db)
	{
		$sql = "select * from trophies where id = 14";
        $data = $db->fetchAll($sql);
		
		$result["stars"] = 1;
		$result["idT"] = $data[0]["id"];
		$result["name"] = $data[0]["name"]; 
		$result["XP"] = $data[0]["XP1"]; 
		$result["coins"] = $data[0]["coins1"]; 
		$result["lives"] = $data[0]["lives1"]; 
		$result["description"] = $data[0]["description"];
		
	    return $result;
	}
	
	public static function chainAchievement($answers,$db)
    {
      	
	  $zeros = array();
 	  
	  for($i=0;$i<sizeof($answers);$i++)
	    if($answers[$i]==0)
		  array_push($zeros,$i);
	
  	  array_push($zeros,sizeof($answers));

	  $chains = array();
	  
	  for($i=0;$i<sizeof($zeros)-1;$i++)	
		   array_push($chains,$zeros[$i+1]-$zeros[$i]-1);
	  
	  $sql = "select * from trophies where id = 9";
        
	 // $sql = sprintf($sql,$idZone);

	  $data = $db->fetchAll($sql);
	  $result=array();
	  $result["idT"] = 9;

	  if(sizeof($chains)>0)
	  {
	  $max_chain = max($chains);	
		
	  
		 if($max_chain>=$data[0]["value5"])
		 	{ $result["stars"] = 5;$result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP5"]; 
			  $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"]; 
			  $result["value"] = $data[0]["value5"];$result["description"] = $data[0]["description"];
			  $found=1;
			}
		 else
		 if($max_chain>=$data[0]["value4"])
		 	{ $result["stars"] = 4;$result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP4"]; 
			  $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"]; 
			  $result["value"] = $data[0]["value4"];$result["description"] = $data[0]["description"];
			  $found=1;
			}
		 else
		 if($max_chain>=$data[0]["value3"])
		 	{ $result["stars"] = 3;$result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP3"]; 
			  $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"]; 
			  $result["value"] = $data[0]["value3"];$result["description"] = $data[0]["description"];
			  $found=1;
			}
		 else
		 if($max_chain>=$data[0]["value2"])
		 	{ $result["stars"] = 2;$result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP2"]; 
			  $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"];
			  $result["value"] = $data[0]["value2"];$result["description"] = $data[0]["description"];
			  $found=1;
		    }
		 else
		 if($max_chain>=$data[0]["value1"])
		 	{ $result["stars"] = 1;$result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP1"]; 
			  $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"];
			  $result["value"] = $data[0]["value1"];$result["description"] = $data[0]["description"];
			  $found=1;
		    }
		 else
			$result["stars"] = 0;	
		}
		else
			$result["stars"] = 0;
	  
      return $result;

    }	
	
				
    public static function isDummyWon($idFacebook,$db)
    {
        $sql = "select count(*) as cant from playersTrophies where idFacebook = '%s' and idTrophy = 16";
        $sql = sprintf($sql,$idFacebook);
      
		$data = $db->fetchAll($sql);	
        return $data[0]["cant"];
    }
	
    public static function isKingWon($idFacebook,$db)
    {
        $sql = "select count(*) as cant from playersTrophies where idFacebook = '%s' and idTrophy = 14";
        $sql = sprintf($sql,$idFacebook);
      
		$data = $db->fetchAll($sql);	
        return $data[0]["cant"];
    }
		
    public static function isTrophyWon($idFacebook,$idTrophy,$stars,$db)
    {
        $sql = "select count(*) as cant from playersTrophies where idFacebook = '%s' and idTrophy = '%s' and stars = '%s' ";
        $sql = sprintf($sql,$idFacebook,$idTrophy,$stars);
        // echo $sql;
		$data = $db->fetchAll($sql);
		
        return $data[0]["cant"];
    }
		
    public static function getCoins($id)
    {
        $sql = "select coins from players where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
    }
	
	
    public static function getRightAnswersByZone($id)
    {
        $sql = "select z.id, z.zone, count(*) as answers 
		from rightAnswers as ra, players as p,questions as q, zones as z 
		where 
		ra.idFacebook=p.idFacebook and 
		ra.idQuestion = q.id and 
		q.idZone = z.id and 
		p.idFacebook = '%s' 
		group by z.id,z.zone";
		
        $sql = sprintf($sql,$id);
        
        return $sql;
    }	
	
    public static function getRightAnswersByZoneId($idFacebook,$idZone,$db)
    {
        $sql = "SELECT COUNT( * ) AS answers
		FROM rightAnswers AS ra, players AS p, questions AS q, zones AS z
		WHERE ra.idFacebook = p.idFacebook
		AND ra.idQuestion = q.id
		AND q.idZone = z.id
		AND p.idFacebook =  '%s'
		AND z.id =%s
		LIMIT 0 , 30";
		
        $sql = sprintf($sql,$idFacebook,$idZone);
		
		$data = $db->fetchAll($sql);	
        return $data[0]["answers"];

    }		
		
	public static function getQuestions()
    {
        $sql = "select * 
                from questions order by rand() limit 20";
        
        return $sql;
    }
    
    public static function updateData($id, $coins,$xpPoints,$highScore,$XP)
    {
        $sql = "update players set coins = coins + " . $coins . ", XPpoints = " . $xpPoints . ",highScore = " . $highScore . ", XP = " . $XP . " where idFacebook = " . $id;
        // $sql = sprintf($sql, $coins,$xpPoints,$highScore,$XP, $id);
        //echo $sql;
        return $sql;
    }
	
    public static function useJoker($id)
    {
        $sql = "update players set jokersUsed = jokersUsed + 1 where idFacebook = %d";
        $sql = sprintf($sql, $id);
        
        return $sql;
    }
	
    public static function buyJoker($idFacebook,$currency)
    {
        $sql = "update players set coins = coins - %d where idFacebook = %s";
        $sql = sprintf($sql,$currency, $idFacebook);
        
        return $sql;
    }
	
    public static function returnJoker($idFacebook,$currency)
    {
        $sql = "update players set coins = coins + %d where idFacebook = %s";
        $sql = sprintf($sql,$currency, $idFacebook);
        
        return $sql;
    }
	
	public static function insertPlayer($profile,$db)
    {
        $sql1 = "select initialCoins from generalSettings"; 
		$data = $db->fetchAll($sql1);
		
		$sql = "insert into players(idFacebook,name,lastName,coins,lastGame) values(%s,'%s','%s'," . $data[0]["initialCoins"] . ",now())";//, XPpoints = %s,highScore = %s, XP = %s
        $sql = sprintf($sql,$profile["id"],$profile["first_name"],$profile["last_name"]);
		$db->exec($sql);
		       
        return $sql;
    }
	
	public static function rightAnswer($idFacebook,$idQuestion)
    {
        $sql = "insert into rightAnswers(idFacebook,idQuestion) values(%s,%s)";//, XPpoints = %s,highScore = %s, XP = %s
        $sql = sprintf($sql,$idFacebook,$idQuestion);
		       
        return $sql;
    }	
	
    public static function getDeleteSQL($id)
    {
        $sql = "delete from comments
                where id = %d";
        $sql = sprintf($sql, $id);
        
        return $sql;
    }
	
	
	public static function addictAchievement($games,$db)
    {
        $sql = "select * from trophies where id = 15";
		$data = $db->fetchAll($sql);
		
		$result=array();
		
		if($games >= $data[0]["value5"])
		  { $result["stars"] = 5; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"];
		   $result["value"] = $data[0]["value5"]; } 
		else	
 		if($games >= $data[0]["value4"])
		  {	$result["stars"] = 4; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"];
		   $result["value"] = $data[0]["value4"]; } 
		else	
		if($games >= $data[0]["value3"])
		  {	$result["stars"] = 3; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"];
		   $result["value"] = $data[0]["value3"]; } 
		else	
		if($games >= $data[0]["value2"])
		  { $result["stars"] = 2; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"];
		   $result["value"] = $data[0]["value2"]; } 
		else
		if($games >= $data[0]["value1"])
		  { $result["stars"] = 1; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"];
		   $result["value"] = $data[0]["value1"]; } 
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
		$result["description"] = $data[0]["description"];	
		     
        return $result;
    }
	
	public static function jokerAchievement($jokersCount,$db)
    {
        $sql = "select * from trophies where id = 8";
        
		//$sql = sprintf($sql,$idZone);
		//echo "answers: " . $answersCount;
		$data = $db->fetchAll($sql);
		$result=array();
		
		if($jokersCount >= $data[0]["value5"])
			{ $result["stars"] = 5; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP5"]; $result["coins"] = $data[0]["coins5"]; $result["lives"] = $data[0]["lives5"];
			 $result["value"] = $data[0]["value5"]; } 
		else	
 		if($jokersCount >= $data[0]["value4"])
			{ $result["stars"] = 4; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP4"]; $result["coins"] = $data[0]["coins4"]; $result["lives"] = $data[0]["lives4"];
			 $result["value"] = $data[0]["value4"]; } 
		else	
		if($jokersCount >= $data[0]["value3"])
			{ $result["stars"] = 3; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP3"]; $result["coins"] = $data[0]["coins3"]; $result["lives"] = $data[0]["lives3"];
			 $result["value"] = $data[0]["value3"]; } 
		else	
		if($jokersCount >= $data[0]["value2"])
			{ $result["stars"] = 2; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP2"]; $result["coins"] = $data[0]["coins2"]; $result["lives"] = $data[0]["lives2"];
			 $result["value"] = $data[0]["value2"]; } 
		else
		if($jokersCount >= $data[0]["value1"])
			{ $result["stars"] = 1; $result["idT"] = $data[0]["id"];$result["name"] = $data[0]["name"]; $result["XP"] = $data[0]["XP1"]; $result["coins"] = $data[0]["coins1"]; $result["lives"] = $data[0]["lives1"];
			 $result["value"] = $data[0]["value1"]; } 
		else
			$result["stars"] = 0; //echo "1 stars";
			
		//$result["answers"] = $score;		
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
	    $result["description"] = $data[0]["description"];	
		     
        return $result;
    }	
	
    
}


?>