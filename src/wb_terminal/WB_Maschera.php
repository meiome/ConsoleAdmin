<?php

namespace App\php\wb_terminal;
/*
 * Authore: Andrea Bettiol
 *
 *
 * Class: WB_Maschera
 *
 * Version: 0.1
*/

use App\php\wb_terminal\WB_MascheraCampi;

class WB_Maschera {

	public $mascheraname;
	private $maintablename;
	public $etichetta;
	private $logger;
	private $database;
	private $campi = array();//indicizzato by name
	private $finaldataarray = array();//array finale che contiene tutti i dati organizzati
	private $prototypedataarray = array();//array finale che contiene tutti i dati organizzati
	private $type; //'main' , 'sub'
	private $querymethod;
	private $queryid;
	private $azienda_id;
	private $mascheramain = null;//maschera principale
	private $queryarraycommand;//array contente tutta la stringa passata al terminale per la chiamata
	private $dbtablelink;
	private $camporiferirelation = 'id';/*per la main table è di default id , altrimenti viene messo il nome del campo collegato all'id nella sub table*/
	//su quest'array option non è stata fatta ancora l'opzione @ che espone i campo anche lato client
	private $arrayoption;//array('orderby'=>'nomecampo','descasc'=>'asc'/*oppure asc- funziona solo se c'è anche l'ordery*/)
	private $querywhere = '';
	private $arraySecurityFilter = array();//array che contiene tutti i filtri che vegono applicati alla maschera di default per tutte le operazioni che fa (lista, select, update, insert ...)

	private $serverconfig;

	public function __construct($name,$maintablename,$etichetta, $type = 'main',$arrayoption = array()) {
		$this->mascheraname = $name;
		$this->maintablename = $maintablename;
		$this->etichetta = $etichetta;
		$this->type = $type;
		$this->arrayoption = $arrayoption;
		$this->logger = $GLOBALS["WB_LOGGER"];
		$this->serverconfig = require(controllerDIR_BASE.'/config/wb/server/serverconfig.php');
	//	require_once($_SERVER['DOCUMENT_ROOT'].'/php/wb_terminal/WB_MascheraCampi.php');
	}

	public function getAziendaId(){
		if ($this->type == 'sub'){
			return $this->mascheramain->getAziendaId();
		} else {
			return $this->azienda_id;
		}
	}

	public function setMascheraMain($mascheramain): void{
		$this->mascheramain = $mascheramain;
	}

	public function setAzienda($azienda_id): void{
		$this->azienda_id = $azienda_id;
	}

	public function getArrayCampiElementByName($name){
		   return $this->campi[$name];
	}

	/*public function addelementtoprototypedataarray($contatoreriga,$nomecampo, $array){
		$trovato = false;
			foreach($this->prototypedataarray as &$t){
				if($t['contatoreriga'] == $contatoreriga) {
					$trovato = true;
					$t[$nomecampo]=$array;
				}
			};
		if ($trovato == false){
			$this->prototypedataarray[] = array(
				'contatoreriga' => $contatoreriga,
				$nomecampo => $array,
			);
		}
	}*/

	public function addelementtoprototypedataarray($contatoreriga,$nomecampo, $array): void{
		if (array_key_exists($contatoreriga,$this->prototypedataarray)){
				$this->prototypedataarray[$contatoreriga][$nomecampo]=$array;
			}	else {
				$this->prototypedataarray[$contatoreriga] = array(
					'contatoreriga' => $contatoreriga,
					$nomecampo => $array,
				);
			}
	}


	/*public function addelementtofinaldataarray($contatoreriga,$nomecampo,$array){
		$trovato = false;
			foreach($this->finaldataarray as &$t){
				if($t['contatoreriga'] == $contatoreriga) {
					$trovato = true;
					$t[$nomecampo]=$array;
				}
			};
		if ($trovato == false){
			$this->finaldataarray[] = array(
				'contatoreriga' => $contatoreriga,
				$nomecampo => $array,
			);
		}
	}*/

	public function addelementtofinaldataarray($contatoreriga,$nomecampo,$array): void{
	if (array_key_exists($contatoreriga,$this->finaldataarray)){
			$this->finaldataarray[$contatoreriga][$nomecampo]=$array;
		}	else {
			$this->finaldataarray[$contatoreriga] = array(
				'contatoreriga' => $contatoreriga,
				$nomecampo => $array,
			);
	  }
	}

	public function setDatabase($db): void{
		$this->database = $db;
	}

	public function setCampoRiferimentoRelation($crr): void{
		$this->camporiferirelation=$crr;
	}

	public function setQuery($method,$id): void{
		$this->querymethod=$method;
		$this->queryid=$id;
		foreach($this->campi as &$t){
			if($t->type == 'maschera') {
				$t->submaschera->setQuery($method,$id);
			}
		};
	}

	public function setQueryCommand($xarray): void{
		//questa funzione è specifica per la lista terminale che uso per filtrare gli articoli
		$this->queryarraycommand=$xarray;
		/*dalla terza posizione dell'array ci sono tutti i filtri che utilizzo per la lista ... controllo che i filtri siano coerenti con quelli impostati dalla maschera (passati con setCampoNamePrefixForList()) e poi vado a scrivere già su $this->querywhere la stringa che utilizzerò poi per filtrare la lista*/
		for ($i = 2; $i < count($this->queryarraycommand); $i+=2) {
			/*questo ciclo for viene fatto su tutti i comandi passati come argomenti a partire dal i=2 perchè i primi 2 sono lista e "nome maschera"*/
				$trovato = false;//perchè così fa il lavoro solo per la prima corrispondenza visto che non può interrompere il ciclo del foreach
				foreach($this->campi as &$c){//ciclo su tutti i campi della maschera e poi sotto qui su tutti gli array prefix per ogni campo della maschera
					foreach($c->arrayprefixforlist as &$p){
						if (strcmp($p, substr($this->queryarraycommand[$i],1)) == 0 and $trovato == false){
							//se la stinga corrisponde al
							$trovato = true;
							if ($i > 2){
								if(strcmp('+',substr($this->queryarraycommand[$i],0,1)) == 0){
									$andor = 'or';
								} else {
									$andor = 'and';
								}
							} else {
								$andor = '';
							}
							$this->querywhere = $this->querywhere.' '.$andor.' '.$c->camponamedb.' '.$c->operatorforlist.' '.$c->stringaprecedentevalueforlist.$this->queryarraycommand[$i+1].$c->stringasuccessivavalueforlist.' ';
						}
					};
				};
		}
	}

