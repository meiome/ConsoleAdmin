<?php

use App\php\wb_database\WB_Table;
/* ======================= E-commerce ================================= */

/*questa struttura del database è quella di un mio progetto non serve conservarla tutta*/

$table = new WB_Table('tbl_server');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]); //non è auto_increment perchè è quello vero
//$table->addField('urlname', 'INT', ['not_null'=>true]);
$table->addField('urlname', 'VARCHAR', ['not_null' => true]);
$table->addField('isthis', 'BOOLEAN');
$table->addField('isreverseproxyserver', 'BOOLEAN');
$table->addField('isecommerceserver', 'BOOLEAN');
$table->addField('isslaveadminserver', 'BOOLEAN');
$table->addField('ismasteradminserver', 'BOOLEAN');
$table->addField('privatetoken', 'VARCHAR');
$table->addField('lastupdate', 'DATETIME', ['not_null' => false]); //contiene la data ultimo aggiornamento prezzi,articoli,clienti
$table->addField('ts', 'TIMESTAMP');
$this->addTable($table);

$table = new WB_Table('tbl_identifier');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('identifier', 'VARCHAR', ['not_null' => true]);
$table->addField('previousidentifier', 'VARCHAR'); //
$table->addField('ts', 'TIMESTAMP');
$table->setRelation('server_id', 'tbl_server', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_utente');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('doubleauthentication', 'BOOLEAN', ['not_null' => true]); //abilita la doppia autenticazione con conferma codice tramite email
$table->addField('doubleauthenticationemail', 'VARCHAR', ['not_null' => false]); //se è null il codice viene inviato alla mail del campo email altrimenti a questa
$table->addField('doubleauthenticationcode', 'VARCHAR', ['not_null' => false]); //penso lo stesso campo verrà utilizzato anche per la registrazione
$table->addField('email', 'VARCHAR', ['not_null' => true]);
$table->addField('password', 'VARCHAR', ['not_null' => true]);
$table->addField('errorlogin', 'INT', ['not_null' => true]);
$table->addField('errordoubleauthentication', 'INT', ['not_null' => true]);
$table->addField('nomecognome', 'VARCHAR', ['not_null' => true]);
$table->addField('role', 'VARCHAR', ['not_null' => true]);
$table->addField('tipologia', 'VARCHAR', ['not_null' => false]); //esempio business o privato
$table->addField('urldefault', 'VARCHAR', ['not_null' => false]); //url default after login
$table->addField('ts', 'TIMESTAMP');
$table->addField('cassa_id', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$table->setRelation('cassa_id', 'tbl_cassa', 'id'); //cassa utilizzata di defualt per l'utente (solo una per utente possibile)
$this->addTable($table);

$table = new WB_Table('tbl_utente_spedizone');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('utente_id', 'INT'); //nullable in caso di carrello sloggato
$table->addField('ts', 'TIMESTAMP');
$table->addField('name', 'LONGTEXT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_account'); //serve a vedere quali account mostrare per browser alla login
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('utente_id', 'INT', ['not_null' => true]);
$table->addField('identifier_id', 'INT', ['not_null' => true]);
$table->addField('lastaccessrequest', 'DATETIME');
$table->addField('lastaccessrequestidentifier_id', 'INT', ['not_null' => false]);
$table->addField('isconnected', 'BOOLEAN', ['not_null' => true]);
$table->addField('doubleauthtested', 'BOOLEAN', ['not_null' => true]);
$table->addField('lastcheckpassword', 'DATETIME', ['not_null' => false]); //contiene l'ultima data di verifica password corretta
$table->addField('ts', 'TIMESTAMP');
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$table->setRelation('identifier_id', 'tbl_identifier', 'id');
$table->setRelation('lastaccessrequestidentifier_id', 'tbl_identifier', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_log');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('tipo', 'VARCHAR', ['not_null' => true]); //staccato identifier , aggiunta articolo , navigazione
$table->addField('identifier_id', 'INT', ['not_null' => true]);
$table->addField('utente_id', 'INT'); //nullable in caso l'utente non voglia essere tracciato dai cookie tecnici  o non sia loggato
$table->addField('account_id', 'INT'); //nullable in caso l'utente non voglia essere tracciato dai cookie tecnici o non sia loggato
$table->addField('url', 'VARCHAR', ['not_null' => true]);
$table->addField('ip', 'VARCHAR', ['not_null' => true]);
$table->addField('ts', 'TIMESTAMP');
$table->setRelation('server_id', 'tbl_server', 'id');
$table->setRelation('identifier_id', 'tbl_identifier', 'id');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$table->setRelation('account_id', 'tbl_account', 'id');
$this->addTable($table);

$table = new WB_Table('azienda');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('ragionesociale', 'VARCHAR', ['not_null' => true]);
$this->addTable($table);

$table = new WB_Table('tbl_locker');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('utente_id', 'INT', ['not_null' => true]);
$table->addField('richiesta', 'VARCHAR', ['not_null' => true]);//tipo di richiesta... possono essere di due tipi (preaut e aut)
$table->addField('tabella', 'VARCHAR', ['not_null' => true]);
$table->addField('tabella_id', 'INT', ['not_null' => true]);
$table->addField('datestart', 'DATETIME', ['not_null' => true]);
$table->addField('dateend', 'DATETIME');
$table->addField('note', 'VARCHAR');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_carrello');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('identifier_id', 'INT', ['not_null' => true]);
$table->addField('utente_id', 'INT'); //nullable in caso di carrello sloggato
$table->addField('utente_spedizone_id', 'INT'); //lo uso nel completamento della procedura di ordine
$table->addField('carrellotype', 'VARCHAR', ['not_null' => true]);
$table->addField('actor_id', 'INT');
$table->addField('actordomicile_id', 'INT');
$table->addField('ts', 'TIMESTAMP');
$table->addField('note', 'LONGTEXT');
$table->addField('stato', 'VARCHAR');
$table->addField('utente_lock_id', 'INT');
$table->addField('utente_lock_datetime', 'DATETIME');
$table->addField('colli', 'INT');
$table->addField('scheduledelivery', 'VARCHAR');
$table->addField('dataconsegna', 'DATE');
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$table->setRelation('identifier_id', 'tbl_identifier', 'id');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$table->setRelation('utente_lock_id', 'tbl_utente', 'id');
$table->setRelation('utente_spedizone_id', 'tbl_utente_spedizone', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_carrello_righe');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('carrello_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('um_id', 'INT', ['not_null' => true]);
$table->addField('qta', 'DOUBLE', ['not_null' => true]);
$table->addField('note', 'VARCHAR');
$table->addField('price', 'DOUBLE');
$table->addField('discountsee', 'VARCHAR');
$table->addField('discountcalc', 'DOUBLE');
$table->addField('lot', 'VARCHAR');
$table->addField('lineisdone', 'BOOLEAN');//flag che serve a mettere la spunta V sulla riga come evasa/preparata nel caso di spesa preparata manualmente
$table->addField('assignto_carrello_id', 'INT');//(vale per gli ordini cliente e fornitore)serve a segnalare a quale spesa è stata assegnata quella riga ordine
$table->addField('carrello_superrow_id', 'INT');//serve a vedere a quale superrow viene assegnato quell'ordine o quella spesa è un pò per capire quale riga spesa ha evaso euella riga ordine
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('carrello_id', 'tbl_carrello', 'id');
$table->setRelation('carrello_superrow_id', 'tbl_carrello_superrow', 'id');
$table->setRelation('article_id', 'article', 'id');
$table->setRelation('um_id', 'um', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_carrello_superrow');//serve a collegare le righe ordine (cliente e fornitore) con la riga spesa (cliente e fornitore) che l'ha evasa
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('carrello_id', 'INT', ['not_null' => true]);//a quale spesa (cliente o fornitore è riferita questa superrow)
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('carrello_id', 'tbl_carrello', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_lavoratore');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('nome', 'VARCHAR', ['not_null' => true]);
$table->addField('cognome', 'VARCHAR', ['not_null' => true]);
$table->addField('badge', 'VARCHAR', ['not_null' => true]);
$table->addField('tipocontratto', 'VARCHAR', ['not_null' => false]);
$table->addField('datainizio', 'DATE', ['not_null' => false]);
$table->addField('datafine', 'DATE', ['not_null' => false]);
$table->addField('ts', 'TIMESTAMP');
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_lavoratore_baseoraria');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('lavoratore_id', 'INT', ['not_null' => true]);
$table->addField('datainizio', 'DATE', ['not_null' => true]);
$table->addField('lunedi', 'DOUBLE', ['not_null' => true]);
$table->addField('martedi', 'DOUBLE', ['not_null' => true]);
$table->addField('mercoledi', 'DOUBLE', ['not_null' => true]);
$table->addField('giovedi', 'DOUBLE', ['not_null' => true]);
$table->addField('venerdi', 'DOUBLE', ['not_null' => true]);
$table->addField('sabato', 'DOUBLE', ['not_null' => true]);
$table->addField('domenica', 'DOUBLE', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('lavoratore_id', 'tbl_lavoratore', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_presenze');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('server_id', 'INT', ['not_null' => true]);
$table->addField('lavoratore_id', 'INT', ['not_null' => true]);
$table->addField('datatime', 'DATETIME', ['not_null' => true]);
$table->addField('tipo', 'VARCHAR', ['not_null' => true]);
$table->addField('note', 'VARCHAR');
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('lavoratore_id', 'tbl_lavoratore', 'id');
$table->setRelation('server_id', 'tbl_server', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_utente_actor'); //questa tabella contiene le abilitazione per ogni utente Business o Dei dipendenti dell'azienda, dei clienti per i quali può inserire ordini
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('utente_id', 'INT', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => true]);
$table->addField('actordomicile_id', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('utente_id', 'tbl_utente', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_cassa');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('ip', 'VARCHAR', ['not_null' => true]);
$table->addField('matricola', 'VARCHAR', ['not_null' => true]);
$table->addField('note', 'LONGTEXT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('tbl_movimenti_cassa');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('cassa_id', 'INT', ['not_null' => true]);
$table->addField('linetipo', 'VARCHAR', ['not_null' => true]);
$table->addField('datatime', 'DATETIME', ['not_null' => true]);
$table->addField('importo', 'DOUBLE', ['not_null' => false]);
$table->addField('note', 'LONGTEXT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('cassa_id', 'tbl_cassa', 'id');
$this->addTable($table);

$table = new WB_Table('um');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => false]);
$table->addField('nameforexport', 'VARCHAR', ['not_null' => true]);
$table->addField('enabledforweightpricemoltiplication', 'BOOLEAN', ['not_null' => true]);
$table->addField('enabledfordetailsell', 'BOOLEAN', ['not_null' => true]);
$table->addField('enabledforweightexposition', 'BOOLEAN', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('tax_code');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('code', 'DOUBLE', ['not_null' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('cashdepartment', 'VARCHAR', ['not_null' => true]);
$table->addField('codeexport', 'VARCHAR', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('historical_sales_order');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => true]);
$table->addField('actordomicile_id', 'INT', ['not_null' => false]); //nullable
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('datetimelastmod', 'DATETIME', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);

$table = new WB_Table('um_sales_order');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('um_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('saleqtyforoneumsalesorder', 'DOUBLE', ['not_null' => false]);
$table->addField('turnintosalesqty', 'BOOLEAN', ['not_null' => true]);
$table->addField('notshowinweb', 'BOOLEAN', ['not_null' => false]);
$table->addField('notshowinsalesorder', 'BOOLEAN', ['not_null' => false]);
$table->addField('notshowonbusinesscli', 'BOOLEAN', ['not_null' => false]);
$table->addField('notshowonorderfornitori', 'BOOLEAN', ['not_null' => false]);
$table->addField('percentualeerrore', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('um_id', 'um', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);

$table = new WB_Table('actor');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('issupplier', 'BOOLEAN', ['not_null' => true]);
$table->addField('iscustomer', 'BOOLEAN', ['not_null' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('sendtomago', 'BOOLEAN', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('actor_domicile');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$this->addTable($table);

$table = new WB_Table('article');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('sendtomago', 'BOOLEAN', ['not_null' => false]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('taxcode_id', 'INT', ['not_null' => true]);
$table->addField('Um_id', 'INT', ['not_null' => true]);
$table->addField('code', 'INT', ['not_null' => false]);
$table->addField('codeprefix', 'VARCHAR', ['not_null' => false]);
$table->addField('lotobligation', 'BOOLEAN', ['not_null' => false]);
$table->addField('father_id', 'INT', ['not_null' => false]);
$table->addField('print', 'BOOLEAN', ['not_null' => false]);
$table->addField('Umforweightexposition_id', 'INT');
$table->addField('netweight', 'DOUBLE', ['not_null' => false]);
$table->addField('tastobilancia', 'INT', ['not_null' => false]);
$table->addField('tarabilancia', 'INT', ['not_null' => false]);
$table->addField('scadenzabilanciaingg', 'INT', ['not_null' => false]);
$table->addField('descr_articolo_bilancia', 'VARCHAR', ['not_null' => false]);
$table->addField('sottotitoloarticolobilancia', 'VARCHAR', ['not_null' => false]);
$table->addField('pezzipercartone', 'VARCHAR', ['not_null' => false]);
$table->addField('ingredientibilancia', 'LONGTEXT', ['not_null' => false]);
$table->addField('posizione_id', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('taxcode_id', 'tax_code', 'id');
$table->setRelation('posizione_id', 'posizione', 'id');
$table->setRelation('Um_id', 'um', 'id');
$table->setRelation('father_id', 'article', 'id');
$table->setRelation('Umforweightexposition_id', 'um', 'id');
$this->addTable($table);

$table = new WB_Table('sales_order');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => true]);
$table->addField('actordomicile_id', 'INT', ['not_null' => true]);
$table->addField('user_id', 'INT', ['not_null' => false]);
$table->addField('salesordertipo_id', 'INT', ['not_null' => true]);
$table->addField('elaborated', 'BOOLEAN', ['not_null' => false]);
$table->addField('reported', 'BOOLEAN', ['not_null' => false]);
$table->addField('sended', 'BOOLEAN', ['not_null' => false]);
$table->addField('note', 'VARCHAR', ['not_null' => false]);
$table->addField('datasend', 'DATETIME', ['not_null' => false]);
$table->addField('datadelivery', 'DATE', ['not_null' => false]);
$table->addField('userlocked_id', 'INT', ['not_null' => false]);
$table->addField('lastlocked', 'DATETIME', ['not_null' => false]);
$table->addField('complete', 'BOOLEAN', ['not_null' => false]);
$table->addField('scheduledelivery_id', 'INT', ['not_null' => false]);
$table->addField('colli', 'INT', ['not_null' => false]);
$table->addField('server_id', 'INT', ['not_null' => false]);
$table->addField('masterserverdatealignment', 'DATETIME', ['not_null' => false]);
$table->addField('ismasterserveralignment', 'BOOLEAN', ['not_null' => false]);
$table->addField('linkedserversalesorderid', 'INT', ['not_null' => false]); //id che ha l'ordine sul server remoto nel quale è stato inserito
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$table->setRelation('user_id', 'user', 'id');
$table->setRelation('salesordertipo_id', 'sales_order_tipo', 'id');
$this->addTable($table);

$table = new WB_Table('sales_order_detail');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('salesorder_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('um_id', 'INT', ['not_null' => true]);
$table->addField('qta', 'LONGTEXT', ['not_null' => true]);
$table->addField('note', 'VARCHAR', ['not_null' => false]);
$table->addField('lot', 'VARCHAR', ['not_null' => false]);
$table->addField('evasoby_id', 'INT', ['not_null' => false]);
$table->addField('evaso', 'BOOLEAN', ['not_null' => false]);
$table->addField('assignedto_id', 'INT', ['not_null' => false]);
$table->addField('linkedserversalesorderdetailid', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('salesorder_id', 'sales_order', 'id');
$table->setRelation('article_id', 'article', 'id');
$table->setRelation('um_id', 'um', 'id');
$this->addTable($table);

$table = new WB_Table('sales_order_tipo');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('isorder', 'BOOLEAN', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('names', 'VARCHAR', ['not_null' => true]);
$table->addField('desinenzaaggettivo', 'VARCHAR', ['not_null' => true]);
$table->addField('bgcolor', 'VARCHAR', ['not_null' => true]);
$table->addField('widthricerca', 'INT', ['not_null' => false]);
$table->addField('widthordiniincorso', 'INT', ['not_null' => false]);
$table->addField('fontsizericerca', 'INT', ['not_null' => false]);
$table->addField('fontsizeordiniincorso', 'INT', ['not_null' => false]);
$table->addField('fontsizecarrello', 'INT', ['not_null' => false]);
$table->addField('widthcarrello', 'INT', ['not_null' => false]);
$table->addField('fromorder', 'BOOLEAN', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('user');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => false]);
$table->addField('actordomicile_id', 'INT', ['not_null' => false]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('email', 'VARCHAR', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$this->addTable($table);

$table = new WB_Table('immagine');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('filename', 'VARCHAR', ['not_null' => false]);
$table->addField('ordine', 'INT', ['not_null' => false]);
$table->addField('article_id', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);

$table = new WB_Table('list_name');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('code', 'VARCHAR', ['not_null' => true]);
$table->addField('isdetailprice', 'BOOLEAN', ['not_null' => true]);
$table->addField('iswebdetailprice', 'BOOLEAN', ['not_null' => true]);
$table->addField('main', 'BOOLEAN', ['not_null' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('price_list');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('listname_id', 'INT', ['not_null' => true]);
$table->addField('datatimecreated', 'DATETIME', ['not_null' => false]);
$table->addField('price', 'DOUBLE', ['not_null' => true]);
$table->addField('discountcalc', 'DOUBLE', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('listname_id', 'list_name', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);

$table = new WB_Table('price_supplier');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('datatimecreated', 'DATETIME');
$table->addField('datestart', 'DATE', ['not_null' => true]);
$table->addField('price', 'DOUBLE', ['not_null' => true]);
$table->addField('discountcalc', 'DOUBLE', ['not_null' => true]);
$table->addField('supplier_id', 'INT', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('supplier_id', 'actor', 'id');
$this->addTable($table);

$table = new WB_Table('categorie');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('categorie_id', 'INT', ['not_null' => false]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('level', 'INT', ['not_null' => false]);
$table->addField('ordine', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('categorie_id', 'categorie', 'id');
$this->addTable($table);

$table = new WB_Table('catalogo');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('description', 'LONGTEXT', ['not_null' => false]);
$table->addField('nomelaterale', 'VARCHAR', ['not_null' => false]);
$table->addField('color', 'VARCHAR', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('elemento'); //elemento cioè modo in cui viene disposto il layout dell'articolo
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('name', 'VARCHAR', ['not_null' => true]);
$table->addField('height', 'VARCHAR', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('catalogo_elemento');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('filename', 'VARCHAR', ['not_null' => false]);
$table->addField('description', 'LONGTEXT', ['not_null' => false]);
$table->addField('height', 'VARCHAR', ['not_null' => false]);
$table->addField('ordine', 'INT', ['not_null' => false]);
$table->addField('pagenumber', 'INT', ['not_null' => false]);
$table->addField('catalogo_id', 'INT', ['not_null' => false]); //non è null perchè nel caso di catalogoelementi di catalogoelementi specifico solo catalogoelemento_id- perchè non è legato direttamente a catologo ma ad un altro catalogoelemento (a se stesso come tabella ma un altra istanza)
$table->addField('elemento_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => false]);
$table->addField('catalogoelemento_id', 'INT', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('catalogo_id', 'catalogo', 'id');
$table->setRelation('elemento_id', 'elemento', 'id');
$table->setRelation('article_id', 'article', 'id');
$table->setRelation('catalogoelemento_id', 'catalogo_elemento', 'id');
$this->addTable($table);

$table = new WB_Table('politica_prezzi');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => true]);
$table->addField('ricarica', 'DOUBLE', ['not_null' => true]);
$table->addField('ricaricamin', 'DOUBLE', ['not_null' => true]);
$table->addField('ricaricamax', 'DOUBLE', ['not_null' => true]);
$table->addField('ricaricadettaglio', 'DOUBLE', ['not_null' => true]);
$table->addField('ricaricadettagliomin', 'DOUBLE', ['not_null' => true]);
$table->addField('ricaricadettagliomax', 'DOUBLE', ['not_null' => true]);
$table->addField('arrotondamento', 'DOUBLE', ['not_null' => true]);
$table->addField('arrotondamentodettaglio', 'DOUBLE', ['not_null' => true]);
$table->addField('isdefault', 'BOOLEAN', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$this->addTable($table);

$table = new WB_Table('sales');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('actor_id', 'INT', ['not_null' => false]);
$table->addField('actordomicile_id', 'INT', ['not_null' => false]);
$table->addField('utente', 'INT', ['not_null' => true]);
$table->addField('salestipo', 'VARCHAR', ['not_null' => true]);
$table->addField('datasend', 'DATETIME', ['not_null' => true]);
$table->addField('documentdate', 'DATETIME', ['not_null' => true]);
$table->addField('note', 'VARCHAR', ['not_null' => false]);
$table->addField('taxableamount', 'DOUBLE', ['not_null' => true]);
$table->addField('taxamount', 'DOUBLE', ['not_null' => true]);
$table->addField('totalamount', 'DOUBLE', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('actor_id', 'actor', 'id');
$table->setRelation('actordomicile_id', 'actor_domicile', 'id');
$this->addTable($table);

$table = new WB_Table('sales_detail');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('sales_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => false]);
$table->addField('um_id', 'INT', ['not_null' => true]);
$table->addField('qta', 'DOUBLE', ['not_null' => true]);
$table->addField('note', 'VARCHAR', ['not_null' => false]);
$table->addField('taxcode_id', 'INT', ['not_null' => false]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('price', 'DOUBLE', ['not_null' => true]);
$table->addField('discountcalc', 'DOUBLE', ['not_null' => true]);
$table->addField('discountsee', 'VARCHAR', ['not_null' => false]);
$table->addField('taxableamount', 'DOUBLE', ['not_null' => true]);
$table->addField('taxamount', 'DOUBLE', ['not_null' => true]);
$table->addField('totalamount', 'DOUBLE', ['not_null' => true]);
$table->addField('linetipo', 'VARCHAR', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('sales_id', 'sales', 'id');
$table->setRelation('article_id', 'article', 'id');
$table->setRelation('um_id', 'um', 'id');
$table->setRelation('taxcode_id', 'tax_code', 'id');
$this->addTable($table);

$table = new WB_Table('key_cassa');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('disable', 'BOOLEAN', ['not_null' => false]);
$table->addField('ordine', 'INT', ['not_null' => false]);
$table->addField('backgroundcolor', 'VARCHAR', ['not_null' => false]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);


$table = new WB_Table('filtro');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('isfiltroproduttore', 'BOOLEAN');
$table->addField('isfiltrocategorie', 'BOOLEAN');
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('categorie_id', 'INT');
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('testo', 'LONGTEXT');
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('categorie_id', 'categorie', 'id');
$this->addTable($table);

$table = new WB_Table('tag');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('azienda_id', 'INT', ['not_null' => true]);
$table->addField('filtro_id', 'INT',  ['not_null' => true]);
$table->addField('article_id', 'INT', ['not_null' => true]);
$table->setRelation('azienda_id', 'azienda', 'id');
$table->setRelation('filtro_id', 'filtro', 'id');
$table->setRelation('article_id', 'article', 'id');
$this->addTable($table);

$table = new WB_Table('posizione');
$table->addField('id', 'INT', ['not_null' => true, 'primary_key' => true, 'auto_increment' => true]);
$table->addField('description', 'VARCHAR', ['not_null' => true]);
$table->addField('ordine', 'INT');
$this->addTable($table);
