<?php

namespace src\Entities;

/**
 * Clase creada para simular la encapsulación de la creación de la sentencia SQL.
 */
class Questions 
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
     * Retorna un SQL de ejemplo para obtener todos los registros y columnas
     * de la tabla
     * @return string 
     */
  
    public static function findAll()
    {
        $sql = "select * 
                from comments";
        
        return $sql;
    }
	
	public static function getQuestions()
    {
       // $sql = "select * from questions as q,zones as z where q.idZone=z.id order by rand() limit 100";
        $sql = "select q.id, q.question, q.answer1, q.answer2, q.answer3, q.answer4, z.zone,z.id as idZone from questions as q,zones as z where q.idZone=z.id order by rand() limit 130";
        
        return $sql;
    }
	
    public static function getQuestionById($id,$db)
    {
        $sql = "select question from questions where id = %d";
		$sql = sprintf($sql,$id);
		
        $data = $db->fetchAll($sql);
        return $data[0]["question"];
    }
	
    public static function getTopicById($id,$db)
    {
        
		$sql = "select z.zone from zones z, questions q where q.idZone = z.id and q.id =  %d";
		$sql = sprintf($sql,$id);
		
        $data = $db->fetchAll($sql);
        return $data[0]["zone"];
    }
		
    public static function getHintById($id,$db)
    {
        $sql = "select factoid from questions where id = %d";
		$sql = sprintf($sql,$id);
		
        $data = $db->fetchAll($sql);
        return $data[0]["factoid"];
    }
	
    public static function getUpdateSQL($id, $content)
    {
        $sql = "update comments
                set content = %s
                where id = %d";
        $sql = sprintf($sql, $content, $id);
        
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
    
}

?>