	public function GetQuery(){
		return array(
				'method' => $this->querymethod,
				'id' => $this->queryid,
		);
	}

	public function GetModel(){
		$arrmodelcampi = null;
		foreach($this->campi as &$c){
			if($c->type != 'shadowsinglerelation') {//la escludo perchè non è visibile lato client
				$arrmodelcampi []= $c->GetModel();
			}
		};
		$cartella = null;
		foreach($this->arrayoption as $key=>$value) {
			if (strcmp($key,'cartella') == 0) {
				$cartella=$value;
			}
		}
		return array(
				'elemento' => 'maschera',
				'type' => $this->type,
				'tablename' => $this->maintablename,
				'name' => $this->mascheraname,
				'etichetta' => $this->etichetta,
				//'qta' => (float) 0.0,
				'campi' => $arrmodelcampi,
				'cartella' => $cartella,
		);
	}

	public function GetModelOBJ(){
		//specifico per essere richiamato dal controller PHP perchè altrimenti se chiamo getmodel viene costruita in maniera diversa e non riesce ad accedere ad alcuni campi-> perchè altrimenti mancano gli object instanziati fuori dagli array.. non ci ho capito molto ma così sembra funzionare.. getmodelOBJ è stato creato sia per WB_Maschera che WB_MascheraCampi
		$arrmodelcampi = null;
		foreach($this->campi as &$c){
			if($c->type != 'shadowsinglerelation') {//la escludo perchè non è visibile lato client
				$arrmodelcampi []= (object)$c->GetModelOBJ();
			}
		};

		return (object)[
				'elemento' => 'maschera',
				'type' => $this->type,
				'tablename' => $this->maintablename,
				'name' => $this->mascheraname,
				'etichetta' => $this->etichetta,
				//'qta' => (float) 0.0,
				'campi' => $arrmodelcampi,
		];
	}


	public function GetData(){
		$arrmodelcampi = null;
		foreach($this->campi as &$c){
			if($c->type != 'shadowsinglerelation') {//la escludo perchè non è visibile lato client
				$arrmodelcampi []= $c->GetData();
			}
		};
		//aggiunto per non mostrare i campi eliminati non rimuovo da finaldataarray la riga che viene eliminata o che non viene inserita ma non la mostro (tanto non è stata inserita oppure viene eliminata con l'update)
		$arraynew=array();
		foreach($this->finaldataarray as &$f){
			if(array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == '-1'){

			} else {
				$arraynew[]=$f;
			}
		};
		$this->finaldataarray = $arraynew;
		//stop aggiunto per non mostrare i campi eliminati
		return array(
				'elemento' => 'maschera',
				'type' => $this->type,
				'tablename' => $this->maintablename,
				'name' => $this->mascheraname,
				'etichetta' => $this->etichetta,
				//'qta' => (float) 0.0,
				'campi' => $arrmodelcampi,
				//'queryresult' => $this->queryresult,
				'fdr' => $this->finaldataarray,
				'fdr_proto' => $this->GetFdrPrototype(),

		);
	}

	private function GetFdrPrototype(){
		$contatoreriga=0;
		foreach($this->campi as &$w){
			if($w->requestbymodel and $w->type == 'campo'){
				$w->add($contatoreriga,null,null,$this->queryid,true);
			} elseif($w->requestbymodel and $w->type == 'singlerelation'){
				$w->add($contatoreriga,null,null,$this->queryid,true);
			} elseif($w->requestbymodel and $w->type == 'shadowsinglerelation'){
				//$w->add($contatoreriga,null,null,$this->queryid,true);
			} elseif($w->requestbymodel and $w->type == 'manipolazione'){
				$w->add($contatoreriga,$w->camponame,null,$this->queryid,true);
			}
		}
		return $this->prototypedataarray;
	}

	public function GetTable(){
		if (is_null($this->dbtablelink)){
			$this->dbtablelink = $this->database->getTable($this->maintablename);
		}
		return $this->dbtablelink;
	}

