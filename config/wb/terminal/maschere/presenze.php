<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('presenze','tbl_presenze','Maschera Presenze','main',array('orderby'=>'id','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addShadowSingleRelation('server_id','server_id','Server','urlname',array('setshadowsinglerelationbyfunction'=>'getserverid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('datatime','datatime','Data Crezione');
  $m->addCampoList('tipo','tipo','ingresso/uscita');
  $m->addSingleRelation('lavoratore_id','lavoratore_id','Lavoratore','nome',array('@addnonselezionato'=>'Non Selezionato *'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
?>
