<?php
  header('Access-Control-Allow-Origin: http://192.168.178.27');
  header('Access-Control-Allow-Methods: GET, POST, PUT');
  header('Access-Control-Allow-Headers: Content-Type');
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->getData('PageTitle', 'Page Title'); ?></title>
      <link rel="stylesheet" id="cassa"  href="/css/cassa.css" type="text/css" media="all" />
      <script type="text/javascript" src="/cassa/backuparticoli.js"></script>
		<?php $this->printBlock( 'head' ); ?>
  </head>
  <body onload="setfocusonsearchbar();">
		<?php $this->printBlock('body'); ?>
    <div class="maindiv">
    <div class="screentop">
        <div class="screensearchbar">
          <input type="text" id="searchbar" name="ricercaarticoli" onkeyup="controllatastopremuto();">
          <div id="ok" onclick="ricercainputbarre();">🆗</div>
          <div class="Totalxl"><label>SUBTOTALE:</label><div id="totaleprovvisorio">0</div></div>
        </div>
        <div class="screenaltro"><div id="altro" onclick="openfinestraopzionimain();">⚙️</div><label id="labelconto">Conto 1</label></div>
      </div>
      <div class="screendisplay">
        <div class="screendisplaytotal">
          <div class="container"><label>SUBTOTALE</label><div>0</div></div>
          <div class="container"><label>Numero Articoli</label><div id="numeroarticoli">0</div></div>
          <div class="container"><label>Incassato Contanti</label><div id="incassatocontanti">0</div></div>
          <div class="container"><label>Incassato Carte</label><div id="incassatocarte">0</div></div></div>
        <div class="screendisplaytitle"><div class="description">Descrizione</div><div class="quantity">Quantità</div><div class="unitprice">UnitPrice</div><div class="department">Reparto</div><div class="quantityprice">Importo</div></div>
        <div class="conto" id="conto1">
          <ul id="ulconto1"></ul>
        </div>
        <div class="conto" id="conto2" style="display:none;">
          <ul id="ulconto2"></ul>
        </div>
        <div class="conto" id="conto3" style="display:none;">
          <ul id="ulconto3"></ul>
        </div>
      </div>
      <div class="screentasti">
        <div class="screentastitop">
          <div class="screentastierino">
            <div class="contenitoretasti">
              <div class="tastoadattivo33testo" onclick="removelastchar();">
                <label style="font-size: 15px;">←<br>CANCELLA</label>
              </div>
              <div class="tastoadattivo33testo" onclick="confermaeliminaconto();">
                <label style="font-size: 15px;">ELIMINA<br>CONTO</label>
              </div>
              <div class="tastoadattivo33testo" onclick="rimuovielementoselezionato();" id="tastoeliminariga">
                <label style="font-size: 15px;">ELIMINA<br>RIGHE</label>
              </div>
            </div>
            <div class="contenitoretasti">
              <div class="tastoadattivo33" onclick="scrivi('7');">
                <label>7</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('8');">
                <label>8</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('9');">
                <label>9</label>
              </div>
            </div>
            <div class="contenitoretasti">
              <div class="tastoadattivo33" onclick="scrivi('4');">
                <label>4</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('5');">
                <label>5</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('6');">
                <label>6</label>
              </div>
            </div>
            <div class="contenitoretasti">
              <div class="tastoadattivo33" onclick="scrivi('1');">
                <label>1</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('2');">
                <label>2</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi('3');">
                <label>3</label>
              </div>
            </div>
            <div class="contenitoretasti">
              <div class="tastoadattivo33" onclick="scrivi('0');">
                <label>0</label>
              </div>
              <div class="tastoadattivo33" onclick="scrivi(',');">
                <label>,</label>
              </div>
              <div class="tastoadattivo33" id="tastovariaqta" onclick="tastomodificaqta();">
                <label>X</label>
              </div>
            </div>
          </div>
          <div class="screenreparti">
            <div class="tastoreparto" onclick="tastoReparto('reparto bibite',rep3);">
              <label>reparto 22</label>
            </div>
            <div class="tastoreparto" onclick="tastoReparto('reparto salumi',rep2);">
              <label>reparto 10</label>
            </div>
            <div class="tastoreparto" onclick="tastoReparto('reparto formaggi',rep1);">
              <label>reparto 4</label>
            </div>
            <div class="tastoemetti" onclick="incassocontanti();" style="background-color :#FFD700;">
              <label>cassa 💵</label>
            </div>
            <div class="tastoemetti" onclick="incassocarte();" style="background-color :#7FFFD4;">
              <label>carte 💳</label>
            </div>
          </div>
        </div>
        <div class="screentastibottom">
          <div class="tastobottom" onclick="addprintRecItem('1','borsetta grande','1','0.15',rep3,'1','5847',false);">
            <label style="font-size: 15px;">borsetta<br>Grande</label>
          </div>
          <div class="tastobottom" onclick="addprintRecItem('1','borsetta media','1','0.1',rep3,'1','5848',false);">
            <label style="font-size: 15px;">borsetta<br>Media</label>
          </div>
          <div class="tastobottom" onclick="addprintRecItem('1','borsetta piccola','1','0.05',rep3,'1','5742',false);">
            <label style="font-size: 15px;">borsetta<br>Piccola</label>
          </div>
          <div class="tastobottom" style="background-color :#EEE8AA;" onclick="testlotteria();">
            <label style="font-size: 15px;">codice<br>lotteria</label>
          </div>
        </div>
      </div>
      <div id="finestraopzioni">
        <div id="mainopzioni">
          <div class="testata">
            <span class="close" onclick="closefinestraopzionimain();">&times;</span>
            <label id="labelopzioni">OPZIONI</label>
          </div>
          <div class="contenitoreconti">
            <div class="tastoconto" onclick="cambiaconto(1);">
              <label>Conto 1</label>
            </div>
            <div class="tastoconto" onclick="cambiaconto(2);">
              <label>Conto 2</label>
            </div>
            <div class="tastoconto" onclick="cambiaconto(3);">
              <label>Conto 3</label>
            </div>
            <div class="tastoconto" onclick="mostraopzioni();">
              <label>Mostra Opzioni</label>
            </div>
          </div>
          <div class="contenuto">
            <div id="menuopzioni" style="display:none;">
              <div class="tastoicon" onclick="confermachiusurafiscale();">
                <label>🧾 Chiusura Fiscale</label>
              </div>
              <div class="tastoicon" onclick="aperturaCassetto();">
                <label>💰 Apertura Cassetto</label>
              </div>
              <div class="tastoicon" onclick="confermachidiapplicazione();">
                <label>💤 Chiudi Applicazione</label>
              </div>
              <div class="tastoicon" onclick="resetCassaFiscale();">
                <label>🚽 Reset Cassa</label>
              </div>
              <div class="tastoicon" onclick="modalitabackupon();">
                <label>💾 modalitàbackup ON</label>
              </div>
            </div>
            <?php
            $reskey = $this->getData('keycassa');
            if( is_null($reskey) == false and $reskey->count() > 0) {
            ?>

            <?php
            foreach ($reskey as $row) {
              echo '<div style="background-color:'.$row->getCol('backgroundcolor').';" class="tastobottom" onclick="addkeycassa(\'1\',\''.$row->getCol('description').'\',\'1\',\''.$row->getCol('pricecassa').'\','.$row->getCol('cashdepartment').',\'1\',\''.$row->getCol('id').'\',false);"><label style="font-size: 15px; background-color:white;">'.$row->getCol('descriptiontasto').'</label></div>';
            }
            ?>


            <?php
            }
            ?>
          </div>
        </div>
      </div>
      <div id="finestraattesa">
        <div>
          Attendi...
        </div>
      </div>
      <div id="finestraconferma">
        <div class="testo" id="testofinestraconferma">
          Confermi la tua scelta?
        </div>
        <div class="scelte">
          <div><label id="labelconfermaconferma">SI</label></div>
          <div><label onclick="chiudifinestraconferma();">NO</label></div>
        </div>
      </div>
    </div>
    <script>
      modalitàbackup = false;
      modalitàtest = false;

      //QUESTI REPARTI VANNO VARIATI ANCHE QUI IN CASO DI VARIAZIONI REPARTI IVA //perchè questi sono usati per i tasti reparto e borsette
      const rep1 = 1;//iva al 4%
      const rep2 = 2;//iva al 10%
      const rep3 = 3;//iva al 22%
      const rep4 = 4;//iva al 5%
      const rep5 = 5;//esente iva art 124 DL 34/2020

      const fpip = "192.168.178.12";
      //const fpip = "192.168.10.197"; // TEST da berti e biancotto
      const importomaxscontrino = 400;// importo massimo per scontrino segnalato come avviso
      const importomaxriga = 250;// importo massimo per riga scontrino
      const importomaxriganoconferma = 99;// importo massimo per riga scontrino senza conferma
      const importomaxqta = 99;// importo max moltiplica qta
      const importomaxincassocontanti = 999;// importo max incasso contanti
      const importomaxincassocarte = 999;// importo max incasso carte

      function testlotteria(){
        //addprintRecLotteryId("1","PGW2JF3T","Codice Lotteria");
        addprintRecLotteryId("1","12345678","Codice Lotteria");
      }

      function createRigaLotteria(lotteryid,comment){
        var div = document.createElement("div");
        div.setAttribute("class", "reclotteryid");
        div.setAttribute("style", "background-color:#87CEEB");
        var divlotteryid = document.createElement("div");
        divlotteryid.setAttribute("class", "lotteryid");
        divlotteryid.appendChild(document.createTextNode(lotteryid));
        var divcomment = document.createElement("div");
        divcomment.setAttribute("class", "comment");
        divcomment.appendChild(document.createTextNode(comment));
        div.appendChild(divlotteryid);
        div.appendChild(divcomment);
        return div;
      }

      function addprintRecLotteryId(ope,lotteryid,comment){
            openfinestraattesa();
            deselezionariga();
            var ul = document.getElementById("ulconto"+this.conto);
            var li = document.createElement("li");
            li.appendChild(createRigaLotteria(lotteryid,comment));
            li.setAttribute("title", "printRecLotteryId");
            li.setAttribute("ope", ope);
            li.setAttribute("lotteryid", lotteryid);
            li.setAttribute("comment", comment);
            li.setAttribute("onclick", "selezionariga(this)");
            ul.appendChild(li);
            removeallchar();
            visualizzamodifiche('Cod Lotteria',lotteryid);
            audioconferma();
            closefinestraattesa();
      }

      /*PREVENT DOPPIA PRESSIONE*/

      const temposubmit = 1000; // 2000 = 2 secondi

      var datelastsubmit = new Date()-temposubmit;//in questo modo il primo submit non ha nessun tempo di attesa
      var numberlastsubmit = 0;

      var numberlastaccess = 0;

      /*Questa combinazione di 2 store consente solo 1 click ogni due secondi*/

      function formpreventsubmitdoppi(){
        mynewdate = new Date();
        mynewnumber = ++numberlastaccess;
        if(mynewnumber==numberlastsubmit+1){
          //se è il successivo all'ultimo submit
          formpreventsubmitdoppisubmit(mynewdate,mynewnumber);
        } else {
          numberlastsubmit = mynewnumber;
          //console.log('exit_no successivo'+mynewnumber);
        }
      }


      function formpreventsubmitdoppisubmit(accessdate,accessnumber){
        differenza = Math.abs(accessdate - datelastsubmit);
        if(differenza > temposubmit){
          numberlastsubmit = accessnumber;
          datelastsubmit = mynewdate;
          //console.log('cliccato'+accessnumber);
          document.getElementById('submitthisform').click();
        } else {
          numberlastsubmit = accessnumber;
          //console.log('NOcliccato'+accessnumber);
        }
      }

      /*FINE DOPPIA PRESSIONE*/

      function incassocontanti(){
        incasso("Incasso Contanti","0","0","#FFD700");
      }

      function incassocarte(){
        //incasso("Incasso Carte di Pagamento","2","1","#7FFFD4");
        document.getElementById("testofinestraconferma").innerHTML = "Confermi di Richiedere incassocarte?";
        //devo sempre settare l'onclick altrimenti prende il vecchio
        document.getElementById("labelconfermaconferma").setAttribute("onclick", "confermaincassocarte();");//in pratica setto nel tasto si la function da eseguire alla conferma
        aprifinestraconferma();
      }

      function confermaincassocarte() {
        chiudifinestraconferma();
        incasso("Incasso Carte di Pagamento","2","1","#7FFFD4");
      }

      function incasso(description,paymentType,index,colorbg){
        openfinestraattesa();
        inputbarparam = document.getElementById("searchbar").value.replace(",",".");
        //controllo che non ci siano solo spazi vuoti e che il numero sia convertibile in FLOAT
        if(inputbarparam.length > 0 && inputbarparam.replace(/ /g,'') > 0 && !isNaN(parseFloat(inputbarparam))){
          addprintRecTotal(colorbg,'1',description,parseFloat(inputbarparam),paymentType,index,'1');
        } else {
          if(inputbarparam.length == 0){
            //quando l'importo dell'incasso è 0 lui chiude lo scontrino per l'intero importo (ed emette)
            openfinestraattesa();
            deselezionariga();
            removeallchar();
            visualizzamodifiche(description,0,true,paymentType,index,true);//passo anche qui i parametri specifici per emissione dello scontrino
            audioconferma();
            closefinestraattesa();
          } else {
            audioerrore();
          }
        }
        removeallchar();
        closefinestraattesa();
      }

      function createRigaIncassoScreen(colorbg,description,payment,paymentType,index){
        var div = document.createElement("div");
        div.setAttribute("class", "rectotal");
        div.setAttribute("style", "background-color:"+colorbg);
        var divdescription = document.createElement("div");
        divdescription.setAttribute("class", "description");
        divdescription.appendChild(document.createTextNode(description));
        var divpayment = document.createElement("div");
        divpayment.setAttribute("class", "payment");
        divpayment.appendChild(document.createTextNode(payment));
        var divpaymenttype = document.createElement("div");
        divpaymenttype.setAttribute("class", "paymenttype");
        divpaymenttype.appendChild(document.createTextNode(paymentType));
        var divindex = document.createElement("div");
        divindex.setAttribute("class", "index");
        divindex.appendChild(document.createTextNode(index));
        div.appendChild(divdescription);
        div.appendChild(divpayment);
        div.appendChild(divpaymenttype);
        div.appendChild(divindex);
        return div;
      }

      function addprintRecTotal(colorbg,operator,description,payment,paymentType,index,justification){
        if ((payment<=importomaxincassocontanti) && (payment<=importomaxincassocarte)) {//se importo inferiore a importo massimo possibile
            openfinestraattesa();
            deselezionariga();
            importoriga = roundPRICE(payment);
            var ul = document.getElementById("ulconto"+this.conto);
            var li = document.createElement("li");
            li.appendChild(createRigaIncassoScreen(colorbg,description,importoriga,paymentType,index));
            li.setAttribute("title", "printRecTotal");
            li.setAttribute("operator", operator);
            li.setAttribute("description", description);
            li.setAttribute("payment", importoriga);
            li.setAttribute("paymenttype", paymentType);
            li.setAttribute("index", index);
            li.setAttribute("justification", justification);
            li.setAttribute("onclick", "selezionariga(this)");
            ul.appendChild(li);
            removeallchar();
            visualizzamodifiche(description,importoriga,false,paymentType,index,true);
            audioconferma();
            closefinestraattesa();
        } else {
          alert('importo INCASSO troppo elevato (max contanti->'+importomaxincassocontanti+' €) (max carte->'+importomaxincassocarte+' €)');
          setfocusonsearchbar();
        }
      }

      //questa funzione viene richiamata indirettamente da visualizzamodifiche()
      function chiudiscontrino(pdescription='',richiediemissione = false,paymentType = null,index = null,dati = null){
        openfinestraattesa();
        retv = getScontrinoXML(pdescription,richiediemissione,paymentType,index,dati);
        xmlScontrinoForDB = getScontrinoForDBNoParser(pdescription,richiediemissione,paymentType,index,dati);
        if (retv != null){
          tinco = dati.totaleincassocontanti;
          tinca = dati.totaleincassocarte;
          if (richiediemissione){
            differenza = dati.totaleprovvisorio - (dati.totaleincassocontanti + dati.totaleincassocarte);
            if (differenza > 0){
              if (paymentType==0 && index==0){
                tinco = tinco + differenza;
              } else {
                if (paymentType==2 && index==1){
                  tinca = tinca + differenza;
                }
              }
            }
          }
          subt=roundPRICE(dati.totaleprovvisorio);
          setdisplaytotaleprovvisorio(subt,dati.numeroarticoli,roundPRICE(tinco),roundPRICE(tinca));

          sendFP(retv,true,xmlScontrinoForDB);
          deselezionariga();//la eseguo per togliere eventuali righe selezionate
          removeallchar();
          var ul = document.getElementById("ulconto"+this.conto);
          ul.innerHTML = "";
        } else {
          audioerrore();
          console.log("NUSSUN ARTICOLO NELLO SCONTRINO");
        }
        closefinestraattesa();
      }

      function modalitabackupon(){
        modalitàbackup = true;
        document.getElementById("conto1").style.backgroundColor = "#f76562";
        closefinestraopzionimain();
      }

      function resetCassaFiscale() {
        sendFP(getResetPrinterXML());
        closefinestraopzionimain();
      }

      function aperturaCassetto() {
        sendFP(getApriCassettoXML());
        closefinestraopzionimain();
      }

      rset = null;
      numeroultimoscontrino = 0;

      //gestoredataScontrinoDB(getScontrinoForDBParse(getScontrinoForDBNoParser()));

      function sendFP(xml,isscontrinovendita = false, xmlfordb=''){
        if (modalitàtest == false){
          var xmlhttp = new XMLHttpRequest();

          //INIZIO parte aggiunta per salvare a db isscontrinovendita
          if (isscontrinovendita){
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    fiscalReceiptNumber = 0;
                    fiscalReceiptAmount = 0;
                    fiscalReceiptDate = 0;
                    fiscalReceiptTime = 0;
                    zRepNumber = 0;
                   // Typical action to be performed when the document is ready:
                    var childNodes = xmlhttp.responseXML.documentElement.firstElementChild.firstElementChild;
                    if (childNodes.attributes.success.nodeValue.localeCompare("true")==0 && childNodes.attributes.status.nodeValue.localeCompare("2")==0) {
                      //console.log(xmlhttp.responseXML.documentElement.firstElementChild);
                      //console.log(childNodes.firstElementChild);
                      childNodes2 = childNodes.firstElementChild.childNodes;
                      for(var i = 0; i < childNodes2.length; i++)
                      {
                        //console.log(childNodes2[i]);
                        if (childNodes2[i].firstChild != null){
                          if ((childNodes2[i].tagName).localeCompare("fiscalReceiptNumber")==0){
                          fiscalReceiptNumber=Number(childNodes2[i].textContent);
                          }
                          if ((childNodes2[i].tagName).localeCompare("fiscalReceiptAmount")==0){
                            fiscalReceiptAmount=childNodes2[i].textContent;
                          }
                          if ((childNodes2[i].tagName).localeCompare("fiscalReceiptDate")==0){
                            fiscalReceiptDate = childNodes2[i].textContent;
                          }
                          if ((childNodes2[i].tagName).localeCompare("fiscalReceiptTime")==0){
                            fiscalReceiptTime = childNodes2[i].textContent;
                          }
                          if ((childNodes2[i].tagName).localeCompare("zRepNumber")==0){
                            zRepNumber = childNodes2[i].textContent;
                          }
                        }
                      }
                      if (fiscalReceiptNumber>numeroultimoscontrino){
                        console.log("EMETTO LO SCONTRINO-> devo anche sovrascrivere il numero dell'ultimo scontrino emesso dopo che ho ricevuto la risposta dal server");
                        gestoredataScontrinoDB(getScontrinoForDBParse(xmlfordb,fiscalReceiptNumber,fiscalReceiptAmount,fiscalReceiptDate,fiscalReceiptTime,zRepNumber));
                      }
                    }
                }
            };
          }
          //FINE parte aggiunta per salvare a db isscontrinovendita

          xmlhttp.open("POST", "http://"+fpip+"/cgi-bin/fpmate.cgi", true);
          xmlhttp.send(xml);
          rset=xmlhttp;
        } else {
          console.log("sendFP: modalità TEST xml non inviato");
        }
      }

      //importati

      function getScontrinoForDBNoParser(pdescription='',richiediemissione = false,paymentType = null,index = null,dati = null){

          var continuaciclo=true;
          var xmlrecitem="";
          var xmlrectotal="";
          var xmlreclottery="";
          var ul = document.getElementById("ulconto"+this.conto);
          if(ul.firstElementChild!=null){
            var lix=ul.firstElementChild;
            while(continuaciclo){
              if (lix.getAttribute("title") === "printRecItem"){
                description=lix.getAttribute("description").substring(0, 37);
                idarticolo=lix.getAttribute("idarticolo");
                xmlrecitem=xmlrecitem+"<printRecItem operator=\"1\" idarticolo=\""+idarticolo+"\" description=\""+description+"\" quantity=\""+lix.getAttribute("quantity")+"\" unitPrice=\""+lix.getAttribute("unitPrice")+"\" department=\""+lix.getAttribute("department")+"\" justification=\""+lix.getAttribute("justification")+"\"/>";
              } else {
                if (lix.getAttribute("title") === "printRecTotal"){
                  description=lix.getAttribute("description").substring(0, 37);
                  xmlrectotal=xmlrectotal+"<printRecTotal operator=\"1\" description=\""+description+"\" payment=\""+lix.getAttribute("payment")+"\" paymentType=\""+lix.getAttribute("paymenttype")+"\" index=\""+lix.getAttribute("index")+"\" justification=\""+lix.getAttribute("justification")+"\"/>";
                } else {
                  if (lix.getAttribute("title") === "printRecLotteryId"){
                    xmlreclottery = "<printRecLotteryId Ope=\"1\" lotteryCode=\""+lix.getAttribute("lotteryid")+"\" comment=\""+lix.getAttribute("comment")+"\"/>";
                    //xmlreclottery = "<printRecLotteryID Code=\"PGW2JF3T\" Ope=\"1\"/>";
                    xmlreclottery ="";
                  }
                }
              }

              if(lix === ul.lastElementChild) {
                continuaciclo=false;
              } else {
                lix=lix.nextElementSibling;
              }
            }
          } else {
            console.log("CICLO Creazione XML scontrino: Nessuna riga nello scontrino");
            return null;
          }
          if (richiediemissione){
            differenza = dati.totaleprovvisorio - (dati.totaleincassocontanti + dati.totaleincassocarte);
            if (differenza > 0){
              differenza=roundPRICE(differenza);
              description=pdescription.substring(0, 37);
              xmlrectotal=xmlrectotal+"<printRecTotal operator=\"1\" description=\""+description+"\" payment=\""+differenza+"\" paymentType=\""+paymentType+"\" index=\""+index+"\" justification=\"1\"/>";
            }
          }

          //var parser = new DOMParser();
          //var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
          //xml = xml + "<body>"+xmlrecitem+xmlreclottery+xmlrectotal+"</body>";

          //var xmlDoc = parser.parseFromString(xml, "application/xml");
          //console.log(xml);
          //return xmlDoc;

          var xml = xmlrecitem+xmlreclottery+xmlrectotal;
          return xml;
      }

      function getScontrinoForDBParse(xmlsenzaparser='',fiscalReceiptNumber = 0,fiscalReceiptAmount = 0,fiscalReceiptDate = 0,fiscalReceiptTime = 0,zRepNumber=0){

        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<body fiscalReceiptNumber =\""+fiscalReceiptNumber+"\" fiscalReceiptAmount = \""+fiscalReceiptAmount+"\" fiscalReceiptDate =\""+fiscalReceiptDate+"\" fiscalReceiptTime = \""+fiscalReceiptTime+"\" zRepNumber = \""+zRepNumber+"\">"+xmlsenzaparser+"</body>";

        var xmlDoc = parser.parseFromString(xml, "application/xml");
        console.log(xml);
        return xmlDoc;

      }

      function gestoredataScontrinoDB(xmlScontrinoForDB){
      	var xhttp = new XMLHttpRequest();
      	xhttp.onreadystatechange = function() {
      		if (this.readyState == 4 && this.status == 200) {
      		//	this.qmd = this.response.data;
      		 //for (var i in this.response.data) {
      		//	 console.log(this.response.data[i]);
      		// }
          console.log(this.response);
      		}
      	};
      	xhttp.open("POST", "cassa/addscontrino", true);
      	//xhttp.responseType = 'json';
      	//xhttp.setRequestHeader("Content-Type", "text/xml; charset=utf-8");
      	xhttp.send(xmlScontrinoForDB);
      }

      function setfocusonsearchbar() {
        document.getElementById("searchbar").focus();
      }

      function searchbybarinbackup(bar){
        //ricerca prima in backup BILANCIA
        var indexbarre = -1;
        if (bar.length == 13) {
          //2CODICEXXXXXC
          var barbase = bar.substring(0,7);
          for(var i=0; i<bckbilancia.length; i++) {
            if(bckbilancia[i].bar.substring(0,7) == barbase) {
              indexbarre=i;
              break;
            }
          }
          if (indexbarre > -1){
            interibarre = bar.substring(7, 10);
            decimalibarre = bar.substring(10, 12);
            prezzo = parseFloat(interibarre+'.'+decimalibarre);
            aggiungirigadabarrebilanciabackup(bckbilancia[indexbarre], prezzo);
          } else {
            //è un barre da 13 ma non è presente tra quelli in bilancia allora provo a vedere se è tra i barre peso
            for(var i=0; i<bckpeso.length; i++) {
              if(bckpeso[i].bar.substring(0,7) == barbase) {
                indexbarre=i;
                break;
              }
            }
            if (indexbarre > -1){
              interibarre = bar.substring(7, 9);
              decimalibarre = bar.substring(9, 12);
              peso = parseFloat(interibarre+'.'+decimalibarre);
              aggiungirigadabarrepesobackup(bckpeso[indexbarre], peso);
            }
          }
        }
        if (indexbarre < 0){
          var index = -1;
          for(var i=0; i<bck.length; i++) {
            if(bck[i].bar == bar) {
              index=i;
              break;
            }
          }
          if (index > -1){
            aggiungirigadabarre(bck[index]);
          } else {
            removeallchar();
            audioerrore();
            console.log("RICERCA IN BACKUP: nessun articolo trovato");
          }
        }
      }

      function controllatastopremuto(){
        if (event.keyCode === 13) {
          ricercainputbarre();
        }
      }

      function ricercainputbarre(){
        openfinestraattesa();
        deselezionariga();
        inputbarparam = document.getElementById("searchbar").value;
        if(inputbarparam.length > 0 && inputbarparam.replace(/ /g,'') > 0){
          if(modalitàbackup){
            searchbybarinbackup(inputbarparam);
          } else {
            ricercabarre('bar='+inputbarparam);
          }
        }
        closefinestraattesa();
      }

      function ricercabarre(param){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
           trovato = false;
           for (var i in this.response.data) {
             trovato = true;
             returnd = { id: this.response.data[i].id, bar:this.response.data[i].bar, unitPrice: this.response.data[i].unitPrice, description: this.response.data[i].description, department: this.response.data[i].department, qty:this.response.data[i].qty};
             aggiungirigadabarreconqty(returnd);
             if (i>1){
               alert("Errore : più di un barre corrispondente; contattare assistenza;")
             }
           }
           if (trovato == false){
             removeallchar();
             audioerrore();
           }
          }
        };
        xhttp.open("POST", "/cassa/ricercabarre", true);
        xhttp.responseType = 'json';
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send(param);
      }

      function aggiungirigadabarre(data){
        addprintRecItem("1",data.description,"1",data.unitPrice,data.department,"1",data.id,false);
      }

      function aggiungirigadabarreconqty(data){
        addprintRecItem("1",data.description,data.qty,data.unitPrice,data.department,"1",data.id,false);
      }

      function aggiungirigadabarrebilanciabackup(data,prezzo){
        addprintRecItem("1",data.description,"1",prezzo,data.department,"1",data.id,false);
      }

      function aggiungirigadabarrepesobackup(data,peso){
        addprintRecItem("1",data.description,peso,Math.round(data.Pricekg*100,2)/100,data.department,"1",data.id,false);
      }

      var conto=1;

      function cambiaconto(contox){
        openfinestraattesa();
        deselezionariga();
        removeallchar();
        document.getElementById("conto1").style.display = 'none';
        document.getElementById("conto2").style.display = 'none';
        document.getElementById("conto3").style.display = 'none';
        this.conto = contox;
        document.getElementById("conto"+contox).style.display = 'block';
        document.getElementById("labelconto").innerHTML = 'Conto '+contox;
        visualizzamodifiche('CAMBIA CONTO','');
        closefinestraopzionimain();
        closefinestraattesa();
      }

      function addkeycassa(operator,description,quantity,unitPrice,department,justification,idarticolo,importoconfermato=false){
        addprintRecItem(operator,description,quantity,unitPrice,department,justification,idarticolo,importoconfermato);
        closefinestraopzionimain();
      }

      function mostraopzioni(){
        openfinestraattesa();
        deselezionariga();
        removeallchar();
        document.getElementById("menuopzioni").style.display = '';
        document.getElementById("labelopzioni").style.display = '';
        closefinestraattesa();
      }

      function audioconferma(){
        if (modalitàtest==false){
          var audio = new Audio('audio/bipcassa.mp3');
          audio.play();
        } else {
          console.log("riproduzione audio CONFERMA");
        }
      }

      function audioeliminazione(){
        if (modalitàtest==false){
          var audio = new Audio('audio/bipeliminazione.mp3');
          audio.play();
        } else {
          console.log("riproduzione audio ELIMINAZIONE");
        }
      }

      function audioerrore(){
        if (modalitàtest==false){
          var audio = new Audio('audio/biperrorecassa.mp3');
          audio.play();
        } else {
          console.log("riproduzione audio ERRORE");
        }
      }

      function audioclick(){
        if (modalitàtest==false){
          var audio = new Audio('audio/bipclick.mp3');
          audio.play();
        } else {
          console.log("riproduzione audio CLICK");
        }
      }

      function openfinestraattesa(){
        document.getElementById("finestraattesa").style.display = 'block';
      }

      function closefinestraattesa(){
        document.getElementById("finestraattesa").style.display = 'none';
      }

      function openfinestraopzionimain(){
        document.getElementById("menuopzioni").style.display = 'none';
        document.getElementById("labelopzioni").style.display = 'none';
        document.getElementById("mainopzioni").style.display = 'block';
        document.getElementById("finestraopzioni").style.display = 'block';
      }

      function closefinestraopzionimain(){
        document.getElementById("mainopzioni").style.display = 'none';
        document.getElementById("finestraopzioni").style.display = 'none';
      }

      function scrivi(carattere){
        if (document.getElementById("searchbar").value.length < 30){
          audioclick();
          document.getElementById("searchbar").value = document.getElementById("searchbar").value + carattere;
        }
      }

      function removeallchar(){
        if (document.getElementById("searchbar").value.length > 0){
          document.getElementById("searchbar").value = "";
        }
      }

      function removelastchar(){
        if (document.getElementById("searchbar").value.length > 0){
          audioclick();
          document.getElementById("searchbar").value = document.getElementById("searchbar").value.slice(0, -1);
        }
      }

      var elementselected = null;

      function selezionariga(element){
        openfinestraattesa();
        deselezionariga();
        removeallchar();
        document.getElementById("tastoeliminariga").style.backgroundColor = "green";
        document.getElementById("tastovariaqta").style.backgroundColor = "green";
        this.elementselected = element;
        this.elementselected.style.backgroundColor = "red";
        closefinestraattesa();
      }

      function deselezionariga(){
        if (this.elementselected != null) {
          this.elementselected.style.backgroundColor = "transparent";
        }
        this.elementselected = null;
        document.getElementById("tastoeliminariga").style.backgroundColor = "transparent";
        document.getElementById("tastovariaqta").style.backgroundColor = "transparent";
        setfocusonsearchbar();
      }

      function rimuovielementoselezionato(){
        removeallchar();
        if (elementselected != null) {
          elementselected.remove();
          visualizzamodifiche(this.elementselected.getAttribute("description"),'(ELIMINATO)');
          elementselected = null;
          audioeliminazione();
          document.getElementById("tastoeliminariga").style.backgroundColor = "transparent";
          document.getElementById("tastovariaqta").style.backgroundColor = "transparent";
        }
        setfocusonsearchbar();
      }

      function tastomodificaqta(){
        openfinestraattesa();
        inputbarparam = document.getElementById("searchbar").value.replace(",",".");
        //controllo che non ci siano solo spazi vuoti e che il numero sia convertibile in FLOAT
        if(inputbarparam.length > 0 && inputbarparam.replace(/ /g,'') > 0 && !isNaN(parseFloat(inputbarparam))){
          modificaqtaelementoselezionato(parseFloat(inputbarparam));
        } else {
          audioerrore();
        }
        removeallchar();
        closefinestraattesa();
      }

      function modificaqtaelementoselezionato(quantity/*FLOAT*/){
        if (elementselected != null) {
          unitPrice = parseFloat(elementselected.getAttribute("unitPrice").replace(",","."));
          if (((quantity*unitPrice) <= importomaxriga) && quantity <= importomaxqta) {//se importo inferiore a importo massimo per riga AND qta inferiore a max
              var importoriga = roundPRICE(quantity*unitPrice,2);//calcolo qui l'importo parziale della riga
              quantity = roundQTA(quantity);
              elementselected.setAttribute("quantity", quantity);
              elementselected.children[0].children[1].innerHTML = quantity;
              elementselected.children[0].children[4].innerHTML = importoriga;
              visualizzamodifiche(this.elementselected.getAttribute("description"),'(MOD QTA) '+importoriga);
              deselezionariga();
              audioconferma();
              document.getElementById("tastoeliminariga").style.backgroundColor = "transparent";
              document.getElementById("tastovariaqta").style.backgroundColor = "transparent";
          } else {
            alert('importo RIGA troppo elevato max('+importomaxriga+' €) oppure quantità superiore a massima quantità: '+importomaxqta);
          }
        } else {
          console.log("nessun elemento SELEZIONATO");
        }
      }

      function eliminaconto(){
        deselezionariga();//la eseguo per togliere eventuali righe selezionate
        removeallchar();
        var ul = document.getElementById("ulconto"+this.conto);
        ul.innerHTML = "";
        visualizzamodifiche('','');
        audioeliminazione();
        chiudifinestraconferma();
      }

      function confermaeliminaconto(){
        document.getElementById("testofinestraconferma").innerHTML = "Confermi di Eliminare il conto?";
        //devo sempre settare l'onclick altrimenti prende il vecchio
        document.getElementById("labelconfermaconferma").setAttribute("onclick", "eliminaconto();");//in pratica setto nel tasto si la function da eseguire alla conferma
        aprifinestraconferma();
      }

      function siconfermaimportoriga(){
        chiudifinestraconferma();
        addprintRecItem(conoperator,condescription,conquantity,conunitPrice,condepartment,conjustification,conidarticolo,true);
      }

      var conoperator;
      var condescription;
      var conquantity;
      var conunitPrice;
      var condepartment;
      var conjustification;
      var conidarticolo;


      function confermaimportoriga(operator,description,quantity,unitPrice,department,justification,idarticolo,importoconfermato){
        this.conoperator=operator;
        this.condescription=description;
        this.conquantity=quantity;
        this.conunitPrice=unitPrice;
        this.condepartment=department;
        this.conjustification=justification;
        this.conidarticolo=idarticolo;
        document.getElementById("testofinestraconferma").innerHTML = "Confermi importo riga superiore a "+importomaxriganoconferma+"?";
        //devo sempre settare l'onclick altrimenti prende il vecchio
        document.getElementById("labelconfermaconferma").setAttribute("onclick", "siconfermaimportoriga();");//in pratica setto nel tasto si la function da eseguire alla conferma
        aprifinestraconferma();
      }

      function chiudiapplicazione() {
        window.close();
      }

      function confermachidiapplicazione(){
        document.getElementById("testofinestraconferma").innerHTML = "Confermi di Chiudere l'Applicazione?";
        //devo sempre settare l'onclick altrimenti prende il vecchio
        document.getElementById("labelconfermaconferma").setAttribute("onclick", "chiudiapplicazione();");//in pratica setto nel tasto si la function da eseguire alla conferma
        aprifinestraconferma();
      }

      function chiusurafiscale() {
        chiudifinestraconferma();
        sendFP(getChiusuraFiscaleXML());
        //alert("chiusura fiscale NON INVIATA perchè non ancora configurata");
      }

      function confermachiusurafiscale(){
        document.getElementById("testofinestraconferma").innerHTML = "Confermi di Eseguire la chiusura Fiscale?";
        //devo sempre settare l'onclick altrimenti prende il vecchio
        document.getElementById("labelconfermaconferma").setAttribute("onclick", "chiusurafiscale();");//in pratica setto nel tasto si la function da eseguire alla conferma
        aprifinestraconferma();
      }

      function aprifinestraconferma(){
        document.getElementById("finestraconferma").style.display = 'block';
      }

      function chiudifinestraconferma(){
        document.getElementById("finestraconferma").style.display = 'none';
        setfocusonsearchbar();
      }

      function createRigaArticoloScreen(description,quantity,unitPrice,department,importoriga){
        var div = document.createElement("div");
        div.setAttribute("class", "recitem");
        var divdescription = document.createElement("div");
        divdescription.setAttribute("class", "description");
        divdescription.appendChild(document.createTextNode(description.substring(0,25)));
        var divquantity = document.createElement("div");
        divquantity.setAttribute("class", "quantity");
        divquantity.appendChild(document.createTextNode(quantity));
        var divunitprice = document.createElement("div");
        divunitprice.setAttribute("class", "unitprice");
        divunitprice.appendChild(document.createTextNode(unitPrice));
        var divdepartment = document.createElement("div");
        divdepartment.setAttribute("class", "department");
        divdepartment.appendChild(document.createTextNode(department));
        var divquantityprice = document.createElement("div");
        divquantityprice.setAttribute("class", "quantityprice");
        divquantityprice.appendChild(document.createTextNode(importoriga));
        div.appendChild(divdescription);
        div.appendChild(divquantity);
        div.appendChild(divunitprice);
        div.appendChild(divdepartment);
        div.appendChild(divquantityprice);
        return div;
      }

      function addprintRecItem(operator,description,quantity,unitPrice,department,justification,idarticolo,importoconfermato=false){
        if ((quantity*unitPrice) <= importomaxriga) {//se importo inferiore a importo massimo per riga
          if (((quantity*unitPrice) > importomaxriganoconferma) && importoconfermato==false) {//se importo è maggiore di quello con conferma apro maschera conferma
            confermaimportoriga(operator,description,quantity,unitPrice,department,justification,idarticolo,false);
          } else {
            openfinestraattesa();
            deselezionariga();
            var importoriga = roundPRICE(quantity*unitPrice,2);//calcolo qui l'importo parziale della riga
            quantity = roundQTA(quantity);
            unitPrice = roundPRICE(unitPrice);
            var ul = document.getElementById("ulconto"+this.conto);
            var li = document.createElement("li");
            li.appendChild(createRigaArticoloScreen(description,quantity,unitPrice,department,importoriga));
            li.setAttribute("title", "printRecItem");
            li.setAttribute("operator", operator);
            li.setAttribute("description", description);
            li.setAttribute("quantity", quantity);
            li.setAttribute("unitPrice", unitPrice);
            li.setAttribute("department", department);
            li.setAttribute("justification", justification);
            li.setAttribute("idarticolo", idarticolo);
            li.setAttribute("onclick", "selezionariga(this)");
            ul.appendChild(li);
            removeallchar();
            visualizzamodifiche(description,importoriga);
            audioconferma();
            closefinestraattesa();
          }
        } else {
          alert('importo RIGA troppo elevato max('+importomaxriga+' €)');
          setfocusonsearchbar();
        }
      }

      function tastoReparto(description,department){
        openfinestraattesa();
        inputbarparam = document.getElementById("searchbar").value.replace(",",".");
        //controllo che non ci siano solo spazi vuoti e che il numero sia convertibile in FLOAT
        if(inputbarparam.length > 0 && inputbarparam.replace(/ /g,'') > 0 && !isNaN(parseFloat(inputbarparam))){
          addprintRecItem('1',description,'1',parseFloat(inputbarparam),department,'1','',false);
        } else {
          audioerrore();
        }
        removeallchar();
        closefinestraattesa();
      }

      function visualizzamodifiche(description,importoriga,richiediemissione = false,paymentType = null,index = null,emissioneabilitata/*cioè tasto premuto cassa o carte*/ = false){
        //aggiorno display misuratore fiscale e anche SUBTOTALE e numero articoli su visualizzazione applicazione
        dati=getSubTotale();

        if (dati.totaleincassocarte > dati.totaleprovvisorio && emissioneabilitata) {//metto in and emissioneabilitata così mi da il messaggio di errore solo al tenativo di emissione
          alert('ATTENZIONE INCASSO CARTE maggiore DEL TOTALE SCONTRINO');
          emissioneabilitata = false;
        }

        if (dati.totaleprovvisorio > importomaxscontrino && emissioneabilitata){//metto in and emissioneabilitata così mi da il messaggio di errore solo al tenativo di emissione
          alert("ATTENZIONE TOTALE SCONTRINO SUPERIORE a € "+importomaxscontrino);
          emissioneabilitata = false;
        }

        /*console.log(dati.totaleprovvisorio+'subt');
        console.log(dati.numeroarticoli+'na');
        console.log(dati.totaleincassocontanti+'inc co');
        console.log(dati.totaleincassocarte+'inc ca');*/

        if(
            emissioneabilitata &&
              (
                richiediemissione ||
                (
                  (dati.totaleincassocarte+dati.totaleincassocontanti)>=dati.totaleprovvisorio
                )
              )
          ){//emetti lo scontrino
          if (dati.totaleprovvisorio > 0){
            //alert('emetto');
            chiudiscontrino(description,richiediemissione,paymentType,index,dati);
          }
        } else {
          if (dati.totaleprovvisorio>0  && dati.numeroarticoli>0){
            subt=roundPRICE(dati.totaleprovvisorio);
            setdisplaytotaleprovvisorio(subt,dati.numeroarticoli,roundPRICE(dati.totaleincassocontanti),roundPRICE(dati.totaleincassocarte));
            sendFP(getDisplayTextXML(visualizzarigaimportofp(description,importoriga),visualizzarigaimportofp('SUBTOTALE €',subt)));
          } else {
            setdisplaytotaleprovvisorio(0,0,roundPRICE(dati.totaleincassocontanti),roundPRICE(dati.totaleincassocarte));
            sendFP(getDisplayTextXML(visualizzarigaimportofp('Cassa Aperta',''),visualizzarigaimportofp('SUBTOTALE €','0')));
          }
        }
      }

      function setdisplaytotaleprovvisorio(subtotale,numeroarticoli,incassatocontanti,incassatocarte){
        document.getElementById("totaleprovvisorio").innerHTML = subtotale;
        document.getElementById("numeroarticoli").innerHTML = numeroarticoli;
        document.getElementById("incassatocontanti").innerHTML = incassatocontanti;
        document.getElementById("incassatocarte").innerHTML = incassatocarte;
      }

      function visualizzarigaimportofp(description,importoriga){
        //restituisce importo e testo formattati per una riga del display cassa
        var n = 20-importoriga.length;
        description = (description+'                    ').substring(0,n-1);//da N tolgo uno per tenere lo spazio tra importo e testo di un char
        return description+' '+importoriga;
      }

      function getSubTotale(){
        //ritorna due valori totale conto(-1 se non è presente nessuna riga altrimenti da il subtotale) numeroarticoli nel conto
        var continuaciclo=true;
        var subt=0.00;
        var numeroarticoli=0;
        var tinco=0;
        var tinca=0;
        var ul = document.getElementById("ulconto"+this.conto);
        if(ul.firstElementChild!=null){
          var lix=ul.firstElementChild;
          while(continuaciclo){
            if (lix.getAttribute("title") === "printRecItem"){
              numeroarticoli=numeroarticoli+1;
              q=parseFloat(lix.getAttribute("quantity").replace(",","."));
              u=parseFloat(lix.getAttribute("unitPrice").replace(",","."));
              subt = subt + roundPRICEreturnFLOAT(q*u);
            } else {
              if (lix.getAttribute("title") === "printRecTotal"){
                if (lix.getAttribute("paymenttype") === "0"){//contanti
                  impx=parseFloat(lix.getAttribute("payment").replace(",","."));
                  tinco=tinco+impx;
                } else {
                  if (lix.getAttribute("paymenttype") === "2"){//carte
                    impx=parseFloat(lix.getAttribute("payment").replace(",","."));
                    tinca=tinca+impx;
                  } else {
                    alert("attenzione paymenttype di printRecTotal non codificato");
                  }
                }
              } else {
                //SIGNIFICA CHE SONO IN UNA printRecLotteryId ma non mi serve a niente qui
                //alert("attenzione TIPO RIGA non codificata");
              }
            }
            if(lix === ul.lastElementChild){
              continuaciclo=false;
              return { totaleprovvisorio: subt, numeroarticoli:numeroarticoli, totaleincassocontanti:tinco, totaleincassocarte:tinca };//subt;
            } else {
              lix=lix.nextElementSibling;
            }
          }
        } else {
          console.log("function getSubTotale(): Nessuna riga nello scontrino");
          return { totaleprovvisorio: 0, numeroarticoli:0, totaleincassocontanti:0, totaleincassocarte:0};//-1;
        }
      }

      function getScontrinoXML(pdescription='',richiediemissione = false,paymentType = null,index = null,dati = null){
        var continuaciclo=true;
        var xmlrecitem="";
        var xmlrectotal="";
        var xmlreclottery="";
        var ul = document.getElementById("ulconto"+this.conto);
        if(ul.firstElementChild!=null){
          var lix=ul.firstElementChild;
          while(continuaciclo){
            if (lix.getAttribute("title") === "printRecItem"){
              description=lix.getAttribute("description").substring(0, 37);
              xmlrecitem=xmlrecitem+"<printRecItem operator=\"1\" description=\""+description+"\" quantity=\""+lix.getAttribute("quantity")+"\" unitPrice=\""+lix.getAttribute("unitPrice")+"\" department=\""+lix.getAttribute("department")+"\" justification=\""+lix.getAttribute("justification")+"\"/>";
            } else {
              if (lix.getAttribute("title") === "printRecTotal"){
                description=lix.getAttribute("description").substring(0, 37);
                xmlrectotal=xmlrectotal+"<printRecTotal operator=\"1\" description=\""+description+"\" payment=\""+lix.getAttribute("payment")+"\" paymentType=\""+lix.getAttribute("paymenttype")+"\" index=\""+lix.getAttribute("index")+"\" justification=\""+lix.getAttribute("justification")+"\"/>";
              } else {
                if (lix.getAttribute("title") === "printRecLotteryId"){
                  xmlreclottery = "<printRecLotteryId Ope=\"1\" lotteryCode=\""+lix.getAttribute("lotteryid")+"\" comment=\""+lix.getAttribute("comment")+"\"/>";
                  //xmlreclottery = "<printRecLotteryID Code=\"PGW2JF3T\" Ope=\"1\"/>";
                  xmlreclottery ="";
                }
              }
            }

            if(lix === ul.lastElementChild) {
              continuaciclo=false;
            } else {
              lix=lix.nextElementSibling;
            }
          }
        } else {
          console.log("CICLO Creazione XML scontrino: Nessuna riga nello scontrino");
          return null;
        }
        if (richiediemissione){
          differenza = dati.totaleprovvisorio - (dati.totaleincassocontanti + dati.totaleincassocarte);
          if (differenza > 0){
            differenza=roundPRICE(differenza);
            description=pdescription.substring(0, 37);
            xmlrectotal=xmlrectotal+"<printRecTotal operator=\"1\" description=\""+description+"\" payment=\""+differenza+"\" paymentType=\""+paymentType+"\" index=\""+index+"\" justification=\"1\"/>";
          }
        }
        //console.log(xmlreclottery);
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerFiscalReceipt><beginFiscalReceipt operator=\"1\" />"+xmlrecitem+xmlreclottery+xmlrectotal+"<endFiscalReceipt operator=\"1\" /></printerFiscalReceipt>"
        +"</s:Body></s:Envelope>";

        console.log(xml);
        //<printRecItem operator=\"1\" description=\"PANINO\" quantity=\"1,235\" unitPrice=\"6,00\" department=\"2\" justification=\"1\"/>
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getResetPrinterXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><resetPrinter operator=\"\" /></printerCommand>"

        +"</s:Body></s:Envelope>";
        //console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getQueryXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><queryContentByDate operator=\"1\" dataType=\"1\" fromDay=\"22\" fromMonth=\"12\" fromYear=\"2020\" toDay=\"22\" toMonth=\"12\" toYear=\"2020\"/></printerCommand>"

        +"</s:Body></s:Envelope>";
        console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

    function getDisplayTextXML(testosuperiore = '01234567890123456789'/*max 20 char*/,testoinferiore = '01234567890123456789'/*20 char*/) {
        testosuperiore=(testosuperiore+'                    ').substring(0,20);
        testoinferiore=(testoinferiore+'                    ').substring(0,20);
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><displayText  operator =\"1\" data=\""+testosuperiore+testoinferiore+"\" /></printerCommand>"

        +"</s:Body></s:Envelope>";
        if (modalitàtest){
          console.log("DISPLAY top FISCALE:"+testosuperiore);
          console.log("DISPLAY bot FISCALE:"+testoinferiore);
        }
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }


      function getApriCassettoXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><openDrawer  operator=\"1\" /></printerCommand>"

        +"</s:Body></s:Envelope>";
        //console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getChiusuraFiscaleXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerFiscalReport><printZReport operator=\"\" timeout=\"30\"/></printerFiscalReport>"

        +"</s:Body></s:Envelope>";
        console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getTestAnnulloScontrinoXML(){
        /*Per annullo scontrino basta mettere il numero scontrino e la data corretta qui sotto LA MATRICOLA FP deve corrispondere*/
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerFiscalReceipt><printRecMessage Ope=\"1\" Text=\"VOID 0710 0001 18022021 99MEX035777\" Type=\"4\" Index=\"1\" Font=\"4\" /></printerFiscalReceipt>"

        +"</s:Body></s:Envelope>";
        console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getTestResoParzialeScontrinoXML(){// questo non funziona ??? non sò perchè
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerFiscalReceipt><printRecMessage Ope=\"1\" Text=\"REFUND 0242 0006 10022021 99MEX200944\" Type=\"4\" Index=\"1\" Font=\"4\" /><printRecRefund Ope=\"1\" Text=\"reparto salumi\" Qty=\"1,000\" UnitCost=\"10,00\" Dep=\"2\" Just=\"1\" /><printRecTotal Ope=\"1\" Text=\"Incasso Contanti\" Amount=\"10,00\" Type=\"0\" Index=\"0\" Just=\"1\" /></printerFiscalReceipt>"

        +"</s:Body></s:Envelope>";
        console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getbeginTrainingXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><beginTraining /></printerCommand>"

        +"</s:Body></s:Envelope>";
        //console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function getendTrainingXML() {
        var parser = new DOMParser();
        var xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
        xml = xml + "<s:Envelope xmlns:s=\"" + "http://schemas.xmlsoap.org/soap/envelope/" + "\"><s:Body>"+

        "<printerCommand><endTraining /></printerCommand>"

        +"</s:Body></s:Envelope>";
        //console.log(xml);
        var xmlDoc = parser.parseFromString(xml, "application/xml");
        return xmlDoc;
      }

      function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
      }

      function deleteCookie(cname) {
        document.cookie = cname + "=;" + "expires=Thu, 01 Jan 1970 00:00:00 UTC" + ";path=/";
      }

      function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return "";
      }

      function roundPRICE(n/*numero da arrot*/, digits=2) {
        var negative = false;
        if (digits === undefined) {
            digits = 0;
        }
        if (n < 0) {
            negative = true;
            n = n * -1;
        }
        var multiplicator = Math.pow(10, digits);
        n = parseFloat((n * multiplicator).toFixed(11));
        //console.log(n);
        n = (Math.round(n) / multiplicator).toFixed(digits);
        //console.log(n);
        if (negative) {
            n = (n * -1).toFixed(digits);
        }
        n = n.replace('.',',');
        return n;
      }

      function roundPRICEreturnFLOAT(n/*numero da arrot*/, digits=2) {
        var negative = false;
        if (digits === undefined) {
            digits = 0;
        }
        if (n < 0) {
            negative = true;
            n = n * -1;
        }
        var multiplicator = Math.pow(10, digits);
        n = parseFloat((n * multiplicator).toFixed(11));
        //console.log(n);
        n = (Math.round(n) / multiplicator).toFixed(digits);
        //console.log(n);
        if (negative) {
            n = (n * -1).toFixed(digits);
        }
        //n = n.replace('.',',');
        return parseFloat(n);
      }

      function roundQTA(n/*numero da arrot*/, digits=3) {
        var negative = false;
        if (digits === undefined) {
            digits = 0;
        }
        if (n < 0) {
            negative = true;
            n = n * -1;
        }
        var multiplicator = Math.pow(10, digits);
        n = parseFloat((n * multiplicator).toFixed(11));
        n = (Math.round(n) / multiplicator).toFixed(digits);
        if (negative) {
            n = (n * -1).toFixed(digits);
        }
        n = n.replace('.',',');
        return n;
      }
      </script>
  </body>
</html>
