<?php

namespace App\php\wb_database;

/**
 * La classe rappresenta la struttura dati di una tabella
 * la classe puo generare il codice sql di
 *
 * calyw: sono caduta adosso a me stessa
 *
 *
 * TODO:
 *
 * -implementare update e delete con arrya in paramaetro
 *
 *
 */

class WB_Table {

	private $database;

	// Table name
	public $name = null;

	// Field name
	public $field = null;

	public $field_encripted;

	// Table primary key
	public $primary_key = array();

	// Foreign key
	public $foreign_key = null;
	//array('column'=>$fk_col, 'reference_table'=>$fk_ref_table, 'reference_column'=>$fk_ref_col);
	// Table charset
	public $charset='utf8';

	public $indexes = array();
	// Table engine
	public $engine='InnoDB';

	public $debug = true;		// Abilita il debug
	public $debug_level = 0;	// Livello di debug 4:Critical 2:Error 2:Warning 1:Info 0:debug off

    public $relation;

	private $new_row;

	private $autocommit = true;


	private $logger;
	/* query var */
	public $limit;
	public $order;


    public $dataset;
    public $commit_dataset;
	public $commit_tables;

	public function __construct($name, $tbl_struct=null) {
		$field = array();
		$field_encripted = array();
		$this->name = $name;
		$this->foreign_key = array();
		$this->primary_key = array();
		$this->logger = $GLOBALS["WB_LOGGER"];
		$this->commit_dataset = new WB_DataSet();
	}

	/*aggiunte andrea per compatibilità php 8.2*/

	/*stop aggiunte andrea per compatibilità php 8.2*/

	public function add_field( $field_array ): void{
		$field = new WB_TableField($field_array['name'], $field_array['type'], $field_array);
		//$field->setFieldFromArray($field_array);
		$this->field[$field->name] = $field;
		if( $field->primary_key){ $this->primary_key[] = $field->name; }
		//$this->field[$field['name']]->name_encripted = $this->encriptString($field['name']);

		//$this->field_encripted[$this->encriptString($field['name'])] = $field['name'];
		//$this->field[$field['name']] = $field;
	}

	public function addTableField( $field ): void{
		$this->field[$field->name]= $field;
	}


    public function addField( $field_name, $field_type, $opt_parameter=null ): void{
		$field = new WB_TableField($field_name, $field_type, $opt_parameter);
        $this->field[$field_name]= $field;
		if($field->primary_key == true){
			$this->primary_key[] = $field->name;
		}
	}

	/*
	* Restitisce un campo
	*/
	public function getField($field_name){
		if (array_key_exists($field_name, $this->field)){
			return $this->field[$field_name];
		} else {
			throw new \Exception("WB_Database::campo (".$field_name.") non mappato", 1);
			//return null;
		}

	}

	/* Deprecato */
	public function get_field($field_name): void{
		$this->getField($field_name);
	}



	public function encriptString($string){
		return sha1($string);

	}

	/*public function getFieldName($field_name){
		if( $WB_DATABASE_FIELD_ENCRIPTION ){
			return $this->encriptString($field_name);
		}else{
			return $field_name;
		}
	}*/


	public function printFieldName($field_name): void{
		if( $GLOBALS['WB_DATABASE_FIELD_ENCRIPTION'] ){
			echo $this->encriptString($field_name);
		}else{
			echo $field_name;
		}
	}

	/* --------------------------------------------------------------------
	 * Function: fieldExist()
	 * Check if a field exist
	 * @param $fieldName
	 * @return if
	 * ------------------------------------------------------------------*/
	public function fieldExist($field_name){
		if (array_key_exists($field_name, $this->field)){
			return true;
		}else{
			return false;
		}

	}




	/* --------------------------------------------------------------------
	 * Function: getFields()
	 * Check if a field exist
	 * @return array of all field object
	 * ------------------------------------------------------------------*/
	public function getFields(){
		return $this->field;
	}

	public function getEncriptedFields(){
		return $this->field_encripted;
	}

	public function getFieldList(){
		$list = array();
		foreach($this->field as $campo){
			$list[] = $campo->name;
		}
		return $list;
	}


	public function getEncriptedFieldList(){
		$list = array();
		foreach($this->field as $campo){
			$list[] = $campo->name_encripted;
		}
		return $list;
	}

	public function decriptFields($prm){
		$parametri = array();


		foreach($prm as $key => $value){
			if( array_key_exists( $key, $this->field_encripted) ){
				echo 'trovato';
				$parametri[ $this->field_encripted[$key] ] = $value;
			}else{
				echo 'non trovato';

			}
		}

		return $parametri;

	}

