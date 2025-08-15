<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('sales','sales','Maschera Vendite','main',array('orderby'=>'id','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('salestipo','salestipo','Tipo Vendita');//nome univoco campo, nome campo database, Etichetta
  $m->addCampo('documentdate','documentdate','Data Documento');
  $m->addCampo('datasend','datasend','Data Inserimento');
  $m->addCampo('note','note','Note');
  $m->addCampo('taxableamount','taxableamount','Taxableamount');
  $m->addCampo('taxamount','taxamount','Taxamount');
  $m->addCampo('totalamount','totalamount','Totalamount');
  $m->setCampoNamePrefixForList('salestipo',array('t','tipo'),'like',"'%","%'");//setta il campo per il filtro name in modo che sia specificato quale è per il terminale
  $m->addSingleRelation('actor_id','actor_id','Destinatario','name',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
  $m->addSingleRelation('actordomicile_id','actordomicile_id','Sese Spedizione Destinatario','name',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
  $m->addSeparatorLabel('Righe Documento','Righe Documento');
    $s = new WB_Maschera('sales_detail','sales_detail','Rige Documento','sub',array('orderby'=>'id','descasc'=>'asc'));//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub'
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addCampo('linetipo','linetipo','Tipo Linea');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('description','description','Description');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('note','note','Note');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('qta','qta','Quantità');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('price','price','Prezzo');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('discountcalc','discountcalc','Sconto Calcolato');
    $s->addCampo('discountsee','discountsee','Sconto Visivo');
    $s->addSingleRelation('article_id','article_id','Articolo','description',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
    $s->addSingleRelation('taxcode_id','taxcode_id','Codice Iva','description',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
    $s->addSingleRelation('um_id','um_id','Unità di misura vendita','name',array('@addnonselezionato'=>'Non Selezionato *'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
    $s->addCampo('taxableamount','taxableamount','Taxableamount');
    $s->addCampo('taxamount','taxamount','Taxamount');
    $s->addCampo('totalamount','totalamount','Totalamount');
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('sales_detail','sales_id',$s);
?>
