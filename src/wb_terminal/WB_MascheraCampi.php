<?php

namespace App\php\wb_terminal;
/*
 * Authore: Andrea Bettiol
 *
 *
 * Class: WB_MascheraCampi
 *
 * Version: 0.1
*/

class WB_MascheraCampi {

	public $dbdatatype = null;//int - booleam
	public $isnullable = null;
	public $requestbymodel = false;//il campo è stato richiesto dal model della richiesta??// elaborata nel prepare getdata
	private $database;
	private $maschera;
	public $type;// 'campo' , 'singlerelation', 'shadowsinglerelation' , 'maschera' , 'manipolazione'(serve per gestire le aggiunte eliminazioni di elementi)
	public $campolist=false;//il campo fa parte dei campi che sono visualizzati nella getsione maschera (lista con filtri)
	public $campofile=false;
	public $camponame;//nome univoco campo
	public $camponamedb;//nome database campo OR nel caso di campo maschera è l'id di riferimento della main relation
	private $etichetta;//Titolo di presentazione
	public $submaschera;//istanza della WB_Maschera
	public $singlerelnamefieldrelated;
	private $logger;
	//private $data = array();
	public $arrayoption;
	public $singlerelationoptionarray = array();
	public $arrayprefixforlist = array();//esempio per lo stesso campo possono essere usati sia la parola di ricerca "n" che "name" per questo è stato usato un array -- la prima char che viene passata poi per la ricerca è "-" o "+" che sono usati rispettivamente per and e or
	public $operatorforlist = null;
	public $stringaprecedentevalueforlist;
	public $stringasuccessivavalueforlist;


	public function __construct($maschera,$database,$type,$camponame,$camponamedb,$etichetta, $submaschera, $singlerelnamefieldrelated, $arrayoption) {
		$this->maschera = $maschera;
		$this->database = $database;
		if ($type == 'campolist'){
			$this->type = 'campo';
			$this->campolist = true;
		} elseif($type == 'campofile'){
			$this->type = 'campo';
			$this->campofile = true;
		} else {
			$this->type = $type;
		}
		$this->camponame = $camponame;
		$this->camponamedb = $camponamedb;
		$this->etichetta = $etichetta;
		$this->submaschera = $submaschera;
		$this->singlerelnamefieldrelated = $singlerelnamefieldrelated; // per le singl
		$this->arrayoption = $arrayoption;
		$this->logger = $GLOBALS["WB_LOGGER"];
	}

/*SONO ARRIVATO QUI,e penso di aggiungiere tutti i dati con id riferimento in modo da non gestire anche le sottomaschere con più righe per ogni maschera*/

	public function add($contatoreriga,$value, $id, $idref,$proto=false): void{
		//qui aggiungo i dati per riga e manca alcune cose da ggiungere
		if ($proto){
			//proto sta a significare la funzione che uso per aggiunger ad un array prototype una riga standard con valori vuoti o default che mi serve per copiare e aggiungere altri elementi all'array
			$this->maschera->addelementtoprototypedataarray($contatoreriga,$this->camponame, array(
				'contatoreriga' => $contatoreriga,
				'value' => $value,
				'id' => $id,
				'idref' => $idref,
			));
		} else {
			$this->maschera->addelementtofinaldataarray($contatoreriga,$this->camponame, array(
				'contatoreriga' => $contatoreriga,
				'value' => $value,
				'id' => $id,
				'idref' => $idref,
			));
		}
	}

	public function LoadDbDataTypeCampiMaschera(): void{
		//reperisco le informazione dal tipo di dato che dal file configurazione della struttura del database
		$this->logger->log('WB_MascheraCampi', 'LoadDbDataTypeCampiMaschera->MASCHERA:'.$this->maschera->mascheraname.' camponame->'.$this->camponame.' campotype->'.$this->type, 3);
		$this->requestbymodel = true;
		if ($this->type == 'campo'){
			$this->dbdatatype = $this->maschera->GetTable()->getField($this->camponamedb)->get_type_sql();
			$this->isnullable = ! $this->maschera->GetTable()->getField($this->camponamedb)->not_null;
		} else {
				if ($this->type == 'singlerelation' or $this->type == 'shadowsinglerelation'){
					$this->dbdatatype = 'INT';
						//non serve fare niente perchè è tipo int
					$this->isnullable = ! $this->maschera->GetTable()->getField($this->camponamedb)->not_null;
				}
		}
	}

