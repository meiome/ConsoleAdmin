# ConsoleAdmin

Terminal-based admin interface inspired by SonataAdmin but framework-agnostic.

## Features

- 🖥️ Linux terminal-like interface  
- 🧩 Modular mask system  
- 🔒 Role-based security  
- 🗃️ Database relations support  
- 📁 File upload capabilities  

---

Le seguenti classi PHP e JavaScript sono nate dalla creazione di un progetto che mirasse alla modifica e interazione con un database MySQL/MariaDB in maniera veloce, senza bisogno di utilizzare bundle Symfony dipendenti dall'utilizzo di questo framework. In pratica, questo codice può essere usato su qualsiasi framework PHP.  

Per la creazione del codice, ho scelto di renderlo indipendente anche da Doctrine e da TWIG, utilizzando delle classi del framework Webbrick creato dal mio amico Alessandro Carrer. Le classi da lui sviluppate sono tutte quelle contenute nelle cartelle `src/wb_database` e `src/wb_framework`. Queste classi possono essere utilizzate anche con Symfony, come mostrato e esemplificato in `src/symfonycontroller` nel controller che ho creato.  

---

## Installazione

**Testato con Symfony LTS 6.4 e versioni precedenti.**  

1. Creare una cartella PHP con questo path `progettosymfony/src/php` dentro il progetto Symfony e copiare le cartelle `wb_framework`, `wb_database` e `wb_terminal` all'interno.  

2. Creare una cartella `template` con questo path `progettosymfony/template` dentro il progetto Symfony e copiare all'interno i file contenuti nella cartella `template`.  

3. Posizionare i file all'interno della cartella `public/` nella cartella `progettosymfony/public/` in modo che siano accessibili pubblicamente.  

4. Posizionare i controller all'interno della cartella `symfonycontroller` in `progettosymfony/src/Controller/`.  

5. Posizionare i file di configurazione contenuti nella cartella `config` all'interno della cartella config di Symfony: `progettosymfony/config/wb`.  

6. Testare il corretto funzionamento:  
   Consiglio inizialmente di rimuovere (o commentare) dalla cartella `config` con la struttura del database alcuni file maschere, in modo da testare il progetto con una sola tabella inizialmente. La tabella che scegliamo di tenere deve essere l'unica presente anche in `config/wb/terminal/maschere.php` (commentare le altre per i test iniziali).  

7. Creare il database:  
   Accedere da browser all'indirizzo `miosito/terminal/creadb`.  

8. Configurare come da esempio le tabelle utente con i dati del vostro utente per l'autenticazione da utilizzare poi nel sito:  

   - `tbl_server`  
   - `tbl_azienda` (con una istanza)  
   - La tabella utente come di seguito:  

   | id | azienda_id | server_id | email                     | password      | nomecognome | ruoli                                                                                         | urldefault | ts                  | disable | tipologia | errorlogin | doubleauthentication | doubleauthenticationemail | errordoubleauthentication | doubleauthenticationcode | cassa_id |  
   |----|------------|-----------|---------------------------|---------------|-------------|-----------------------------------------------------------------------------------------------|------------|---------------------|---------|-----------|------------|----------------------|---------------------------|---------------------------|--------------------------|----------|  
   | 2  | 1          | 1         | mail@email.com            | lamiapassword | MARIO ROSSI | role_gestioneordini#1111;role_bolle#1111;role_articoli#1111;role_etichette#1111;role_terminal#1111;role_clifor#1111;role_presenze#1111;role_admin#1111;role_cassa#1111;role_statistiche#1111 | NULL       | 2025-08-05 16:04:35 | 0       | backend   | 0          | 1                    | NULL                      | 0                         | 123456                  | 1        |  

9. Accedere all'indirizzo `http://miosito/terminal` per utilizzare.  

---

**Per eventuali info sulla configurazione, contattatemi**, perché questa guida è solo una bozza e dovrò semplificarla e renderla più intuitiva per l'utilizzo.  
