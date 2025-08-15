<?php
//config/wb/server/serverconfig.php

return array(
    'server_id' => '1',
    'debug' => true,
    //dbmariadb
    'db' => 'nomedb',
    'user' => 'userdb',
    'password' => 'lamiapassword',
    'databasehost' => '127.0.0.1',
    //login
    'max_errologin' => 15,//errori massimi al login per utente prima di disabilitare l'utente
    //directory per upload file
    'filedir' => array('articoli','catalogo'),//nomi delle cartelle nelle quali è consentito il salvataggio da terminal/gestorefile (maschera)
);

?>