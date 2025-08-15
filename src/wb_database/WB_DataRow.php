<?php

namespace App\php\wb_database;

class WB_DataRow {



	public $row;
	public $alias;


	public function __construct($data=null) {
		$this->row = $data;
		$this->alias = array();
	}



    /*
    public function print( $col ){
        if( array_key_exists($row, $this->row) ){
            echo $this->row[$col];
        }else{
            throw new Exception('Cannot print column');
        }

    }*/



	public function setAlias( $col, $alias ): void{
		if( array_key_exists($col, $this->row) ){
			$this->alias[$col] = $alias;
		}
	}



	public function output( $col ): void{
		$out = $this->getCol( $col );
		echo $out;
	}

	public function printCol( $col ): void{
		$out = $this->getCol( $col );
		echo $out;
	}

/*
 * Dismiss
*/
    public function get( $col ){
        // chech for alias
        if( array_key_exists($col, $this->alias) ){
            $col = $this->alias[$col];
        }
        if( array_key_exists($col, $this->row) ){
            return $this->row[$col];
        }else{
            throw new \Exception('Datarow Cannot print column '.$col);
        }

    }

    public function getCol( $col ){
        // chech for alias
        if( array_key_exists($col, $this->alias) ){
 	   $col = $this->alias[$col];
        }
        if( array_key_exists($col, $this->row) ){
 	   return $this->row[$col];
        }else{
 	   throw new \Exception('Datarow Cannot print column '.$col);
        }
    }

    public function add( $col, $data): void{
		$this->row[$col] = $data;
    }


}//fine classe
?>
