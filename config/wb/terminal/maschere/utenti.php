<?php
  use App\php\wb_terminal\WB_Maschera;

  $m = new WB_Maschera('utenti','tbl_utente','Maschera Utenti','main',array('orderby'=>'id','descasc'=>'desc'));//nome univoco maschera, tabella di riferimento, 'main' as default value
  $this->terminal->addMaschera($m);//va fatto qui perchè così poi posso passare il database anche ai campi
  //$m->addSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array());//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addSecurityFilter('azienda_id','getaziendaid');
  $m->addShadowSingleRelation('azienda_id','azienda_id','Azienda','ragionesociale',array('setshadowsinglerelationbyfunction'=>'getaziendaid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addShadowSingleRelation('server_id','server_id','Server','urlname',array('setshadowsinglerelationbyfunction'=>'getserverid'));//nome univoco campo, nome campo database, Etichetta, nome campo tabella collegata visualizzato in scelta, arrayoption (con @ davanti sono visibili anche lato client)
  $m->addCampoList('disable','disable','Disabilitato');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('disable',array('disable','d'),'=',"","");
  $m->addCampoList('email','email','Email');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('email',array('email','e'),'like',"'%","%'");
  $m->addCampoList('nomecognome','nomecognome','Nome e Cognome');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('nomecognome',array('name','n','nome'),'like',"'%","%'");
  $m->addCampo('password','password','Password');//nome univoco campo, nome campo database, Etichetta
  $m->addCampoList('tipologia','tipologia','Tipologia');//nome univoco campo, nome campo database, Etichetta
  $m->setCampoNamePrefixForList('tipologia',array('tipo','t','tipologia'),'like',"'%","%'");
  $m->addCampo('urldefault','urldefault','Urldefault');
  $m->addCampo('role','role','role');
  $m->addCampo('errorlogin','errorlogin','Errori login');
  $m->addCampo('doubleauthentication','doubleauthentication','Richiesta Doppia Autenticazione');
  $m->addCampo('doubleauthenticationemail','doubleauthenticationemail','Sovrascrivi Email Doppia Autenticazione');
  $m->addCampo('doubleauthenticationcode','doubleauthenticationcode','Codice Doppia Autenticazione');
  $m->addCampo('errordoubleauthentication','errordoubleauthentication','Errori Doppia Autenticazione');


?>
