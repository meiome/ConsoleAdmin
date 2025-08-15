/*
0   UNSENT  open() has not been called yet.
1   OPENED  send() has been called.
2   HEADERS_RECEIVED    send() has been called, and headers and status are available.
3   LOADING Downloading; responseText holds partial data.
4   DONE    The operation is complete.
*/

var qmd = [];

var qmdmethod = null;

var dataoggi = new Date();

function getfirstparameterreq(){
	return JSON.stringify(this.parameterreq);
}

//INIZIO funzioni per non avere doppi click su function javascript
//LA funzione controlla che non venga eseguita due volte la stessa funzione nell'intervallo di tempo prefissato (per stessa funzione si intende la parte del nome che sta prima della parentesi)
const tempoattesaoneclick = 500;

//uso un array perchè la funzione array push non può essere eseguito contemporaneamente, quindi il primo elemento è quello eseguito
var stackclick = [];

//controllo il nome della funzione prima della parentesi da una stringa
function getnamefunction(st){
	var returnval = "";
	var index=0;
	while(index < st.length){
		if(st.charAt(index).localeCompare("(")==0) {
			break;
		} else{
			returnval = returnval + st.charAt(index);
			index = index +1;
		}
	}
	return returnval;
}

//clickoneclick("alert(1)");
//clickoneclick("console.log(1)");
function clickoneclick(funzionedaeseguire){//non mettere il punto e virgola alla fine
	//LA funzione controlla che non venga eseguita due volte la stessa funzione nell'intervallo di tempo prefissato (per stessa funzione si intende la parte del nome che sta prima della parentesi) esegue solo quello che non è doppio nel tempo stabilito
	var clk = new oneclick(funzionedaeseguire,Math.random(),Date.now());
	this.stackclick.push(clk);
//	console.log("id:"+clk.id);
//	console.log("funzionedaeseguire:"+clk.funzionedaeseguire);
//	console.log("starttime:"+clk.starttime);
	var funzionevalida = true;//la funzione può essere eseguita
	var index = this.stackclick.length;
	if (index > 0){
		//cerco altre occorrenze clk della stessa procedura inserita precedenti a quella e controllo che siano state inserite almeno tot secondi prima in (esempio 1000)
		var indexattuale = -1;
		while(index > 0){
			if (indexattuale > -1){//controllo se è gia stata trovata l'occorenza clk attuale
				//una volta trovata cerco se ce ne sono di precedenti a questa con la stessa funzione da eseguire e valuto se la differenza del tempo di inserimento è abbastanza lunga
				if(getnamefunction(this.stackclick[index-1].funzionedaeseguire).localeCompare(getnamefunction(clk.funzionedaeseguire)) == 0){
//					console.log("DATETIME");
//					console.log("start time trovato nello stack: "+this.stackclick[index-1].starttime);
//					console.log("start time occorrenza attuale: "+clk.starttime);
//					console.log("differenza: "+(clk.starttime-this.stackclick[index-1].starttime));
					if(clk.starttime-this.stackclick[index-1].starttime < tempoattesaoneclick){//è passato almeno 1 secondo
						console.log("click annullato tempo trascorso troppo breve");
						funzionevalida = false;
					}
					break;
				}
			} else {
				//se non è stata trovata la cerco
				if(this.stackclick[index-1].id == clk.id){
					indexattuale = index;
				}
			}
			index = index-1;
		}
	} else {
		funzionevalida = false;
		console.log("clickoneclick-> click perso: array stackclick vuoto (probabilmente array svuotato da altra procedura dopo push)");
	}

//this.stackclick[0].id == clk.id
	if (funzionevalida){
		//alert(funzionedaeseguire);
		eval(funzionedaeseguire);//eval esegue il codice nella variabile
		//qui dentro adesso vado a disabilitare il click per quella funzione in modo che non vengano fatti altri clickoneclick (trovare un modo sicuro)
		//poi eseguo la funzione che è stata passata
	}
}

function oneclick(funzionedaeseguire, id, starttime){
	this.funzionedaeseguire = funzionedaeseguire;
	this.id=id;
	this.starttime=starttime;
}

//FINE funzioni per non avere doppi click su function javascript

//inizio messaggi a scomparsa

//usare questa funzione per caricare i vari messaggi durante il ciclo di controllo e quella sotto "VisualizzaMessaggiEffimeri()" questa per mostrarli alla fine dei controlli
//text->messaggio da visualizzare (string)
//dbtype->tipo del campo (string) - lo viaualizza poi nel testo del messaggio - mettere null se non lo si vuole visualizzare
//color->color che sarà il messaggio
function AggiungiMessaggioEffimero(text,dbtype,color){
	var msg = new MessaggioEffimero(text,dbtype,color);
	this.arrayMessaggiEffimeri.push(msg);
}

function VisualizzaMessaggiEffimeri(){
	for (let i = 0; i < this.arrayMessaggiEffimeri.length; i++) {
		var showeddbtype = "";
		if (this.arrayMessaggiEffimeri[i].dbtype != null){
			var showeddbtype = "["+String(this.arrayMessaggiEffimeri[i].dbtype)+"]: ";
		}
		StampaMessaggioEffimero(showeddbtype+this.arrayMessaggiEffimeri[i].text,this.arrayMessaggiEffimeri[i].color);
	}
	this.arrayMessaggiEffimeri = [];
}

function creacontenitoremessaggi(){
	//creo una volta solo il contenitore dei messaggi
	this.contenitoremessaggi = document.createElement("div");
	this.contenitoremessaggi.style.position = "fixed";
	this.contenitoremessaggi.style.top = "20px";
	this.contenitoremessaggi.style.zIndex = "999";
	this.contenitoremessaggi.style.width = "100%";
	this.contenitoremessaggi.id = "contenitoremessaggi";
	document.body.appendChild(this.contenitoremessaggi);
}

