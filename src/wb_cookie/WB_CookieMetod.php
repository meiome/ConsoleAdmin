<?php

namespace App\php\wb_cookie;
/*
 * Authore: Andrea Bettiol
 *
 *
 * Class: WB_CookieMetod
 *
 * Version: 0.1
 *
 *questo sistema permette di gestire lo stesso utente su più browser come utenti diversi.. in questo modo se si vuole si può possono gestire diversi browsere con lo stesso utente aperti su browsere/pc diversi come diversi.. questo può permettere di fare 2 acquisti su ecommerce da postazioni diverse senza che si intralcino nell'acquisto ma mantenendo il carrello lato server.
 *
 * Ci sono 2 cookie, uno il COOKIE_ACCOUNT_SHOW serve a memorizzare nel database gli account che sono stati loggati in quel computer. una volta che * * hanno superato correttamente il login vengono aggiunti a quelli disponibili nella pagina scegli account
 * Ci sono 2 campi importanti a database nella tabella tbl_account per capire il funzionamento di questi metodi:
 * 1-> identifier_id che memorizza il cookie (è una stringa denominata identifier salvato nella tabella tbl_identifier) relativo agli account
 * c'è una riga in tbl_account per ogni account di ogni browser con lo stesso identifier_id (COOKIE_ACCOUNT_SHOW).. poi quando viene aggiunto un nuovo account allo stesso browser (dopo il login con utente diverso) il nuovo identifier_id che viene staccato e inserito nella tabella tbl_account per il nuovo account loggato. In pratica una volta andato a buon fine il login quello stesso identifier_id viene aggiornato anche sugli account che erano stati precedentemente loggati su quella browser e poi viene aggiornato anche il cookie sul browser

 *2->lastaccessrequestidentifier_id che serve per il la verifica password
 * il flag da utilizzare per vedere se un utente è loggato è in tbl_account è isconnected
 *
*/

use App\php\wb_cookie\WB_CookieResult;

//require_once($_SERVER['DOCUMENT_ROOT'].'/php/wb_cookie/WB_CookieResult.php');

class WB_CookieMetod
{


	private const COOKIE_ACCOUNT_SHOW = "w"; //nome del cookie per gli account nel browser
	private const COOKIE_PASSWORD_SHOW = "c"; //nome del cookie per la verifica password nel browser
	private $database;
	private $serverconfig;
	//private $cookieoutput = ['account' => 'd','accesso' => 'l','log' => 't','carrello' => 'w'];//vecchio modello
	private $cookieresult;
	private $logger;

	public function __construct($database, $serverconfig)
	{

		$this->logger = $GLOBALS["WB_LOGGER"];
		$this->database = $database;
		$this->serverconfig = $serverconfig;
		$this->cookieresult = new WB_CookieResult();
	}

	public function Esegui()
	{
	}

	public function ExecInstructionArrayCookie($arraycookie_instruction)
	{
		/*
		name -> nome del valore di ritorno
		entity -> riferimento per indicare tabella o insieme di tabelle
		method -> metodo che uso relativamente a quell'entity
		*/
		$this->logger->log('wb_cookie', 'ExecInstructionArrayCookie', 3);
		/*eseguo il method dell'array che gli ho passato*/
		foreach ($arraycookie_instruction as &$value) {
			switch ($value['method']) {
				case "getrole":
					$this->getrole($value);
					break;
				case "getall":
					$this->getAll($value);
					break;
				case "existsbyrelatedfield":
					$this->existsbyrelatedfield($value);
					break;
				case "createcookieforpasswordcheck":
					$this->createcookieforpasswordcheck($value);
					break;
				case "getlabeluserbycookierforpassword": //questo metodo è quello usato per visualizzare quale utente sta facendo il check della password
					$this->getlabeluserbycookierforpassword($value);
					break;
				case "removeaccount":
					$this->removeaccount($value);
					break;
				case "removecookiepassword":
					$this->removecookiepassword();
					break;
				case "verifypassword": //questo metodo verifica se la password passata con POST è corretta
					$this->verifypassword($value);
					break;
				case "logout":
					$this->logout($value);
					break;
				default:
					throw new \Exception('Non trovata nessuna corrispondenza per il cookie richiesto tra quelli mappati - errore istruzione method WB_CookieMetod');
			}
		};
		return $this->cookieresult;
	}