	public function LoadDbDataTypeCampiMaschera(): void{
		//con questa funzione vado a reperire i tipi di dato dei campi database dal file di configurazione database (modificata con questa perchè prima ciclavo sul model passato dal client in modo che potevo usare anche meno campi ma non andava bene per sicurezza) -> l'ho modificata quando ho iniziato ad usare shadow single relation
		$this->logger->log('WB_Maschera', 'LoadDbDataTypeCampiMaschera Maschera:'.$this->mascheraname, 3);
		foreach($this->campi as &$w){
				if($w->type == 'maschera') {
					$w->submaschera->LoadDbDataTypeCampiMaschera();
				} else {
					$w->LoadDbDataTypeCampiMaschera();
				}
			}
		}

/*public function PrepareGetData($qmd,$sub = false){
	//con questa funzione vado a reperire i tipi di dato dei campi database dal file di configurazione database e controllo che tutti i campi passati dal dal file di configurazione della maschera siano presenti
		$this->logger->log('WB_Maschera', 'PrepareGetData Maschera:'.$this->mascheraname, 3);
		if ($sub)//se è una sub maschera
		{
		$this->logger->log('WB_Maschera', 'PrepareGetData sub campi:'.count($this->campi), 3);
			foreach($qmd->maschera->campi as &$cm){
				$this->logger->log('WB_Maschera', 'foreach campi '.$cm->name, 3);
				if (array_key_exists($cm->name, $this->campi)) {
						$this->campi[$cm->name]->PrepareGetData($cm);
				} else {
					throw new Exception("WB_Maschera GetData() campo nel model non presente nella maschera", 1);
				}
			};
		} else {
			$this->logger->log('WB_Maschera', 'PrepareGetData main', 3);
			foreach($qmd->model->campi as &$cm){
			//	$this->logger->log('WB_Maschera', implode(", ",$cm), 3);
			//var_dump($qmd->model->campi);
				$this->logger->log('WB_Maschera', 'foreach campi '.$cm->name, 3);
				if (array_key_exists($cm->name, $this->campi)) {
					//questo prepare get data è una funzione diversa da WB_MascheraCampi anche se ha lo stesso nome
					$this->campi[$cm->name]->PrepareGetData($cm);
				} else {
					throw new Exception("WB_Maschera GetData() campo nel model non presente nella maschera", 1);
				}
			};
		}
	}*/

	public function SelectOptionSingleRelation($campo): void{

		$fk = $campo->GetSingleRelationDbInfo();

		//$this->logger->log('WB_Maschera', 'START SelectOptionSingleRelation Maschera:'.$this->mascheraname, 3);

		$query_str = 'SELECT id,'.$campo->singlerelnamefieldrelated.' FROM '.$fk['referenced_table']; //.' WHERE '.$this->camporiferirelation.' = '.$this->queryid;
		//aggiungo le clausole del where
		$contatorewhere = 0;
		foreach($campo->arrayoption as $key=>$value) {
			if ($key == 'whereand') {
				$contatorewhere++;
				if ($contatorewhere > 1){
					$query_str = $query_str.' and '.$value;
				} else {
					$query_str = $query_str.' WHERE '.$value;
				}
			} else {
				if ($key == 'whereor') {
					$contatorewhere++;
					if ($contatorewhere > 1){
						$query_str = $query_str.' or '.$value;
					} else {
						$query_str = $query_str.' WHERE '.$value;
					}
				}
			}
		}
		//estraggo i risultati della query
		$result = $this->database->query($query_str);

		//aggiungo le option alla WB_MascheraCampi
		$contatoreriga = 0;
		foreach($result->query_result as &$t) {
			$contatoreriga++;
			$campo->addSingleRelationOption($contatoreriga,$t['id'],$t[$campo->singlerelnamefieldrelated]);
		};
	}

	private $queryresult;

public function SelectCampi($sub = false/*true se maschera non main*/): void{

	$this->logger->log('WB_Maschera', 'START SelectCampi Maschera:'.$this->mascheraname, 3);
	$query_str = 'SELECT id';
	foreach($this->campi as &$t){
	if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation' /*or $t->type == 'shadowsinglerelation'*/)){
			$query_str = $query_str.','.$t->camponamedb.' as '.$t->camponame;
		if ($t->requestbymodel and ($t->type == 'singlerelation' /*or $t->type == 'shadowsinglerelation'*/)){
				$this->SelectOptionSingleRelation($t);
			}
		} else {
			if($t->type == 'maschera') {
				$t->submaschera->SelectCampi(true);
			}
		}
	};

	$this->querywhere = $this->camporiferirelation.' = '.$this->queryid;
	//add security filter (esempio per azienda)
	foreach($this->arraySecurityFilter as &$z) {
		//$this->logger->log('WB_Maschera', 'addSecurityFilter'.$this->getAziendaId(), 3);
		if($z[0] == 'azienda_id'){
			if($z[1] == 'getaziendaid'){
				if (strcmp($this->querywhere, '') == 0){
					//se non ci sono altre condizioni nel WHERE lo aggiungo senza and
					$this->querywhere = $z[0].' = '.$this->getAziendaId();
				} else {
					$this->querywhere = $this->querywhere.' and '.$z[0].' = '.$this->getAziendaId();
				}
			}
		}
	}

	$query_str = $query_str.' FROM '.$this->maintablename.' WHERE '.$this->querywhere;

	//$this->logger->log('WB_Maschera', 'SelectCampi Maschera QWERTY:'.$query_str, 3);

	/*if ($sub==true){
		$query_str = $query_str.' FROM '.$this->maintablename.' WHERE '.$this->camporiferirelation.' = '.$this->queryid;
	} else {
		$query_str = $query_str.' FROM '.$this->maintablename.' WHERE '.$this->camporiferirelation.' = '.$this->queryid;
	}*/

	/*PARTE ORDINAMENTO DATI ALL'interno della maschera, creata per oridnamento nelle sub maschere e sarà utilizzata anche per l'ordinamento nelle maschere di ricerca probabilmente*/
	$orderby = null;
	$descasc = null;
	/*controllo se nell'array option sono presenti le due key che mi servono per decidere come ordinare la maschera*/
	foreach($this->arrayoption as $key=>$value) {
		if (strcmp($key,'orderby') == 0) {
			$orderby=$value;
		} elseif(strcmp($key,'descasc') == 0) {
			$descasc=$value;
		}
	}
	//aggiungo alla query la parte order by se serve
	if(is_null($orderby)==false){
		$query_str = $query_str.' ORDER BY '.$orderby;
		/*è all'interno dell'if perchè solo se è presente un order valuto di aggiungere anche asc o desc*/
		if(is_null($descasc)==false){
			$query_str = $query_str.' '.$descasc;
		}
	}
	/*fine PARTE ORDINAMENTO DATI ALL'interno della maschera*/

