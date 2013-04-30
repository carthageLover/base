<?php

namespace src\Entities;

/**
 * Clase creada para simular la encapsulación de la creación de la sentencia SQL.
 */
class ApStore 
{
    
    public $id;
    public $author;
    public $email;
    public $content;
    public $created_at;
    public $updated_at;
    
    /**
     * Retorna un SQL de ejemplo para hacer el insert
     * @return string 
     */
	 
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
    
    /**
     * Retorna un SQL de ejemplo para buscar un comentario cuyo id sea igual a $id
     * @param int $id
     * @return string 
     */
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
                where idFacebook = %d";		
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
	
    public static function getItems($item,$db)
    {
        $sql = "select * from apStore where itemId = '%s'";
        $sql = sprintf($sql,$item);
        $data = $db->fetchAll($sql);
		//echo $sql;
        return $data[0];
    }	
	
    public static function getBags($db)
    {
        $sql = "select * from apStore where itemId like 'bag%'";
       // $sql = sprintf($sql,$item);
        $data = $db->fetchAll($sql);
		//echo $sql;
        return $data;
    }
		   
    public static function getLives($db)
    {
        $sql = "select * from apStore where itemId like 'life%'";
       // $sql = sprintf($sql,$item);
        $data = $db->fetchAll($sql);
		//echo $sql;
        return $data;
    }	
			
    public static function updateLives($idFacebook,$lives,$db)
    {
        $sql = "update players set lives = '%s' where idFacebook = '%s'";
        $sql = sprintf($sql,$lives,$idFacebook);
        $data = $db->exec($sql);
		
        return $data;
    }	
	
	public static function addCredit($id)
    {
        $sql = "update players set lives = lives + 1 where idFacebook = '%s'";
        $sql = sprintf($sql,$id);
        
        return $sql;
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
		from rightanswers as ra, players as p,questions as q, zones as z 
		where 
		ra.idFacebook=p.idFacebook and 
		ra.idQuestion = q.id and 
		q.idZone = z.id and 
		p.idFacebook = '%s' 
		group by z.id,z.zone";
		
        $sql = sprintf($sql,$id);
        
        return $sql;
    }	
		
	public static function getQuestions()
    {
        $sql = "select * 
                from questions order by rand() limit 20";
        
        return $sql;
    }
    
    /**
     * Retorna un SQL de ejemplo para hacer un update del campo $content al 
     * comentario int $id
     * @param string $content
     * @return string
     */
    public static function updateData($id, $coins,$xpPoints,$highScore,$XP)
    {
        $sql = "update players
                set coins = coins + %s, XPpoints = %s,highScore = %s, XP = %s
                where idFacebook = %d";
        $sql = sprintf($sql, $coins,$xpPoints,$highScore,$XP, $id);
        
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
		    
	public static function insertPlayer($profile)
    {
        $sql = "insert into players(idFacebook,name,lastName) values(%s,'%s','%s')";//, XPpoints = %s,highScore = %s, XP = %s
        $sql = sprintf($sql,$profile["id"],$profile["first_name"],$profile["last_name"]);
		       
        return $sql;
    }
	
	public static function rightAnswer($idFacebook,$idQuestion)
    {
        $sql = "insert into rightAnswers(idFacebook,idQuestion) values(%s,%s)";//, XPpoints = %s,highScore = %s, XP = %s
        $sql = sprintf($sql,$idFacebook,$idQuestion);
		       
        return $sql;
    }	
	
    /**
     * Retorna un SQL de ejemplo para eliminar el comentario con id igual a $id
     * @param int $id
     * @return string 
     */
    public static function getDeleteSQL($id)
    {
        $sql = "delete from comments
                where id = %d";
        $sql = sprintf($sql, $id);
        
        return $sql;
    }
	
	
	public static function addictAchievement($games,$db)
    {
        $sql = "select * from trophies where id = 8";
		$data = $db->fetchAll($sql);
		
		$result=array();
		
		if($games >= $data[0]["value5"])
			$result["stars"] = 5; //echo "5 stars";
		else	
 		if($games >= $data[0]["value4"])
			$result["stars"] = 4; //echo "4 stars";
		else	
		if($games >= $data[0]["value3"])
			$result["stars"] = 3; //echo "3 stars";
		else	
		if($games >= $data[0]["value2"])
			$result["stars"] = 2; //echo "2 stars";
		else
		if($games >= $data[0]["value1"])
			$result["stars"] = 1; //echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
			     
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
			$result["stars"] = 5; //echo "5 stars";
		else	
 		if($jokersCount >= $data[0]["value4"])
			$result["stars"] = 4; //echo "4 stars";
		else	
		if($jokersCount >= $data[0]["value3"])
			$result["stars"] = 3; //echo "3 stars";
		else	
		if($jokersCount >= $data[0]["value2"])
			$result["stars"] = 2; //echo "2 stars";
		else
		if($jokersCount >= $data[0]["value1"])
			$result["stars"] = 1; //echo "1 stars";
		else
			$result["stars"] = 0; //echo "1 stars";
			
		//$result["answers"] = $score;		
		$result["idT"] = $data[0]["id"];		
		$result["name"] = $data[0]["name"];
			     
        return $result;
    }	
	
    
}


?>