	private function removecookiepassword(): void
	{
		setcookie(self::COOKIE_PASSWORD_SHOW, "", time() - 3600); //rimuovo il cookie password (mi serve altrimenti se passo una mail errata mi dà l'ultima corretta che avevo passato precedentemente perchè il cookie restava con quel valore)
	}

	/* createcookieforpasswordcheck()
	Se è stato scelto account verificato e non è il caso di rimozione account si va a settare un cookie session che sarà utilizzato poi per la verifica password -> il cookie session lo stacco e lo salvo a db legando all'account scelto nel campo lastaccessrequestidentifier_id che mi lega all'account che sta effettivamente navigando
	Nel caso in cui l'account non sia ancora mai stato loggato prima (quindi accedi vs scegliaccount) su quel browser vado a staccare anche l'altro identifier che è quello w che serve alla lista account-- anche se lo stacco solo lato server senza andare a salvarlo sul browser (lo salvo solo una volta completata la verifica password (se però ci sono già altri account su quel browser devo sovrascrivere su qull'account il cookie w che avevo già nel browser... altrimenti salvo quello nuovo -> quindi dovrò controllare se è già stato staccato un w che sia coerente e presente nel server con altri account altrimenti uso quello staccato in precendenza e lo setto anche nei cookie))
	Quindi sono pronto a verificare la password.. nel controller /passwordcheck andrò a tirare fuori tramite il cookie session/identifier l'account con quell'identifier campo lastaccessrequestidentifier_id*/

	private function createcookieforpasswordcheck($cookie_instruction): void
	{

		$errore = false;

		if (isset($_POST['email'])) {
			$this->logger->log('wb_cookie', 'createcookieforpasswordcheck->isset', 3);
			$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i"; //per la mail
			if (preg_match($pattern, $_POST['email'])) {
				$this->logger->log('wb_cookie', 'createcookieforpasswordcheck->preg_match ok', 3);
				/*CON $risultato verifica se la mail è presente tra quelle del browser così posso usare la stessa procedura sia per ACCEDI che per SCEGLIACCOUNT*/
				$risultato = $this->existsbyrelatedfield($cookie_instruction, false);

				/*is_null($risultato) == false controlla che ci sia un risultato*/
				/* $risultato != false controlla che existsbyrelatedfield abbia passato tutti i passaggi di verifica (esmpio cookie account presente)*/
				/* $risultato->count() > 0 controlla che la query abbia trovato almeno un account presente nei cookie*/
				if (is_null($risultato) == false and $risultato != false and $risultato->count() > 0) {
					//account già presente nei cookie
					$this->logger->log('wb_cookie', 'createcookieforpasswordcheck->ACCOUNT 1' . $_POST['email'] . ' PRESENTE NEI COOKIE', 3);
					$this->InsertCookie(true);
				} else {
					$this->logger->log('wb_cookie', 'createcookieforpasswordcheck->ACCOUNT 2a' . $_POST['email'] . ' NON PRESENTE NEI COOKIE', 3);
					//account non già presente nei cookie
					//verifica che la mail sia di un utente presente in tabella
					$query_str = "SELECT tbl_utente.email FROM tbl_utente WHERE tbl_utente.email = :email and tbl_utente.disable = 0";
					$resemail = $this->database->query($query_str, ['email' => $_POST['email']]);
					if ($resemail->count() > 0) {
						$this->logger->log('wb_cookie', 'createcookieforpasswordcheck->ACCOUNT 2b' . $_POST['email'] . ' NON PRESENTE NEI COOKIE', 3);
						$this->InsertCookie(false);
					} else {
						$errore = true;
					}
				}
			} else {
				$errore = true;
			}
		} else {
			$errore = true;
		}
		if ($errore) {
			$this->cookieresult->appendunset($cookie_instruction['name']);
		} else {
			$this->cookieresult->append($cookie_instruction['name'], true);
		}
	}

