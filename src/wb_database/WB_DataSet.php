<?php
namespace App\php\wb_database;

use \Iterator as Iterator;

class WB_DataSet implements Iterator{

	public $query_result = null;
	public $query_result_count = 0;
	public $query_index = 0;

    public $extraCol = null;

    public $bind_col;

	private $debug = false;

	public $query_string;
	public $query_parameter;


	public $related_dataset;


	public function __construct($data=null) {
		if($data == null){
			if($this->debug){echo 'Init empty dataset';}

			$this->query_result = array();
			$this->query_result_count = 0;
			//if($this->debug){echo "Record trovati: ".$this->query_result_count;}
			$this->query_index = 0;
		}else{
			//print_r($data);

			$this->query_result = $data;
			//print_r($data);
			$this->query_result_count = count($data);
			if($this->debug){echo "Record trovati: ".$this->query_result_count;}
			$this->query_index = 0;
		}
		$this->bind_col = array();
	}


public function toJson(){
	/*$json = "[";
	if( $this->query_result_count > 0 ){
		foreach( $this->query_result as $row){
			$json .= "{";
			foreach( $row as $key=>$val){
				$json .= '"'.$key.'":"'.$val.'",';
			}
			$json .= "},";
		}
	}
	$json .= "]";
	return $json;
	*/
	$json = "";
	if( $this->query_result_count > 0 ){
		$json =  json_encode($this->query_result);
	}
	return $json;
}

	public function toArray(){
		return $this->query_result;
	}

    /* Aggiunge un alias alla colonna
     * @param col - string - nome della colonna
     * @param label - string - etichetta da assegnare alla colonna
    */
	public function bind( $col, $label ): void{
		if (array_key_exists($col, $this->query_result[0])) {
			$this->bind_col[$label] = $col;
		}
	}



    /* -----------------------------------------------------------------------------------
	* Restituisce la colonna specificata, se non esiste il campo specificato
	* stampa un carattere vuoto.
	* @param field - string - Specifica il campo da stampare
    * ----------------------------------------------------------------------------------*/
	public function get_col($field){
		return $this->getCol($field);
	}


	public function getCol($field){
		if (array_key_exists($field, $this->query_result[$this->query_index])) {
		    return $this->query_result[$this->query_index][$field];
		}else{
		    return null;
		}

	}

	public function addCol( $name, $data): void{
        $this->query_result[$this->query_index][$name] = $data;
    }

    public function addRow( $row ): void{
		$this->query_result[]=$row;
		$this->query_result_count++;
    }

	public function prependRow( $row ): void{
		array_unshift($this->query_result, $row);
		$this->query_result_count++;

	}


    public function setCol( $name, $data): void{
        $this->query_result[$this->query_index][$name] = $data;
    }



public function count(){
	return $this->query_result_count;
}

public function size(){
	return $this->query_result_count;
}

/* -----------------------------------------------------------------------------------
* Stampa il campo specificato, se non esiste il campo specificato
* stampa un carattere vuoto.
* @param field - string - Specifica il nome del campo da stampare
  ----------------------------------------------------------------------------------*/
	/*Deprecato*/
public function print_col($field): void{
	$this->printCol($field);
}


public function printCol($field): void{
	if($this->valid()){
			if (array_key_exists($field, $this->query_result[$this->query_index])) {
				echo $this->query_result[$this->query_index][$field];
			}else{
				echo '';
				throw new \Exception('Dataset Column '.$field.' does not exist');
			}
	}else{
		echo '';
		throw new \Exception('Dataset Row '.$field.' does not exist');
	}
}



/* -----------------------------------------------------------------------------------

	Controlla se esiste il prossimo record
  ----------------------------------------------------------------------------------*/
function hasNext(): bool{
	if($this->query_index < $this->query_result_count){return true;}
	else{return false;}
}





/* -----------------------------------------------------------------------------------
* Interfaccia Iterator
* Carica il prossimo record se esiste
*  ----------------------------------------------------------------------------------*/
function next(): void{
	if($this->query_index < $this->query_result_count){
		++$this->query_index;
	}
}





/* -----------------------------------------------------------------------------------
* Interfaccia Iterator
* Carica il prossimo record se esiste
  ----------------------------------------------------------------------------------*/
function current() :mixed{
    $r = $this->query_result[$this->query_index];

    $row = new WB_DataRow( $this->query_result[$this->query_index] );
	return	$row ;
}





/* -----------------------------------------------------------------------------------
 * Iterator Interface
	Carica il prossimo record se esiste
  ----------------------------------------------------------------------------------*/
function valid(): bool{
	if( ($this->query_index < $this->query_result_count) & ($this->query_result != null) ){
		return true;
	}else{
		return false;
	}

}





/* -----------------------------------------------------------------------------------
 * Iterator Interface
	Torna al primo record
-------------------------------------------------------------------------------------*/
function rewind(): void{
	$this->query_index=0;
	//$this->query_result = $this->query_stmt->fetch(PDO::FETCH_ASSOC);
}




/* -----------------------------------------------------------------------------------
 * Iterator Interface
 *
-------------------------------------------------------------------------------------*/
function key() :int{
	return $this->query_index;
}




}//fine classe
?>
