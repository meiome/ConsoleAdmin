<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('politicaprezzi','politica_prezzi','Maschera Politica prezzi','main',array('orderby'=>'id','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('description','description','Descrizione');
  $m->addCampoList('disable','disable','Disabilitato');
  $m->addCampoList('ricarica','ricarica','Ricarica');
  $m->addCampoList('ricaricamax','ricaricamax','Ricarica max');
  $m->addCampoList('ricaricamin','ricaricamin','Ricarica min');
  $m->addCampoList('ricaricadettaglio','ricaricadettaglio','Ricarica Dettaglio');
  $m->addCampoList('ricaricadettagliomax','ricaricadettagliomax','Ricarica Dettaglio max');
  $m->addCampoList('ricaricadettagliomin','ricaricadettagliomin','Ricarica Dettaglio min');
  $m->addCampoList('arrotondamento','arrotondamento','Arrotondamento');
  $m->addCampoList('arrotondamentodettaglio','arrotondamentodettaglio','Arrotondamento Dettaglio');
  $m->addCampoList('isdefault','isdefault','Campo default se non selezionato');
?>