	/*questa procedura inserisce/sovrascrive COOKIE_PASSWORD_SHOW del browser con valore nuovo identifier staccato appositamente
*in caso di acceso da scegli account viene sovrascritto il campo lastaccessrequestidentifier_id di tbl_account per dell'utente selezionato tramite *UPDATE in modo che il COOKIE_PASSWORD_SHOW (browser) riporti lo stesso valore di identifier in modo da capire quale account ha richiesto la verifica *password. nel caso in cui sia un accesso da accedi dovrà invece esserci un insert (anche se il COOKIE_ACCOUNT_SHOW non viene salvato a browser fino
*alla verifica della password)
*/
	public function InsertCookie($accountpresente): void
	{

		$this->logger->log('WB_CookieMetod', 'START InsertCookie', 3);
		$randpasswd = rand(1000000000, 9999999999);
		$query_str = "INSERT INTO tbl_identifier(server_id,identifier) VALUES (:server_id," . "'" . $randpasswd . "'" . ");";
		$query_str = $query_str . 'SET @idnewp = LAST_INSERT_ID();';

		if ($accountpresente) {

			$query_str = $query_str . "UPDATE tbl_account SET lastaccessrequestidentifier_id = @idnewp, lastaccessrequest = now() WHERE identifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifier) and utente_id = (SELECT id FROM tbl_utente WHERE email =:email and tbl_utente.disable = 0)";

			//	$this->logger->log('WB_CookieMetodQUERY', $query_str, 3);

			$this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW], 'email' => $_POST['email'], 'server_id' => $this->serverconfig['server_id']]);
		} else {

			$this->logger->log('wb_cookie', 'YOGANANDA', 3);
			//questo serve solo nel caso sia un accesso di un nuovo account mai verificato prima
			// in questo secondo caso si farà l'insert e non l'update ACCOUNT
			$randaccount = rand(1000000000, 9999999999);
			$query_str = $query_str . "INSERT INTO tbl_identifier(server_id,identifier) VALUES (:server_id," . "'" . $randaccount . "'" . ");";
			$query_str = $query_str . 'SET @idnewa = LAST_INSERT_ID();';

			$query_str = $query_str . "INSERT INTO tbl_account (azienda_id ,server_id, utente_id, identifier_id, lastaccessrequest, lastaccessrequestidentifier_id,isconnected,doubleauthtested) VALUES ((SELECT azienda_id FROM tbl_utente WHERE email =:email and tbl_utente.disable = 0),:server_id, (SELECT id FROM tbl_utente WHERE email =:email and tbl_utente.disable = 0), @idnewa, now(),@idnewp,false,false)";

			//	$this->logger->log('WB_CookieMetodQUERY', $query_str, 3);

			$this->database->query($query_str, ['email' => $_POST['email'], 'server_id' => $this->serverconfig['server_id']]);
		}

		setcookie(self::COOKIE_PASSWORD_SHOW, $randpasswd, 0, "/"); //0 expire when session stop (close browser)

		//setcookie(self::COOKIE_PASSWORD_SHOW, $randpasswd,time() + (10 * 365 * 24 * 60 * 60),"/");//10 anni expire
	}

	/*il parametro $appendresult se true fa aggiungere alla variabile globale $this->cookieresult i risultati della procedura altrimenti se false ritorna il risultato direttamente al chiamante (false in caso di errore/ risultato della query in caso tutto ok)*/
	private function existsbyrelatedfield($cookie_instruction, $appendresult = true)
	{
		/*controlla se il cookie del browser è coerente*/
		$errore = false;
		if ($this->testcookievaluecoerence(self::COOKIE_ACCOUNT_SHOW)) {
			$this->logger->log('wb_cookie', 'existsbyrelatedfield->testcookievaluecoerence PASSED', 3);
			/*controlla se la mail è stata passata correttamente*/
			if (isset($_POST['email'])) {
				$this->logger->log('wb_cookie', 'existsbyrelatedfield->isset', 3);
				$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i"; //per la mail
				/*controlla se la mail è una mail*/
				if (preg_match($pattern, $_POST['email'])) {
					$this->logger->log('wb_cookie', 'existsbyrelatedfield->preg_match ok', 3);
					$query_str = "SELECT tbl_utente.email FROM tbl_utente inner join tbl_account on tbl_utente.id = tbl_account.utente_id inner join tbl_identifier on tbl_account.identifier_id = tbl_identifier.id WHERE tbl_identifier.identifier = :identifier and tbl_utente.disable = 0 and tbl_utente.email = :email";
					$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW], 'email' => $_POST['email']]);
				} else {
					$errore = true;
				}
			} else {
				$errore = true;
			}

			//	$this->logger->log('wb_cookie', 'existsbyrelatedfield-> CI SONO TOT RISULTATI:'.$res->count(), 3);
			/*foreach ($res as $row) {
				//rimuovo single quote , double quote, caratteri new line
				echo ' stampo tipo:'.$row->getCol('email');
			}
			*/
		} else {
			$errore = true;
		}
		if ($appendresult) {
			if ($errore) {
				//$this->cookieresult->appendunset($cookie_instruction['name']);
				$this->cookieresult->append($cookie_instruction['name'], false);
			} else {
				$this->cookieresult->append($cookie_instruction['name'], $res);
			}
		} else {
			if ($errore) {
				return false;
			} else {
				return $res;
			}
		}
	}

	private function getAll($cookie_instruction): void
	{

		/*la funzione torna la mail degli utenti che hanno account registrati su quel browser tramite il cookie COOKIE_ACCOUNT_SHOW (tutti gli account con identifier salvato nei cookie)  */

		if ($this->testcookievaluecoerence(self::COOKIE_ACCOUNT_SHOW)) {

			$query_str = "SELECT tbl_utente.email, tbl_utente.nomecognome, tbl_account.isconnected FROM tbl_utente inner join tbl_account on tbl_utente.id = tbl_account.utente_id inner join tbl_identifier on tbl_account.identifier_id = tbl_identifier.id WHERE identifier = :identifier and tbl_utente.disable = 0";
			$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);
			$this->cookieresult->append($cookie_instruction['name'], $res);

			//echo " CI SONO TOT RISULTATI:".$res->count()."-";
			/*foreach ($res as $row) {
				//rimuovo single quote , double quote, caratteri new line
				echo ' stampo tipo:'.$row->getCol('email');
			}
			*/
		} else {

			//$this->logger->log('wb_cookie', 'SI ASS SUI', 3);
			$this->cookieresult->appendunset($cookie_instruction['name']);
		}
	}

	private function testcookievaluecoerence($cookieconst)
	{
		/*testo la coerenza del cookie (contains only english letters & digits)
		* return ->true se coerente
		*				 ->false se non coerente
		*/

		if (isset($_COOKIE[$cookieconst])) {
			if (!preg_match('/[^A-Za-z0-9]/', $_COOKIE[$cookieconst])) { // string contains only english letters & digits

				return true;
			} else {
				//non coerente inserire errore e segnalazione
				return false;
			}
		} else {
			//non settato
			return false;
		}
	}

	private function getlabeluserbycookierforpassword($cookie_instruction): void
	{

		$errore = false;

		if ($this->testcookievaluecoerence(self::COOKIE_PASSWORD_SHOW)) {
			$this->logger->log('wb_cookie', 'getlabeluserbycookierforpassword->valcoerence passed', 3);

			$query_str = "SELECT tbl_utente.email, tbl_utente.nomecognome, tbl_utente.doubleauthentication, tbl_utente.errorlogin, tbl_utente.errordoubleauthentication, tbl_account.doubleauthtested FROM tbl_identifier inner join tbl_account on tbl_identifier.id = tbl_account.lastaccessrequestidentifier_id inner join tbl_utente on tbl_account.utente_id = tbl_utente.id WHERE tbl_identifier.identifier = :identifier and tbl_utente.disable = 0";
			$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_PASSWORD_SHOW]]);

			if ($res->count() > 0) {
				//nessun errore
			} else {
				$errore = true;
			}
		} else {
			$errore = true;
		}
		if ($errore) {
			$this->cookieresult->appendunset($cookie_instruction['name']);
		} else {
			$this->cookieresult->append($cookie_instruction['name'], $res);
		}
	}

	private function verifypassword($cookie_instruction): void
	{

		$errore = false;

		if ($this->testcookievaluecoerence(self::COOKIE_PASSWORD_SHOW)) {
			$this->logger->log('wb_cookie', 'verifypassword', 3);

			/*estraggo con la query nome, cognome, e il nuovo identifier staccato*/
			$query_str = "SELECT tbl_utente.id, tbl_utente.email, tbl_utente.nomecognome, tbl_identifieraccount.identifier, tbl_utente.urldefault, tbl_utente.doubleauthentication, tbl_utente.doubleauthenticationcode, tbl_utente.errordoubleauthentication, tbl_account.doubleauthtested FROM tbl_identifier inner join tbl_account on tbl_identifier.id = tbl_account.lastaccessrequestidentifier_id inner join tbl_utente on tbl_account.utente_id = tbl_utente.id inner join tbl_identifier tbl_identifieraccount on tbl_account.identifier_id = tbl_identifieraccount.id WHERE tbl_identifier.identifier = :identifier and BINARY tbl_utente.password = :password and tbl_utente.disable = 0";
			$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_PASSWORD_SHOW], 'password' => $_POST['password']]);

			if ($res->count() == 1) {

				$x = $res->get_col('identifier'); //Restituisce il valore identifier del primo elemento (preso dalla query sopra)
				$urldefault = $res->get_col('urldefault');
				$xid = $res->get_col('id');
				$xdoubbool = $res->get_col('doubleauthentication');
				$xdoubcode = $res->get_col('doubleauthenticationcode');
				$xerrordoubleauthentication = $res->get_col('errordoubleauthentication');
				$xdoubleauthtested = $res->get_col('doubleauthtested'); //controlla se per quel account è già stata testata la doppia autenticazione

				$autcorretta = false; //resta false se è richiesta la doppia autenticazione e non è corretto il token passato
				$doppiaautenticazionecorretta = false;

				if ($xdoubleauthtested == '0' and $xdoubbool == '1') { //entra nel ciclo se $xdoubleauthtested == '0' (cioè per quell'account non è stata precedentemente verificata la doppia auth) e $xdoubbool == '1' (se è richiesta la doppia autenticazione per quell'utente)

					if (isset($_POST['doublecode'])) {

						if ($_POST['doublecode'] == $xdoubcode) {
							$autcorretta = true;
							$doppiaautenticazionecorretta = true;
						} else {
							//aggiungo errore autenticazione e incremento contatore errordoubleauthentication

							$xerrorlogin = (int)$xerrordoubleauthentication;
							$xerrorlogin++;

							if ($xerrorlogin > $this->serverconfig['max_errologin']) {
								$query_str = "UPDATE tbl_utente SET disable = 1 WHERE id = :id";
								$this->database->query($query_str, ['id' => $xid]);
							} else {
								$query_str = "UPDATE tbl_utente SET errordoubleauthentication = :errorlogin WHERE id = :id";
								$this->database->query($query_str, ['id' => $xid, 'errorlogin' => $xerrorlogin]);
							}
						}
					}
				} else {
					$autcorretta = true;
				}

				if ($autcorretta) {
					//setto il COOKIE_ACCOUNT_SHOW
					setcookie(self::COOKIE_ACCOUNT_SHOW, $x, time() + (10 * 365 * 24 * 60 * 60), "/"); //0 expire when session stop (close browser)

					if ($doppiaautenticazionecorretta) {
						$query_str = "UPDATE tbl_account SET doubleauthtested = 1, isconnected = 1, lastcheckpassword = now() WHERE lastaccessrequestidentifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifierpwd)";
					} else {
						$query_str = "UPDATE tbl_account SET isconnected = 1, lastcheckpassword = now() WHERE lastaccessrequestidentifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifierpwd)";
					}

					$this->database->query($query_str, ['identifierpwd' => $_COOKIE[self::COOKIE_PASSWORD_SHOW]]);
					$this->logger->log('wb_cookie', 'isconnected', 3);

					//aggiungo la parte di reset errori login
					$query_str = "UPDATE tbl_utente SET errorlogin = 0, errordoubleauthentication = 0 WHERE id =  :id";
					$this->database->query($query_str, ['id' => $xid]);

					if (!isset($_COOKIE[self::COOKIE_ACCOUNT_SHOW])) {

						//se la password è corretta e non c'è un COOKIE_ACCOUNT_SHOW settato nel BROWSER vado a settarlo perchè non è stato mai staccato prima

						$this->logger->log('wb_cookie', 'COOKIE_ACCOUNT_SHOW non settato - > quindi setto:' . $x, 3);
					} else {

						$this->logger->log('wb_cookie', 'COOKIE_ACCOUNT_SHOW già settato - > quindi aggiorno quelli vecchi:' . $x, 3);

						// la password è corretta e il COOKIE_ACCOUNT_SHOW è già settato

						//devo segnalare come non connessi tutti gli account del cookiew dello stesso
						//devo anche mettere il nuovi cokkie w su tutti gli account che avevano il vecchio cookie w
						//quindi aggiorno tutti gli utenti col vecchio identifier

						$query_str = "UPDATE tbl_account SET isconnected = 0 ,identifier_id=(SELECT id FROM tbl_identifier WHERE identifier = :identifiernew) WHERE identifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifierold) and lastaccessrequestidentifier_id IS NULL";

						/*$query_str = "UPDATE tbl_account SET isconnected = 0 ,identifier_id=(SELECT id FROM tbl_identifier WHERE identifier = :identifiernew) WHERE identifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifierold) and (lastaccessrequestidentifier_id IS NULL or lastaccessrequestidentifier_id != (SELECT id FROM tbl_identifier WHERE identifier = :identifierpwd))";

							$this->database->query($query_str, ['identifiernew'=>$x,'identifierold'=>$_COOKIE[self::COOKIE_ACCOUNT_SHOW],'identifierpwd'=>$_COOKIE[self::COOKIE_PASSWORD_SHOW]]);*/

						$this->database->query($query_str, ['identifiernew' => $x, 'identifierold' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);
						$this->logger->log('wb_cookie', 'isconnected 0 per tutti gli altri account del vecchio COOKIE_ACCOUNT_SHOW cioè:' . $_COOKIE[self::COOKIE_ACCOUNT_SHOW], 3);
					}
				} else {
					$errore = true;
				}

				//$this->logger->log('wb_cookie', $_COOKIE[self::COOKIE_ACCOUNT_SHOW], 3);

			} else {
				$errore = true;
				//aggiungo la parte in qui segnalo i tentativo di password errato a db incrementando il flag errori login
				$query_str = "SELECT tbl_utente.id, tbl_utente.errorlogin FROM tbl_identifier inner join tbl_account on tbl_identifier.id = tbl_account.lastaccessrequestidentifier_id inner join tbl_utente on tbl_account.utente_id = tbl_utente.id inner join tbl_identifier tbl_identifieraccount on tbl_account.identifier_id = tbl_identifieraccount.id WHERE tbl_identifier.identifier = :identifier and tbl_utente.disable = 0";
				$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_PASSWORD_SHOW]]);

				if ($res->count() == 1) {

					$xid = $res->get_col('id');
					$xerrorlogin = $res->get_col('errorlogin');

					$xerrorlogin = (int)$xerrorlogin;
					$xerrorlogin++;

					if ($xerrorlogin > $this->serverconfig['max_errologin']) {
						$query_str = "UPDATE tbl_utente SET disable = 1 WHERE id = :id";
						$this->database->query($query_str, ['id' => $xid]);
					} else {
						$query_str = "UPDATE tbl_utente SET errorlogin = :errorlogin WHERE id = :id";
						$this->database->query($query_str, ['id' => $xid, 'errorlogin' => $xerrorlogin]);
					}
				}
			}
		} else {
			$errore = true;
		}
		if ($errore) {
			$this->cookieresult->appendunset($cookie_instruction['name']);
		} else {
			$this->cookieresult->append($cookie_instruction['name'], $res);
			$this->cookieresult->append('urldefault', $urldefault);
		}
	}

	private function removeaccount($cookie_instruction): void
	{

		$errore = false;

		if ($this->testcookievaluecoerence(self::COOKIE_ACCOUNT_SHOW)) {
			$this->logger->log('wb_cookie', 'removeaccount', 3);

			$query_str = "DELETE FROM tbl_account WHERE id=(SELECT tbl_account.id FROM tbl_identifier inner join tbl_account on tbl_identifier.id = tbl_account.identifier_id inner join tbl_utente on tbl_account.utente_id = tbl_utente.id WHERE tbl_identifier.identifier = :identifier and tbl_utente.email = :email and tbl_utente.disable = 0)";
			$res = $this->database->query($query_str, ['identifier' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW], 'email' => $_POST['email']]);
		} else {
			$errore = true;
		}
		if ($errore) {
			$this->cookieresult->appendunset($cookie_instruction['name']);
		} else {
			$this->cookieresult->append($cookie_instruction['name'], $res);
		}
	}

	private function logout($cookie_instruction): void
	{

		/*Non serve verificare che ci sia anche il COOKIE_PASSWORD_SHOW settato perchè basta mettere tutti a non connessi e senza identifier_id relativo alla password per tutti gli utenti che hanno quel COOKIE_ACCOUNT_SHOW*/

		$errore = false;
		$this->logger->log('wb_cookie', 'logout', 3);

		if ($this->testcookievaluecoerence(self::COOKIE_ACCOUNT_SHOW)) {
			$this->logger->log('wb_cookie', 'logout', 3);

			//cerco se ci sono utenti corrispondenti ai 2 cookie account e password per andare poi a settare a database non è connesso

			$query_str = "SELECT tbl_utente.email, tbl_utente.nomecognome FROM tbl_account inner join tbl_utente on tbl_account.utente_id = tbl_utente.id inner join tbl_identifier tbl_identifieraccount on tbl_account.identifier_id = tbl_identifieraccount.id WHERE tbl_identifieraccount.identifier = :identifieracc and tbl_utente.disable = 0";
			$res = $this->database->query($query_str, ['identifieracc' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);

			$this->logger->log('wb_cookie', $query_str, 3);

			if ($res->count() > 0) {

				$this->logger->log('wb_cookie', 'logout COUNT>1', 3);

				//se è settato il cookie account
				if (isset($_COOKIE[self::COOKIE_ACCOUNT_SHOW])) {

					/* vecchio metodo in cui settavo solo quello connesso in quel momento a sloggato

							$query_str = "UPDATE tbl_account SET isconnected = 0, lastaccessrequestidentifier_id = NULL WHERE lastaccessrequestidentifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifierpwd) and identifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifieracc)";

							$this->database->query($query_str, ['identifierpwd'=>$_COOKIE[self::COOKIE_PASSWORD_SHOW], 'identifieracc'=>$_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);*/

					$query_str = "UPDATE tbl_account SET isconnected = 0, lastaccessrequestidentifier_id = NULL WHERE identifier_id = (SELECT id FROM tbl_identifier WHERE identifier = :identifieracc)";

					$this->database->query($query_str, ['identifieracc' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);


					$this->logger->log('wb_cookie', 'logout set to DBz', 3);
				}

				//rimuovo il cookie password dal browser anche per il caso in cui non è stato trovata la corrispondenza a database
				$this->removecookiepassword();
			} else {
				$errore = true;
			}
		} else {

			$errore = true;
		}
		if ($errore) {
			$this->cookieresult->appendunset($cookie_instruction['name']);
		} else {
			$this->cookieresult->append($cookie_instruction['name'], $res);
		}
	}

	private function getrole($cookie_instruction): void
	{
		/*il limite di questa funzione è che non può testare lo stesso role con due permessi diversi esempio (role_uguale#0011 e role_uguale#0100) perchè aggiunge all'array dei risultati solo la parte rolename senza quello che sta dopo il #. se si vuole ovviare alla cosa la funzione va chiamata più volte*/
		/*la funzione restituisce true o false a seconda che il role e i permessi richiesti siano soddisfatti dai privilegi dell'utente*/
		/* per l'esecuzione della funzione bisogna passare tramite il cookie_instruction anche il value del role tramite la chiave "role" componendo il valore in questo modo nome_role#clse dove clse possono assumere valore 0 o 1 e riguardano la possibilità o meno di effettuare quella data operazione per quel role. quindi un utente può avere il permesso se il valore è 1 e 0 non ce l'ha */

		$utenteconnesso = false; //diventa true se c'è almeno un role valido per quell'utente
		$azienda_id = 0; //0 vuol dire non trovata-> errore
		$utente_id = 0;
		$cassa_id = 0;
		$nomecognome = 'NONAME';
		$accessoconcesso = array(); //array contenente per ogni rolecercato (chiave arrray) true o false a seconda del fatto che il privilegio si garantino dall'utente o meno

		$this->logger->log('wb_cookie', 'getRole -> inizio esecuzione', 3);

		if ($this->testcookievaluecoerence(self::COOKIE_PASSWORD_SHOW) and $this->testcookievaluecoerence(self::COOKIE_ACCOUNT_SHOW)) {

			//cerco se ci sono utenti corrispondenti ai 2 cookie account e password per andare poi a settare a database non è connesso

			$query_str = "SELECT tbl_utente.id, tbl_utente.azienda_id, tbl_utente.role, tbl_utente.nomecognome, tbl_utente.cassa_id FROM tbl_identifier tbl_identifierpwd inner join tbl_account on tbl_identifierpwd.id = tbl_account.lastaccessrequestidentifier_id inner join tbl_utente on tbl_account.utente_id = tbl_utente.id inner join tbl_identifier tbl_identifieraccount on tbl_account.identifier_id = tbl_identifieraccount.id WHERE tbl_identifierpwd.identifier = :identifierpwd and tbl_identifieraccount.identifier = :identifieracc and tbl_account.isconnected = 1 and tbl_utente.disable = 0";
			$res = $this->database->query($query_str, ['identifierpwd' => $_COOKIE[self::COOKIE_PASSWORD_SHOW], 'identifieracc' => $_COOKIE[self::COOKIE_ACCOUNT_SHOW]]);

			if ($res->count() == 1) {
				$nomecognome = $res->get_col('nomecognome');
				$azienda_id = $res->get_col('azienda_id');
				$utente_id = $res->get_col('id');
				$cassa_id = $res->get_col('cassa_id');
				$x = $res->get_col('role'); //Restituisce il valore role del primo elemento (preso dalla query sopra)

				$arrayx = explode(";", $x);

				//var_dump($arrayx);
				//var_dump('----STOP-----');

				foreach ($cookie_instruction['role'] as $onerole) {

					/*se la stringa role da cercare tra i role dell'utente è benformata procedo */
					//INZIO if coerenza role
					if (str_contains($onerole, '#') and strlen($onerole) > 5) {
						$rolecercato = explode("#", $onerole);
						$rolenamecercato = $rolecercato[0]; //name role
						$accessoconcesso[$rolenamecercato] = false; //setto a false i privilegi utente per quel role. poi se li ha passerà a true
						$roleccercato = $rolecercato[1][0]; //c
						$rolelcercato = $rolecercato[1][1]; //l
						$rolescercato = $rolecercato[1][2]; //s
						$roleecercato = $rolecercato[1][3]; //e
						//var_dump($roleccercato);
						foreach ($arrayx as $value) {
							//var_dump($value);
							if (str_contains($value, '#') and strlen($value) > 5) {
								$roleutente = explode("#", $value);
								$rolenameutente = $roleutente[0]; //name role
								$rolecutente = $roleutente[1][0]; //c
								$rolelutente = $roleutente[1][1]; //l
								$rolesutente = $roleutente[1][2]; //s
								$roleeutente = $roleutente[1][3]; //e
								/**/
								if (strcmp($rolenameutente, $rolenamecercato) == 0 and strcmp($rolecutente, $roleccercato) >= 0 and strcmp($rolelutente, $rolelcercato) >= 0 and strcmp($rolesutente, $rolescercato) >= 0 and strcmp($roleeutente, $roleecercato) >= 0) {
									$accessoconcesso[$rolenamecercato] = true;
									$utenteconnesso = true;
								} else {
									$utenteconnesso = true;
								}
							}
						}
					} else {
						$rolecercato = explode("#", $onerole);
						$rolenamecercato = $rolecercato[0]; //name role
						$accessoconcesso[$rolenamecercato] = false; //setto a false i privilegi utente per quel role perchè non soddisfa i critiri minimi per cui viene anche escluso dalla ricerca
					} //fine if coerenza role
				} //fine foreach elementi array role ricercati

			}
		}
		//var_dump($accessoconcesso);
		//$this->cookieresult->append($cookie_instruction['name'],$accessoconcesso);
		$this->cookieresult->append('accessoconcesso', $accessoconcesso);
		$this->cookieresult->append('utenteconnesso', $utenteconnesso);
		$this->cookieresult->append('nomecognome', $nomecognome);
		$this->cookieresult->append('azienda_id', $azienda_id);
		$this->cookieresult->append('utente_id', $utente_id);
		$this->cookieresult->append('cassa_id', $cassa_id);
	}

	/*la funzione serve a evitare che si tenti di vedere il boolean di un roole che non esiste nell'array e quindi restituisce false anche se la key ricercata non è presente nell'array oltre al valore della key quando presente
	$arraywithrole è il valore di ritorno che la funzione getrole sopra che è composto da un array con chiave name_role(senza la parte #) e valore false o true a seconda che quel ruolo sia verificato per l'utente_id
	$roletested va passato solo la parte name_role(senza la parte #) e questa funzione restituisce true o false a seconda del valore dell'array*/
	public function testrole($arraywithrole, $roletested)
	{
		if (array_key_exists($roletested, $arraywithrole)) {
			return $arraywithrole[$roletested];
		} else {
			return false;
		}
	}
} //fine classe
