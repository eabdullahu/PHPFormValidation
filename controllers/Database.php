<?php
    class Database {
    	private $host;
    	private $username;
    	private $password;
    	private $db_name;
    	private $conn;

    	function __construct($host, $username, $password, $db_name){
    		$this->host = $host;
    		$this->username = $username;
    		$this->password = $password;
    		$this->db_name = $db_name;

    		$this->connect();
    	}

		// creates PDO connection and it's called inside constructor when an instance of this class is created.
    	function connect(){
    		try {
		        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
		        // $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		        $this->conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		    }catch(PDOException $exe){
		        die("Connection error: " . $exe->getMessage());
		    }
    	}

		// Gets data from database
		// $sql parameter refers to sql query
		// $data is an array of elements which can be of format associative array or basic array.
		// $forcetomultirow if 1(true) it returns a single 'Object' inside another array.
		// $withPk excepts a column name which value will be array key.
		// $removePkField if this is set to 1 it will remove the $withPk column that we have used from the array data.
		
		// Examples SENDING $sql and $data parameter
		// get_data('SELECT * FROM USERS where id = ?', array(1)); OR get_data('SELECT * FROM USERS where id = :id', array(':id' => 1));
		
		// Examples SENDING $sql and $withPk, $removePkField parameter
		// get_data('SELECT id, name FROM Users', null, 'id', 1); RETURNS array; [1] => Enis, [5] => Filani, [6] => Filanja
		// 1, 5, 6 array key's are id value from database.
		
		// CAN RETURN ALL DATA TYPES(ARRAY, STRING, INTEGER);
    	function get_data($sql=null, $data=null, $forcetomultirow=0, $withpk=0, $removePkField=0){
	        $pk = "";
	        $row = array();
	        $que = $this->conn->prepare($sql);

	        if($data){
	            $que->execute($data);
	        }else{
				$que->execute();
	        }
	        
	        $num = $que->rowCount();
	        if ($withpk == 1){
	            $numcol = $que->columnCount();
	            for ($i = 0; $i < $numcol; $i++) {
	                $f = $que->getColumnMeta($i);
	                if (ereg("primary_key", $f)){
	                    $pk = $que->getColumnMeta($i);
	                    break;
	                }
	            }
	        } else {
	            $pk = $withpk;
	        }

	        if ($num == 0){ //if norows
	            $row = false;
	        } else if ($num == 1 && !$forcetomultirow){ //if 1 row
	            if ($que->columnCount() == 1){
	                $rowx = $que->fetch(PDO::FETCH_NUM);
	                $row = $rowx[0];
	            } else {
	                $row = $que->fetch(PDO::FETCH_ASSOC);
	            }
	            $que->closeCursor();
	        } else {// if multirow
	            if ($que->columnCount() == 1){
	                while ($rowx = $que->fetch(PDO::FETCH_NUM)){
	                    $row[] = $rowx[0];
	                }
	            } else {
	                if ($que->columnCount() == 2){
	                    $otherThanPk = "";
	                    for ($i = 0; $i < 2; $i++){
	                        if ($que->getColumnMeta($i)['name'] != $pk){
	                            $otherThanPk = $que->getColumnMeta($i)['name'];
	                        }
	                    }
	                }
	                while ($rowx = $que->fetch(PDO::FETCH_ASSOC)){
	                    if ($pk){
	                        if (sizeof($rowx) == 2 and $removePkField){
	                            $key = key($rowx);
	                            $row[$rowx[$pk]] = $rowx[$otherThanPk];
	                        } else {
	                            $row[$rowx[$pk]] = $rowx;
	                            if ($removePkField){
	                                unset($row[$rowx[$pk]][$pk]);
	                            }
	                        }
	                    } else {
	                        $row[] = $rowx;
	                    }
	                }
	            }
	            $que->closeCursor();
	        }
	        return $row;
	    }
    
		// get_count returns number of rows found on db
		// excepts two parameters $sql and $data
		// $sql parameter refers to sql query
		// $data is an array of elements which can be of format associative array or basic array.
		
		// RETURNS INTEGER VALUE
	    function get_count($sql="", $data=null){
	        $que = $this->conn->prepare($sql);
	        if($data){
	            $que->execute($data);
	        }else{
	        	$que->execute();
	        }
	        return $que->rowCount();
	    }
		
		// execute function execute's query.
		// excepts three parameters $sql, $data, $last_insert
		// $sql parameter refers to sql query
		// $data is an array of elements which can be of format associative array or basic array.
		// $last_insert is boolean type
		
		// RETURNS BOOLEAN IF $last_insert is set to false, nor if $last_insert is set to true it returns $last inserted row id.
	    function execute($sql="", $data=null, $last_insert=false){
	        if($sql){
	            $query = $this->conn->prepare($sql);
	            if($last_insert){
					if($query->execute($data))
						return $pdo->lastInsertId();
	            }
	            return $query->execute($data);
	        }
	        return false;
	    }

		// closeConnection function closes this PDO connection and should be called in the end of the file.
	    function closeConnection(){
	    	// $this->conn->query('KILL CONNECTION_ID()');
			$this->conn = null;
	    }
	}
?>