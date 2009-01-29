<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP for Windows";
	$TEXT['global-showcode'] = "Få kildekoden her";
	$TEXT['global-sourcecode'] = "Kildekoden";

	// ---------------------------------------------------------------------
	// NAVIGASJON
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Velkommen";
	$TEXT['navi-status'] = "Status";
	$TEXT['navi-security'] = "Sikkerhet";
	$TEXT['navi-doc'] = "Dokumentasjon";
	$TEXT['navi-components'] = "Komponenter";
	$TEXT['navi-about'] = "Om XAMPP";

	$TEXT['navi-demos'] = "Demoer";
	$TEXT['navi-cdcol'] = "CD Samling";
	$TEXT['navi-bio'] = "Bursdags Grafe";
	$TEXT['navi-guest'] = "Gjestebok";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Øyeblikkelig Kunst";
	$TEXT['navi-iart2'] = "Flash Kunst";
	$TEXT['navi-phonebook'] = "Telefonbok";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Teksteditor";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "Verktøy";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Nåværende Gjest";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Språk";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP Status";
	$TEXT['status-text1'] = "Denne siden gir deg all informasjon om dine applikasjoner som kjører og ikke er opperative";
	$TEXT['status-text2'] = "Noen endringer i demo konfigurasjonene gir feil status som med  SSL (https://localhost) alle disse rapportene fungerer ikke!";

	$TEXT['status-mysql'] = "MySQL database";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl med mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python med mod_python";
	$TEXT['status-mmcache'] = "PHP tilleggspakke »eAccelerator«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-xampp-windows-en.html#mmcache";
	$TEXT['status-smtp'] = "SMTP Tjeneste";
	$TEXT['status-ftp'] = "FTP Tjeneste";
	$TEXT['status-tomcat'] = "Tomcat Tjeneste";
	$TEXT['status-named'] = "Navneserver (DNS/NS)";
	$TEXT['status-oci8'] = "PHP tilleggspakke »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";

	$TEXT['status-lookfaq'] = "se FAQ";
	$TEXT['status-ok'] = "OPPERATIV";
	$TEXT['status-nok'] = "NEDE";

	$TEXT['status-tab1'] = "Komponent";
	$TEXT['status-tab2'] = "Status";
	$TEXT['status-tab3'] = "Hint";

	// ---------------------------------------------------------------------
	// SIKKERHET
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "XAMPP SIKKERHET";
	$TEXT['security-text1'] = "Denne siden gir deg en oversikt om statusen på sikkerheten på din XAMPP installasjon.(Vennligst forsett lesingen etter tabellen.)";
	$TEXT['security-text2'] = "Det grønnmerkede området er sikkert; det rødmerkede området er usikkert og gulmerkede området er statusen ukjent for (for eksempel applikasjonen kjører ikke).<p><p>To fix the problems for mysql, phpmyadmin and the xampp directory simply use</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[allowed only for localhost]";
	$TEXT['security-text3'] = "";
	$TEXT['security-text4'] = "";

	$TEXT['security-ok'] = "SIKRET";
	$TEXT['security-nok'] = "USIKRET";
	$TEXT['security-noidea'] = "UKJENT STATUS";

	$TEXT['security-tab1'] = "Tittel";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "Disse XAMPP sidene er tillatt for alle brukere av Nettverket";
	$TEXT['security-checkapache-ok'] = "Disse XAMPP sidene er ikke lengre tillatt for alle brukere av Nettverket";
	$TEXT['security-checkapache-text'] = "Hver eneste  XAMPP side du ser på akkurat nå er tilgjengelig for alle brukere som nettverket. Alle som vet din Ip Adresse kan se disse sidene.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL er tilgjengelig over Nettverket";
	$TEXT['security-checkmysqlport-ok'] = "MySQL er ikke lengre tilgjengelig over Nettverket";
	$TEXT['security-checkmysqlport-text'] = "Dette er en potensiell eller mulighet sikkerhetsrisiko, og er du nøye på sikkerheten burde du sperre MySql brukersnitt tilgangen via Nettverket.";

	$TEXT['security-checkpmamysqluser-nok'] = "Brukeren av phpMyAdmin har ikke noe passord satt";
	$TEXT['security-checkpmamysqluser-ok'] = "Brukeren av phpMyAdmin har ikke lengre noen passord";
	$TEXT['security-checkpmamysqluser-ytext'] = "phpMyAdmin lagrer dine inntillinger i en ekstra MySQL database. For å få tilgang til denne dataen bruk phpMyAdmin og superbrukeren pma. Denne brukeren har i standard installasjonen ikke noen passord satt, og for å unngå et sikkerhetsproblem burde du gi Vedkommende et passord.";

	$TEXT['security-checkmysql-nok'] = "Administratorbrukeren av MySQL har IKKE satt noen passord";
	$TEXT['security-checkmysql-ok'] = "Administratorbrukeren av MySQL har ikke lengre passord satt";
	$TEXT['security-checkmysql-out'] = "A MySQL server is not running  or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Hver lokal Windowsmaskin kan få tilgang til din MySql database med administrator rettigheter. Du BURDE sette et passord.";

	$TEXT['security-pop-nok'] = "Testbrukeren (nybruker) for Mercury Mail serveren (POP3) har et gammelt passord(wampp)";
	$TEXT['security-pop-ok'] = "Testbrukeren \"newuser\" for POP3 serveren (Mercury Mail?) eksisterer ikke lengre eller har nytt passord";
	$TEXT['security-pop-out'] = "En POP3 server som Mercury Mail er ikke opperativ eller er blokkert av en brannmur!";
	$TEXT['security-pop-notload'] = "<i>Den nødvendige IMAP tilleggspakken for denne sikkerhetstesten starter ikke (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Vennligst sjekk og kanskje rediger brukerne og passord i konfigurasjonen til Mercury Mail serveren!";

	$TEXT['security-checkftppassword-nok'] = "FTP passordet er forsatt 'lampp'";
	$TEXT['security-checkftppassword-ok'] = "FTP passordet var velykket endret";
	$TEXT['security-checkftppassword-out'] = "FTP serveren er nede eller blir blokkert av en brannmur!";
	$TEXT['security-checkftppassword-text'] = "Hvis FTP serveren var startet, standardbrukeren 'nobody' med passordet  'lampp' kan laste opp å endre filer på XAMPP webserveren.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin er fullt tilgjengelig via Nettverket";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdmin passord login is aktivert.";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: Kunne ikke finne 'config.inc.php' ...";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin er tilgjengelig fra nettverk uten passord. Konfigurasjonen 'httpd' eller 'cookie' i filen \"config.inc.php\" kan hjelpe.";

	$TEXT['security-checkphp-nok'] = "PHP kjører ikke i \"safe mode\"";
	$TEXT['security-checkphp-ok'] = "PHP kjører i \"safe mode\"";
	$TEXT['security-checkphp-out'] = "Ingen mulighet for å kontrollere PHP innstillingen!";
	$TEXT['security-checkphp-text'] = "Hvis du vil tilate andre utenfor netverket å se din php opplegg, vennligst foretrekk \"safe mode\" innstillingene. Men for kun kjøring av PHP sider internt og utvikling annbefales da IKKE å bruke \"safe mode\" innstillingene pga. noen viktige funksjoner vil da ikke lengre fungere. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>Mer Informasjon</font></a>";


	// ---------------------------------------------------------------------
	// SIKKERHETS INNSTILLINGER
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Sikkerhets konsoll MySQL & XAMPP katalog beskyttelse";
	$TEXT['mysql-rootsetup-head'] = "MYSQL SECTION: \"ROOT\" PASSORD";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "MySQL serveren er nede eller blokkert av en brannmur! Vennligst sjekk at den er startet ev. Brannmurer";
	$TEXT['mysql-rootsetup-passwdnotok'] = "The nye passordet er identisk med det gamle. Vennligs tast inn passord to ganger for å vertifisere og få satt nytt passord!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Null passord ('') vil ikke bli akseptert!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "SUKSESS: Passordet for Superbrukeren eller'root' ble oppdatert eller satt!
	Legg merke til: For at det nye passordet skal tre i kraft for \"root\" må MYSQL restartes !!!! Dataen med det nye passordet er sikkert i følgende fil:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "ERROR: Da muligens root passordet var feil, MySQL nektet login med angitt root passord.";
	$TEXT['mysql-rootsetup-passwdold'] = "Nåværende passord:";
	$TEXT['mysql-rootsetup-passwd'] = "Nytt passord:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Gjenta det nye passordet:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Passord endring";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdmin identifikasjon:";

	$TEXT['xampp-setup-head'] = "XAMPP KATALOG BESKYTTELSE (.htaccess)";
	$TEXT['xampp-setup-bruker'] = "bruker:";
	$TEXT['xampp-setup-passwd'] = "Passord:";
	$TEXT['xampp-setup-start'] = "Gjø XAMPP katalogen sikker";
	$TEXT['xampp-setup-notok'] = "<br><br>ERROR: Linjen for brukernavn og passord må ha minst tre bokstaver/tegn og ikke mer enn 15. Spesielle bokstaver/tegn som <öäü (usw.) og tomme rom er ikke lov!<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>The root password was successfully changed. Please restart MYSQL for loading these changes!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>SUCCESS: The XAMPP directory is protected now! All personal data was safed in the following file:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>ERROR: Ditt system kunne IKKE aktivere katalog gjenoppretelse beskyttelse med filen \".htaccess\" og \"htpasswd.exe\". Kanskje PHP er i \"Safe Mode\". <br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Velkommen til XAMPP for Windows";

	$TEXT['start-subhead'] = "Gratulerer:<br>Du har vellykket installert XAMPP på din maskin!";

	$TEXT['start-text-newest'] = "";

	$TEXT['start-text1'] = "Nå kan du starte å bruke  Apache og Co. Først burde du prøve  »Status« på menyen på venstre, og sørge for at alt funger slik det skal.";

	$TEXT['start-text2'] = "<b>Nyhet i gjeldene versjon: Sikkerhets sjekken!</b>";

	$TEXT['start-text3'] = "For OpenSSL støtte vennligst bruk test sertifikatet <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> or <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text4'] = "";

	$TEXT['start-text5'] = "Og en veldig viktig påminnelse! Stor takk for hjelp og support til Nemesis, KriS, Boppy, Pc-Dummy og alle andre venner av XAMPP!";

	$TEXT['start-text6'] = "Lykke Til, Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALER
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Online dokumentasjon";

	$TEXT['manuals-text1'] = "XAMPP består av mange forskjellige programmer sammensveiset til en pakke.Her er en liste av med linker til dokumentasjon for de ulike programmene som er brukt i XAMPP.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Apache 2 dokumentasjon</a>
	<li><a href=\"http://www.php.net/manual/en/\">PHP <b>referenz </b>dokumentasjon</a>
	<li><a href=\"http://perldoc.perl.org/\">Perl dokumentasjon</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">MySQL dokumentasjon</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">FPDF Class dokumentasjon</a>
	</ul>";

	$TEXT['manuals-text2'] = "Og en liten liste med verktøy og dokumentasjon av Apachefriends:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Apache Friends dokumentasjon</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">PHP Verktøy</a> av David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - Et Interaktiv Verktøy For Nybegynnere</a> av Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Perl Verktøy</a> av Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Lykke til å ha det gøy! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "XAMPP komponenter";

	$TEXT['components-text1'] = "XAMPP består av mange forskjellige programmer sammensveiset til en pakke. Her er en oversikt over alle pakkene.";

	$TEXT['components-text2'] = "Stor takk til utviklerne av disse programmene.";

	$TEXT['components-text3'] = "I katalogen <b>\\xampp\licenses</b> vil du finne alle lisens informasjonen for disse programmene.";

	// ---------------------------------------------------------------------
	// CD SAMLING EKSEMPEL
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "CD Samling (Eksempel for PHP+MySQL+PDF Class)";
	$TEXT['cds-head-fpdf'] = "CD Samling (Eksempel for PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "Et veldig enkelt cd program.";

	$TEXT['cds-text2'] = "CD listen er <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF dokument</a>.";

	$TEXT['cds-error'] = "Kunne ikke koble til databse!<br>Er MySQL opprativ eller har du endret passord?";
	$TEXT['cds-head1'] = "Mine CDer";
	$TEXT['cds-attrib1'] = "Artister";
	$TEXT['cds-attrib2'] = "Tittel";
	$TEXT['cds-attrib3'] = "År";
	$TEXT['cds-attrib4'] = "Kommando";
	$TEXT['cds-sure'] = "Sikker?";
	$TEXT['cds-head2'] = "Legg til CD";
	$TEXT['cds-button1'] = "Slett CD";
	$TEXT['cds-button2'] = "Legg til CD";

	// ---------------------------------------------------------------------
	// BURSDAGSRYTME EKSEMPEL
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Bursdagsrytme (Eksempel for PHP+GD)";

	$TEXT['bio-by'] = "av";
	$TEXT['bio-ask'] = "Vennligst skriv datoen for din bursdag";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Dato";
	$TEXT['bio-error2'] = "er ugyldig";

	$TEXT['bio-birthday'] = "Bursdag";
	$TEXT['bio-today'] = "I dag";
	$TEXT['bio-intellectual'] = "Intelligens";
	$TEXT['bio-emotional'] = "Følelsesfull";
	$TEXT['bio-physical'] = "Pysisk";

	// ---------------------------------------------------------------------
	// ØYEBLIKKELIG KUNST EKSEMPEL
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Øyeblikkelig Kunst (Eksempel for PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Font »AnkeCalligraph« av <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH KUNST EKSEMPEL
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Eksempel for PHP+MING)";
	$TEXT['flash-text1'] = "MING prosjektet for win32 eksisterer ikke lengre og er ikke komplett.<br>Vennligst les denne:<a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - en SWF bibliotek og Php Modul</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Telefonbok (Eksempel for  PHP+SQLite)";

	$TEXT['phonebook-text1'] = "En veldig enkel telefonbok skript, med en veldig moderne og up-to-date teknologi: SQLite, SQL database uten server.";

	$TEXT['phonebook-error'] = "Kan ikke åpne databasen";
	$TEXT['phonebook-head1'] = "Mine telefonnr:";
	$TEXT['phonebook-attrib1'] = "Etternavn";
	$TEXT['phonebook-attrib2'] = "Fornavn";
	$TEXT['phonebook-attrib3'] = "Telefonnr:";
	$TEXT['phonebook-attrib4'] = "Handling";
	$TEXT['phonebook-sure'] = "Sikker?";
	$TEXT['phonebook-head2'] = "Legg til";
	$TEXT['phonebook-button1'] = "SLETT";
	$TEXT['phonebook-button2'] = "LEGG TIL";

	// ---------------------------------------------------------------------
	// OM
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "Om XAMPP";

	$TEXT['about-subhead1'] = "Idé og gjennomførelse";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Collaboration";

	$TEXT['about-subhead4'] = "Kontakt personer";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mailing med Mercury Mail SMTP og  POP3 Serveren";
	$TEXT['mail-hinweise'] = "Noen viktige notater for bruk av Mercury!";
	$TEXT['mail-adress'] = "Sender:";
	$TEXT['mail-adressat'] = "Mottaker:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Tittel:";
	$TEXT['mail-message'] = "Melding:";
	$TEXT['mail-sendnow'] = "Denne meldingen sendes nå...";
	$TEXT['mail-sendok'] = "Meldingen var sent vellykket!";
	$TEXT['mail-sendnotok'] = "Error! Meldingen var ikke vellykket sent!";
	$TEXT['mail-help1'] = "Notater for bruk av Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercury behøver en ekstern tilkobling når den startes;</li>
	<li>i oppstarten, Mercury deffinerer Navneserver tjenesten (DNS) automatisksom navneserveren av din provider;</li>
	<li>For all bruk av gateway servere: Vennligst sett din DNS via TCP/IP (f.e. via InterNic with the IP nr. 198.41.0.4);</li>
	<li>konfigurasjonsfilen til  Mercury er kalt MERCURY.INI;</li>
	<li>for å teste, vennligst sendt en melding til postmaster@localhost eller admin@localhost og sjekk for disse meldingene i følgende mappe: xampp.../mailserver/MAIL/postmaster eller (...)/admin;</li>
	<li> en testbruker kalt \"newbruker\" (nybruker@localhost) med passordet = wampp;</li>
	<li>spam og annen mail som inneholder mistenkelige scripts er totall forbudt med Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "FileZilla FTP Server";
	$TEXT['filezilla-install'] = "Apache er <U>IKKE</U> en FTP Server ... men FileZilla FTP er det. Vennligst ta til vurdering følgende refferanser.";
	$TEXT['filezilla-install2'] = "I hovedkatalogen til xampp, start \"filezilla_setup.bat\" for setup. Advarsel: For Windows NT, 2000 and XP Professional, FileZilla må installeres som en tjeneste.";
	$TEXT['filezilla-install3'] = "Konfigurer \"FileZilla FTP\". For dette vennligst bruk FileZillas Grafiske brukergrensesnitt\"FileZilla Server Interface.exe\". To brukere er i dette eksemplet:<br><br>
	A: En standardbruker \"newbruker\", passord \"wampp\". Hjemmekatalogen er xampp\htdocs.<br>
	B: En anonymbruker \"anonymous\", ingen passord. Hjemmekatalogen er  xampp\anonymous.<br><br>
	Standard adressen for grensesnittet er loopback adresse 127.0.0.1.";
	$TEXT['filezilla-install4'] = "FTP Serveren avsluttes med \"FileZillaFTP_stop.bat\". For FileZilla FTP som en tjeneste, vennligst bruk \"FileZillaServer.exe\" katalogen, da kan du konfigurere alle oppstartsvalg.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Excel eksport med PEAR (PHP)";
	$TEXT['pear-text'] = "En kort <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manual</A> fra Björn Schotte av <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (bare på Tysk)";
	$TEXT['pear-cell'] = "Verdien for en celle";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Grafe Bibliotek for PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Annen DB access (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb står for Active Data Objects Data Base. For øyeblikket har støtte for MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite og generisk ODBC. Sybase, Informix, FrontBase and PostgreSQL drivers er støttespillere. Du vil finne den her \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "Eksempel:";
	$TEXT['ADOdb-dbserver'] = "Database server (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Database server Host (name or IP)";
	$TEXT['ADOdb-bruker'] = "brukernavn ";
	$TEXT['ADOdb-password'] = "Passord";
	$TEXT['ADOdb-database'] = "Databasen som er forøyeblikket tilgjengelig på serveren";
	$TEXT['ADOdb-table'] = "Valgt tabell i database";
	$TEXT['ADOdb-nottable'] = "<p><b>Tabell ikke funnet!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>Driveren for denne  database serveren eksisterer ikke eller det er en ODBC, ADO eller OLEDB driver!</b>";


	// ---------------------------------------------------------------------
	// INFORMASJON
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Pakke";
	$TEXT['info-pages'] = "sider";
	$TEXT['info-extension'] = "Tilleggspakker";
	$TEXT['info-module'] = "Apache modul";
	$TEXT['info-description'] = "Beskrivelse";
	$TEXT['info-signature'] = "Signatur";
	$TEXT['info-docdir'] = "Dokument root";
	$TEXT['info-port'] = "Standard port";
	$TEXT['info-service'] = "Tjenester";
	$TEXT['info-examples'] = "Eksempel";
	$TEXT['info-conf'] = "Konfigurasjons filer";
	$TEXT['info-requires'] = "Krever";
	$TEXT['info-alternative'] = "Alternativ";
	$TEXT['info-tomcatwarn'] = "Advarsel! Tomcat er ikke opprativ på  port 8080.";
	$TEXT['info-tomcatok'] = "OK! Tomcat opprativ på port 8080 vellykket.";
	$TEXT['info-tryjava'] = "Java eksempelet (JSP) med Apache MOD_JK.";
	$TEXT['info-nococoon'] = "Advarsel! Tomcat er ikke opprativ på port 8080. Kan ikke installere
	\"Cocoon\" uten at Tomcat er Opprativ!";
	$TEXT['info-okcocoon'] = "Ok! The Tomcat kjører normalt. Installasjonen foregå noen minutter! Installere \"Cocoon\" klikk her ...";

	// ---------------------------------------------------------------------
	// PHP STØTTE BYTTE
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Bytte 1.0 win32 for XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>For øyeblikket XAMPP bruker ";
	$TEXT['switch-whatis'] = "<b>Hva gjør PHP bytte?</b><br>Apachefriends PHP bytte for XAMPP bytter mellom PHP versjon 4 og 5, og (!) tilbake, slik at du kan teste dine skripts både med PHP 4 og PHP 5.<p>";
	$TEXT['switch-find'] = "<b>Hvor er det PHP bytter?</b><br>PHP Bytter for XAMPP vil pakke en PHP file (XAMPP install katalogen) med det samme \"php-switch.php\". Du burde bruke denne batch filen for executing: ";
	$TEXT['switch-care'] = "<b>Hva kan være vanskelig?</b><br>PHP Bytte vil ikke endre  PHP version, når a) the Apache HTTPD kjører eller/og b)  \".phpversion\" filen i installasjonskatalogen er ledig eller har en feil. I \".phpversion\", var skrevet for XAMPP nåværende PHP versjon nr. \"4\" or \"5\". Vennligst begynn med  \"shutdown\" av Apache HTTPD OG SÅ start \"php-switch.bat\".<p>";
	$TEXT['switch-where4'] = "<b>Etter dette! Hvor er mine (gamle) konfigurasjonsfiler?</b><br><br>For PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>For PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>Hva er det med endringene i konfigurasjonsfilen?</b><br><br>De fungerer! For PHP4 eller PHP5 i<br>";
	$TEXT['switch-make2'] = "<br><br> .. sikret for PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. sikret for PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>Og disse filene går tilbake med PHP byttet!!<p>";
	$TEXT['switch-not'] = "<b>Min PHP fungerer bra OG jeg vil IKKE \"BYTTE\" !!!</b><br>Supert! Bare glem dette her ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon -  http://localhost/cocoon/";
	$TEXT['path-cocoon'] = "Og den riktige katalogen på din maskin: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// GJEST
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Nåværende gjester i denne versjonen: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "En veldig flott Html online editor med flere JavaScript,beregnet for IE men fungerer ikke med Mozilla FireFox.";
	$TEXT['guest1-text2'] = "FCKeditor Hjemmeside: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. Legg merke til:Arial fonten vil ikke fungere her av en eller annen Grunn!";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynsiden.php\" target=\"_new\">Eksempel siden var skrevet med FCKeditor.</A>";

	// ---------------------------------------------------------------------
	// NAVI SPECIALS SECTION
	// ---------------------------------------------------------------------
	
	$TEXT['navi-specials'] = "Extra";
	
	// ---------------------------------------------------------------------
	// PS AND PARADOX EXAMPLE
	// ---------------------------------------------------------------------

    $TEXT['navi-ps'] = "PHP PostScript";
	$TEXT['ps-head'] = "PostScript Modul Eksempel";
	$TEXT['ps-text1'] = "PostScript Modul »php_ps« av <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['ps-text2'] = "Tip: For å konvertere PS filer til PDF filer i  win32 system, du kan bruke <a href=\"http://www.shbox.de/\" target=\"_new\">FreePDF</a> med <a href=\"http://www.ghostscript.com/awki/\" target=\"_new\">GhostScript</a>.";
	
	$TEXT['navi-paradox'] = "PHP Paradox";
	$TEXT['paradox-head'] = "Paradox Modul Eksempel";
	$TEXT['paradox-text1'] = "Paradox Modul »php_paradox« av <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['paradox-text2'] = "<h2>Lese og skrive en paradox database</h2>";
	$TEXT['paradox-text3'] = "Flere eksempler kan du finne i mappen";
	$TEXT['paradox-text4'] = "Mere informasjon til Paradox databaser på <a href=\"http://en.wikipedia.org/wiki/Paradox\" target=\"_new\">WikiPedia</a>.";

?>