	/* --------------------------------------------------------------------
	 * Function: getFields()
	 * Check if a field exist
	 * @return array of all field object
	 * ------------------------------------------------------------------*/
	public function add_foreign_key( $fk_name, $fk_col, $fk_ref_table, $fk_ref_col): void{
		$this->foreign_key[$fk_name] = array('column'=>$fk_col, 'referenced_table'=>$fk_ref_table, 'referenced_column'=>$fk_ref_col);
	}

	public function addForeignKey( $fk_name, $fk_col, $fk_ref_table, $fk_ref_col, $fk_on_update='NO ACTION', $fk_on_delete='NO ACTION'): void{
		$this->foreign_key[$fk_name] = array('column'=>$fk_col, 'referenced_table'=>$fk_ref_table, 'referenced_column'=>$fk_ref_col, 'on_update'=>$fk_on_update, 'on_delete'=>$fk_on_delete);
	}


	public function getForeignKeyByTable($tbl_name){
		$found = null;
		foreach($this->foreign_key as $fk){
			if( $fk['referenced_table'] == $tbl_name ){
				$found = $fk;
			}
		}
		return $found;
	}

	public function getForeignKeyByField($field){
		$found = null;
	//	$this->logger->log('WB_Maschera', 'getForeignKeyByField:'.$field.'->'.$this->name, 3);
		foreach($this->foreign_key as $fk){
		//	$this->logger->log('WB_Maschera', 'getForeignKeyByField:CICLO'.$field.'->'.$this->name, 3);
			if( $fk['column'] == $field ){
		//		$this->logger->log('WB_Maschera', 'getForeignKeyByField:TROVATO'.$field.'->'.$this->name, 3);
				$found = $fk;
			}
		}
		return $found;
	}



	/*
    * Restituisce un dataset utilizzando l'id della tabella
    */
	public function find($id){
		$query_string="SELECT * FROM ".$this->name." ";
		$pk_parameter_name = $this->name.'_'.$this->primary_key[0];
		$query_string.="WHERE ".$this->primary_key[0]."=:".$pk_parameter_name;
		//$dataset = $this->database->query($query_string, array('id'=>$id));
		return $this->database->query($query_string, array($pk_parameter_name=>$id));
	}


    /*
    * Restituisce un dataset di articoli
    * accetta in ingresso un array di filtri
    * array('campo1'=>'valore', 'campo2'=>'valore');
    */
    public function findBy($parameters){
        $query_string="SELECT * FROM ".$this->name." ";
		$query_string.="WHERE ";

        if(sizeof($parameters)>0){
            end($parameters);
            $last = key($parameters);

            //print_r($last);

            //array_key_last ( $parameters );
            foreach($parameters as $key=>$value){

                $query_string.=$key.'=:'.$key;
                if( $key != $last){
                    $query_string.=' AND ';
                }

            }
        }

		return $this->database->query($query_string, $parameters);
    }


    /*
    * Crea un join di tabelle
    */
    public function joinTable( $table_name, $table_field ){


    }


    public function findRelated( $id, $table_name ){
        $tbl = $this->database->getTable( $table_name );
		$fk = $tbl->getForeignKeyByTable($this->name);
		$field = null;

		if($fk != null){
			$field = $fk['column'];

		}

		return $tbl->findBy(array($field=>$id));
    }


	/*
	* Aggiunge un record di dati
	* @param array( 'field1'=>'data1', 'field2'=>'data2')
	*/
	public function add( $data_array ){
		$query_string = $this->insert($data_array);
      //  echo $query_string;
        if(!$this->database){
            $this->logger->log("WB_TABLE", "Blah", 3);

        }
        if(!$this->database->dgetDbLink()){
            $this->logger->log("WB_TABLE", "Connecting to db", 3);
            $this->database->connect();
            //throw new Exception("WB_Database::Connessione non effetuata", 1);

        }
		//$this->db_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$this->logger->log("QUERY", "INSERT INTO natore.tbl_server(id, urlname) VALUES(1,1);", 3);
		$stmt = $this->database->getDbLink()->prepare("INSERT INTO natore.tbl_account(urlname) VALUES(1);");

		$stmt = $this->database->getDbLink()->prepare($query_string);
		$this->database->bind($stmt, $data_array);

			$this->logger->log("AAAAA", "aaa", 3);
		$ID = null;
		// Eseguo query di aggiornaento

		try{
			$stmt->execute();
			$ID = $this->database->getDbLink()->lastInsertId();
		}
		catch (\PDOException $e) {
			$this->logger->log("Errore", $e->getMessage(), 3);
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $ID;
		}

	}