var contenitoremessaggi=null;
var numeromessaggi=0;

function eliminacontenitoremessaggi(){
	this.numeromessaggi=this.numeromessaggi-1;
	if(this.numeromessaggi<1){
		this.contenitoremessaggi.remove();
		this.contenitoremessaggi = null;
	}
}

function StampaMessaggioEffimero(messaggio="",color = "red",tempo=3000){//tempo scomparsa default
	if (contenitoremessaggi == null){
		//se non è già creato creo il contenitore
		creacontenitoremessaggi();
	}
	this.numeromessaggi=this.numeromessaggi+1;
	var divmessaggio = document.createElement("div");
	divmessaggio.style.backgroundColor = color;
	divmessaggio.style.border = "2px solid #4CAF50";
	divmessaggio.style.fontSize = "x-large";
	divmessaggio.style.width = "60%";
	divmessaggio.style.textAlign = "center";
	divmessaggio.style.paddingTop = "10px";
	divmessaggio.style.paddingBottom = "10px";
	divmessaggio.style.marginRight = "auto";
	divmessaggio.style.marginLeft = "auto";
	divmessaggio.innerHTML=messaggio;
	document.getElementById('contenitoremessaggi').appendChild(divmessaggio);
	setTimeout(
  function() {
  	eliminacontenitoremessaggi();
  }, tempo);
}

function MessaggioEffimero(text,dbtype=null,color){
  this.text = text;
  this.dbtype = dbtype;
	this.color = color;
}

var arrayMessaggiEffimeri = [];

//fine messaggi a scomparsa

function getparameterreq(){
	var oldqmd = this.qmd;//salvo il vecchio qmd
	//console.log("getparameterreq() -> stampo qui il QMD prima di ricevere quello nuovo");
	//console.log(this.qmd);
	this.qmd = [];//lo svuoto per salvare i nuovi dati
	if (this.qmdmethod != null){
		//aggiorno il metodo della maschera esterna con il query method attuale (update)
		for (index = 0; index < oldqmd.length; ++index) {
			var ob = oldqmd[index].query;
			ob.method = this.qmdmethod;
		}
	}
	return JSON.stringify(oldqmd);
}

/* gestore data richiamato al caricamento della pagina con ONLOAD */
function firstgestoredata(){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		//	this.qmd = this.response.data;
		 for (var i in this.response.data) {
			 qmd.push(this.response.data[i]);//possono esserci più di una istruzione contemporanea (esempio delete di più elementi selezionati da checkbox)
		 }
		 stamparigheconsole();
		 stampaHtml();
		 //console.log('questo:->');
		 //console.log(this.response.data2);
		}
	};
	xhttp.open("POST", "/terminal/gestoredati", true);
	xhttp.responseType = 'json';
	xhttp.setRequestHeader("Content-Type", "application/json;charset=utf-8");
	xhttp.send(getfirstparameterreq());
}

function gestoredata(){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		//	this.qmd = this.response.data;
		 for (var i in this.response.data) {
			 qmd.push(this.response.data[i]);//possono esserci più di una istruzione contemporanea (esempio delete di più elementi selezionati da checkbox)
		 }
		 stamparigheconsole();
		 stampaHtml();
		 //console.log('questo:->');
		 //console.log(this.response.data2);
		 nascondifinestraattesa();
		}
	};
	document.getElementById('maincontenitor').innerHTML = "";
	xhttp.open("POST", "/terminal/gestoredati", true);
	xhttp.responseType = 'json';
	xhttp.setRequestHeader("Content-Type", "application/json;charset=utf-8");
	xhttp.send(getparameterreq());
}

function mostrafinestraattesa(){
	document.getElementById('finestraattesa').style.display = 'block';
}

function nascondifinestraattesa(){
	document.getElementById('finestraattesa').style.display = 'none';
}

function annullaeditmaschera(){
	console.log("premuto tasto Annulla edit maschera");
	mostrafinestraattesa();
	location.reload();//ho messo questo perchè così ricarica ancge tutti i dati
	/*var inputs, index, selects;
	//MANCA LA RICOLORAZIONE DI TUTTE LE CASELLE CHE AVEVANO ERRORI
	inputs = document.getElementById('maincontenitor').getElementsByTagName('input');
	for (index = 0; index < inputs.length; ++index) {
		 inputs[index].value = inputs[index].getAttribute("dbvalue");
	   inputs[index].disabled = true;
	}
	selects = document.getElementById('maincontenitor').getElementsByTagName('select');
	for (index = 0; index < selects.length; ++index) {
		selects[index].value = selects[index].getAttribute("dbvalue");
		selects[index].disabled = true;
	}
	abilitadisabilitatastimaschera(true,true,false,false);
	nascondifinestraattesa();*/
}

function editmaschera(){
	console.log("premuto tasto Edit maschera");
	mostrafinestraattesa();
	this.qmdmethod = "update";//specifico che poi al salvataggio si comporterà come un update
	var inputs, index, selects;
	inputs = document.getElementById('maincontenitor').getElementsByTagName('input');
	for (index = 0; index < inputs.length; ++index) {
	   inputs[index].disabled = false;
	}
	selects = document.getElementById('maincontenitor').getElementsByTagName('select');
	for (index = 0; index < selects.length; ++index) {
		 selects[index].disabled = false;
		 //$(selects[index]).chosen();
	}
	$("SELECT").chosen();
	//$(".chosen-select").attr('disabled', true).trigger("chosen:updated");
	abilitadisabilitatastimaschera(false,false,true,true);
	nascondifinestraattesa();
}

function nuovamaschera(){
	console.log("premuto tasto Nuova maschera MAIN");
	mostrafinestraattesa();

	window.location.href = "http://natore.test/terminal/maschera/prepareinsert/articoli/0";


	/*this.qmdmethod = "prepareinsert";//specifico che poi al salvataggio si comporterà come un update
	stampaHtml(true);//true per new maschera
	abilitadisabilitatastimaschera(false,false,true,true);
	nascondifinestraattesa();*/
}


