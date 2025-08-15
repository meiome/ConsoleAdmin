<?php
//config/wb/terminal/maschere.php

return array(
    'articoli' => array('articoli.php','role_articoli#0001'),//la chiave deve essere uguale al nome univoco della maschera.. il nome file può essere a piacere
    'cataloghi' => array('cataloghi.php','role_articoli#0001'),
    'cataloghidettagli' => array('cataloghidettagli.php','role_articoli#0001'),
    'presenze' => array('presenze.php','role_presenze#0001'),//clse
    'utenti' => array('utenti.php','role_admin#1111'),//clse
    'actor' => array('actor.php','role_clifor#0001'),//clse
    'politicaprezzi' => array('politica_prezzi.php','role_admin#1111'),//clse
    'sales' => array('sales.php','role_admin#1111'),//clse
    'keycassa' => array('keycassa.php','role_articoli#1111'),//clse
    'categorie' => array('categorie.php','role_articoli#1111'),//clse
    'articolisoloimmagini' => array('articolisoloimmagini.php','role_articoli#0001'),//clse
    'movimenticassa' => array('movimenticassa.php','role_statistiche#1111'),//clse
    'baseoraria' => array('baseoraria.php','role_presenze#1111'),//clse
);

//role_gestioneordini#1111;role_bolle#1111;role_articoli#1111;role_etichette#1111;role_terminal#0101;role_clifor#1111;role_presenze#1111
?>