		/*
	* Aggiunge un record di dati
	* @param array( 'field1'=>'data1', 'field2'=>'data2')
	*/
	public function update( $data_array ){
		$query_string = $this->updateQuery($data_array);
        echo $query_string;
        if(!$this->database){
            $this->logger->log("WB_TABLE", "Blah", 3);

        }
        if(!$this->database->getDbLink()){
            $this->logger->log("WB_TABLE", "Connecting to db", 3);
            $this->database->connect();
            //throw new Exception("WB_Database::Connessione non effetuata", 1);

        }
		$stmt = $this->database->getDbLink()->prepare($query_string);
		$this->database->bind($stmt, $data_array);
		$ID = null;
		// Eseguo query di aggiornaento
		try{
			$stmt->execute();
			$ID = $this->database->getDbLink()->lastInsertId();
		}
		catch (\PDOException $e) {
			echo 'Errore di esecuzione';
			echo $e->getMessage();
		}
		finally{
			return $ID;
		}

	}

	/*
	* Aggiunge un record di dati
	* @param array( 'field1'=>'data1', 'field2'=>'data2')
	* return temporary index
	*/
	public function addPersistent( $data_array ){
		echo '<br>';
		//print_r($data_array);
		$dataset_index = $this->commit_dataset->count();
		print_r($dataset_index);
		$data_array[$this->primary_key[0]] = $dataset_index;
		echo '<br> ReprintArray'.$this->primary_key[0].'<br>';
		//print_r($data_array);

		$this->commit_dataset->addRow($data_array);
		return $dataset_index;

		echo '<br>';
		//print_r($this->commit_dataset->query_result);
		/*
		$query_string = $this->insert($data_array);
		$stmt = $this->database->db_link->prepare($query_string);
		$this->database->bind($stmt, $data_array);
		$ID = null;*/


	}


	/*public function addPersistentRelated( $table, $data_array ): void{

		if(!in_array($this->commit_table, $table) ){
			$this->commit_table[] = $table;
		}
		$this->commit_table[] = $table;
		$dataset_index = $this->commit_dataset->count();
		if($dataset_index > 0){ $dataset_index--;}
		$fk_tbl = $this->database->getTable($table);
		$fk = $fk_tbl->getForeignKeyByTable($table);
		$fk_field = $fk['column'];
		$data_array[$fk_field] = $dataset_index;
		$fk_tbl->addPersistent($data_array);
	}*/


	public function commitWithChange( $data_array ): void{
		foreach($this->commit_dataset as $row){
			foreach($data_array as $change){
				$row[key($change)] = $change[key($change)];
				$this->add($row);
			}

		}
	}

	public function commit(): void{
		foreach($this->commit_dataset as $row){
			$this->add($row);
		}
	}