	$this->queryresult = $this->database->query($query_str);

	//SONO QUI ADESSO DEVO SALVARE I DATI prelevati nell'array campi
	$contatoreriga = 0;
	foreach($this->queryresult->query_result as &$t) {
		//$this->pluto = $t['id'];

		$contatoreriga++;
		foreach($this->campi as &$w){
			$this->logger->log('WB_Maschera', 'SelectCampi CICLO su w campo:'.$w->camponame, 3);

			if($w->requestbymodel and $w->type == 'campo'){
				$w->add($contatoreriga,$t[$w->camponame],$t['id'],$this->queryid);
			} elseif($w->requestbymodel and $w->type == 'singlerelation'){
				$w->add($contatoreriga,$t[$w->camponame],$t['id'],$this->queryid);
			} elseif($w->requestbymodel and $w->type == 'shadowsinglerelation'){
				//$w->add($contatoreriga,$t[$w->camponame],$t['id'],$this->queryid);
			} elseif($w->requestbymodel and $w->type == 'manipolazione'){
				$w->add($contatoreriga,$w->camponame,$t['id'],$this->queryid);
			}
		}
	};
}

public function SelectCampiForList($sub = false/*true se maschera non main*/): void{

	/*valutare quanto scritto sotto per implementare in fututo anche dei filtri per submaschera*/

	$this->logger->log('WB_Maschera', 'START SelectCampiFORLIST Maschera:'.$this->mascheraname, 3);
	$query_str = 'SELECT id';
	foreach($this->campi as &$t){
	if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation' /* or $t->type == 'shadowsinglerelation'*/)){
			$query_str = $query_str.','.$t->camponamedb.' as '.$t->camponame;
		if ($t->requestbymodel and ($t->type == 'singlerelation' /*or $t->type == 'shadowsinglerelation'*/)){
				$this->SelectOptionSingleRelation($t);
			}
		} else {
			if($t->type == 'maschera') {
				/*si può abilitare in un secondo momento anche questa parte se si vogliono filtrare le maschere anche per submaschera ma bisogna tenere conto anche del filtro automaticato sub maschera che penso sia una cosa nel where tipo ($this->camporiferirelation.' = '.$this->queryid) e poi a questo vanno aggiunti i filtri specifici passati da terminale.. ma bisogna riscrivere anche setQueryCommand($xarray) aggiungendo il richiamo della procedura stessa per le submaschere in caso di filri di sub maschere*/
//				$t->submaschera->SelectCampiForList(true);
			}
		}
	};

	//$query_str = $query_str.' FROM '.$this->maintablename.' WHERE '.$this->camporiferirelation.' = '.$this->queryid;

	foreach($this->arraySecurityFilter as &$z) {
		//$this->logger->log('WB_Maschera', 'addSecurityFilter'.$this->getAziendaId(), 3);
		if($z[0] == 'azienda_id'){
			if($z[1] == 'getaziendaid'){
				if (strcmp($this->querywhere, '') == 0){
					//se non ci sono altre condizioni nel WHERE lo aggiungo senza and
					$this->querywhere = $z[0].' = '.$this->getAziendaId();
				} else {
					$this->querywhere = $this->querywhere.' and '.$z[0].' = '.$this->getAziendaId();
				}
			}
		}
	}

	if (strcmp($this->querywhere, '') == 0) {
		$query_str = $query_str.' FROM '.$this->maintablename;
	} else {
		$query_str = $query_str.' FROM '.$this->maintablename.' WHERE '.$this->querywhere;
	}

	/*PARTE ORDINAMENTO DATI ALL'interno della maschera, creata per oridnamento nelle sub maschere e sarà utilizzata anche per l'ordinamento nelle maschere di ricerca probabilmente*/
	$orderby = null;
	$descasc = null;
	/*controllo se nell'array option sono presenti le due key che mi servono per decidere come ordinare la maschera*/
	foreach($this->arrayoption as $key=>$value) {
		if (strcmp($key,'orderby') == 0) {
			$orderby=$value;
		} elseif(strcmp($key,'descasc') == 0) {
			$descasc=$value;
		}
	}
	//aggiungo alla query la parte order by se serve
	if(is_null($orderby)==false){
		$query_str = $query_str.' ORDER BY '.$orderby;
		/*è all'interno dell'if perchè solo se è presente un order valuto di aggiungere anche asc o desc*/
		if(is_null($descasc)==false){
			$query_str = $query_str.' '.$descasc;
		}
	}
	/*fine PARTE ORDINAMENTO DATI ALL'interno della maschera*/

	$this->logger->log('WB_MascheraQUERYSTRING', $query_str, 3);

	$this->queryresult = $this->database->query($query_str);

	//SONO QUI ADESSO DEVO SALVARE I DATI prelevati nell'array campi
	$contatoreriga = 0;
	foreach($this->queryresult->query_result as &$t) {
		//$this->pluto = $t['id'];
		$contatoreriga++;
		foreach($this->campi as &$w){
			if($w->requestbymodel and $w->type == 'campo'){
				$w->add($contatoreriga,$t[$w->camponame],$t['id'],$this->queryid);
			} else{
			if($w->requestbymodel and ($w->type == 'singlerelation'/* or $w->type == 'shadowsinglerelation'*/)){
					$w->add($contatoreriga,$t[$w->camponame],$t['id'],$this->queryid);
				}
			}
		}
	};
}

private function getshadowsinglerelationvalue($mascheracampo){
	//$this->logger->log('getshadowsinglerelationvalue', implode(" ",$mascheracampo->arrayoption), 3);
	$pippo="NULL";
	foreach($mascheracampo->arrayoption as $key => $value)
	{
		if ($key == 'setshadowsinglerelationbyfunction'){
			if ($value == 'getaziendaid'){
				$pippo=$this->getAziendaId();
			} else {
				if ($value == 'getserverid'){
					$pippo=$this->serverconfig['server_id'];
				}
			}
		}



	}
	return $pippo;
}

