<?php
	// ---------------------------------------------------------------------
	// GLOBALE
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP for Windows";
	$TEXT['global-showcode'] = "Visualizza il codice sorgente";
	$TEXT['global-sourcecode'] = "Codice sorgente";

	// ---------------------------------------------------------------------
	// MENU DI NAVIGAZIONE
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Benvenuto";
	$TEXT['navi-status'] = "Stato";
	$TEXT['navi-security'] = "Sicurezza";
	$TEXT['navi-doc'] = "Documentazione";
	$TEXT['navi-components'] = "Componenti";
	$TEXT['navi-about'] = "Informazioni su XAMPP";

	$TEXT['navi-demos'] = "Demo";
	$TEXT['navi-cdcol'] = "Collezione CD";
	$TEXT['navi-bio'] = "Bioritmo";
	$TEXT['navi-guest'] = "Guest Book";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Grafica istantanea";
	$TEXT['navi-iart2'] = "Grafica Flash";
	$TEXT['navi-phonebook'] = "Rubrica";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "Strumenti";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Ospite attuale";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Lingue";

	// ---------------------------------------------------------------------
	// STATO
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "Stato di XAMPP";
	$TEXT['status-text1'] = "In questa pagina sono racchiuse informazioni sui server attualmente in esecuzione.";
	$TEXT['status-text2'] = "Modifiche ai file di configurazione possono a volte generare report di stato errati. Con SSL (https://localhost) i report non funzionano!";

	$TEXT['status-mysql'] = "Database MySQL";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl con mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python con mod_python";
	$TEXT['status-mmcache'] = "Estensione PHP »eAccelerator«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-xampp-windows-en.html#mmcache";
	$TEXT['status-smtp'] = "Server SMTP";
	$TEXT['status-ftp'] = "Server FTP";
	$TEXT['status-tomcat'] = "Server Tomcat";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-oci8'] = "Estensione PHP »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";

	$TEXT['status-lookfaq'] = "Vedi FAQ";
	$TEXT['status-ok'] = "ATTIVO";
	$TEXT['status-nok'] = "DISATTIVO";

	$TEXT['status-tab1'] = "Componente";
	$TEXT['status-tab2'] = "Stato";
	$TEXT['status-tab3'] = "Consiglio";

	// ---------------------------------------------------------------------
	// SICUREZZA
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "SICUREZZA DI XAMPP";
	$TEXT['security-text1'] = "Questa pagina offre una panoramica sulla sicurezza della tua installazione XAMPP. (Cortesemente, continua a leggere dopo la tabella.)";
	$TEXT['security-text2'] = "Le voci contrassegnate in verde sono sicure; quelle in rosso sono assolutamente non sicure e quelle in giallo non sono state valutate (ad esempio perchè il software di verifica non è in esecuzione).<p><p>To fix the problems for mysql, phpmyadmin and the xampp directory simply use</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[allowed only for localhost]";
	$TEXT['security-text3'] = "";
	$TEXT['security-text4'] = "";

	$TEXT['security-ok'] = "SICURO";
	$TEXT['security-nok'] = "INSICURO";
	$TEXT['security-noidea'] = "N.D.";

	$TEXT['security-tab1'] = "Oggetto";
	$TEXT['security-tab2'] = "Stato";

	$TEXT['security-checkapache-nok'] = "Queste pagine sono acccessibili da chiunque in rete";
	$TEXT['security-checkapache-ok'] = "Queste pagine non sono accessibili da chiunque in rete";
	$TEXT['security-checkapache-text'] = "Tutte le pagine che stai visualizzando sono accessibili da chiunque in rete. Chiunque conosca il tuo indirizzo IP può vedere queste pagine.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL è accessibile dalla rete";
	$TEXT['security-checkmysqlport-ok'] = "MySQL non è accessibile dalla rete";
	$TEXT['security-checkmysqlport-text'] = "Questo è un problema potenziale o al limite teorico. Se sei un patito della sicurezza dovresti disattivare l'interfaccia di rete di MySQL.";

	$TEXT['security-checkpmamysqluser-nok'] = "L'utente pma di phpMyAdmin non ha una password";
	$TEXT['security-checkpmamysqluser-ok'] = "L'utente pma di phpMyAdmin ha una password";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin salva le impostazioni in un database MySQL. Per accedere ai dati phpMyAdmin usa l'utente speciale pma. Da default questo utente non ha una password e per evitare problemi di sicurezza dovresti sceglierne una.";

	$TEXT['security-checkmysql-nok'] = "L'utente admin di MySQL non ha una password";
	$TEXT['security-checkmysql-ok'] = "L'utente admin di MySQL ha una password";
	$TEXT['security-checkmysql-out'] = "A MySQL server is not running  or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Qualisasi utente locale su macchine Windows può accedere al database MySQL con diritti di amministratore. Dovresti scegliere una password.";

	$TEXT['security-pop-nok'] = "L'utente di test (newuser) per il server Mercury Mail (POP3) ha una vecchia password (wampp)";
	$TEXT['security-pop-ok'] = "L'utente di test \"newuser\" per il server POP3 (Mercury Mail?) non esiste più oppure ha una nuova password";
	$TEXT['security-pop-out'] = "Un server POP3 come Mercury Mail non è in esecuzione o è bloccato da un firewall!";
	$TEXT['security-pop-notload'] = "<i>L'estensione necessaria IMAP per questo test di sicurezza non è caricato (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Controlla ed eventualmente modifica tutti gli utenti e le password nella configurazione del server Mercury Mail!";

	$TEXT['security-checkftppassword-nok'] = "La password FTP è ancora 'lampp'";
	$TEXT['security-checkftppassword-ok'] = "La password FTP è stata cambiata";
	$TEXT['security-checkftppassword-out'] = "Un server FTP non è in esecuzione o è bloccato da un firewall!";
	$TEXT['security-checkftppassword-text'] = "Se il server FTP è stato lanciato, l'utente di default 'nobody' con password 'lampp' può caricare e modificare file sul webserver XAMPP.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin è accessibile dalla rete";
	$TEXT['security-phpmyadmin-ok'] = "Il login con password di PhpMyAdmin è abilitato.";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: file 'config.inc.php' non trovato...";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin è accessibile dalla rete senza alcuna password. La configurazione 'httpd' or 'cookie' nel file \"config.inc.php\" è consigliata.";

	$TEXT['security-checkphp-nok'] = "PHP non è in esecuzione in \"safe mode\"";
	$TEXT['security-checkphp-ok'] = "PHP è in esecuzione in \"safe mode\"";
	$TEXT['security-checkphp-out'] = "Impossibile controllare le impostazioni PHP!";
	$TEXT['security-checkphp-text'] = "Se desideri che persone esterne eseguano PHP, prendi in considerazione la configurazione \"safe mode\". Per gli sviluppatori consigliamo di NON usare la modalità \"safe mode\", poiché alcune funzioni importanti non funzioneranno. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>Maggiori informazioni</font></a>";


	// ---------------------------------------------------------------------
	// IMPOSTAZIONI SULLA SICUREZZA
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Protezione di sicureza per la console di MySQL & la directory di XAMPP";
	$TEXT['mysql-rootsetup-head'] = "SEZIONE MYSQL: PASSWORD \"ROOT\"";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "Il server MySQL non è in esecuzione o è bloccato da un firewall! Risolvi innanzitutto questo problema...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "La nuova password è identica alla password di ripetizione. Inserisci entrambe le password per la nuova!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Password nulle ('') non sono ammesse!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "SUCCESSO: La password per il SuperUtente 'root' è stata creata o aggiornata!
	N.B.: Per poter usare la nuova password bisogna riavviare il server MySQL!!!! Il dato con la nuova password è stato salvato nel file:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "ERRORE: Forse la password di root è errata. MySQL rifiuta il login con la password corrente.";
	$TEXT['mysql-rootsetup-passwdold'] = "Password corrente:";
	$TEXT['mysql-rootsetup-passwd'] = "Nuova password:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Ripeti la nuova password:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Cambia la password";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "Autenticazione PhpMyAdmin:";

	$TEXT['xampp-setup-head'] = "PROTEZIONE DIRECTORY XAMPP (.htaccess)";
	$TEXT['xampp-setup-user'] = "Utente:";
	$TEXT['xampp-setup-passwd'] = "Password:";
	$TEXT['xampp-setup-start'] = "Rendi sicura la directory XAMPP";
	$TEXT['xampp-setup-notok'] = "<br><br>ERRORE: Il nome utente e la password devono contenere almeno tre caratteri e meno di quindici. Caratteri speciali come <öäü (usw.) e spazi vuoti non sono ammessi!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>SUCCESSO: La directory XAMPP ora è protetta! Tutti i dati personali sono stati salvati nel seguente file:<br>";
	$TEXT['xampp-setup-ok'] = "<br><br>The root password was successfully changed. Please restart MYSQL for loading these changes!<br><br>";
	$TEXT['xampp-config-notok'] = "<br><br>ERRORE: Il tuo sistema NON può attivare la protezione della directory con \".htaccess\" e \"htpasswd.exe\". Forse PHP è in esecuzione in \"Safe Mode\". <br><br>";

	// ---------------------------------------------------------------------
	// INIZIO
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Benvenuto a XAMPP per Windows";

	$TEXT['start-subhead'] = "Congratulazioni:<br>Hai installato XAMPP con successo!";

	$TEXT['start-text-newest'] = "";

	$TEXT['start-text1'] = "Ora puoi iniziare ad utilizzare Apache &amp; Co. Innanzitutto dovresti andare su »Stato« sul menu di navigazione a sinistra per essere certo che tutto funzioni correttamente.";

	$TEXT['start-text2'] = "";

	$TEXT['start-text3'] = "";

	$TEXT['start-text4'] = "";

	$TEXT['start-text5'] = "";

	$TEXT['start-text6'] = "Buon lavoro, Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALI
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Documentazione online";

	$TEXT['manuals-text1'] = "XAMPP unisce diversi pacchetti software in un unica soluzione. Ecco una lista di documenti standard dei pacchetti più importanti.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Documentazione Apache 2</a>
	<li><a href=\"http://www.php.net/manual/en/\">Documentazione PHP <b>reference </b></a>
	<li><a href=\"http://perldoc.perl.org/\">Documentazione Perl</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">Documentazione MySQL</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccellerator per PHP</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">Documentazione classe fpdf</a>
	</ul>";

	$TEXT['manuals-text2'] = "E una piccola lista di tutorial e la pagina di documentazione di Apache Friends:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Documentazione Apache Friends</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">Tutorial PHP</a> a cura di David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - Un Tutorial interattivo per i principianti</a> a cura di Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Tutorial Perl</a> a cura di Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Buon lavoro e divertimento! :)";

	// ---------------------------------------------------------------------
	// COMPONENTI
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "Componenti di XAMPP";

	$TEXT['components-text1'] = "XAMPP combina molti differenti pacchetti software in un unico pacchetto. Ecco una panoramica di tutti i pacchetti.";

	$TEXT['components-text2'] = "Tante grazie a tutti gli sviluppatori dei pacchetti.";

	$TEXT['components-text3'] = "Nella directory <b>\\xampp\licenses</b> tutti i file di licenza per questi programmi.";

	// ---------------------------------------------------------------------
	// DEMO COLLEZIONE CD
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "Collezione CD (Esempio della classe PHP+MySQL+PDF class)";
	$TEXT['cds-head-fpdf'] = "Collezione CD (Esempio della classe PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "Un programma CD molto semplice.";

	$TEXT['cds-text2'] = "Lista del CD in <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF</a>.";

	$TEXT['cds-error'] = "Impossibile connettersi al database!<br>MySQL è in esecuzione o hai cambiato la password?";
	$TEXT['cds-head1'] = "I miei CD";
	$TEXT['cds-attrib1'] = "Artista";
	$TEXT['cds-attrib2'] = "Titolo";
	$TEXT['cds-attrib3'] = "Anno";
	$TEXT['cds-attrib4'] = "Comando";
	$TEXT['cds-sure'] = "Sicuro?";
	$TEXT['cds-head2'] = "Aggiungi CD";
	$TEXT['cds-button1'] = "CANCELLA CD";
	$TEXT['cds-button2'] = "AGGIUNGI CD";

	// ---------------------------------------------------------------------
	// DEMO BIORITMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Bioritmo (Esempio per PHP+GD)";

	$TEXT['bio-by'] = "a cura di";
	$TEXT['bio-ask'] = "Inserisci la data di nascita";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Data";
	$TEXT['bio-error2'] = "è invalida";

	$TEXT['bio-birthday'] = "Compleanno";
	$TEXT['bio-today'] = "Oggi";
	$TEXT['bio-intellectual'] = "Intellettuale";
	$TEXT['bio-emotional'] = "Emotivo";
	$TEXT['bio-physical'] = "Fisico";

	// ---------------------------------------------------------------------
	// DEMO DI GRAFICA ISTANTANEA
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Grafica istantanea (Esempio per PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Font »AnkeCalligraph« a cura di <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// DEMO DI GRAFICA FLASH
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Grafica Flash (Esempio per PHP+MING)";
	$TEXT['flash-text1'] = "Il progetto MING per win32 non esiste più e non è completo.<br>Per favore leggi questo: <a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - una libreria per output SWF e un modulo PHP</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// DEMO DELLA RUBRICA
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Rubrica (Esempio per PHP+SQLite)";

	$TEXT['phonebook-text1'] = "Uno script per la rubrica molto semplice. Ma implementato con una tecnologia molto moderna: SQLite, il database SQL senza server.";

	$TEXT['phonebook-error'] = "Impossibile aprire il database!";
	$TEXT['phonebook-head1'] = "I miei numeri telefonici";
	$TEXT['phonebook-attrib1'] = "Cognome";
	$TEXT['phonebook-attrib2'] = "Nome";
	$TEXT['phonebook-attrib3'] = "Numero telefonico";
	$TEXT['phonebook-attrib4'] = "Comando";
	$TEXT['phonebook-sure'] = "Sicuro?";
	$TEXT['phonebook-head2'] = "Aggiungi voce";
	$TEXT['phonebook-button1'] = "CANCELLA";
	$TEXT['phonebook-button2'] = "AGGIUNGI";

	// ---------------------------------------------------------------------
	// INFORMAZIONI
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "Informazioni su XAMPP";

	$TEXT['about-subhead1'] = "Idea e realizzazione";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Collaborazione";

	$TEXT['about-subhead4'] = "Contatti";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mail server Mercury Mail SMTP e POP3";
	$TEXT['mail-hinweise'] = "Alcune note importanti per utilizzare Mercury!";
	$TEXT['mail-adress'] = "Mittente:";
	$TEXT['mail-adressat'] = "Destinatario:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Oggetto:";
	$TEXT['mail-message'] = "Messaggio:";
	$TEXT['mail-sendnow'] = "Invio messaggio in corso ...";
	$TEXT['mail-sendok'] = "Messaggio inviato correttamente!";
	$TEXT['mail-sendnotok'] = "Errore! Impossibile inviare il messaggio!";
	$TEXT['mail-help1'] = "Note all'utilizzo di Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercury ha bisogno di una connessione esterna in fase di avviamento;</li>
	<li>all'avvio, Mercury definisce il Domain Name Service (DNS) automaticamente come il server del tuo provider;</li>
	<li>Per tutti gli utenti di server gateway: imposta il DNS attraverso TCP/IP (ad esempio usando InterNic con IP 198.41.0.4);</li>
	<li>il file di configurazione di Mercury si chiama MERCURY.INI;</li>
	<li>per eseguire il test, invia un messaggio a postmaster@localhost oppure admin@localhost and controlla la ricezione di questo messaggio nelle cartelle: xampp.../mailserver/MAIL/postmaster o (...)/admin;</li>
	<li>l'utente di test è \"newuser\" (newuser@localhost) con Password = wampp;</li>
	<li>lo spam e altre e-mail oscene sono vietate con Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "Server FileZilla FTP";
	$TEXT['filezilla-install'] = "Apache <U>non</U> è un server FTP ... ma FileZilla FTP sì. Prendi in cosiderazione i riferimenti seguenti.";
	$TEXT['filezilla-install2'] = "Nella directory principale di xampp, esegui \"filezilla_setup.bat\" per installare. Attenzione: per Windows NT, 2000 ed XP Professional, FileZilla deve essere installato come un servizio.";
	$TEXT['filezilla-install3'] = "Configura \"FileZilla FTP\". A tal fine, utilizza FileZilla Interface con \"FileZilla Server Interface.exe\". In questo esempio ci sono due utenti:<br><br>
	A: Un utente di default \"newuser\", password \"wampp\". La home directory è xampp\htdocs.<br>
	B: Un utente anonimo \"anonymous\", senza password. La home directory è xampp\anonymous.<br><br>
	L'interfaccia di default è l'indirizzo di loopback 127.0.0.1.";
	$TEXT['filezilla-install4'] = "Il server FTP viene stoppato con \"FileZillaFTP_stop.bat\". Per FileZilla FTP come servizio, utilizza direttamente \"FileZillaServer.exe\". Puoi configurare tutte le opzioni di avvio.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Esportazione Excel con PEAR (PHP)";
	$TEXT['pear-text'] = "Un breve <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manuale</A> da Björn Schotte di <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (solo in tedesco)";
	$TEXT['pear-cell'] = "Valore di una cella";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Libreria grafica per PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Un altro accesso a DB (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb è acronimo per Active Data Objects Data Base. Attualmente supportiamp MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite e database ODBC generici. I driver Sybase, Informix, FrontBase e PostgreSQL sono contributi della comunità. Puoi trovarli nella directory \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "Esempio:";
	$TEXT['ADOdb-dbserver'] = "Server Database (MySQL, Oracle ...)";
	$TEXT['ADOdb-host'] = "Host del server DB (nome o IP)";
	$TEXT['ADOdb-user'] = "Nome utente ";
	$TEXT['ADOdb-password'] = "Password";
	$TEXT['ADOdb-database'] = "Database attuale su questo server database";
	$TEXT['ADOdb-table'] = "Tabelle del database";
	$TEXT['ADOdb-nottable'] = "<p><b>Tabella non trovata!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>Il driver per questo server database non esiste o forse è un driver ODBC, ADO o OLEDB!</b>";


	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Pacchetto";
	$TEXT['info-pages'] = "Pagine";
	$TEXT['info-extension'] = "Estensioni";
	$TEXT['info-module'] = "Modulo Apache";
	$TEXT['info-description'] = "Descrizione";
	$TEXT['info-signature'] = "Firma";
	$TEXT['info-docdir'] = "Radice documento";
	$TEXT['info-port'] = "Porta di default";
	$TEXT['info-service'] = "Servizi";
	$TEXT['info-examples'] = "Esempi";
	$TEXT['info-conf'] = "File di configurazione";
	$TEXT['info-requires'] = "Requisiti";
	$TEXT['info-alternative'] = "Alternative";
	$TEXT['info-tomcatwarn'] = "Attenzione! Tomcat non è in esecuzione sulla porta 8080.";
	$TEXT['info-tomcatok'] = "OK! Tomcat è stato avviato con successo sulla porta 8080.";
	$TEXT['info-tryjava'] = "Esempio Java (JSP) con Apache MOD_JK.";
	$TEXT['info-nococoon'] = "Attenzione! Tomcat non è in esecuzione sulla porta 8080. Non posso installare
	\"Cocoon\" senza il server Tomcat in esecuzione!";
	$TEXT['info-okcocoon'] = "Ok! Tomcat è in esecuzione. L'installazione può durare alcuni minuti! Per installare \"Cocoon\" adesso clicca adesso ...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 per XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>Attualmente in QUESTO XAMPP è ";
	$TEXT['switch-whatis'] = "<b>Cosa fa PHP switch?</b><br>PHP Switch per XAMPP cambia PHP dalla versione 4 alla 5 e (!) viceversa. Così puoi testare i tuoi script con PHP 4 o PHP 5.<p>";
	$TEXT['switch-find'] = "<b>Dov'è PHP Switch?</b><br>PHP Switch per XAMPP esegue un file PHP (nella cartella di installazione di XAMPP) di nome \"php-switch.php\". Dovresti usare questo file batch per eseguire: ";
	$TEXT['switch-care'] = "<b>Ci sono difficoltà?</b><br>PHP Switch non cambia la versione di PHP, quando a) il demone Apache HTTPD è in esecuzione o/e b) il file \".phpversion\" nella cartella di installazione non esiste o ha un problema. Nel file \".phpversion\", c'è scritta la versione corrente di PHP \"4\" o \"5\". Per prima cosa stoppa il server Apache, successivamente esegui il file batch \"php-switch.bat\".<p>";
	$TEXT['switch-where4'] = "<b>Dopo ciò! Dove sono i miei (vecchi) file di configurazione?</b><br><br>Per PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>Per PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>Cosa cambia ai miei file di configurazione?</b><br><br>There lives! For PHP4 or PHP5 in the<br>";
	$TEXT['switch-make2'] = "<br><br> .. secured for PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. secured for PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>E questi file saranno ripristinati con PHP switch!!<p>";
	$TEXT['switch-not'] = "<b>Il mio PHP è ok E io NON eseguirò uno \"switch\" !!!</b><br>Splendido! Allora dimentica questa pagina ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon adesso con http://localhost/cocoon/";
	$TEXT['path-cocoon'] = "E la cartella corretta sul tuo disco è: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Ospiti attuali in questa release: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "Un editor HMTL ONLINE molto carino con supporto per JavaScript. Ottimizzato per IE. Non funziona con Mozilla FireFox.";
	$TEXT['guest1-text2'] = "FCKeditor Homepage: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. Nota: Il font Arial NON funziona, ma non so perché!";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">La pagina di esempio scritta con FCKeditor.</A>";

	// ---------------------------------------------------------------------
	// NAVI SPECIALS SECTION
	// ---------------------------------------------------------------------
	
	$TEXT['navi-specials'] = "Specials";
	
	// ---------------------------------------------------------------------
	// PS AND PARADOX EXAMPLE
	// ---------------------------------------------------------------------

    $TEXT['navi-ps'] = "PHP PostScript";
	$TEXT['ps-head'] = "PostScript Module Example";
	$TEXT['ps-text1'] = "PostScript Module »php_ps« by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['ps-text2'] = "Tip: To convert PS files to PDF files on win32, you can use <a href=\"http://www.shbox.de/\" target=\"_new\">FreePDF</a> with <a href=\"http://www.ghostscript.com/awki/\" target=\"_new\">GhostScript</a>.";
	
	$TEXT['navi-paradox'] = "PHP Paradox";
	$TEXT['paradox-head'] = "Paradox Module Example";
	$TEXT['paradox-text1'] = "Paradox Module »php_paradox« by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['paradox-text2'] = "<h2>Reading and writing a paradox database</h2>";
	$TEXT['paradox-text3'] = "More examples you can find in the directory ";
	$TEXT['paradox-text4'] = "Further information to Paradox databases in <a href=\"http://en.wikipedia.org/wiki/Paradox\" target=\"_new\">WikiPedia</a>.";
?>
