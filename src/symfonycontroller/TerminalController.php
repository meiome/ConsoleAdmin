<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\php\wb_framework\WB_Composer;
use App\php\wb_cookie\WB_CookieMetod;
use App\php\wb_terminal\WB_Terminal;
use App\php\wb_framework\WB_HttpResponse;
use App\php\wb_framework\WB_Logger;

class TerminalController extends AbstractController
{

  public $composer;
  private $cookieiteratorresult;
  private $database;
  private $cookiemetod;
  private $logger;
  private $serverconfig;
  private $arraymaschere;
  private $terminal;

  private function construct_surrogato(): void{

    define('controllerDIR_BASE', dirname(dirname(dirname( __FILE__ ))).'/');

    $WB_LOG_FILE = dirname($_SERVER['DOCUMENT_ROOT']).'/logs/log.txt';
		$this->logger = new WB_Logger($WB_LOG_FILE);
		$GLOBALS["WB_LOGGER"] = $this->logger;

    $this->composer = new WB_Composer( controllerDIR_BASE.'template/terminal/skel.php' );

    $this->composer->extend( 'head', controllerDIR_BASE.'template/terminal/head.php');
    //$this->composer->extend( 'root/head/app-bar', 'template/wb-panel/app-bar.php');
		$this->composer->extend( 'app-bar', controllerDIR_BASE.'template/terminal/terminal-bar.php');

    require_once(controllerDIR_BASE.'/config/wb/db/database.php');
		$this->database = $database;
		$this->database->connect();

    $this->serverconfig = require(controllerDIR_BASE.'/config/wb/server/serverconfig.php');

		require_once(controllerDIR_BASE.'src/php/wb_terminal/WB_Terminal.php');
		$this->terminal = new WB_Terminal($this->database);
		//require_once(controllerDIR_BASE.'/config/wb/terminal/maschere.php');
    $this->arraymaschere = require(controllerDIR_BASE.'/config/wb/terminal/maschere.php');
  }

  
  #[Route(path: '/terminal/creadb', name: 'terminalcreadb')]
  public function creadb(){

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_admin#1111']//clse
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_admin');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');

    //controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        //controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

    $this->database->create_database_structure();

    $this->composer->setData('nomecognome', $nomecognome);
    $this->composer->setData('utenteconnesso', $utenteconnesso);
    $this->composer->setData('accessoconcesso', $accessoconcesso);

    $this->composer->extend('body', controllerDIR_BASE.'view/terminal/index.php');
    $this->composer->setData('PageTitle', 'terminal');

    $response = $this->composer->render();

    //$this->response->appendContent( $response );

    $response = new Response($response);

    $response->headers->addCacheControlDirective('no-cache', true);
    $response->headers->addCacheControlDirective('max-age', 0);
    $response->headers->addCacheControlDirective('must-revalidate', true);
    $response->headers->addCacheControlDirective('no-store', true);