//NON UTILIZZATA perchè ora uso quella by step
public function GetInsertQuery($sub = false){
	$this->logger->log('WB_Maschera', 'START GetInsertQuery Maschera:'.$this->mascheraname, 3);
	$sub_query_str = '';
	$query_str = '';
	$arrayquery = array();
	foreach($this->finaldataarray as &$f){
		if(array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == '-1'){
			//se quella riga di final data array è un riga aggiungielimina cancellata allora non la inserisco
		} else {
			$query_value = ') VALUES (';
			$query_str = $query_str.'INSERT INTO '.$this->maintablename.' (';
			foreach($this->campi as &$t){
				if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation')){
					$rif = $f[$t->camponame];
					if ($t->dbdatatype == 'VARCHAR(255)' or $t->dbdatatype == 'LONGTEXT' or $t->dbdatatype == 'DATE' or $t->dbdatatype == 'DATETIME'){
						if ($rif["value"]==""){
							$app = "NULL,";
						} else {
							$app = "'".$rif["value"]."',";
						}
					} else {
						if($t->dbdatatype == 'TINYINT(1)'){
							if ($rif["value"]==true){
								$app = "true,";
							} else {
								if ($rif["value"]==false){
									$app = "false,";
								} else {
									$app = "null,";
								}
							}
						} else {
							if ($rif["value"]==""){
								$app = "NULL,";
							} else {
								$app = $rif["value"].",";
							}
						}
					}
					$query_str = $query_str.$t->camponamedb.',';
					$query_value = $query_value.$app;
				} else {//non mi sovrascrivere mai $rif in nessun modo perchè lo uso per l'id nel WHERE
					if($t->type == 'shadowsinglerelation') {
						$app = $this->getshadowsinglerelationvalue($t).",";
						//$app = "NULL,";
						$query_str = $query_str.$t->camponamedb.',';
						$query_value = $query_value.$app;
					} else {
						if($t->type == 'maschera') {
							$sub_query_str =$sub_query_str.$t->submaschera->GetInsertQuery(true);
						}
					}
				}
			};
			$query_str = rtrim($query_str, ",");
			$query_value = rtrim($query_value, ",");
			if ($sub){
				$query_str = $query_str.','.$this->camporiferirelation.$query_value.',@idnew);';
			} else {
				$query_str = $query_str.$query_value.');'.'SET @idnew = LAST_INSERT_ID();'.$sub_query_str;
			}
		}
	};
	$this->logger->log('WB_Maschera', 'STAMP GetInsertQuery Maschera:'.$query_str, 3);
	return $query_str;
}

public function GetInsertQueryByStep($sub = false/*true se maschera non main*/){
	$this->logger->log('WB_Maschera', 'START GetInsertQuery Maschera:'.$this->mascheraname, 3);
	$sub_query_str = '';
	$query_str = '';
	$arrayquery = array();
	if ($sub == false){$arrayquery[] = 'SELECT @idnew as id;';}
	foreach($this->finaldataarray as &$f){
		if(array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == '-1'){
			//se quella riga di final data array è un riga aggiungielimina cancellata allora non la inserisco
		} else {
			$query_value = ') VALUES (';
			$query_str = 'INSERT INTO '.$this->maintablename.' (';
			foreach($this->campi as &$t){
				if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation')){
					$rif = $f[$t->camponame];
					if ($t->dbdatatype == 'VARCHAR(255)' or $t->dbdatatype == 'LONGTEXT' or $t->dbdatatype == 'DATE' or $t->dbdatatype == 'DATETIME'){
						if ($rif["value"]==""){
							$app = "NULL,";
						} else {
							$app = "'".$rif["value"]."',";
						}
					} else {
						if($t->dbdatatype == 'TINYINT(1)'){
							if ($rif["value"]==true){
								$app = "true,";
							} else {
								if ($rif["value"]==false){
									$app = "false,";
								} else {
									$app = "null,";
								}
							}
						} else {
							if ($rif["value"]==""){
								$app = "NULL,";
							} else {
								$app = $rif["value"].",";
							}
						}
					}
					$query_str = $query_str.$t->camponamedb.',';
					$query_value = $query_value.$app;
				} else {//non mi sovrascrivere mai $rif in nessun modo perchè lo uso per l'id nel WHERE
					if($t->type == 'shadowsinglerelation') {
						$app = $this->getshadowsinglerelationvalue($t).",";
						//$app = "NULL,";
						$query_str = $query_str.$t->camponamedb.',';
						$query_value = $query_value.$app;
					} else {
						if($t->type == 'maschera') {
							$arrayquery=array_merge($arrayquery , $t->submaschera->GetInsertQueryByStep(true));
						}
					}
				}
			};
			$query_str = rtrim($query_str, ",");
			$query_value = rtrim($query_value, ",");
			if ($sub){
				$arrayquery[] = $query_str.','.$this->camporiferirelation.$query_value.',@idnew);';
			} else {
				$arrayquery[] = 'SET @idnew = LAST_INSERT_ID();';
				$arrayquery[] = $query_str.$query_value.');';
			}
		}
	};
	//$this->logger->log('WB_Maschera', 'STAMP GetInsertQuery Maschera:'.$query_str, 3);
	return $arrayquery;
}