function checknullvalueecoerenza(){
	var tuttook = true;//diventa false se vengono trovati errori
	var inputs, index;
	//trovo tutti gli elementi INPUT dentro a maincontenitor
	inputs = document.getElementById('maincontenitor').getElementsByTagName('input');
	for (index = 0; index < inputs.length; ++index) {
		//INIZIO CICLO SU TUTTI GLI INPUT
		campook = true;
		if((inputs[index].getAttribute("campo")).localeCompare("DATE") == 0){//se è un input con dati tipo date
			if(inputs[index].getAttribute("isnullable") == 'false'){
				//qui se non è nullable aggiungo il messaggio effimero e metto il flag campook a false
				if((inputs[index].value).localeCompare("") == 0){
					campook = false;
					AggiungiMessaggioEffimero("Data Non Valorizzata",inputs[index].getAttribute("camponame"));
				}
			}
			parola = inputs[index].value; //es 2021-08-22
			/*parola.length è 0 in caso di campo vuoto*/
			if ( parola.length != 10
				|| parseInt(parola.substring(0, 4)) > (dataoggi.getFullYear()+1) //anno troppo nel futuro
				|| parseInt(parola.substring(0, 4)) < 2010 //anna troppo vecchio
				|| parseInt(parola.substring(8, 10)) > 31 // giorno superiore al 31
				|| (parseInt(parola.substring(5, 7)) == 2 && parseInt(parola.substring(8, 10)) > 29) //febbraio con giorni sopra al 29
				|| ( (parseInt(parola.substring(5, 7)) == 4 || parseInt(parola.substring(5, 7)) == 6 || parseInt(parola.substring(5, 7)) == 9 || parseInt(parola.substring(5, 7)) == 11) && parseInt(parola.substring(8, 10)) > 30) //mesi con giorni di 30
			){//i browser permettono di inserire solo numeri per i campi date, basta controllare che lunghezza sia corretta
				campook = false;
				AggiungiMessaggioEffimero("Data non coerente",inputs[index].getAttribute("camponame"));
			}
			/* NON SERVONO PIù questi controlli che ho deciso di non mettere parseInt()
			parola.substring(0, 4);//anno es 2021
			parola.substring(4, 5);//trattino divisorio - tra anno e mese
			parola.substring(5, 7);//mese es 08
			parola.substring(7, 8);//trattino divisorio - tra mese e gg
			parola.substring(8, 10);//giorno es 22*/
		} else {
			if((inputs[index].getAttribute("campo")).localeCompare("DOUBLE") == 0){//se è un DOUBLE
				if(inputs[index].getAttribute("isnullable") == 'false'){
					if((inputs[index].value).localeCompare("") == 0){
						campook = false;
						AggiungiMessaggioEffimero("Campo Numerico Decimale Non Valorizzato",inputs[index].getAttribute("camponame"));
					}
				}
				parola = inputs[index].value;
				//console.log();

				if (parola.indexOf(' ') >= 0 || parola.match('[^.0-9]') /*se contiene qualcosa che non è . o numeri 0-9*/|| Number.isNaN(parseFloat(parola))){
					campook = false;
					AggiungiMessaggioEffimero("Campo Numerico Decimale non coerente",inputs[index].getAttribute("camponame"));
				}
			} else {
				if((inputs[index].getAttribute("campo")).localeCompare("TINYINT(1)") == 0){//se è un boolean
					//non faccio nessun controllo
				} else {
					//in tutti gli altri classi
					if(inputs[index].getAttribute("isnullable") == 'false'){
						if((inputs[index].value).localeCompare("") == 0){
							campook = false;
							AggiungiMessaggioEffimero("Campo Non Valorizzato",inputs[index].getAttribute("camponame"));
						}
					}
				}
			}
		}

		//controllo finale
		if (campook == false){
			//se sono state trovate incoerenze evidenzio l'errore
			inputs[index].style.backgroundColor = "#e16771";
			tuttook = false;
		} else {
			inputs[index].style.backgroundColor = "";
		}
	}//FINE CICLO SU TUTTI GLI INPUT

	//ciclo sullo SELECT <OPTION>
	selects = document.getElementById('maincontenitor').getElementsByTagName('select');
	for (index = 0; index < selects.length; ++index) {
		//selects[index].options[ selects[index].selectedIndex ].value;

		//devo settare il colore qui sotto nel caso in cui value -1
		/*
		if(){

		}*/
		if ((selects[index].getAttribute("isnullable") == 'false' && selects[index].options[ selects[index].selectedIndex ].value == 'null') //controllo che nel caso di campi non nullable sia selezionato un elemento
		||selects[index].options[ selects[index].selectedIndex ].value == '-1') {
			//controllo che se la select non è nullable sia selezionato almento un elemento
			//selects[index].style.backgroundColor = "#e16771";
			selects[index].previousSibling.style.backgroundColor = "#f40000";//coloro l'etichetta di rosso per segnalare errore

			tuttook = false;
			AggiungiMessaggioEffimero("Campo Non Valorizzato",selects[index].getAttribute("camponame"));
		} else {
			//	selects[index].style.backgroundColor = "";
			selects[index].previousSibling.style.backgroundColor = ""; // tolgo da quelli che erano prima in errore in un successivo salvataggio
		}
	}
	return tuttook;
}