	/* --------------------------------------------------------------------
	 * Crea una query di aggiornamento, controlla che i parametri passati
	 * corrispondano ai campi
	 * Aggiorna un record leggendo i parametri inviati tramite GET o POST
	 * @param metodo POST o GET
	-------------------------------------------------------------------- */
	public function updateQuery($prm){
		$parametri = $prm;

		$pk = null;
		$QStr = "UPDATE ".$this->name.' SET ';

		$tmpI=0;
		foreach($parametri as $key=>$value){

			if($key == $this->primary_key[0]){
				$pk = $value;
				$updatedID = $value;
			}else{
				if($tmpI>0){$QStr .=', ';}
				$QStr .= $key.'=:'.$key;
				$tmpI++;
			}

		}

		$QStr .= ' WHERE '.$this->primary_key[0]."=:".$this->primary_key[0];
		if($this->debug){echo $QStr;}
		return $QStr;

	}





/* -----------------------------------------------------
* Inserisce un record leggendo i parametri inviati tramite GET o POST oppure
* passando un array
* @param metodo POST o GET
* @param $prm array di parametri
--------------------------------------------------------- */
public function insert($prm){
    $this->logger->log('WB_TABLE', 'Preparo insert query sql', 3);
	if( ($prm == null) | (!is_array($prm)) ){
		throw new \Exception("WBTable:: errore lettura parametri", 1);
	}

	$parametri  = $prm;



	//$parametri = $this->read_POST();
	$pk = null;
	$QStr = "INSERT INTO ".$this->name.' ( ';

	$tmpI=0;
	foreach($parametri as $key => $value){

		if($key == $this->primary_key[0]){
			$pk = $value;
		}else{
			if($tmpI>0){$QStr .=', ';}
			$QStr .= $key;
			$tmpI++;
		}
	}

	$QStr .= ') VALUES ( ';

	$tmpI=0;
	foreach($parametri as $key => $value){
		if($key == $this->primary_key){
		}else{
			if($tmpI>0){$QStr .=', ';}
			$QStr .= ':'.$key;
			$tmpI++;
		}
	}
	$QStr .= ' ) ';
    $this->logger->log('WB_TABLE', 'query elaborata:'.$QStr, 3);

	//echo $QStr;
	return $QStr;
}





/* -----------------------------------------------------
* Rimuove un record leggendo l'id tramite post o get
* @param metodo POST o GET
-------------------------------------------------------- */
public function delete(){
	$pk = $this->primary_key[0];
	$QStr = "DELETE FROM ".$this->name.' WHERE ';
	$QStr .= $pk.'=:'.$pk;
	return $QStr;
}



public function drop(){
	$sql = "DROP TABLE IF EXISTS ".$this->name;
	return $sql;
}




/* -----------------------------------------------------
 * Crea tabella
 * @param metodo POST o GET
 *
-------------------------------------------------------- */
public function create(){

	//echo 'inizio';
	$format = false;
	$pk = null;
	//print_r($this->field);
	try {

		$i = 0;
		// use exec() because no results are returned
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->name." (";
		foreach($this->field as $field){

			if($i > 0){$sql .= ', ';}

			if($format){$sql .= '<br>';}
			$sql .= $field->name." ";
			//$sql .= $field->get_type_sql();
            //print_r($field);
			$sql .= $field->get_mysql_options();
			$i++;
		}

		//if($this->primary_key != null){
			if($i > 0){$sql .= ',';}
			$sql .= " PRIMARY KEY (";
			//$this->logger->log('WB_table', ''.$this->primary_key, 3);
			foreach($this->primary_key as $pk){
				$sql .= $pk;
			}

			$sql.= ")";
		//}



		// Genera indici
		foreach($this->indexes as $idx){

		}

		// Genera indici automatici foreign key
		foreach($this->foreign_key as $fk){
			$sql .= ' ,';
			$sql .= ' INDEX '.'fk_'.$fk['column'].'_idx ( '.$fk['column'].' ASC)';

		}

		//echo 'ci sono';
		// Foreign key
		foreach($this->foreign_key as $fk_name => $fk){
			/*echo 'Stampo contenuto:';
			print_r($fk);*/
			$sql .= ' ,';
			$sql .= ' CONSTRAINT '.$fk['name'];
			$sql .= ' FOREIGN KEY ('.$fk['column'].') ';
			$sql .= ' REFERENCES '.$fk['referenced_table'].'('.$fk['referenced_column'].')';
			$sql .= ' ON UPDATE '.$fk['on_update'];
			$sql .= ' ON DELETE '.$fk['on_delete'];
		}

		$sql .= " ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->logger->log('WB_TABLE', 'query generata:'.$sql, 3);

		return $sql;
		//echo $sql.'<br>';
		//$this->db_link->exec($sql);
		//echo "Tabella ".$this->tbl_struct->name." creata con successo<br>";
	}catch(\PDOException $e){
		echo $sql . "<br>" . $e->getMessage().'<br>';
	}catch(\Exception $e){
		echo "Error while generating sql code";
	}
}


/* -----------------------------------------------------
 * Restituisce i valori di default
 *
 *
-------------------------------------------------------- */
public function getDefaultValues(){
	$res = array();
	$t = array();
	foreach($this->field as $field ){
		$res[$field->name] = $field->default;
	}
	$t[] = $res;
	//print_r( $res );
	return $t;
}





/* -----------------------------------------------------
 * Crea tabella
 * @param metodo POST o GET
-------------------------------------------------------- */
public function selectRecord(  ){

	$format = false;
	$pk = null;
	$i = 0;
	// use exec() because no results are returned
	$sql = "SELECT  * FROM ".$this->name." ";
	$sql .= 'WHERE '.$this->primary_key[0].'=:'.$this->primary_key[0];
	return $sql;

}


/* Legge tutta la tabella

*/
/*public function getData($limit = 0){
    $query_string = "SELECT * FROM ".$this->name;
        if($limit > 0){
            $query_str .= " LIMIT ".$limit;
        }
    return $this->database->query($query_string);
}*/


/*public function size(): void{
	$QStr = 'SELECT COUNT(*) as size FROM '.$this->tbl_struct->name;
	$Stmt->execute();

}*/

	public function setDatabase(&$db): void{
		$this->database = $db;
	}

	public function getDatabase(){
		return $this->database;
	}



  public function setRelation($table_field, $foreign_table, $foreign_field, $on_update='cascade', $on_delete='cascade'): void{
		//$fk_name = 'fk_'.$this->name.'_'.$foreign_table;//modificato ANDREA perchè non permetteva più relazioni alla stessa tabella
		$fk_name = 'fk_'.$this->name.'_'.$foreign_table.'_'.$table_field;
		$this->foreign_key[$fk_name] = array('name'=>$fk_name, 'column'=>$table_field, 'referenced_table'=>$foreign_table, 'referenced_column'=>$foreign_field, 'on_update'=>$on_update, 'on_delete'=>$on_delete);
	}


}//Fine classe
?>
