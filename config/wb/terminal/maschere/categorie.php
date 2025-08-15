<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('categorie','categorie','Maschera Categorie','main',array('orderby'=>'level,ordine','descasc'=>'asc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('name','name','Nome Categoria');
  $m->addCampoList('level','level','Livello Categoria');
  $m->addCampoList('ordine','ordine','Ordinamento (inserire numero intero)');
  $m->addSingleRelation('categorie_id','categorie_id','Categoria Padre','name',array('@addnonselezionato'=>'Non Selezionato *'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
?>