function salvamaschera(){
	console.log("premuto tasto Salva maschera");
	mostrafinestraattesa();
	$("SELECT").chosen("destroy");
	test = checknullvalueecoerenza();
	if (test==false){//inizio IF checknullvalueecoerenza()
		//se ci sono errori non la salvataggio
		$("SELECT").chosen();
		VisualizzaMessaggiEffimeri();
		nascondifinestraattesa();
	} else {
		//if (qmdmethod)
		//se tutti gli input e select sono coerenti e i valori non null valorizzati salvo la maschera
		var inputs, index;
		//trovo tutti gli elementi INPUT dentro a maincontenitor
		inputs = document.getElementById('maincontenitor').getElementsByTagName('input');
		for (index = 0; index < inputs.length; ++index) {
			if((inputs[index].getAttribute("campo")).localeCompare("DATE") == 0){//se è un input con dati tipo date
				if((inputs[index].value).localeCompare(inputs[index].getAttribute("dbvalue"))!= 0) {//controllo se il valore di input è diverso da quello da DB
					console.log('salvamaschera()trovato date diverso da dbvalue/'+inputs[index].value+'/');
					setDataQmd(inputs[index],inputs[index].value);//vado a modificare anche il qmd javascript
				}
			} else {
				if((inputs[index].getAttribute("campo")).localeCompare("TINYINT(1)") == 0){

					if( inputs[index].checked == ( inputs[index].getAttribute("dbvalue").localeCompare('1') != 0 )) {//controllo se il valore di input è diverso da quello da DB
						console.log('salvamaschera()trovato TINYINT(1) diverso da dbvalue/'+inputs[index].checked+'/');
						setDataQmd(inputs[index],inputs[index].checked);//vado a modificare anche il qmd javascript
					}

				} else{
					//tutti gli altri
					if((inputs[index].value).localeCompare(inputs[index].getAttribute("dbvalue"))) {
						console.log('salvamaschera()altro/'+inputs[index].value+'/');
						setDataQmd(inputs[index],inputs[index].value);
					}
				}
			}
			 //inputs[index].value = inputs[index].getAttribute("dbvalue");
		}

		selects = document.getElementById('maincontenitor').getElementsByTagName('select');
		for (index = 0; index < selects.length; ++index) {

		//	alert(selects[index].options[ selects[index].selectedIndex ].value);

			setDataQmd(selects[index],selects[index].options[ selects[index].selectedIndex ].value);

		}
		abilitadisabilitatastimaschera(true,true,false,false);
		gestoredata();
	}//fine IF checknullvalueecoerenza()
}

/*
*esegue tutte le operazioni sulla maschera e abilita disabilita i tasti relativi in modo che clickoneclick le faccia eseguire solo una al secondo visto che è la stessa funzione e clickoneclick fa eseguire al massimo una al secondo a patto che sia la stessa funzione
*/
function mascheraaction(operazione){
	switch(operazione) {
	  case "modifica":
	    // code block
			if (this.btn_modifica){
				editmaschera();
			}
	    break;
	  case "nuovo":
	    // code block
			if (this.btn_nuovo){
				nuovamaschera();
			}
		/*	document.getElementById('btn_nuovo').src = "/imgs/terminal/new64pxdisable.png";
			document.getElementById('btn_modifica').src = "/imgs/terminal/edit64pxdisable.png";
			document.getElementById('btn_salva').src = "/imgs/terminal/floppy64px.png";
			document.getElementById('btn_annulla').src = "/imgs/terminal/cancel64px.png";*/
	    break;
		case "salva":
			// code block
			if (this.btn_salva){
				salvamaschera();
			}
			break;
		case "annulla":
		    // code block
			if (this.btn_annulla){
				annullaeditmaschera();
			}
	    break;
	  default:
	    // code block
			console.log("mascheraaction operazione non trovata");
	}
}

/*abilita disabilita i tasti della maschera in base al tipo di boolean passato per il tasto*/
var btn_nuovo = false;
var btn_modifica = false;
var btn_salva = false;
var btn_annulla = false;

function abilitadisabilitatastimaschera(nuovo,modifica,salva,annulla){
	this.btn_nuovo = nuovo;
	this.btn_modifica = modifica;
	this.btn_salva = salva;
	this.btn_annulla = annulla;

	if (nuovo){
		document.getElementById('btn_nuovo').src = "/imgs/terminal/new64px.png";
	} else {
		document.getElementById('btn_nuovo').src = "/imgs/terminal/new64pxdisable.png";
	}

	if (modifica){
		document.getElementById('btn_modifica').src = "/imgs/terminal/edit64px.png";
	} else {
		document.getElementById('btn_modifica').src = "/imgs/terminal/edit64pxdisable.png";
	}

	if (salva){
		document.getElementById('btn_salva').src = "/imgs/terminal/floppy64px.png";
	} else {
		document.getElementById('btn_salva').src = "/imgs/terminal/floppy64pxdisable.png";
	}

	if (annulla){
		document.getElementById('btn_annulla').src = "/imgs/terminal/cancel64px.png";
	} else {
		document.getElementById('btn_annulla').src = "/imgs/terminal/cancel64pxdisable.png";
	}
}



function setDataQmd(input,value){

	var mascherasub = input.getAttribute("mascherasub") === 'true';//diventa true se stringa uguale a 'true' altrimenti false
	var indexqmd = input.getAttribute("indexqmd");
	var indexfdr = input.getAttribute("indexfdr");
	var indexcampi = input.getAttribute("indexcampi");
	var camponame = input.getAttribute("camponame");

	if (mascherasub){

		var indexfdrsub = input.getAttribute("indexfdrsub");

		var data = qmd[indexqmd].data;
		var campo = data.campi[indexcampi];
		var m = campo.maschera;
		var fdr = m.fdr[indexfdrsub];
		var c = fdr[camponame];
		c.value = value;
		console.log('campoSUB:'+c.value);

	} else {

		var data = qmd[indexqmd].data;
		var fdr = data.fdr[indexfdr];
		var c = fdr[camponame];
		c.value = value;
		console.log('campoMAIN:'+c.value);
	}
}


