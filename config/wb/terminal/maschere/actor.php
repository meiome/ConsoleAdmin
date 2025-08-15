<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('actor','actor','Maschera Clienti Foritori','main',array('orderby'=>'id','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('name','name','Nome');
  $m->addCampoList('iscustomer','iscustomer','Cliente');
  $m->addCampoList('issupplier','issupplier','Fornitore');
  $m->addCampoList('sendtomago','sendtomago','Invia a Mago');
  $m->setCampoNamePrefixForList('iscustomer',array('iscustomer','c','cli','iscli','cliente'),'=',"","");
  $m->setCampoNamePrefixForList('issupplier',array('issupplier','f','for','isfor','fornitore'),'=',"","");
  $m->setCampoNamePrefixForList('sendtomago',array('sendtomago','m','mago','s','issendtomago'),'=',"","");
?>
