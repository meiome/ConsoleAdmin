<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('keycassa','key_cassa','Maschera Tasti Cassa','main',array('orderby'=>'ordine','descasc'=>'asc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('disable','disable','Disabilitato');
  $m->addCampoList('description','description','Descrizione');
  $m->addCampoList('ordine','ordine','Ordine (Intero)');
  $m->addCampoList('backgroundcolor','backgroundcolor','Sfondo (colore)');
  $m->addSingleRelation('article_id','article_id','Articolo di Riferimento','description',array('@addnonselezionato'=>'Non Selezionato','whereand'=>'COALESCE(disable,0) = 0'));
?>
