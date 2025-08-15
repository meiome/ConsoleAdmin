<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('articoli','article','Maschera Articoli','main',array('orderby'=>'id','descasc'=>'desc','cartella' => 'articoli'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('sendtomago','sendtomago','Invia a Mago');
  $m->setCampoNamePrefixForList('sendtomago',array('sendtomago','m','mago','s','issendtomago'),'=',"","");
  $m->addCampoList('disable','disable','Disabilitato');//nome univoco campo, nome campo database, Etichetta
  $m->addCampoList('description','description','Nome Articolo');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('description',array('name','n','nome'),'like',"'%","%'");//setta il campo per il filtro name in modo che sia specificato quale è per il terminale
  $m->setCampoNamePrefixForList('disable',array('disable','d'),'=',"","");
  $m->addSingleRelation('taxcode_id','taxcode_id','Codice Iva','description',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
  $m->addSingleRelation('um_id','Um_id','Unità di misura vendita','name',array('@addnonselezionato'=>'Non Selezionato *'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('lotobligation','lotobligation','Obbligo inserimento lotto');//nome univoco campo, nome campo database, Etichetta
  $m->addSingleRelation('father_id','father_id','Articolo Padre','description',array('@addnonselezionato'=>'Non Selezionato *','whereand'=>'disable = false'));
  $m->addSeparatorLabel('Specifiche Vendita al Minuto','Specifiche Vendita al Minuto');
  $m->addCampoList('print','print','Stampa Etichetta');
  $m->addSingleRelation('umqtaE','Umforweightexposition_id','Unità misura ESPOSIZIONE €/℮','name',array('whereand'=>'enabledforweightexposition = true'));
  $m->addCampoList('netweight','netweight','Peso/Volume Netto (IN GRAMMI o MILLILITRI)');
    $m->addSeparatorLabel('Gestione Prezzi Articolo','Gestione Prezzi Articolo');
    $s = new WB_Maschera('price_supplier','price_supplier','Prezzi Di Acquisto','sub',array('orderby'=>'datestart','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub'
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addSingleRelation('supplier_id','supplier_id','Fornitore','name',array('whereand'=>'COALESCE(disable,0) = 0 and COALESCE(issupplier,0) = 1'));
    $s->addCampo('datestart','datestart','Data Inizio');
    $s->addCampo('price','price','Prezzo',array('@divcampowidth'=>'45%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('discountcalc','discountcalc','Sconto Calcolato');
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('price_supplier','article_id',$s);
    $s = new WB_Maschera('price_list','price_list','Listini di Vendita','sub',array('orderby'=>'price','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub', array option (orderby,descasc)
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addCampo('datatimecreated','datatimecreated','Data Crezione');
    $s->addSingleRelation('listname_id','listname_id','Listino','description',array('whereand'=>'COALESCE(disable,0) = 0'));
    $s->addCampo('price','price','Prezzo',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('discountcalc','discountcalc','Sconto Calcolato');
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('price_list','article_id',$s);//nome univoco campo, nome campo id_riferimento maschera colegata riferimento relation, Etichetta, submaschera
  $m->addSeparatorLabel('Specifiche Bilancia Dettaglio','Specifiche Bilancia Dettaglio');
  $m->addCampoList('tastobilancia','tastobilancia','Numero Tasto Bilancia (massimo 999) deve essere univoco');
  $m->addCampoList('tarabilancia','tarabilancia','Tara bilancia in grammi (massimo 20)');
  $m->addCampoList('scadenzabilanciaingg','scadenzabilanciaingg','Numero giorni scadenza da confezionamento');
  $m->addCampoList('descr_articolo_bilancia','descr_articolo_bilancia','Nome articolo specifico Bilancia (Se presente sovrascrive nome articolo)');
  $m->addCampoList('sottotitoloarticolobilancia','sottotitoloarticolobilancia','Sottotitolo Presentazione specifico etichetta bilancia (esempio grassetto: $testo in grasseto$)');
  $m->addCampoList('ingredientibilancia','ingredientibilancia','Ingredienti per etichetta bilancia');
  $s = new WB_Maschera('immagine','immagine','Immagini','sub',array());//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub', array option (orderby,descasc)
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addCampoFile('filename','filename','File Foto (seleziona per aggiungere o sostituire)',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('ordine','ordine','Ordine',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('immagine','article_id',$s);//nome univoco campo, nome campo id_riferimento maschera colegata riferimento relation, Etichetta, submaschera
?>
