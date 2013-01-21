<?php
class DB {

	private static $conf = array('host' => 'localhost',
								 'user' => 'alnwick_zm',
								 'pass' => '65xEWasw3bRAtAp',
								 'db'   => 'alnwick_zm');

	private static $connection;
	
	public static function getInstance(){
		if(self::$connection == null){
			self::$connection = new MySQLi(self::$conf['host'], self::$conf['user'], self::$conf['pass'], self::$conf['db']);
			if (mysqli_connect_errno()) { 
				printf("Can't connect to MySQL Server. Errorcode: %s\n", mysqli_connect_error()); 
				exit; 
			}
		}
		return self::$connection;
	}
	
	public static function escape($string){
		return self::getInstance()->real_escape_string($string);
	}
	
	/**
	 *	Automatically converts resultset to an array of rows based on query.
	**/
	public static function select($query){
		$result = self::query($query);
		$return_array = array();
		if(isset($result->num_rows) && $result->num_rows > 0){
			while($row = $result->fetch_assoc())
				$return_array[] = $row;
		}
		
		return $return_array;

	}
	
	/**
	 *	Return resultset based on query.
	**/	
	public static function query($q){
		if (is_string($q) && !empty($q)){
			return self::getInstance()->query($q);

		}		
	}
	
	public static function last_insert_id(){
		return self::getInstance()->insert_id;
	}


}
?>
