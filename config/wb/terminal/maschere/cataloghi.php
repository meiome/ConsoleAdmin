<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('cataloghi','catalogo','Maschera Cataloghi','main',array('orderby'=>'name','descasc'=>'asc','cartella' => 'catalogo'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('name','name','Nome Catalogo');
  $m->addCampo('description','description','Descrizione estesa catalogo');//nome univoco campo, nome campo database, Etichetta
  $m->addCampo('nomelaterale','nomelaterale','Etichetta Laterale Pagina');
  $m->addCampo('color','color','Color Pagina');
  $m->setCampoNamePrefixForList('name',array('name','n','nome'),'like',"'%","%'");//setta il campo per il filtro name in modo che sia specificato quale è per il terminale
    $s = new WB_Maschera('catalogo_elemento','catalogo_elemento','Elemento Catalogo','sub',array('orderby'=>'pagenumber asc ,ordine asc'));//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub', array option (orderby,descasc)
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addSingleRelation('elemento_id','elemento_id','Tipo Elemento*','name',array('@divcampowidth'=>'10%'));
    $s->addSingleRelation('article_id','article_id','Articolo di Riferimento','description',array('@addnonselezionato'=>'Non Selezionato','@divcampowidth'=>'15%','whereand'=>'COALESCE(disable,0) = 0'));
    $s->addCampoFile('filename','filename','File Foto (seleziona per aggiungere o sostituire)',array('@divcampowidth'=>'20%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('description','description','Testo',array('@divcampowidth'=>'20%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('height','height','Altezza Elemento',array('@divcampowidth'=>'10%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('pagenumber','pagenumber','Numero Pagina');//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('ordine','ordine','Ordine',array('@divcampowidth'=>'5%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('catalogo_elemento','catalogo_id',$s,array('@linkwithidintoelement'=>'/terminal/maschera/select/cataloghidettagli/'));//nome univoco campo, nome campo id_riferimento maschera colegata riferimento relation, Etichetta, submaschera
?>