public function getDeleteQuery($id){
	if(is_numeric($id)){
		$query_str = 'DELETE FROM '.$this->maintablename;
		$xwhere = 'id = '.$id;
		//add security filter (esempio per azienda)
		foreach($this->arraySecurityFilter as &$z) {
			//$this->logger->log('WB_Maschera', 'addSecurityFilter'.$this->getAziendaId(), 3);
			if($z[0] == 'azienda_id'){
				if($z[1] == 'getaziendaid'){
					if (strcmp($xwhere, '') == 0){
						//se non ci sono altre condizioni nel WHERE lo aggiungo senza and
						$xwhere = $z[0].' = '.$this->getAziendaId();
					} else {
						$xwhere = $xwhere.' and '.$z[0].' = '.$this->getAziendaId();
					}
				}
			}
		}
		//addSecurityFilter
		return $query_str.' WHERE '.$xwhere.';';
	}
}

public function UpdateQuery($sub = false/*true se maschera non main*/): void{
		$this->logger->log('WB_Maschera', 'START UpdateQuery Maschera:'.$this->mascheraname, 3);
		$query_str = '';
		foreach($this->finaldataarray as &$f){
			if($sub and array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == '-1' and $f['aggiungielimina']['id'] == null){
				//se quella riga di final data array è una riga aggiungielimina cancellata allora devo fare il delete (ma in questo caso è una riga nuova aggiunta e poi cancellata quindi basta non fare niente)
			} elseif($sub and array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == '-1'){
				//se quella riga di final data array è una riga aggiungielimina cancellata allora devo fare il delete
				$query_str = $query_str.$this->getDeleteQuery($f['aggiungielimina']['id']);
			} elseif($sub and array_key_exists('aggiungielimina',$f) and $f['aggiungielimina']['value'] == 'aggiungielimina' and $f['aggiungielimina']['id'] == null) {
				//FUNZIONA SOLO PER LE SUB QUERY -> vedi clausola $sub == true nell'if
				//In questo caso devo aggiungere la query di insert
				$query_value = ') VALUES (';
				$query_str = $query_str.'INSERT INTO '.$this->maintablename.' (';
				foreach($this->campi as &$t){
					if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation')){
						$rif = $f[$t->camponame];
						if ($t->dbdatatype == 'VARCHAR(255)' or $t->dbdatatype == 'LONGTEXT' or $t->dbdatatype == 'DATE' or $t->dbdatatype == 'DATETIME'){
							if ($rif["value"]==""){
								$app = "NULL,";
							} else {
								$app = "'".$rif["value"]."',";
							}
						} else {
							if($t->dbdatatype == 'TINYINT(1)'){
								if ($rif["value"]==true){
									$app = "true,";
								} else {
									if ($rif["value"]==false){
										$app = "false,";
									} else {
										$app = "null,";
									}
								}
							} else {
								if ($rif["value"]==""){
									$app = "NULL,";
								} else {
									$app = $rif["value"].",";
								}
							}
						}
						$query_str = $query_str.$t->camponamedb.',';
						$query_value = $query_value.$app;
					} else {//non mi sovrascrivere mai $rif in nessun modo perchè lo uso per l'id nel WHERE
						if($t->type == 'shadowsinglerelation') {
							$app = $this->getshadowsinglerelationvalue($t).",";
							//$app = "NULL,";
							$query_str = $query_str.$t->camponamedb.',';
							$query_value = $query_value.$app;
						}
					}
				};
				$query_str = rtrim($query_str, ",");
				$query_value = rtrim($query_value, ",");
				$query_str = $query_str.','.$this->camporiferirelation.$query_value.','.$f['aggiungielimina']['idref'].');';
				//stop aggiunta query insert
			} else {
				$query_str = $query_str.'UPDATE '.$this->maintablename.' SET';
				foreach($this->campi as &$t){
					if ($t->requestbymodel and ($t->type == 'campo' or $t->type == 'singlerelation')){//non serve aggiornare shadowsinglerelation per il momento perchè considero che sia sempre inserita e non si possa cambiare
						$rif = $f[$t->camponame];
						if ($t->dbdatatype == 'VARCHAR(255)' or $t->dbdatatype == 'LONGTEXT' or $t->dbdatatype == 'DATE' or $t->dbdatatype == 'DATETIME'){
							if ($rif["value"]==""){
								$app = "NULL,";
							} else {
								$app = "'".$rif["value"]."',";
							}
						} else {
							if($t->dbdatatype == 'TINYINT(1)'){
								if ($rif["value"]==true){
									$app = "true,";
								} else {
									if ($rif["value"]==false){
										$app = "false,";
									} else {
										$app = "null,";
									}
								}
							} else {
								if ($rif["value"]==""){
									$app = "NULL,";
								} else {
									$app = $rif["value"].",";
								}
							}
						}
						$query_str = $query_str.' '.$t->camponamedb.' ='.$app;
					} else {//non mi sovrascrivere mai $rif in nessun modo perchè lo uso per l'id nel WHERE
						if($t->type == 'maschera') {
							$t->submaschera->UpdateQuery(true);
						}
					}
				};

				$query_str = rtrim($query_str, ",");

				//addSecurityFilter
				$this->querywhere = 'id = '.$rif["id"];
				//add security filter (esempio per azienda)
				foreach($this->arraySecurityFilter as &$z) {
					//$this->logger->log('WB_Maschera', 'addSecurityFilter'.$this->getAziendaId(), 3);
					if($z[0] == 'azienda_id'){
						if($z[1] == 'getaziendaid'){
							if (strcmp($this->querywhere, '') == 0){
								//se non ci sono altre condizioni nel WHERE lo aggiungo senza and
								$this->querywhere = $z[0].' = '.$this->getAziendaId();
							} else {
								$this->querywhere = $this->querywhere.' and '.$z[0].' = '.$this->getAziendaId();
							}
						}
					}
				}
				//addSecurityFilter
				$query_str = $query_str.' WHERE '.$this->querywhere.';';
				//QUESTO LO USERò per INSERT l'inserimento: $this->camporiferirelation.' = '.$this->queryid
			}
		};
		//$this->logger->log('WB_Maschera', 'UpdateQuery'.$query_str, 3);
		$this->queryresult = $this->database->query($query_str);
	}

	/* Con questa funzione carico i dati per l'update sull'FDR(finaldataarray) dal qmd in input (normalmente l'fdr viene caricato dalla select) */
	public function CaricaFdrDaQmdInput($qmd,$sub = false): void{
		$this->logger->log('WB_Maschera', 'CaricaFdrDaQmdInput Maschera:'.$this->mascheraname, 3);

		$this->logger->log('WB_Maschera SUB?', $sub, 3);

		$contatoreriga = 0;

		if ($sub){

			$this->logger->log('WB_Maschera', 'CaricaFdrDaQmdInput SUBTRUE dentro allIF:'.$this->mascheraname, 3);

			foreach($qmd->maschera->fdr as &$t) {
					$contatoreriga++;

					foreach($qmd->maschera->campi as &$w) {
						if($w->type == 'campo') {
							$pn = $w->name;
							$this->addelementtofinaldataarray($contatoreriga,$pn, array(
								'contatoreriga' => $contatoreriga,
								'value' => ($t->$pn)->value,
								'id' => ($t->$pn)->id,
								'idref' => ($t->$pn)->idref,
							));
						} else {
							if($w->type == 'singlerelation') {
								$pn = $w->name;
								$this->addelementtofinaldataarray($contatoreriga,$pn, array(
									'contatoreriga' => $contatoreriga,
									'value' => ($t->$pn)->value,
									'id' => ($t->$pn)->id,
									'idref' => ($t->$pn)->idref,
								));
								$this->SelectOptionSingleRelation($this->getArrayCampiElementByName($pn));
							} else {
								if ($w->type == 'maschera'){
									$this->logger->log('WB_Maschera', 'CaricaFdrDaQmdInput ERRORE maschera sub non può avere un altra maschera sub ', 3);
								} else {
									if ($w->type == 'manipolazione'){
										$pn = $w->name;
										$this->addelementtofinaldataarray($contatoreriga,$pn, array(
											'contatoreriga' => $contatoreriga,
											'value' => ($t->$pn)->value,
											'id' => ($t->$pn)->id,
											'idref' => ($t->$pn)->idref,
										));
									}
								}
							}
						}
					};
				}
		} else {

			foreach($qmd->data->fdr as &$t) {
					$contatoreriga++;
					$this->logger->log('WB_Maschera', 'CaricaFdrDaQmdInput Maschera: cicloFDR'.$contatoreriga, 3);

					foreach($qmd->data->campi as &$w) {
						$this->logger->log('WB_Maschera', 'CaricaFdrDaQmdInput Maschera: ciclocampi ', 3);
						if($w->type == 'campo') {
							$pn = $w->name;
							$this->addelementtofinaldataarray($contatoreriga,$pn, array(
								'contatoreriga' => $contatoreriga,
						    'value' => ($t->$pn)->value,
						    'id' => ($t->$pn)->id,
						    'idref' => ($t->$pn)->idref,
						  ));
						} else {
							if ($w->type == 'singlerelation'){
								$pn = $w->name;
								$this->addelementtofinaldataarray($contatoreriga,$pn, array(
									'contatoreriga' => $contatoreriga,
									'value' => ($t->$pn)->value,
									'id' => ($t->$pn)->id,
									'idref' => ($t->$pn)->idref,
								));
								$this->SelectOptionSingleRelation($this->getArrayCampiElementByName($pn));
							} else {
								if ($w->type == 'maschera'){
									foreach($this->campi as &$cam){
										if($w->name == $cam->camponame){
											$cam->submaschera->CaricaFdrDaQmdInput($w,true);
										}
									}
								}
							}
						}
					};
				}
			}
	}

	public function PrepareDbQueryForData($qmd): void{
		$this->logger->log('WB_Maschera', 'PrepareDbQueryForData Maschera:'.$this->mascheraname.' METHOD:'.$this->querymethod, 3);
		if ($this->querymethod == 'select' OR $this->querymethod == 'prepareinsert') {
			$this->SelectCampi();
		} elseif ($this->querymethod == 'update') {
			$this->CaricaFdrDaQmdInput($qmd);
			$this->UpdateQuery();
			$this->querymethod = 'select';//lo imposto perchè è quello che do indietro al client e quindi è una select, ristampa tutto
		} elseif ($this->querymethod == 'insert'){
			$this->CaricaFdrDaQmdInput($qmd);

			//$this->queryresult = $this->database->query($this->GetInsertQuery());
			$arrquery = array_reverse($this->GetInsertQueryByStep());
			foreach($arrquery as &$q) {
				$this->logger->log('WB_Maschera', 'QUERY BY STEP:'.$q, 3);
				$this->queryresult = $this->database->query($q);
			}

			$returnid = 0;
			$contatoreriga = 0;
			foreach($this->queryresult->query_result as &$t) {
				//$this->pluto = $t['id'];
				$contatoreriga++;
				$returnid =(int)$t['id'];
			};

			if ($contatoreriga>0){
				$this->setQuery('select',$returnid);
			} else {
				$this->querymethod = 'select';//lo imposto perchè è quello che do indietro al client e quindi è una select, ristampa tutto
			}


		} elseif ($this->querymethod == 'list') {
			$this->SelectCampiForList();
			$this->querymethod = 'list';
		}
	}

	public function elaboraRichiesta($qmd): void{

		//$this->logger->log('WB_MascheraMODELtype', $qmd->model->type, 3);
		/*controllo che la maschera sia main (principale)e abbia lo stesso nome db di quella caricata dal controller e abbia lo stesso nome, e che l'elemento del model richiesto specifichi che sia maschera */
		if ($qmd->model->type == 'main' /*and $qmd->model->tablename == $this->maintablename*//*tolta perchè non permetteva di fare ricerca senza impostare il nome tabella lato client, sulla select è visibile*/ and $qmd->model->name == $this->mascheraname and $qmd->model->elemento == 'maschera') {
			$this->logger->log('WB_Maschera', 'Inizio elaborazione richiesta', 3);
			//$this->PrepareGetData($qmd);//qui carico tutti i nomi dei campi a db
			$this->LoadDbDataTypeCampiMaschera();
			$this->PrepareDbQueryForData($qmd);
		} else {
			$this->logger->log('WB_Maschera', 'Errore elaborazione richiesta', 3);
			throw new \Exception("WB_Terminal maschera non trovata", 1);
		}
	}

	public function getMascheraHtml(){
		$r = '<div>testata</div>';
		foreach($this->campi as &$c){
			$r = $r.$c->getlayout();
		};
		return $r;
	}

	/*
	IDEAPER i possibili tipi di relation
	LIST -> usato anche per la ricerca
	SHADOW -> ombra che agisce solo nel backend in base a delle logiche programmabili (non presente lato client)
	HIDE -> campo presente lato client ma non visibile (modificabile da un utente javascript)
	*/