/*col parametro DATA gli passo il riferimento a quel data del qmd e varia al variare del qmd.. non gli passo una copia ma un riferimento alla variabile*/
function setDataQmdAggiungiElemento(indexqmd,indexfdrmain,indexcampimain,data,query,mascherasub,value,ul){

	if (mascherasub){

		var pdata = qmd[indexqmd].data;
		var pcampo = pdata.campi[indexcampimain];
		var m = pcampo.maschera;

		console.log('Aggiungo elemento a FDR maschera :');

		var fdr = m.fdr_proto[0];//copio l'istanza vergine (prototype) del oggetto pronto per spedirlo

		cloneElement = JSON.parse(JSON.stringify(fdr)); // QUESTO è l'unico metodo che funziona.. gli altri due ... e Object.assign({} non vanno

		//cloneElement = { ...fdr };// questo crea un nuovo elemento copiato.. perchè javascript funziona coi puntatori normalmente serve questo metodo

		lenfdrforindex = m.fdr.length;//la uso perchè corrisponde alla posizione che andrà occupata dall'elemento. (se la prendevo dopo il push dovevo fare -1)

		m.fdr.push(cloneElement);

		var li = document.createElement("li");
		/*al posto del contatore i ho passato null perchè è un nuovo elemento, vedi primo parametro passato a TRUE*/
		contatorecampi = getMascheraLi(true,li,indexqmd,indexfdrmain,indexcampimain,lenfdrforindex,data,mascherasub,query);

		ul.insertBefore(li, ul.childNodes[0]);

	} else {
		console.log("ERRORE:non abilitata manipolazione maschera non sub");
/*
		var data = qmd[indexqmd].data;
		var fdr = data.fdr[indexfdr];
		var c = fdr[camponame];
		c.value = value;
		console.log('setDataQmdRimuoviElemento-- campoMAIN:'+c.value);*/
	}
	nascondifinestraattesa();
}

function setDataQmdRimuoviElemento(indexqmd,indexfdr,indexcampi,indexfdrsub,indexcampisub,camponame,mascherasub,value,li){

	//console.log(" indexqmd:"+indexqmd+" indexfdr:"+indexfdr+" indexcampi:"+indexcampi+" indexfdrsub:"+indexfdrsub+" indexcampisub:"+indexcampisub);

	if (mascherasub){

		var data = qmd[indexqmd].data;
		var campo = data.campi[indexcampi];
		var m = campo.maschera;
		var fdr = m.fdr[indexfdrsub];
		console.log(fdr);
		var c = fdr[camponame];
		c.value = value;
		//console.log('setDataQmdRimuoviElemento-- campoSUB:'+c.value);
		//stamparigheconsole();
		li.style.display = 'none';

	} else {
		console.log("ERRORE:non abilitata manipolazione maschera non sub");
/*
		var data = qmd[indexqmd].data;
		var fdr = data.fdr[indexfdr];
		var c = fdr[camponame];
		c.value = value;
		console.log('setDataQmdRimuoviElemento-- campoMAIN:'+c.value);*/
	}
	nascondifinestraattesa();
}

function stamparigheconsole(){
	for (var i in this.qmd) {
		console.log(this.qmd[i]);
//	console.log(this.qmd[i].model[0].type);
//	console.log(this.qmd[i].model[0].value);
	}
}

function stampaHtml(){
	//la finestra di attesa è già aperta di default
	isnewmascherabool = false;
	for (var i in this.qmd) {
		if (i == 0 && this.qmd[i].query.method.localeCompare('prepareinsert') == 0){
			/* con questo if vado a vedere per il primo elemento del qmd se il query method è un insert vado a caricare la maschera con modalità inserimento e metto nel qmd anche il dato vergine */

			console.log('Aggiungo elemento a FDR maschera :');

			var fdr = this.qmd[i].data.fdr_proto[0];//copio l'istanza vergine (prototype) del oggetto pronto per spedirlo

			cloneElement = JSON.parse(JSON.stringify(fdr)); // QUESTO è l'unico metodo che funziona.. gli altri due ... e Object.assign({} non vanno

			lenfdrforindex = this.qmd[i].data.fdr.length;//la uso perchè corrisponde alla posizione che andrà occupata dall'elemento. (se la prendevo dopo il push dovevo fare -1)

			if (lenfdrforindex == 0){
				this.qmd[i].data.fdr.push(cloneElement);
			} else {
				alert("errore- contattare amministratore di sistema Si sta cercando di inserire elementi in una maschera main non vuota");
			}
			this.qmdmethod = "insert";//specifico quale sarà il query method da utilizzare quando spedisco i dati poi
			isnewmascherabool = true;

		}
		/*indexfdrmain sempre 0 il primo elemento perchè questa modalità di inserimento è sempre per un elemento alla volta per qdm*/
		/*indexcampimain vuoto perchè creo la maschera main e quindi devo stampare tutti i campi*/
		var divx = getHtmlMaschera(isnewmascherabool,false/*isaddelement*/,i,0/*indexfdrmain*/,null/*indexcampimain*/,this.qmd[i].data, this.qmd[i].query);
		document.getElementById("maincontenitor").appendChild(divx);

		/*parte che abilita i tasti maschera*/
		if (i == 0){/*LO ESEGUO SOLO PER IL PRIMO CAMPO IN MODO CHE IL primo campo è quello che decide per tutte gli altri elementi del qmd se ci sono campi successivi con query diverse dovrei segnalare l'errore */
			if (this.qmd[i].query.method.localeCompare('select') == 0) {
				abilitadisabilitatastimaschera(true,true,false,false);
			} else {
				if (this.qmd[i].query.method.localeCompare('prepareinsert') == 0){
					abilitadisabilitatastimaschera(false,false,true,true);
				} else {
					console.log("stampaHtml() -> query del qmd non trovata disabilito tutti i tasti"+this.qmd[i].query.method);
					//quando stampo la maschera è sempre tramite valori di select
					abilitadisabilitatastimaschera(false,false,false,false);
				}
			}
		}
		/*fine parte che abilita i tasti maschera*/
	}
	nascondifinestraattesa();
}