	/*public function PrepareGetData($modelcampo){
		//reperisco le informazione dal tipo di dato che dal file configurazione della struttura del database
		$this->logger->log('WB_MascheraCampi', 'PrepareGetData->MASCHERA:'.$this->maschera->mascheraname.' camponame->'.$this->camponame.' campotype->'.$this->type, 3);
		//$this->logger->log('WB_MascheraCampi', 'PrepareGetData->MASCHERA:', 3);
		$this->requestbymodel = true;
		if ($this->type == 'campo'){
			$this->dbdatatype = $this->maschera->GetTable()->getField($this->camponamedb)->get_type_sql();
			$this->isnullable = ! $this->maschera->GetTable()->getField($this->camponamedb)->not_null;
		} else {
			if ($this->type == 'maschera'){
				//qui devo aggiungere il prepare per l'altra maschera
				$this->submaschera->PrepareGetData($modelcampo, true);
			} else {
				if ($this->type == 'singlerelation' or $this->type == 'shadowsinglerelation'){
					$this->dbdatatype = 'INT';
					//non serve fare niente perchè è tipo int
					$this->isnullable = ! $this->maschera->GetTable()->getField($this->camponamedb)->not_null;
				}
			}
		}
	}*/

	public function GetSingleRelationDbInfo(){
		$this->logger->log('WB_MascheraCampi', 'GetSingleRelationDbInfo():'.$this->maschera->mascheraname.' camponame->'.$this->camponame.' campotype->'.$this->type, 3);
		//recupero i dati della struttura (esempio nome tabella collegata) dalla classe WB_Table
		return $this->maschera->GetTable()->getForeignKeyByField($this->camponamedb);
	}

	public function addSingleRelationOption($contatore,$id, $label): void{
		array_push($this->singlerelationoptionarray,array('id'=>$id,'label'=>$label));
	}

	public function GetData(){
		/*COME IL MODEL PIù i dati*/
		if (is_null($this->submaschera)) {
			$valmaschera = null;
		} else {
			$valmaschera = $this->submaschera->GetData();
		}
		$arraytomerge = array();
		foreach($this->arrayoption as $key=>$value) {
				if ($key[0] == '@') {//aggiungo solo le option con la chioccola davanti in modo che le altre restano solo lato server
					$arraytomerge=array_merge($arraytomerge,array(ltrim($key,"@")=>$value));
				}/* else {
					$arraytomerge=array_merge($arraytomerge,array($key=>$value));
				}*/
		}

		//$this->logger->log('WB_Maschera', 'arrayoption->fine'.$this->camponame.implode('-',$arraytomerge), 3);

		return array_merge($arraytomerge,array(
				'elemento' => 'campo',
				'type' => $this->type,
				'iscampolist' => $this->campolist,//aggiunto perchè permette di vedere quali campi servono
				'iscampofile' => $this->campofile,
				//'value' => false,
				'name' => $this->camponame,
				'etichetta' => $this->etichetta,
				'maschera' => $valmaschera,
				'dbdatatype' => $this->dbdatatype,
				'isnullable' => $this->isnullable,
				'sinreloptionarray' => $this->singlerelationoptionarray,
			//	'dati' => $this->data,
				//'qta' => (float) 0.0,
		));
	}

	public function GetModel(){
		/*il modello dei dati*/
		if (is_null($this->submaschera)) {
			$valmaschera = null;
		} else {
			$valmaschera = $this->submaschera->GetModel();
		}
		return array(
				'elemento' => 'campo',
				'type' => $this->type,
				//'value' => false,
				'name' => $this->camponame,
				'etichetta' => $this->etichetta,
				'maschera' => $valmaschera,
				//'qta' => (float) 0.0,
		);
	}

	public function GetModelOBJ(){
		//specifico per essere richiamato dal controller PHP perchè altrimenti se chiamo getmodel viene costruita in maniera diversa e non riesce ad accedere ad alcuni campi-> perchè altrimenti mancano gli object instanziati fuori dagli array.. non ci ho capito molto ma così sembra funzionare.. getmodelOBJ è stato creato sia per WB_Maschera che WB_MascheraCampi
		if (is_null($this->submaschera)) {
			$valmaschera = null;
		} else {
			$valmaschera = $this->submaschera->GetModelOBJ();
		}
		return array(
				'elemento' => 'campo',
				'type' => $this->type,
				'iscampolist' => $this->campolist,//aggiunto perchè permette di vedere quali campi servono
				'iscampofile' => $this->campofile,
				'name' => $this->camponame,
				'etichetta' => $this->etichetta,
				'maschera' => $valmaschera,
				//'qta' => (float) 0.0,
		);
	}


	public function getlayout(){
		return '<div>campo</div>';
	}


}//fine classe

?>