    return $response;

    }

  }

  
  #[Route(path: '/terminal', name: 'terminal')]
  public function index(){

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

    $this->composer->setData('nomecognome', $nomecognome);
    $this->composer->setData('utenteconnesso', $utenteconnesso);
    $this->composer->setData('accessoconcesso', $accessoconcesso);

    $this->composer->extend('body', controllerDIR_BASE.'view/terminal/index.php');
    $this->composer->setData('PageTitle', 'terminal');

    $response = $this->composer->render();

    //$this->response->appendContent( $response );

    $response = new Response($response);

    $response->headers->addCacheControlDirective('no-cache', true);
    $response->headers->addCacheControlDirective('max-age', 0);
    $response->headers->addCacheControlDirective('must-revalidate', true);
    $response->headers->addCacheControlDirective('no-store', true);

    return $response;

    }

  }

  
  #[Route(path: '/terminal/maschera/{method}/{mascheraname}/{id}', name: 'maschera', requirements: ['id' => '\d+', 'method' => "[a-zA-Z0-9%'èòàùì]+", 'mascheraname' => "[a-zA-Z0-9%'èòàùì]+"])]
  public function maschera($method,$mascheraname,$id){// natore.test/terminal/maschera/new/articoli/0

    /*questo non serve più perchè ci pensa symfony a fare il controllo che la route sia ben formata

    $method = null;// solo letter
    if( isset($_GET['method']) and isset($_GET['mascheraname']) and isset($_GET['id']) ){
        $method = $_GET['method'];
        $mascheraname = $_GET['mascheraname'];
        $id = $_GET['id'];
    } else {
      throw new \Exception("terminalController route non corretta", 1);
    }*/

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

      if (array_key_exists($mascheraname,$this->arraymaschere)){

        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }


        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'role' => ['role_terminal#0101']/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati

        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte

      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }


      $masc = $this->terminal->getmascherabyname($mascheraname);
      $masc->setAzienda($azienda_id);
      $masc->setQuery($method,$id);
      $this->composer->setData('maschera', $masc);

      $this->composer->setData('nomecognome', $nomecognome);
      $this->composer->setData('utenteconnesso', $utenteconnesso);
      $this->composer->setData('accessoconcesso', $accessoconcesso);

      $this->composer->extend('body', controllerDIR_BASE.'view/terminal/maschera.php');
      $this->composer->setData('PageTitle', $method.'-'.$mascheraname);

      $response = $this->composer->render();

    //  $this->response->appendContent( $response );

      $response = new Response($response);

      $response->headers->addCacheControlDirective('no-cache', true);
      $response->headers->addCacheControlDirective('max-age', 0);
      $response->headers->addCacheControlDirective('must-revalidate', true);
      $response->headers->addCacheControlDirective('no-store', true);

      return $response;
    }

  }// natore.test/terminal/maschera/select/articoli/3150 (203 disable)
  
  #[Route(path: '/terminal/getmascheramodel/{mascheraname}', name: 'getmascheramodel', requirements: ['mascheraname' => "[a-zA-Z0-9%'èòàùì]+"])]
  public function getmascheramodel($mascheraname){

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

      if (array_key_exists($mascheraname,$this->arraymaschere)){

        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte


      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }

      $masc = $this->terminal->getmascherabyname($mascheraname);
      $arrmodel = $masc->GetModel();

      $responsedata = array("code" => 100, "success" => true, "data" => $arrmodel,);

      $response = new Response();
      $response->setContent(json_encode($responsedata));
      $response->headers->set('Content-Type', 'application/json; charset=utf-8');

      return $response;
    }

  }

  
  #[Route(path: '/terminal/deletefile/{mascheraname}/{cartella}/{filenamewithext}', name: 'deletefile', requirements: ['mascheraname' => '[a-zA-Z0-9_]+', 'filenamewithext' => '[0-9a-zA-Z_.-]{32,55}', 'cartella' => '[a-zA-Z0-9]+'])]
  public function deletefile($mascheraname,$cartella,$filenamewithext){

    //$this->response = new WB_HttpResponse();

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

      if (array_key_exists($mascheraname,$this->arraymaschere)){

        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte


      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {


        if (in_array($cartella, $this->serverconfig['filedir'])){//se  il nome della cartella è tra quelli consentiti

          $path = controllerDIR_BASE.'public/uploads/terminal/'.$cartella.'/'.$filenamewithext;
          unlink($path);


        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Cartella non trovata"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la cartella dalla quale si richiede cancellazione file"),
          ]);
        }

      }//fine if POST

      $risposta []= array(
        'nomefile' => null,
        'tipo' => null,
        'data' => null,
      );


      $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

      $response = new Response();
      $response->setContent(json_encode($responsedata));
      $response->headers->set('Content-Type', 'application/json; charset=utf-8');

      return $response;
    }

  }

  
  #[Route(path: '/terminal/gestorefile/{mascheraname}/{cartella}/{filename}', name: 'gestorefile', requirements: ['mascheraname' => '[a-zA-Z0-9_]+', 'filename' => '[0-9a-zA-Z_-]{30,50}', 'cartella' => '[a-zA-Z0-9]+'])]
  public function gestorefile($mascheraname,$cartella,$filename){

    //$this->response = new WB_HttpResponse();

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

      if (array_key_exists($mascheraname,$this->arraymaschere)){

        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte


      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {


        if (in_array($cartella, $this->serverconfig['filedir'])){//se  il nome della cartella è tra quelli consentiti

          $str =file_get_contents('php://input');

          $a = $this->strToHex($str, 12);

          //$filename = md5(time()).'.'.$this->getfiletype($a);//.'.jpg';

          $filename = $filename.'.'.$this->getfiletype($a);//.'.jpg';

          $path = controllerDIR_BASE.'public/uploads/terminal/'.$cartella.'/'.$filename;
          file_put_contents($path,$str);


        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Cartella non trovata"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la cartella per la quale si richiede il salvataggio non esiste"),
          ]);
        }

      }//fine if POST

      $risposta []= array(
        'nomefile' => null,
        'tipo' => null,
        'data' => null,
      );


      $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

      $response = new Response();
      $response->setContent(json_encode($responsedata));
      $response->headers->set('Content-Type', 'application/json; charset=utf-8');

      return $response;
    }

  }

  function getfiletypeisok($test){
    if ($this->testsig($test, "FF D8 FF")){
        return true;
    } elseif ($this->testsig($test, "89 50 4E 47 0D 0A 1A 0A")){
        return true;
    }
    /*elseif (testsig($test, "25 50 44 46")){
        return "pdf";
    }
    elseif (testsig($test, "D0 CF 11 E0 A1 B1 1A E1")){
        return "doc";
    }*/
    else{
        return false;
    }
  }

  function getfiletype($test){
    if ($this->testsig($test, "FF D8 FF")){
        return "jpeg";
    } elseif ($this->testsig($test, "89 50 4E 47 0D 0A 1A 0A")){
        return "png";
    }
    /*elseif (testsig($test, "25 50 44 46")){
        return "pdf";
    }
    elseif (testsig($test, "D0 CF 11 E0 A1 B1 1A E1")){
        return "doc";
    }*/
    else{
        return "unknown";
    }
  }

  function testsig($test, $sig){
      // remove spaces in sig
      $sig = str_replace(" ","", $sig);
      if (substr($test, 0, strlen($sig)) == $sig){
              return true;
      }
      return false;
  }



  function strToHex($string, $stop=null){
      $hex = "";
      if ($stop == null){
          $stop = strlen($string);
      }
      $stop = min(strlen($string), $stop);

      for ($i=0; $i<$stop; $i++){
          $ord = ord($string[$i]);
          $hexCode = dechex($ord);
          $hex .= substr('0'.$hexCode, -2);
      }
      return strtoupper($hex);
  }

  
  #[Route(path: '/terminal/gestoredati', name: 'gestoredati')]
  public function gestoredati(){

    //$this->response = new WB_HttpResponse();

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $datajava = file_get_contents("php://input");
      $datajava = json_decode( $datajava );

      $mascheraname = $datajava[0]->model->name;

      if (array_key_exists($mascheraname,$this->arraymaschere)){
        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte

        //require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }

      $masc = $this->terminal->getmascherabyname($mascheraname);//viene restituita l'istanza WB_Maschera della corrispondente maschera
      /*normalmente non viene utilizzato il model restituito da questa maschera ma quello passato dal client, in modo che ci sia un controllo ulteriore.. il cliente deve produrre un model corretto per avere la possibilità di far elaborare la richiesta... questa in teoria è l'idea ispiratrice.. poi bisognerebbe implementare altri passaggi perchè venga fatto questo controllo*/
      $masc->setQuery($datajava[0]->query->method,$datajava[0]->query->id);
      $masc->setAzienda($azienda_id);

      //elaboro la prima richiesta perchè poi potra essere fatta dentro un ciclo di più richieste
      $masc->elaboraRichiesta($datajava[0]);

      //$masc->setQuery($method,$id);

      $arrmodel = $masc->GetModel();
      $arrquery = $masc->GetQuery();
      $arrdata = $masc->GetData();
      //aggiungo gli elementi ad un array perchè posssono essere più risposte contermparanee a più chiamate
      $risposta []= array(
        'query' => $arrquery,
        'model' => $arrmodel,
        'data' => $arrdata,
      );
    /*	$model []= array(
          'type' => 'maschera',
          'value' => false,
          'name' => 'disable',
          'etichetta' => 'Maschera Articoli',
          'qta' => (float) 0.0,
      );*/
    } else {
      //chiamata non post
      $risposta []= array(
        'query' => null,
        'model' => null,
        'data' => null,
      );
    }

    /*$this->response->setHeader('Content-Type', 'application/json; charset=utf-8');

    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $this->response->setContent(json_encode($responsedata));

    return new Response($this->response); //NON FUNZIONA PERCHè accetta solo stringhe*/


    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $response = new Response();
    $response->setContent(json_encode($responsedata));
    $response->headers->set('Content-Type', 'application/json; charset=utf-8');

    return $response;
    }

  }

  
  #[Route(path: '/terminal/gestoredatiwithmodel', name: 'gestoredatiwithmodel')]
  public function gestoredatiwithmodel(){

    //$this->response = new WB_HttpResponse();

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $datajava = file_get_contents("php://input");
      $datajava = json_decode( $datajava );

    //  $this->logger->log('WB_Maschera->modelDECODE', 'sss', 3);

      //var_dump($datajava[0]);

      $mascheraname = $datajava[0]->model->name;

      if (array_key_exists($mascheraname,$this->arraymaschere)){

        //nuova parte
        $mascherarole = $this->arraymaschere[$mascheraname][1];//qui c'è il role obbligatorio per la maschera
        if (str_contains($mascherarole, '#') and strlen($mascherarole)>5) {
          $xxx=explode("#",$mascherarole);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$mascherarole]/*clse*/
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);

        if ($accessoconcesso){
          require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
            'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
          ]);
        }
        //fine nuova parte

        //require_once(controllerDIR_BASE.'/config/wb/terminal/maschere/'.$this->arraymaschere[$mascheraname][0]);
      } else {
        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","Maschera non trovata"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo che la maschera della quale si fa richiesta non esiste"),
        ]);
      }

      $masc = $this->terminal->getmascherabyname($mascheraname);//viene restituita l'istanza WB_Maschera della corrispondente maschera
      $masc->setQuery($datajava[0]->query->method,$datajava[0]->query->id);
      $masc->setAzienda($azienda_id);
      $masc->setQueryCommand($datajava[0]->query->arraycommand);

      /*prendo il model corretto e lo sovrascrivo*/
      $datajava[0]->model = (object)$masc->GetModelOBJ();
      //var_dump("$$%$$");
      //var_dump($datajava[0]);

      //$this->logger->log('WB_Maschera->model', print_r($datajava[0]), 3);

      //elaboro la prima richiesta perchè poi potra essere fatta dentro un ciclo di più richieste
      $masc->elaboraRichiesta($datajava[0]);

      //$masc->setQuery($method,$id);

      $arrmodel = $masc->GetModel();
      $arrquery = $masc->GetQuery();
      $arrdata = $masc->GetData();
      //aggiungo gli elementi ad un array perchè posssono essere più risposte contermparanee a più chiamate
      $risposta []= array(
        'query' => $arrquery,
        'model' => $arrmodel,
        'data' => $arrdata,
      );
    /*	$model []= array(
          'type' => 'maschera',
          'value' => false,
          'name' => 'disable',
          'etichetta' => 'Maschera Articoli',
          'qta' => (float) 0.0,
      );*/
    } else {
      //chiamata non post
      $risposta []= array(
        'query' => null,
        'model' => null,
        'data' => null,
      );
    }

    /*$this->response->setHeader('Content-Type', 'application/json; charset=utf-8');

    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $this->response->setContent(json_encode($responsedata));

    return new Response($this->response); //NON FUNZIONA PERCHè accetta solo stringhe*/


    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $response = new Response();
    $response->setContent(json_encode($responsedata));
    $response->headers->set('Content-Type', 'application/json; charset=utf-8');

    return $response;
  }

  }

  
  #[Route(path: '/terminal/listamaschere', name: 'listamaschere')]
  public function listamaschere(){

    //$this->response = new WB_HttpResponse();

    $this->construct_surrogato();

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
      //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
      ['name' => 'accessoconcessoutenteconnesso'
      ,'entity' => 'account'
      ,'method' => 'getrole'
      ,'role' => ['role_terminal#0101']/*clse*/
      //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
      ]
    ));

    $arrayaccessoconcesso = $retv->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
    $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcesso,'role_terminal');
    $utenteconnesso = $retv->getContent('utenteconnesso');
    $nomecognome = $retv->getContent('nomecognome');
    $azienda_id = $retv->getContent('azienda_id');

    /*controllo con isbool che il valore di ritorno sia un bool perchè se torna un errore altrimenti non lo rilevo esemio null*/
    if (is_bool($accessoconcesso)==false or !$accessoconcesso) {
        /*controllo sempre prima accesso concesso perchè so che se l'accesso è concesso vuol dire che anche l'utente è connesso*/
      if (is_bool($utenteconnesso)==false or !$utenteconnesso) {

        return $this->redirectToRoute('passwordcheck');

      } else {

        return $this->redirectToRoute('exception', [
          'exceptionname' => str_replace(" ","_","ACCESS DENIED"),
          'exceptionmessage' => str_replace(" ","_","Attenzione segnaliamo mancanza di Autorizzazione per il vostro tentativo di accesso"),
        ]);

      }
    } else {

      $risposta = array();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      //arrivato qui bisogna estrarre le maschere ora
      $arraylista = array();

      foreach ($this->arraymaschere as $key => $val) {
        $retvmas = $this->cookiemetod->ExecInstructionArrayCookie(array(
          //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
          ['name' => 'accessoconcessomas'
          ,'entity' => 'account'
          ,'method' => 'getrole'
          ,'role' => [$val[1]]//quindi prendo il role per quella maschera e la testo
          //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
          ]
        ));

        if (str_contains($val[1], '#') and strlen($val[1])>5) {
          $xxx=explode("#",$val[1]);
          $yyy=$xxx[0];// qui ho il role solo col prefisso prima del # mi serve per il testrole sotto
        } else {
          return $this->redirectToRoute('exception', [
            'exceptionname' => str_replace(" ","_","Role obbligatorio per maschera ".$key." non configurato correttamente"),
            'exceptionmessage' => str_replace(" ","_","Attenzione è un problema di configuarazione che richiede intervento dell'amministratore del server"),
          ]);
        }

        $arrayaccessoconcessomas = $retvmas->getContent('accessoconcesso');//estrare array con tutti i role che sono stati testati
        $accessoconcesso = $this->cookiemetod->testrole($arrayaccessoconcessomas,$yyy);
        if ($accessoconcesso){
          $risposta[]=$key;
        }
      }

    }

    /*$this->response->setHeader('Content-Type', 'application/json; charset=utf-8');

    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $this->response->setContent(json_encode($responsedata));

    return new Response($this->response); //NON FUNZIONA PERCHè accetta solo stringhe*/


    $responsedata = array("code" => 100, "success" => true, "data" => $risposta,);

    $response = new Response();
    $response->setContent(json_encode($responsedata));
    $response->headers->set('Content-Type', 'application/json; charset=utf-8');

    return $response;
  }

  }

}