function getHtmlMaschera(isnewmaschera,isaddelement,indexqmd,indexfdrmain,indexcampimain,data,query){

	if (data.type.localeCompare('main')==0) {
		var mascherasub = false;
	} else {
		var mascherasub = true;
		var contatorerighe = 0;
		var contatorecampi = 0;
		var ulmaschera = document.createElement("ul");
		ulmaschera.setAttribute("class","ulmaschera");
	}

	var divmaschera = document.createElement("div");//maschera
	var divtitle = document.createElement("div");//titolo

	if(mascherasub == false){
		divmaschera.setAttribute("class","contenitormascheratop");
		divtitle.setAttribute("class","titlemascheratop");
	} else {
		divmaschera.setAttribute("class","contenitormascherasub");
		divtitle.setAttribute("class","titlemascherasub");
	}

	var taoimg = document.createElement("img");
	taoimg.src = "/imgs/terminal/spillasrf.png";
	taoimg.setAttribute("class","mascheraimg");
	divtitle.appendChild(taoimg);

	var divetichetta = document.createElement("div");//titolo
	divetichetta.setAttribute("class","titlemascheraetichetta");
	divetichetta.appendChild(document.createTextNode(data.etichetta));
	divtitle.appendChild(divetichetta);

	if(mascherasub == false){
		//sulla maschera principale appendo gli elementi di operazioni sulla maschera

		var divoperations = document.createElement("div");
		divoperations.setAttribute("class","operations");

		var imgnew = document.createElement("img");
		imgnew.src = "/imgs/terminal/new64px.png";
		imgnew.id = "btn_nuovo";
		imgnew.onclick = function () {
    	clickoneclick("mascheraaction(\"nuovo\")");
		};
		divoperations.appendChild(imgnew);

		var imgsalva = document.createElement("img");
		imgsalva.src = "/imgs/terminal/floppy64px.png";
		imgsalva.id = "btn_salva";
		imgsalva.onclick = function () {
    	clickoneclick("mascheraaction(\"salva\")");
		};
		divoperations.appendChild(imgsalva);

		var imgmodifica = document.createElement("img");
		imgmodifica.src = "/imgs/terminal/edit64px.png";
		imgmodifica.id = "btn_modifica";
		imgmodifica.onclick = function () {
    	clickoneclick("mascheraaction(\"modifica\")");
		};
		divoperations.appendChild(imgmodifica);

		var imgannulla = document.createElement("img");
		imgannulla.src = "/imgs/terminal/cancel64px.png";
		imgannulla.id = "btn_annulla";
		imgannulla.onclick = function () {
    	clickoneclick("mascheraaction(\"annulla\")");
		};
		divoperations.appendChild(imgannulla);

		divmaschera.appendChild(divoperations);

	} else {
		//aggiungo elemento aggiunta elementi in maschera sub

		divtitle.appendChild(getHtmlCampoAggiungiElementoMascheraSub(indexqmd,indexfdrmain,indexcampimain,data,query,mascherasub,ulmaschera));

	}

	divmaschera.appendChild(divtitle);

	if (isnewmaschera){

		contatorerighe++;

		if (mascherasub){
			divmaschera.appendChild(ulmaschera);
		} else {
			contatorecampi = getMascheraLi(isnewmaschera,divmaschera,indexqmd,indexfdrmain,null,null,data,mascherasub,query);
		}

	} else {

		for (var i in data.fdr) {//Questo ciclo gira sui dati FDR per avere indietro le informazioni

			contatorerighe++;

			if (mascherasub){
				var li = document.createElement("li");
				contatorecampi = getMascheraLi(isaddelement,li,indexqmd,indexfdrmain,indexcampimain,i,data,mascherasub,query);
			} else {
				contatorecampi = getMascheraLi(isaddelement,divmaschera,indexqmd,i,null,null,data,mascherasub,query);
			}

			if (mascherasub && contatorecampi>0){
				ulmaschera.appendChild(li);
			}
		}
		if (mascherasub){
			divmaschera.appendChild(ulmaschera);
		}
	}

	return divmaschera;
}

/*

getMascheraLi(isnewelement(boolean è una riga vuota nuova? o va valorizzata da dati esistenti),elementpadreappend (elemento al quale vengono appesi i child),indexqmd (indice dell'array contenente i dati),Xindexfdrmain(indice dell'fdr principale),indexcampimain(indice relativo ai campi principale),Xindexfdrsub (eventuale indicide per maschera sub relativo all'fdr della submaschera),data(dati che passo sui quale fare il ciclo),mascherasub (campo true false per dire se è una submaschera),query(campo query del qmd))

*/

/*isnewelement uso un solo campo che vale sia per addelement di maschera sub che per nuova maschera perchè tanto le due cose si escludono nel senso che quando creo un nuovo elemento non stampo a video i campi precompilati solo per la maschera main perchè le sub maschere le creo senza elementi. Mentre non ho mai la possibilità di aggiungere un elemento main dinamicamente come succede per le submaschere (alle quali posso aggiungere tante righe quante voglio). Ma opero sempre su una maschera main alla volta*/