/*AGGIUNTA CAMPO CHE VIENE USATO ANCHE PER LA MASCHERA DI VISUALIZZAZIONE LIST (cioè quella di ricerca e filtro elementi)*/
	public function addCampoList($camponame, $camponamedb, $etichetta, $arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type campolist'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'campolist',$camponame,$camponamedb, $etichetta, null, null, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}
/*campo semplice*/
	public function addCampo($camponame, $camponamedb, $etichetta, $arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type campo'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'campo',$camponame,$camponamedb, $etichetta, null, null, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}
//campo file
	public function addCampoFile($camponame, $camponamedb, $etichetta, $arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type campolist'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'campofile',$camponame,$camponamedb, $etichetta, null, null, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}
/*campo operazione*/
	public function addCampoManipolazione($camponame, $etichetta = null, $arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type manipolazione'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'manipolazione',$camponame,null, $etichetta, null, null, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}
/*campo relativo a un id che fa riferimento ad un altra tabella*/
/*arrayoption (con @ davanti sono visibili anche lato client)*/
	public function addSingleRelation($camponame/*nome univoco*/, $camponamedb, $etichetta, $srnamefieldrelated ,$arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type singlerelation'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'singlerelation',$camponame,$camponamedb, $etichetta, null, $srnamefieldrelated, $arrayoption);
		//$this->logger->log('WB_Maschera', 'arrayoption'.implode('-',$arrayoption), 3);
		$this->campi[$mc->camponame] = $mc;
	}

	public function addSeparatorLabel($camponame/*nome univoco*/,$testo): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type separatorLabel ('.$testo.') a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'separatorlabel',$camponame,null, $testo, null, null, array());
		$this->campi[$mc->camponame] = $mc;
	}

/*addshadowsinglerelation aggiunge negli INSERT un campo relazionale che seleziona il tipo in base a delle logiche.. va usato insieme a addSecurityFilter perchè il primo funziona come insert, mentre il secondo fa da filtro ad altre funzioni (update, delete, lista)*/
	public function addShadowSingleRelation($camponame/*nome univoco*/, $camponamedb, $etichetta, $srnamefieldrelated ,$arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type SHADOWsinglerelation'.$camponame.' a maschera '.$this->mascheraname, 3);
		$mc = new WB_MascheraCampi($this,$this->database,'shadowsinglerelation',$camponame,$camponamedb, $etichetta, null, $srnamefieldrelated, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}

	public function addSubMaschera($camponame/*nome univoco*/, $camponamedb/*id main relation*/, $maschera, $arrayoption = array()): void{
		$this->logger->log('WB_Maschera', 'Aggiunto campo type maschera'.$camponame.' a maschera '.$this->mascheraname, 3);
		$maschera->setDatabase($this->database);
		$maschera->setCampoRiferimentoRelation($camponamedb);
		$maschera->setMascheraMain($this);
		$mc = new WB_MascheraCampi($this,$this->database,'maschera',$camponame,$camponamedb, $maschera->etichetta, $maschera, null, $arrayoption);
		$this->campi[$mc->camponame] = $mc;
	}

	/*questa funzione serve a settare un array di prefisssi (esempio -name, -description) che possono poi essere utilizzati dal terminale per la ricerca*/
public function setCampoNamePrefixForList($xcamponame/*nome univoco*/,$arraycampoprefix/*prefix che va poi preceduto dal meno "-" per la ricerca tramite terminale*/,$operator/*operatore che serve alla query esempio =,like,beetween*/,$stringaprecedentevalue/*stringa percedente o successiva il valore cercato esempio gli apici che servono per l'operatore like default ''*/,$stringasuccessivavalue): void{
		$this->campi[$xcamponame]->arrayprefixforlist = $arraycampoprefix;
		$this->campi[$xcamponame]->operatorforlist = $operator;
		$this->campi[$xcamponame]->stringaprecedentevalueforlist = $stringaprecedentevalue;
		$this->campi[$xcamponame]->stringasuccessivavalueforlist = $stringasuccessivavalue;

	}

//questa funzione permette di aggiungere un filtro che viene utilizzato per tutte le operazioni della maschera tranne quelle di insert quindi queste:update, delete, lista. Per quelle di insert invece bisogna utilizzare l'altra funzione. Shadow single relation
	public function addSecurityFilter($xcamponame/*nome univoco*/,$targetfunction): void{
		$this->arraySecurityFilter[] = array($xcamponame,$targetfunction);
	}

}//fine classe

?>
