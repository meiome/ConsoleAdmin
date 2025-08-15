<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('cataloghidettagli','catalogo_elemento','Maschera Dettagli Catalogo','main',array('orderby'=>'ordine','descasc'=>'asc','cartella' => 'catalogo'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampo('description','description','Descrizione Elemento Contenitore');//nome univoco campo, nome campo database, Etichetta
    $s = new WB_Maschera('catalogo_elementosub','catalogo_elemento','Elemento Dettaglio','sub',array('orderby'=>'ordine','descasc'=>'asc'));//nome univoco maschera, tabella di riferimento, se 'main' table o 'sub', array option (orderby,descasc)
    $s->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));
    $s->addSecurityFilter('azienda_id','getaziendaid');
    $s->addSingleRelation('elemento_id_sub','elemento_id','Tipo Elemento*','name',array('@divcampowidth'=>'10%'));
    $s->addSingleRelation('article_id','article_id','Articolo di Riferimento','description',array('@addnonselezionato'=>'Non Selezionato','@divcampowidth'=>'15%','whereand'=>'COALESCE(disable,0) = 0'));
    $s->addCampoFile('filename','filename','File Foto (seleziona per aggiungere o sostituire)',array('@divcampowidth'=>'30%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('description','description','Testo',array('@divcampowidth'=>'20%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampo('ordine','ordine','Ordine',array('@divcampowidth'=>'5%'));//nome univoco campo, nome campo database, Etichetta
    $s->addCampoManipolazione('aggiungielimina');
  $m->addSubMaschera('catalogoelemento_id','catalogoelemento_id',$s);//nome univoco campo, nome campo id_riferimento maschera colegata riferimento relation, Etichetta, submaschera
?>