function getMascheraLi(isnewelement,elementpadreappend,indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,data,mascherasub,query){
	contatorecampi = 0;
	//quetso ciclo gira sull'array campi per avere i dati fissi di ogni campo
	for (var c in data.campi) {
		if (data.campi[c].type.localeCompare("campo") == 0){//se è un campo
			contatorecampi++;
			if(isnewelement){
				if (mascherasub){
					elementpadreappend.appendChild(getHtmlCampo(isnewelement,indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,c,null, data.campi[c],mascherasub,query));
				} else {
					elementpadreappend.appendChild(getHtmlCampo(isnewelement,indexqmd,Xindexfdrmain,c,null,null,data.fdr[Xindexfdrmain], data.campi[c],mascherasub,query));
				}
			} else {
				if (mascherasub){
					elementpadreappend.appendChild(getHtmlCampo(isnewelement,indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,c,data.fdr[Xindexfdrsub], data.campi[c],mascherasub,query));
				} else {
					elementpadreappend.appendChild(getHtmlCampo(isnewelement,indexqmd,Xindexfdrmain,c,null,null,data.fdr[Xindexfdrmain], data.campi[c],mascherasub,query));
				}
			}
		} else {
			if (data.campi[c].type.localeCompare("singlerelation") == 0){
				contatorecampi++;
				if(isnewelement){
					if (mascherasub){
						elementpadreappend.appendChild(getHtmlSingleRelation(isnewelement,indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,c,null, data.campi[c],mascherasub,query));
					} else {
						elementpadreappend.appendChild(getHtmlSingleRelation(isnewelement,indexqmd,Xindexfdrmain,c,null,null,data.fdr[Xindexfdrmain], data.campi[c],mascherasub,query));
					}
				} else {
					if (mascherasub){
						elementpadreappend.appendChild(getHtmlSingleRelation(isnewelement,indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,c,data.fdr[Xindexfdrsub], data.campi[c],mascherasub,query));
					} else {
						elementpadreappend.appendChild(getHtmlSingleRelation(isnewelement,indexqmd,Xindexfdrmain,c,null,null,data.fdr[Xindexfdrmain], data.campi[c],mascherasub,query));
					}
				}
			} else {
				if (data.campi[c].type.localeCompare("maschera") == 0){
					contatorecampi++;
					elementpadreappend.appendChild(getHtmlMaschera(false,isnewelement,indexqmd,Xindexfdrmain,c,data.campi[c].maschera, query));
				} else {
					if (data.campi[c].type.localeCompare("manipolazione") == 0){
						if (mascherasub){
							elementpadreappend.appendChild(getHtmlCampoCestino(indexqmd,Xindexfdrmain,indexcampimain,Xindexfdrsub,c,data.fdr[Xindexfdrsub], data.campi[c],mascherasub,query,elementpadreappend));
						} else {
							//per il momento non faccio niente
						}
					}
				}
			}
		}
	}
	return contatorecampi;
}

function getHtmlSingleRelation(isnewelement,indexqmd,indexfdr,indexcampi,indexfdrsub,indexcampisub,rigafdr,campo,mascherasub,query){
	var divcampo = document.createElement("div");//maschera
	if (mascherasub){
		divcampo.setAttribute("class","contenitorcampomaschsub");
	} else {
		divcampo.setAttribute("class","contenitorcampomaschmain");
	}

	var divtitle = document.createElement("div");//titolo
	divtitle.setAttribute("class","titlecampo");

	divtitle.appendChild(document.createTextNode(campo.etichetta));
	divcampo.appendChild(divtitle);

	var inp = document.createElement("SELECT");
	/*	aggiunta valore non selezionato	nel caso di scelta di aggiungere di defult il valore non selezionato oppure nel caso di campi che possono essere nullable*/
	if (campo.addnonselezionato !== undefined || campo.isnullable) {
		var opt = document.createElement('option');
		opt.value = null;
		//se è stato aggiunto il valore non selezionato metto l'etichetta scelta (che può essere usato per sovrascrivere etichette di campi nulli)
		if (campo.addnonselezionato !== undefined){
			opt.innerHTML = campo.addnonselezionato;
		} else {
			opt.innerHTML = "Non selezionato";
		}
		inp.appendChild(opt);
	}
	/*	fine aggiunta valore non selezionato	*/
	var trovato = false;
	for (var c in campo.sinreloptionarray) {
		var opt = document.createElement('option');
		opt.value = campo.sinreloptionarray[c].id;
		opt.innerHTML = campo.sinreloptionarray[c].label;
		inp.appendChild(opt);
		if (isnewelement == false && rigafdr[campo.name].value != null && campo.sinreloptionarray[c].id == rigafdr[campo.name].value){
			//se trovo un elemento nel sinreloptionarray che è corrispondente alla value da db setto trovato a TRUE
			trovato = true;
		}
	}

	inp.setAttribute("campo", campo.dbdatatype);
	inp.setAttribute("isnullable", campo.isnullable);
	inp.setAttribute("camponame", campo.name);
	inp.setAttribute("mascherasub", mascherasub);
	inp.setAttribute("indexqmd", indexqmd);
	inp.setAttribute("indexfdr", indexfdr);
	inp.setAttribute("indexcampi", indexcampi);
	inp.setAttribute("indexfdrsub", indexfdrsub);
	inp.setAttribute("indexcampisub", indexcampisub);

	if (isnewelement == true){

	} else {
		inp.setAttribute("dbid", rigafdr[campo.name].id);
		inp.setAttribute("dbidref", rigafdr[campo.name].idref);
		//ho dovuto aggiungere anche la seconda parte "(rigafdr[campo.name].value).localeCompare("null") == 0" perchè solo con questa riconosce anche i valori null di ritorno dopo l'update
		if(rigafdr[campo.name].value == null || (rigafdr[campo.name].value).localeCompare("null") == 0) {
			inp.setAttribute("dbvalue", "null"); // attenzione ho cambiato il valore da "" a "null" perchè data errore quando utilizzavo la funziona annulla e restauraca i vecchi valori
		} else {
			if (trovato){
				inp.setAttribute("dbvalue", rigafdr[campo.name].value);
				inp.value=rigafdr[campo.name].value;
			} else {
				//se non è stato trovato il value corrispondente tra quelli disponibili nell'array option TROVATO resta false ed aggiungo l'option di seguito per segnalare l'errore
				var opt = document.createElement('option');
				opt.value = -1;
				opt.innerHTML = 'value'+rigafdr[campo.name].value+' non codificata';

				/*Ho scelto di segnalare qui l'errore sul titolo perchè altrimenti cambiando background mi sballava tutte le dimensioni delle celle*/
				divtitle.style.backgroundColor = "#f40000";

				inp.appendChild(opt);
				//set select VALUE
				inp.setAttribute("dbvalue", -1);
				inp.value=-1;
			}
		}

		if (query.method.localeCompare('select') == 0){
			//imposto non modificabile nel caso di select
			//inp.readonly = true;
			inp.disabled = true;
		}
	}



	if (campo.divcampowidth !== undefined && campo.divcampowidth !== null){
		divcampo.style.width=campo.divcampowidth;
		inp.style.width='100%';
	}

	divcampo.appendChild(inp);
	return divcampo;
}

