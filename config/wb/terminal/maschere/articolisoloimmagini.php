<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('articolisoloimmagini','article','Maschera Articoli','main',array('orderby'=>'id','descasc'=>'desc','cartella' => 'articoli'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('disable','disable','Disabilitato');//nome univoco campo, nome campo database, Etichetta
  $m->addCampoList('description','description','Nome Articolo');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('description',array('name','n','nome'),'like',"'%","%'");//setta il campo per il filtro name in modo che sia specificato quale è per il terminale
  $m->setCampoNamePrefixForList('disable',array('disable','d'),'=',"","");
  $s = new WB_Maschera('immagine','immagine','Immagini','sub',array());//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub', array option (orderby,descasc)
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addCampoFile('filename','filename','File Foto (seleziona per aggiungere o sostituire)',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('ordine','ordine','Ordine',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('immagine','article_id',$s);//nome univoco campo, nome campo id_riferimento maschera colegata riferimento relation, Etichetta, submaschera
?>
