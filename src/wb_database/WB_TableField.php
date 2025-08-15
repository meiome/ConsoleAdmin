<?php

namespace App\php\wb_database;
/****************************************************
 * Autore: Alessandro Carrer
 *
 * Classe:
 *
****************************************************/


class WB_TableField{


	public $name = null;
	public $type = 'VARCHAR';
	public $size = 0;
	public $unique = false;
	public $binary = false;
	public $unsigned = false;
	public $zero_fill = false;
	public $auto_increment = false;
	public $not_null = false;
	public $default_data = null;
    public $has_default = false;
	public $comment = null;
	public $primary_key = false;

	public $name_encripted = null;


	public function __construct($name, $type, $array_opt=null) {
		$this->name = $name;
		$this->type = $type;
        $this->setDefaultOptions();
		if( $array_opt != null || is_array($array_opt) ){
			$this->setFieldFromArray($array_opt);
		}
	}


    public function setDefaultOptions(): void{
        switch (strtoupper($this->type)) {
            case 'INT':

                break;

            case 'DOUBLE':

                break;

            case 'VARCHAR':
                $this->size = 255;
                break;

						case 'TIMESTAMP':
								break;

            case 'DATETIME':
                break;

            case 'DATE':
                break;

            case 'LONGTEXT':
                break;

            case 'BOOLEAN':
                $this->size = 1;
                break;

            case 'TINYINT':
                $this->size = 1;
                break;

            default:
                $this->size= 0;

        }

    }



    function get_type_sql(){
        $type = null;
        $res = 'ERROR';

        switch (strtoupper($this->type)) {
            case 'INT':
                $res =  'INT';
                break;

            case 'DOUBLE':
                $res =  'DOUBLE';
                break;

            case 'VARCHAR':
                $res =  'VARCHAR('.$this->size.')';
                break;

						case 'TIMESTAMP':
                $res =  'TIMESTAMP';
                break;

            case 'DATETIME':
                $res =  'DATETIME';
                break;
            case 'DATE':
                $res =  'DATE';
                break;
            case 'LONGTEXT':
                $res =  'LONGTEXT';
                break;
            case 'BOOLEAN':
                $res= 'TINYINT('.$this->size.')';
                break;
            case 'TINYINT':
                $res= 'TINYINT('.$this->size.')';
                break;
            default:
                $res =  'ERROR';
        }

        return $res;
    }



    function get_mysql_options(){
            $type = null;
            $str = 'ERROR';

            switch (strtoupper($this->type)) {
                case 'INT':
                    $str =  'INT';
                    if($this->unsigned == true){ $str .= ' UNSIGNED';}
                    if($this->not_null == true){ $str .= ' NOT NULL';}
                    if($this->auto_increment == true){ $str .= ' AUTO_INCREMENT';}
                    if($this->has_default){
                        $str .= ' DEFAULT';
                        $str .= ' '.$this->default_data;
                    }
                    break;

                case 'DOUBLE':
                    $str =  'DOUBLE';
                    break;
                case 'BOOLEAN':
                    $str =  'TINYINT';
                    break;
                case 'VARCHAR':
                    $str =  'VARCHAR('.$this->size.')';

                    if($this->not_null == true){ $str .= ' NOT NULL';}
                    if($this->has_default){
                        $str .= ' DEFAULT';
                        $str .= ' \''.$this->default_data.'\'';
                    }
                    break;

								case 'TIMESTAMP':
										$str =  'TIMESTAMP';
										break;

                case 'DATETIME':
                    $str =  'DATETIME';
                    break;
                case 'DATE':
                    $str =  'DATE';
                    break;
                case 'LONGTEXT':
                    $str =  'LONGTEXT';
                    break;
                default:
                    $str =  'ERROR';
            }

        return $str;

    }


	/*
	*	Carica le impostazioni da un array associativo
	*/
	public function setFieldFromArray( $field ): void{
        if(!is_array($field)){return;}
		if (array_key_exists('type', $field)){ $this->type = $field['type']; }
		if (array_key_exists('size', $field)){ $this->size = $field['size']; }
		if (array_key_exists('unique', $field)){ $this->unique = $field['unique']; }
		if (array_key_exists('auto_increment', $field)){ $this->auto_increment = $field['auto_increment']; }
		if (array_key_exists('not_null', $field)){ $this->not_null = $field['not_null']; }
		if (array_key_exists('default', $field)){ $this->has_default=true; $this->default_data = $field['default']; }
		if (array_key_exists('primary_key', $field)){ $this->primary_key = $field['primary_key']; }
	}

}// fine classe



?>