function getHtmlCampoAggiungiElementoMascheraSub(indexqmd,indexfdrmain,indexcampimain,data,query,mascherasub,ul){

	var addelement = document.createElement("img");
	addelement.src = "/imgs/terminal/addtolist.svg";
	addelement.setAttribute("class","mascheraimgadd");

	addelement.onclick = function () {
		if (btn_modifica==false){//questo perchè lascia passare solo i click quando sono in modifica
			mostrafinestraattesa();
			setDataQmdAggiungiElemento(indexqmd,indexfdrmain,indexcampimain,data,query,mascherasub,1,ul);//funzione senza clickoneclick
		}
	};

	return addelement;
}

function getHtmlCampoCestino(indexqmd,indexfdr,indexcampi,indexfdrsub,indexcampisub,rigafdr,campo,mascherasub,query,li){
	var divcampo = document.createElement("div");
	divcampo.setAttribute("class","contenitorcampomaschsub");

	var divcont = document.createElement("div");
	divcont.setAttribute("class","contenitorcestino");
	var cestino = document.createElement("img");
	cestino.src = "/imgs/terminal/trash.svg";
	cestino.setAttribute("class","mascheraimg");

	cestino.onclick = function () {
		if (btn_modifica==false){//(if serve per prendere solo click quando abilitato)NON METTO THIS davanti perchè altrimenti prende quello della funztion
			mostrafinestraattesa();
			/*Imposto il valore -1 per il campo Manipolazione così che poi venga inviato al backend in questo modo che è un campo da eliminare*/
			setDataQmdRimuoviElemento(indexqmd,indexfdr,indexcampi,indexfdrsub,indexcampisub,campo.name,mascherasub,-1,li);//funzione senza clickoneclick
		}
	};

	divcampo.appendChild(divcont);
	divcont.appendChild(cestino);
	return divcampo;
}

function getHtmlCampo(isnewelement,indexqmd,indexfdr,indexcampi,indexfdrsub,indexcampisub,rigafdr,campo,mascherasub,query){
	var divcampo = document.createElement("div");//maschera
	if (mascherasub){
		divcampo.setAttribute("class","contenitorcampomaschsub");
	} else {
		divcampo.setAttribute("class","contenitorcampomaschmain");
	}

	var divtitle = document.createElement("div");//titolo
	divtitle.setAttribute("class","titlecampo");

	divtitle.appendChild(document.createTextNode(campo.etichetta));
	divcampo.appendChild(divtitle);

	var inp = document.createElement("INPUT");

	inp.setAttribute("campo", campo.dbdatatype);
	inp.setAttribute("isnullable", campo.isnullable);
	inp.setAttribute("camponame", campo.name);

	inp.setAttribute("isnewelement", isnewelement);
	inp.setAttribute("mascherasub", mascherasub);
	inp.setAttribute("indexqmd", indexqmd);
	inp.setAttribute("indexfdr", indexfdr);
	inp.setAttribute("indexcampi", indexcampi);
	inp.setAttribute("indexfdrsub", indexfdrsub);
	inp.setAttribute("indexcampisub", indexcampisub);

	if (campo.divcampowidth !== undefined && campo.divcampowidth !== null){
		divcampo.style.width=campo.divcampowidth;
		inp.style.width='100%';
	}

	if (isnewelement){
		inp.setAttribute("dbvalue", "");

	} else {
		inp.setAttribute("dbid", rigafdr[campo.name].id);
		inp.setAttribute("dbidref", rigafdr[campo.name].idref);
		if(rigafdr[campo.name].value == null) {
			inp.setAttribute("dbvalue", "");
		} else {
			inp.setAttribute("dbvalue", rigafdr[campo.name].value);
		}

		if (query.method.localeCompare('select') == 0) {
			//imposto non modificabile nel caso di select
			//inp.readonly = true;
			inp.disabled = true;
		}

	}

	if (campo.dbdatatype.localeCompare("VARCHAR(255)") == 0){
		inp.setAttribute("type", "text");
		inp.setAttribute("class","inputcampovarchar");
		if (isnewelement){
			inp.value = "";
		} else {
			inp.value = rigafdr[campo.name].value;
		}
	} else {
		if(campo.dbdatatype.localeCompare("TINYINT(1)") == 0){
			inp.setAttribute("type", "checkbox");
			inp.setAttribute("class","inputcampobool");
			if (isnewelement){
				inp.checked = false;
				inp.value = false;
			} else {
				inp.checked = rigafdr[campo.name].value == true;
				inp.value = rigafdr[campo.name].value;
			}
		} else {
			if(campo.dbdatatype.localeCompare("DOUBLE") == 0){
				inp.setAttribute("type", "text");
				inp.setAttribute("class","inputcampovarchar");
				if (isnewelement){
					inp.value = "";
				} else {
					inp.value = rigafdr[campo.name].value;
				}
			} else {
				if(campo.dbdatatype.localeCompare("DATE") == 0){
					inp.setAttribute("type", "date");
					inp.setAttribute("class","inputcampodate");
					if (isnewelement){
						inp.value = "";
					} else {
						inp.value = rigafdr[campo.name].value;
					}
				}
			}
		}
	}

	divcampo.appendChild(inp);

	return divcampo;
}
