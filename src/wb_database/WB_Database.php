<?php

namespace App\php\wb_database;

use \PDO as PDO;
use \PDOException as PDOException;
use App\php\wb_framework\WB_Stopwatch;
use App\php\wb_database\WB_TableField;
use App\php\wb_database\WB_Table;
use App\php\wb_database\WB_DataSet;
use App\php\wb_database\WB_DataRow;

define('wb_databaseDIR_BASE', dirname(dirname(dirname( __FILE__ ))).'/');

//require_once(wb_databaseDIR_BASE.'php/wb_database/WB_TableField.php');
//require_once(wb_databaseDIR_BASE.'php/wb_database/WB_Table.php');
//require_once(wb_databaseDIR_BASE.'php/wb_database/WB_DataSet.php');

//require_once(wb_databaseDIR_BASE.'php/wb_database/WB_DataRow.php');

class WB_Database{

	public $name = null;
	public $db_link = null;
	public $host = 'localhost';
	public $type = 'mysql';
	public $username = 'username';
	public $password = 'password';
	public $debug = false;
	// List of table object
	private $table = null;
	private $logger;
	private $logginto;//dice se il riferimento è ad un host o ad un server

	private $serverconfig;




	public function __construct($type, $host, $name, $username, $password, $logginto = 'host') {
		$this->type = $type;
		$this->host = $host;
		$this->name = $name;
		$this->username = $username;
		$this->password = $password;
		$this->table = array();
		$this->db_link = null;
		//$this->debug=false;
		$this->logger = $GLOBALS["WB_LOGGER"];
		$this->logginto = $logginto;
		$this->serverconfig = require(controllerDIR_BASE.'/config/wb/server/serverconfig.php');
		$this->debug=$this->serverconfig['debug'];
	}



