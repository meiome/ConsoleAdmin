<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\php\wb_framework\WB_Composer;
use App\php\wb_cookie\WB_CookieMetod;
use App\php\wb_framework\WB_Logger;

class LoginController extends AbstractController
{

  public $composer;
  private $cookieiteratorresult;
  private $database;
  private $cookiemetod;
  private $logger;
  private $serverconfig;

  private function construct_surrogato(): void{

    define('controllerDIR_BASE', dirname(dirname(dirname( __FILE__ ))).'/');

    $WB_LOG_FILE = dirname($_SERVER['DOCUMENT_ROOT']).'/logs/log.txt';
		$this->logger = new WB_Logger($WB_LOG_FILE);
		$GLOBALS["WB_LOGGER"] = $this->logger;

    $this->composer = new WB_Composer( controllerDIR_BASE.'template/login/skel.php' );

    $this->composer->extend( 'head', controllerDIR_BASE.'template/login/head.php');

		$this->composer->extend( 'footer', controllerDIR_BASE.'template/login/footer.php');

		require_once(controllerDIR_BASE.'/config/wb/db/database.php');
		$this->database = $database;
		$this->database->connect();

    $this->serverconfig = require(controllerDIR_BASE.'/config/wb/server/serverconfig.php');
  }

  #[Route('/accedi', name:'accedi')]
  public function accedi(Request $request)
  {
    $this->construct_surrogato();

    //questo vede se è stato submittato il form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

			$this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

      $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
        //['name' => 'accounts','entity' => 'account','meremovecookiepasswordthod' => 'getall']
        ['name' => 'cookiepassword'
        ,'entity' => 'account'
        ,'method' => 'createcookieforpasswordcheck'
        //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
        ]
      ));
      if ($retv->getContent('cookiepassword') == true) {

        return $this->redirectToRoute('passwordcheck');

      } else {
        //aggiungere un messaggio che la mail non è corretta
        return $this->redirectToRoute('scegliaccount');

      }
      //usare test data form: https://www.w3schools.com/php/php_form_validation.asp

    } else {

      require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

			$this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

      $retv = $this->cookiemetod->ExecInstructionArrayCookie(array(
        //['name' => 'accounts','entity' => 'account','method' => 'getall']
        ['name' => 'cookiepassword'
        ,'entity' => 'account'
        ,'method' => 'logout'//'removecookiepassword' //sostituito con logout per più sicurezza
        //,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
        ]
      ));

      $this->composer->setData('resaccounts', null);
      $this->composer->extend('body', controllerDIR_BASE.'view/login/index.php');
      $this->composer->setData('PageTitle', 'Accedi');

      $response = $this->composer->render();

      return new Response($response);

    }
  }
  
  #[Route('/scegliaccount', name:'scegliaccount')]
  public function scegliaccount(){

    $this->construct_surrogato();

		//questo vede se è stato submittato il form
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

			$this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

			$this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
				//['name' => 'accounts','entity' => 'account','method' => 'getall']
				['name' => 'submitaccount'
				,'entity' => 'account'
				,'method' => 'existsbyrelatedfield'
				//,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
				]
			));

			if (is_null($this->cookieiteratorresult->getContent('submitaccount')) == false ){
				//redirect to check password

        if (strcmp($_POST['removeaccount'],"true") == 0){

          /*aggiungere qui la rimozione dell'account nel caso sia stato postato il form con il boolean del rimuovi accont altrimenti andare al verifica password*/

          $this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
    				//['name' => 'accounts','entity' => 'account','method' => 'getall']
    				['name' => 'removeaccount'
    				,'entity' => 'account'
    				,'method' => 'removeaccount'
    				//,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
    				]
    			));

          return $this->redirectToRoute('scegliaccount');

        } else {

          $this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
    				//['name' => 'accounts','entity' => 'account','method' => 'getall']
    				['name' => 'cookiepassword'
    				,'entity' => 'account'
    				,'method' => 'createcookieforpasswordcheck'
    				//,'field' => ['tbl_utente.email' => ['from' => 'post','name' => 'inputEmail']]
    				]
    			));

          return $this->redirectToRoute('passwordcheck');

        }
      //  header("Location: /accedi");
				//die();//non fa fare tutti i log del caso perchè non continua

			} else {

        return $this->redirectToRoute('scegliaccount');
				//se è null faccio redirect alla stessa pagina -- esempio rimuovi account
			//	header("Location: /accedi");// header("Location: http://example.com/scegliaccount");
				//die();
			}
			//usare test data form: https://www.w3schools.com/php/php_form_validation.asp
		} else {

			require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

      $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

			$this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
        ['name' => 'accounts','entity' => 'account','method' => 'logout'/*'removecookiepassword' ##sostituito con logout per più sicurezza*/],
				['name' => 'accounts','entity' => 'account','method' => 'getall']//estraggo tutti gli account registrati sul browser (tutte le mail)
			));
			$this->composer->setData('resaccounts', $this->cookieiteratorresult->getContent('accounts'));

			$this->composer->extend('body', controllerDIR_BASE.'view/login/index.php');
			$this->composer->setData('PageTitle', 'Scegli Account');

			$response = $this->composer->render();

      //echo($_COOKIE["w"]);

			//$this->response->appendContent( $response );

      return new Response($response);
		}
	}

  #[Route('/passwordcheck', name:'passwordcheck')]
  public function passwordcheck(Request $request)
  {
    $this->construct_surrogato();

    require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    //questo vede se è stato submittato il form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      //usare test data form: https://www.w3schools.com/php/php_form_validation.asp

      $this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
        ['name' => 'pwdverify','entity' => 'account','method' => 'verifypassword']
      ));

      if (is_null($this->cookieiteratorresult->getContent('pwdverify'))){

        //se è null vuol dire che c'è stato un errore allora ripropongo schermata di verifica password
        return $this->redirectToRoute('passwordcheck');

      } else {

        $url = $this->cookieiteratorresult->getContent('urldefault');

        $this->logger->log('wb_cookie', $url, 3);
        if (is_null($url)==false){
          return $this->redirect($url);

        } else {
          //return $this->redirectToRoute('shop');
          return $this->redirectToRoute('indexlogged', ['salesordertipoid' => 1]);
        }
        //return $this->redirectToRoute('indexlogged', ['salesordertipoid' => 1]);

      /*  $this->composer->setData('resaccounts', $this->cookieiteratorresult->getContent('pwdverify'));

        $this->composer->extend('body', controllerDIR_BASE.'view/login/passwordcheck.php');
        $this->composer->setData('PageTitle', 'Verifica Password OK');

        $response = $this->composer->render();

        return new Response($response);*/
      }

    } else {

      $this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
				['name' => 'pwdverify','entity' => 'account','method' => 'getlabeluserbycookierforpassword']
			));
      if (is_null($this->cookieiteratorresult->getContent('pwdverify'))){

        //se è null vuol dire che c'è stato un errore allora ripropongo schermata login (potrei inserire messaggio mail non valida a scomparsa)
        return $this->redirectToRoute('scegliaccount');

      } else {
        $this->composer->setData('resaccounts', $this->cookieiteratorresult->getContent('pwdverify'));
        $xy = $this->cookieiteratorresult->getContent('pwdverify')->get_col('errorlogin'); // il valore errorlogin del primo elemento
        $xe = $this->cookieiteratorresult->getContent('pwdverify')->get_col('errordoubleauthentication');
        $da = $this->cookieiteratorresult->getContent('pwdverify')->get_col('doubleauthentication');
        $dat = $this->cookieiteratorresult->getContent('pwdverify')->get_col('doubleauthtested');

        $this->composer->setData('errorlogin', $xy);
        $this->composer->setData('errordoubleauthentication', $xe);
        $this->composer->setData('doubleauthentication', $da);
        $this->composer->setData('doubleauthtested', $dat);
        $this->composer->extend('body', controllerDIR_BASE.'view/login/passwordcheck.php');
        $this->composer->setData('PageTitle', 'Verifica Password');

        $response = $this->composer->render();

        return new Response($response);
      }

    }
  }

  #[Route('/logout', name:'logout')]
  public function logout(Request $request)
  {
    $this->construct_surrogato();

    require_once(controllerDIR_BASE.'src/php/wb_cookie/WB_CookieMetod.php');

    $this->cookiemetod = new WB_CookieMetod($this->database,$this->serverconfig);

    $this->cookieiteratorresult = $this->cookiemetod->ExecInstructionArrayCookie(array(
      ['name' => 'logout','entity' => 'account','method' => 'logout']
    ));

    return $this->redirectToRoute('indexlogged', ['salesordertipoid' => 1]);
    //return $this->redirectToRoute('shop');

  }
}
