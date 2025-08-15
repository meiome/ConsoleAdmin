<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('baseoraria','tbl_lavoratore_baseoraria','Maschera Base Oraria Lavoratore','main',array('orderby'=>'datainizio','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSingleRelation('lavoratore_id','lavoratore_id','Lavoratore','nome',array('@addnonselezionato'=>'Non Selezionato *'));
  $m->addCampoList('datainizio','datainizio','Data Inizio');
  $m->addCampoList('lunedi','lunedi','Lunedì');
  $m->addCampoList('martedi','martedi','Martedì');
  $m->addCampoList('mercoledi','mercoledi','Mercoledì');
  $m->addCampoList('giovedi','giovedi','Giovedì');
  $m->addCampoList('venerdi','venerdi','Venerdì');
  $m->addCampoList('sabato','sabato','Sabato');
  $m->addCampoList('domenica','domenica','Domenica');
?>
