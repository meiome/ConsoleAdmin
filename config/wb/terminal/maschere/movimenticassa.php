<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('movimenticassa','tbl_movimenti_cassa','Maschera Movimenti Cassa','main',array('orderby'=>'datatime','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('cassa_id','cassa_id','Id Cassa');
  $m->addSingleRelation('cassa_id','cassa_id','Id Cassa','description',array('@addnonselezionato'=>'Non Selezionato *'));
  $m->addCampoList('datatime','datatime','Data Crezione');
  $m->addCampoList('linetipo','linetipo','entrata/uscita/apertura/chiusura');
  $m->addCampoList('importo','importo','Importo');
  $m->addCampoList('note','note','Note');
  
?>