	public function connect(): void{
		//$CONF_DB_STRING = $this->type.':host='.$this->host.';dbname='.$this->name;
		$CONF_DB_STRING = $this->type.':'.$this->logginto.'='.$this->host.';dbname='.$this->name;
		try{
			//echo 'connetto...';
			$this->logger->log('wb_database', 'Connessione al database', 3);
			$this->db_link = new PDO($CONF_DB_STRING, $this->username, $this->password);
			$this->db_link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch (PDOException $e) {
			//\var_dump($e);
			$this->logger->log('wb_database', 'Errore di connessione al database', 0);
			$this->logger->log('wb_database', $CONF_DB_STRING, 3);
			$this->logger->log('wb_database', $e->getMessage(), 3);
			$this->db_link = null;
            throw new \Exception('Errore di connessione al database');
		}
	}



	public function disconnect(): void{
		$this->db_link = null;
	}

	public function getDbLink(): PDO{
		if ($this->db_link!= null){
			return $this->db_link;
		} else {
			throw new \Exception('DBLINK non presente');
		}
	}



    /*-------------------------------------------------------------------------
    * Aggiunge una tabella a partire da un array associativo
    * che descrive la tabella
    * @param string $tbl_name nome della tabella da inserire
    * @param array $tbl_struct array associativo che descrive la tabella
    */
	function addTableFromArray($tbl_name, $tbl_struct=null): void{
		$this->table[$tbl_name] = new WB_Table($tbl_name);
        if($tbl_struct != null){
            $this->table[$tbl_name]->setDatabase($this);
            foreach($tbl_struct as $row){
                $this->table[$tbl_name]->add_field($row);
            }
        }

	}


    function addTable( $table ): void{
        $table->setDatabase($this);
        $this->table[$table->name] = $table;
    }


    /*
    * Aggiunge le tabelle a partire da un array associativo che
	* descrive il database
    * @param string $db_struct nome della tabella da inserire
    * @param array $tbl_struct array associativo che descrive la tabella
    */
	function setStructure( $db_struct ): void{
        $this->logger->log('WB_DATABASE','Leggo struttura database da file array', 3);

		foreach($db_struct as $tbl_name=>$tbl_struct){
			//key($tbl_struct);

			//echo 'inserisco tabella:'.$tbl_name.'<br>';
			$this->addTableFromArray($tbl_name, $tbl_struct['field']);

			if( array_key_exists('foreign_keys', $tbl_struct) ){
				foreach($tbl_struct['foreign_keys'] as $fk){
					if( !array_key_exists('on_update', $fk) ){ $fk['on_update'] = 'NO ACTION'; }
					if( !array_key_exists('on_delete', $fk) ){ $fk['on_delete'] = 'NO ACTION'; }

					//print_r($fk);
					$this->table[$tbl_name]->addForeignKey( $fk['name'], $fk['column'], $fk['referenced_table'], $fk['referenced_column'], $fk['on_update'], $fk['on_delete'] );
				}
			}
		}

	}




/*
 * Restituisce la classe della tabella specificata
 * @param string $tbl_name nome tabella
 * @return restituisce la classe tabella altrimenti restituisce null
 */
	function getTable($tbl_name){
        if (array_key_exists($tbl_name, $this->table)){
            return $this->table[$tbl_name];
        }
        else{

            throw new \Exception("WBDatabase::try to access non existent table", 1);

        }
	}
/*
!!!!!! Deprecate in eliminazione
*/
	function get_table($tbl_name){
		//$this->getTable($tbl_name);
        if (array_key_exists($tbl_name, $this->table)){
            return $this->table[$tbl_name];
        }
        else{
			return null;
        }
	}




    /*
    * Restituisce la classe della tabella specificata
    * @param void
    * @return array restituisce un array di stringe contenenti il nome delle tabelle
    */
	function get_table_list(){
        $tmp = array();
        foreach($this->table as $tbl){
            $tmp[] = $tbl->name;
        }
        return $tmp;

	}

	function getTables(){
        return $this->table;

	}





    function printHtml_table_list(): void{
        echo count($this->table);
        $keys = array_keys($this->table);
        //print_r($keys);
        echo "<table>";
        for ($i=0; $i<count($keys); $i++){

            echo "<tr><td>".$this->table[$keys[$i]]->name."</td></tr>";

        }
        echo "</table>";
    }


  /* -----------------------------------------------------------------------------------
	Esegue una query nella tabella
	@param query_string - string - Specifica la query da eseguire
	@param param - array - Array associativo chiave valore per il bind dei parametri
  ----------------------------------------------------------------------------------*/
public function query($query_string, $param_array=null, $type='SELECT'){
		$query_stmt = null;
		$this->logger->log('wb_database', 'Execute query', 3);
		//$this->logger->log('wb_database', $query_string, 3);
		$stopwatch = new WB_Stopwatch();
		$stopwatch->start('evvai');
		if($query_string != null){

		$query_stmt = $this->getDbLink()->prepare($query_string);
      
      if(is_null($param_array) == false and sizeof($param_array)>0){

          $this->bind($query_stmt, $param_array);
      }
			//esegue query

			$iterator = null;

			try{

				$query_stmt->execute();
				$iterator = new WB_DataSet();
				if($type == 'SELECT'){
					$query_stmt->setFetchMode(PDO::FETCH_ASSOC);
					$iterator = new WB_DataSet($query_stmt->fetchAll());
				}
				$query_stmt = null;
					$stopwatch->stop();
            	return $iterator;
			}catch(PDOException $e) {

				if($this->debug){
					$this->logger->log('wb_database', 'Query errror:'.$query_string, 2);
                    throw new \Exception('Errore di connessione al database');

					//echo 'Errore nella query:'.$query_string;
				}
				$query_stmt = null;
				$iterator = new WB_DataSet(array('Message'=>'Error'));
				$iterator->query_string=$query_string;
				$iterator->query_parameter = $param_array;
        		return $iterator;
    		}finally{
				return $iterator;
			}
		}
}

/*ANDREA: creato per avere lastInsertId*/
public function queryInsert($query_string, $param_array=null){
		//$this->connect();
		$this->logger->log('wb_database', 'Query insert' , 3);

		if( $query_string == null)
			{
				return null;
			}

		$Stmt = $this->getDbLink()->prepare($query_string);
		if(is_null($param_array) == false and sizeof($param_array)>0){
			$this->bind($Stmt, $param_array);
		}

		$ID = null;
		// Eseguo query di aggiornaento
		try{
			$Stmt->execute();
			$ID = $this->getDbLink()->lastInsertId();
		}
		catch (PDOException $e) {
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $ID;
		}

}


	public function startTransaction(): void{
		$this->getDbLink()->beginTransaction();
	}


	public function rollbackTransaction(): void{
		$this->getDbLink()->rollBack();
	}

	public function commitTransaction(): void{
		$this->getDbLink()->commit();

	}


/* Utilizzata solo dalle classi interne
 *
 *
*/
public function bind( $statment, $parametri ): void{
		if( $parametri == null ){
			$this->logger->log('wb_database', 'No parameter to bind for query', 2);

			throw new \Exception("WBDatabase: Nessun parametro per il bind della query", 1);
		}
        /*
		if($this->debug){
			echo '<br>';
		}*/
		//print_r($parametri);
		// Bind parametri
		foreach($parametri as $key => $value){
			$this->logger->log('wb_database', 'Binding parameter:'.$key.':'.$value , 3);
            /*
			if($this->debug){

				echo '---------- Bind parametro:';
				echo $key.': '.$value."<br>";
			}*/
			try{
				//todo controllo vaidita tipo di dato


			  $statment->bindValue(':'.$key, $value); // use bindParam to bind the variable
			}catch (PDOException $e) {
				$this->logger->log('wb_database', 'Error binding value on query:' , 1);
				$this->logger->log('wb_database', $e->getMessage() , 3);
			     throw new \Exception("WBDatabase: Nessun parametro per il bind della query", 1);

				//echo 'Errore di esecuzione';
				//echo $e->getMessage();
			}
		}

}



public function updateRecord( $table_name, $parametri ){
		$this->logger->log('wb_database', 'Execute update query', 3);
		$this->logger->log('wb_database', 'Updating table '.$table_name, 3);

		if($this->debug){
			echo 'Aggiorno record tabella:'.$table_name.'<br>';
			print_r($parametri);
		}
		$query_string = $this->getTable($table_name)->update($parametri);
		if($this->debug){
			echo $query_string;
		}
		if( $query_string == null){ return null;}

		$Stmt = $this->getDbLink()->prepare($query_string);
		//$this->bind($Stmt, array($this->getTable($table_name)->primary_key[0]=>$id));
		$this->bind($Stmt, $parametri);

		$updatedID = null;
		// Eseguo query di aggiornaento
		try{
			if($this->debug){ echo "Model: Eseguo query di aggiornamento <br>";}
			$Stmt->execute();
		}
		catch (PDOException $e) {
			echo '<br>Errore di esecuzione ';
			echo $e->getMessage();
		}
		finally{
			return $updatedID;
		}
}




public function selectRecord( $table_name, $id ){
		//$this->connect();
		//echo 'Aggiorno record';
		$query_string = $this->getTable($table_name)->selectRecord();

		$pk = $this->getTable($table_name)->primary_key[0];

		if( $query_string == null){ return null;}

		$Stmt = $this->getDbLink()->prepare($query_string);

		$this->bind($Stmt, array($pk=>$id));

		$iterator = null;

		// Eseguo query di aggiornaento
		try{
			if($this->debug){ echo "Model: Eseguo query di selezione record <br>";}
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$iterator = new WB_DataSet($Stmt->fetchAll());
			return $iterator;
		}
		catch (PDOException $e) {
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $iterator;
		}

}




public function deleteRecord( $table_name, $id ){
		//$this->connect();
		//echo 'Aggiorno record';
		$query_string = $this->getTable($table_name)->delete();

		$pk = $this->getTable($table_name)->primary_key[0];

		if( $query_string == null){ return null;}

		$Stmt = $this->getDbLink()->prepare($query_string);

		$this->bind($Stmt, array($pk=>$id));

		$iterator = null;

		// Eseguo query di eliminazione
		try{
			if($this->debug){ echo "Model: Eseguo query di eliminazione record <br>";}
			$iterator = $Stmt->execute();

			//$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			//$iterator = new QueryIterator($Stmt->fetchAll());
			return $iterator;
		}
		catch (PDOException $e) {
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $iterator;
		}

}


public function getDefaultValues( $table_name ){
	return $this->getTable( $table_name )->getDefaultValues();

}


public function insertRecord( $table_name, $parametri ){
		//$this->connect();
		$this->logger->log('wb_database', 'Query insert' , 3);

		$query_string = $this->getTable($table_name)->insert($parametri);
		$this->logger->log('wb_database', $query_string , 3);

		if( $query_string == null){ return null;}

		$Stmt = $this->getDbLink()->prepare($query_string);
		$this->bind($Stmt, $parametri);

		$ID = null;
		// Eseguo query di aggiornaento
		try{
			if($this->debug){ echo "Model: Eseguo query di aggiornamento <br>";}
			$Stmt->execute();
			$ID = $this->getDbLink()->lastInsertId();
		}
		catch (PDOException $e) {
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $ID;
		}

}









public function resolve_dependencies(){
	$create_list=array();
	foreach($this->table as $tbl){

		$this->logger->log('Resolve dependecie for table '.$tbl, 3);
		// tabella ha dipendenza
		if( count($tbl->foreign_key) > 0 ){
			foreach( $tbl->foreign_key as $fk ){

				// dipendenza non in elenco
				if( !in_array( $fk['referenced_table'], $create_list )){
					$this->logger->log('Dependecie found  '.$fk['referenced_table'], 3);

					//echo 'Dipendenza trovata, inserisco dipendenza '.$fk['referenced_table'].'<br>';
					if( array_key_exists($fk['referenced_table'], $this->table) ){
						$this->logger->log('Realted table exist', 3);

						//echo 'Tabella Dipendenza esiste'.'<br>';
						$create_list[]=$fk['referenced_table'];
					}else{
						$this->logger->log('Related table not exist', 3);

						//echo 'Tabella Dipendenza non esiste'.'<br>';
						throw new \Exception('Impossibile risolvere dipendenze di '
							.$tbl->name.' referenced '
							.$fk['referenced_table'].' non esiste'
						);
					}

				}

			}

			if( !in_array($tbl->name, $create_list) ){
                $this->logger->log('WB_DATABASE','fine dipendeze inserisco '.$tbl->name, 3);
				$create_list[]=$tbl->name;
			}
		}else{
			if( !in_array($tbl->name, $create_list) ){
                $this->logger->log('WB_DATABASE','Nessuna dipendeza trovata inserisco '.$tbl->name, 3);
				$create_list[]=$tbl->name;
			}
		}

	}
	return $create_list;
}


public function create_database_structure(): void{
    $this->logger->log('WB_DATABASE','Genero struttura database', 3);

	$tbl_list = $this->generate_create_list();
        $this->logger->log('WB_DATABASE','Genero', 3);

	foreach($tbl_list as $tbl){
		$this->create_table($tbl);
	}
}


public function delete_database_structure(): void{
	$tmp = $this->generate_create_list();
	$tbl_list = array_reverse ( $tmp );
	foreach($tbl_list as $tbl){
		$this->delete_table($tbl);
	}
}


private function generate_create_list(){
	$this->logger->log('WB_DATABASE','Genero lista di creazione tabelle', 3);
    //print_r($this->table);
	$create_list=array();
	foreach($this->table as $tbl){
		$this->logger->log('WB_DATABASE','Elaboro tabella:'.$tbl->name, 3);

		$tmp_list = $this->resolve_dependencies_tree($tbl->name);
		foreach ($tmp_list as $tmp) {
			if( !in_array($tmp, $create_list) ){ $create_list[] = $tmp; }
		}

	}
	return $create_list;
}

private function resolve_dependencies_tree( $table_name ){
	$this->logger->log('WB_DATABASE','Resolve tree dependecie for table '.$table_name, 3);

	$dep_list = array();

	if( !array_key_exists($table_name, $this->table) ){
		throw new \Exception( 'Cannot solve dependencies of '.$table_name );
	}
	$tbl = $this->table[$table_name];
	if( count($tbl->foreign_key) < 1 ){
		$dep_list[]=$table_name;
		return $dep_list;
	}



	foreach ($tbl->foreign_key as $fk) {
        if($fk['referenced_table'] == $table_name){
            $tmp_list[] = $table_name;
        }else{
		  $tmp_list = $this->resolve_dependencies_tree( $fk['referenced_table'] );
        }
		foreach ($tmp_list as $tmp) {
			if( !in_array($tmp, $dep_list) ){ $dep_list[] = $tmp; }
		}
	}


	if( !in_array($table_name, $dep_list) ){ $dep_list[] = $table_name; }
	return $dep_list;
}




public function create_table( $tbl_name ): void{

	$sql_code = $this->getTable($tbl_name)->create();
	//echo $sql_code;
	if($this->db_link == null){
		$this->logger->log('wb_database', 'Connessione al database non effetuata', 0);
		return;
	}
	$this->logger->log('wb_database', ''.$sql_code, 3);

	$this->db_link->exec($sql_code);

}

public function delete_table( $tbl_name ): void{
	$sql_code = $this->getTable($tbl_name)->drop();
	$this->getDbLink()->exec($sql_code);
}


    public function loadStructure( $create_function ): void{
        //$create_function();
        //call_user_func( $create_function );
        ///include $_SERVER['DOCUMENT_ROOT'].$create_function;

				include dirname(wb_databaseDIR_BASE).$create_function;

    }



}/* Fine Classe */

?>
