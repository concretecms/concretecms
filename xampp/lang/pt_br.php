<?php //Translation for the Windows version of XAMPP by Marcio <mpg@mpg.com.br>
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP para Windows";
	$TEXT['global-showcode'] = "Obtenha o código-fonte aqui";
	$TEXT['global-sourcecode'] = "Código-fonte";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Bem Vindo";
	$TEXT['navi-status'] = "Status";
	$TEXT['navi-security'] = "Segurança";
	$TEXT['navi-doc'] = "Documentação";
	$TEXT['navi-components'] = "Componentes";
	$TEXT['navi-about'] = "Sobre o XAMPP";

	$TEXT['navi-demos'] = "Demos";
	$TEXT['navi-cdcol'] = "Coleção de CD";
	$TEXT['navi-bio'] = "Biorítimo";
	$TEXT['navi-guest'] = "Livro de Visitas";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Arte Instantânea";
	$TEXT['navi-iart2'] = "Arte Flash";
	$TEXT['navi-phonebook'] = "Agenda de Telefones";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "Trocar PHP";

	$TEXT['navi-tools'] = "Ferramentas";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Visitante Atual";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Idiomas";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP Status";
	$TEXT['status-text1'] = "Esta página lhe oferece uma visualização de todas as informações sobre o que está rodando e funcionando como também o que não está.";
	$TEXT['status-text2'] = "Algumas vezes as alterações feitas nos arquivos de configuração poderão causar um status negativo. Com SSL (https://localhost) todos os relatórios não funcionarão!";

	$TEXT['status-mysql'] = "Banco de dados MySQL";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl com mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python com mod_python";
	$TEXT['status-mmcache'] = "extensão PHP »Turck MMCache«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp-en.html#mmcache";
	$TEXT['status-smtp'] = "Serviço SMTP";
	$TEXT['status-ftp'] = "Serviço FTP";
	$TEXT['status-tomcat'] = "Serviço Tomcat";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-oci8'] = "extensão PHP »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";

	$TEXT['status-lookfaq'] = "ver FAQ";
	$TEXT['status-ok'] = "ATIVADO";
	$TEXT['status-nok'] = "DESATIVADO";

	$TEXT['status-tab1'] = "Componente";
	$TEXT['status-tab2'] = "Status";
	$TEXT['status-tab3'] = "Sugestão";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "SEGURANÇA XAMPP";
	$TEXT['security-text1'] = "Esta página lhe dá uma visão geral rápida sobre o status da segurança de sua instalação de XAMPP. (Por favor, continue lendo após a tabela.)";
	$TEXT['security-text2'] = "
	Os pontos marcados em verde são seguros; os pontos marcados em vermelho são definitivamente inseguros e os pontos marcados em amarelo não foram possíveis de serem verificados (Por exemplo: O sofware que deseja verificar não está funcionando).<p>Para simplesmente reparar problemas para o mysql, o phpmyadmin e o diretório do xampp use</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[permitido somente para localhost (servidor local)]<br>&nbsp;<br>&nbsp;<br>
	Algumas notas inportantes:<ul>
	<li>Todos estes testes são feitos UNICAMENTE no \"localhost\" (servidor local) (127.0.0.1).</li>
	<li><i><b>Para o FileZilla FTP e Mercury Mail, você necessita corrigir todos os problemas de segurança por você mesmo! Desculpe. </b></i></li>
	<li>Se seu computador não estiver online ou protegido por um firewall, seus servidores estarão SEGUROS contra ataques externos.</li>
	<li>Se os servidores não estiverem rodando, estes servidores estarão SEGUROS!</li></ul>";
	$TEXT['security-text3'] = "<b>Por favor, considere:
	Com uma segurança maior do XAMPP alguns exemplos não irão executar sem uma mensagem de erro. Caso use o PHP em \"safe mode\" (modo seguro) por exemplo, algumas funções deste sistema da segurança não funcionarão mais. Frequentemente mais segurança significa menos funcionalidade ao mesmo tempo.</b>";
	$TEXT['security-text4'] = "As portas padrão do XAMPP:";

	$TEXT['security-ok'] = "SEGURO";
	$TEXT['security-nok'] = "INSEGURO";
	$TEXT['security-noidea'] = "DESCONHECIDO";

	$TEXT['security-tab1'] = "Assunto";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "Estas páginas do XAMPP estão acessíveis a qualquer pessoa na rede";
	$TEXT['security-checkapache-ok'] = "Estas páginas do XAMPP não estão acessíveis por qualquer pessoa na rede";
	$TEXT['security-checkapache-text'] = "Qualquer página de demostração do XAMPP que você estiver visualizando está acessível por qualquer pessoa da rede. Qualquer um que conheça o endereço de IP poderá vê-las.";

	$TEXT['security-checkmysqlport-nok'] = "O MySQL está acesível na rede";
	$TEXT['security-checkmysqlport-ok'] = "O MySQL não está acesível na rede";
	$TEXT['security-checkmysqlport-text'] = "Este é um potencial ou ao menos uma falha teórica de segurança. E se você for louco sobre a segurança você deve desativar a interface de rede do MySQL.";

	$TEXT['security-checkpmamysqluser-nok'] = "O usuário principal do phpMyAdmin não possue uma senha";
	$TEXT['security-checkpmamysqluser-ok'] = "O usuário principal do phpMyAdmin possui uma senha";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin armazena suas informações em um banco de dados MySQL extra. Para acessar as informações do phpMyAdmin utilize-se do usuário especial pma. Este usuário não possui na instalação padrão uma senha definida e por razões de segurança é necessário que você defina uma senha para ele.";

	$TEXT['security-checkmysql-nok'] = "O usuário administrador do MySQL NÃO possui uma senha";
	$TEXT['security-checkmysql-ok'] = "O usuário administrador do MySQL possui uma senha";
	$TEXT['security-checkmysql-out'] = "A MySQL server is not running  or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Qualquer usuário local poderá acessar o banco de dados MySQL com privilégios de Administrador. Você deve definir uma senha.";

	$TEXT['security-pop-nok'] = "O usuário teste (newuser) para o servidor POP3 Mercury Mail possui uma senha antiga (wampp)";
	$TEXT['security-pop-ok'] = "O usuário teste \"newuser\" para o servidor POP3 Mercury Mail não mais existe ou possui uma senha nova";
	$TEXT['security-pop-out'] = "Um servidor POP3 (como o Mercury Mail) não está rodando ou está sendo bloqueado pelo firewall!";
	$TEXT['security-pop-notload'] = "<i>A extensão IMAP necessária para o teste de segurança não foi habilitada (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Por favor, verifique e caso seje necessário edite todos os usuários e senhas no arquivo de configuração do servidor Mercury Mail!";

	$TEXT['security-checkftppassword-nok'] = "A senha do servidor de FTP FileZilla permanece ainda como 'wampp'";
	$TEXT['security-checkftppassword-ok'] = "A senha do servidor de FTP FileZilla foi alterada";
	$TEXT['security-checkftppassword-out'] = "O servidor FTP não está rodando ou está sendo bloqueado pelo firewall!";
	$TEXT['security-checkftppassword-text'] = "O servidor de FTP FileZilla foi iniciado, o usuário padrão 'newuser' com senha 'wampp' pode enviar arquivos que poderão alterar seu servidor XAMPP. Caso você ative o servidor FileZilla FTP você deverá definir uma nova senha para o usuário 'newuser'.";

	$TEXT['security-phpmyadmin-nok'] = "O PhpMyAdmin pode ser acessado pela rede";
	$TEXT['security-phpmyadmin-ok'] = "A senha do PhpMyAdmin está ativada.";
	$TEXT['security-phpmyadmin-out'] = "Não foi possível encontrar o arquivo 'config.inc.php' do PhpMyAdmin";
	$TEXT['security-phpmyadmin-text'] = "O PhpMyAdmin pode ser acessado pela rede sem senha. A configuração 'httpd' ou 'cookie' no arquivo \"config.inc.php\" poderá ajudá-lo a resolver o problema.";

	$TEXT['security-checkphp-nok'] = "O PHP NÃO está rodando no modo seguro (\"safe mode\")";
	$TEXT['security-checkphp-ok'] = "O PHP está rodando no modo seguro (\"safe mode\")";
	$TEXT['security-checkphp-out'] = "Impossível controlar as configurações do PHP!";
	$TEXT['security-checkphp-text'] = "Caso deseje oferecer a execução de arquivo PHP para outras pessoas, por favor, pense em utilizar a configuração de modo de seguraça (\"safe mode\"). Caso só você tenha acesso ao PHP e o utilizará para desenvolvimento de aplicativos recomendamos NÃO ativar a configuração de modo seguro (\"safe mode\") porque algumas funções importantes não funcionarão com ela. <A HREF=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>Mais informações</font></A>";


	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Console de Segurança MySQL & Proteção de Diretórios do XAMPP";
	$TEXT['mysql-rootsetup-head'] = "SEÇÃO MYSQL: SENHA \"ROOT\"";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "O servidor MySQL não está ativo ou foi bloqueado por um firewall! Por favor, verifique isto primeiramente ...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "A nova senha é identica a senha de repetição. Por favor, entre com a nova senha novamente!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Senhas em branco ('') não são aceitas!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "SUCESSO: A senha para o Super Usuário 'root' foi definida e atualizada!
	Nota: A definição de uma nova senha para o Super Usuário \"root\" necessita que você REINICIE O MYSQL !!!! As informações da nova senha foram gravadas no seguinte arquivo:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "ERRO: A senha root possivelmente está incorreta. MySQL recusa em entrar com a senha atual de root.";
	$TEXT['mysql-rootsetup-passwdold'] = "Senha atual:";
	$TEXT['mysql-rootsetup-passwd'] = "Nova senha:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Repetir nova senha:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Alterar Senha";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "Atenticação PhpMyAdmin:";

	$TEXT['xampp-setup-head'] = "Proteção de Diretórios do XAMPP (.htaccess)";
	$TEXT['xampp-setup-user'] = "Usuário:";
	$TEXT['xampp-setup-passwd'] = "Senha:";
	$TEXT['xampp-setup-start'] = "Tornar seguro o diretório XAMPP";
	$TEXT['xampp-setup-notok'] = "<br><br>ERRO: A string para o nome de usuário e a senha devem ter pelo menos três (3) characteres e não mais que quinze (15) caracteres. Caracteres especiais tal como <öäü (usw.) e caracteres em branco não são permitidos!<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>A senha de root foi alterada. Por favor, reinicie o MYSQL para que estas alterções tenha efeito!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>SUCESSO: O diretório XAMPP está agora protegido! Todas as informações pessoais foram gravadas no arquivo:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>ERRO: Seu sistema NÃO pode ativar o sistema de proteção de diretório com \".htaccess\" e o arquivo \"htpasswd.exe\". Talvez o PHP esteja em modo seguro (\"Safe Mode\"). <br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Bem vindo ao XAMPP para Windows";

	$TEXT['start-subhead'] = "Congratulações:<br>Você instalou corretamente o XAMPP em seu sistema!";

	$TEXT['start-text1'] = "Você pode agora iniciar a utilização do Apache e outros aplicativos. Primeiramente tente verificar o »Status«  no menu lateral para ter certeza que tudo está funcionando corretamente.";

	$TEXT['start-text2'] = "";

	$TEXT['start-text3'] = "";

	$TEXT['start-text4'] = "Para suporte em OpenSSL por favor utilize o certificado teste em <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> ou <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text5'] = "E muito importante! Especial agradecimento pela ajuda e suporte dada por Carsten, Nemesis, KriS, Boppy, Pc-Dummy e todos os outros amigos do XAMPP!";

	$TEXT['start-text6'] = "Boa sorte, Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Documentação Online";

	$TEXT['manuals-text1'] = "XAMPP combina muitos diferentes softwares em um só pacote. Você encontrará a lista padrão e documentação de referência dos mais importantes pacotes nos links:";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Documentação do Apache 2</a>
	<li><a href=\"http://www.php.net/manual/en/\">PHP <b>Manual de Referência</b></a>
	<li><a href=\"http://perldoc.perl.org/\">Documentação do Perl</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">Documentação do MySQL</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">Documentação da Classe fpdf</a>
	</ul>";

	$TEXT['manuals-text2'] = "Como também uma pequena lista de tutoriais e documentação do Apache Friends:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Documentação Apache Friends</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">Tutorial PHP</a> por David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - Tutorial Interativo para Iniciantes</a> por Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Tutorial Perl</a> por Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Boa sorte e divirta-se! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "Componentes do XAMPP";

	$TEXT['components-text1'] = "O XAMPP combina diferentes softwares em um só pacotes. Aqui está as informações dos pacotes utilizados.";

	$TEXT['components-text2'] = "Agradecemos aos desenvolvedores destes programas.";

	$TEXT['components-text3'] = "No diretório <b>\\xampp\licenses</b> você irá encontrar todas as licenças destes programas.";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "Coleção de CD (Exemplo com PHP MySQL e Classe PDF)";
	$TEXT['cds-head-fpdf'] = "Coleção de CD (Exemplo com PHP MySQL e FPDF)";

	$TEXT['cds-text1'] = "Um programa muito simples para coletânea de CD's.";

	$TEXT['cds-text2'] = "Lista de CD's como um <a href='$_SERVER[PHP_SELF]?action=getpdf'>Arquivo PDF</a>.";

	$TEXT['cds-error'] = "Não foi possível conectar ao banco de dados!<br>O servidor MySQL está rodando ou você alterou a senha?";
	$TEXT['cds-head1'] = "Meus CD's";
	$TEXT['cds-attrib1'] = "Artista";
	$TEXT['cds-attrib2'] = "Título";
	$TEXT['cds-attrib3'] = "Ano";
	$TEXT['cds-attrib4'] = "Comando";
	$TEXT['cds-sure'] = "Certeza?";
	$TEXT['cds-head2'] = "Adicionar um CD";
	$TEXT['cds-button1'] = "APAGAR CD";
	$TEXT['cds-button2'] = "ADICIONAR CD";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Biorítimo (Exemplo com PHP e GD)";

	$TEXT['bio-by'] = "por";
	$TEXT['bio-ask'] = "Por favor, entre com a data de seu aniversário";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Data";
	$TEXT['bio-error2'] = "é invalido(a)";

	$TEXT['bio-birthday'] = "Aniversário";
	$TEXT['bio-today'] = "Hoje";
	$TEXT['bio-intellectual'] = "Intelectual";
	$TEXT['bio-emotional'] = "Emocional";
	$TEXT['bio-physical'] = "Físico";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Arte Instantânea (Exemplo com PHP GD e FreeType)";
	$TEXT['iart-text1'] = "Fonte »AnkeCalligraph« por <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Arte Flash (Exemplo com PHP e MING)";
	$TEXT['flash-text1'] = "O Projeto MING para win32 não existe mais e não está completo.<br>Por favor, leia isso: <a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - uma biblioteca de saída SWF e Módulo PHP</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Agenda de Telefones (Exemplo com PHP e SQLite)";

	$TEXT['phonebook-text1'] = "Um script simples para Agenda de Telefones. Implementado com um tecnologia moderna e atual: SQLite, o banco de dados SQL sem servidor.";

	$TEXT['phonebook-error'] = "Não foi possível abrir o banco de dados!";
	$TEXT['phonebook-head1'] = "Meus telefones";
	$TEXT['phonebook-attrib1'] = "Sobrenome";
	$TEXT['phonebook-attrib2'] = "Nome";
	$TEXT['phonebook-attrib3'] = "Telefone";
	$TEXT['phonebook-attrib4'] = "Comando";
	$TEXT['phonebook-sure'] = "Certeza?";
	$TEXT['phonebook-head2'] = "Adicionar dados";
	$TEXT['phonebook-button1'] = "APAGAR";
	$TEXT['phonebook-button2'] = "ADICIONAR";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "Sobre o XAMPP";

	$TEXT['about-subhead1'] = "Idéias e Realizações";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Colaboração";

	$TEXT['about-subhead4'] = "Contatos pessoais";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mala-direta com o servidor SMTP e POP3 Mercury Mail";
	$TEXT['mail-hinweise'] = "Algumas notas importantes para utilizar o Mercury!";
	$TEXT['mail-adress'] = "Remetente:";
	$TEXT['mail-adressat'] = "Destinatário:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Assunto:";
	$TEXT['mail-message'] = "Mensagem:";
	$TEXT['mail-sendnow'] = "Esta mensagem ser agora enviada ...";
	$TEXT['mail-sendok'] = "Esta mensagem foi enviada com sucesso!";
	$TEXT['mail-sendnotok'] = "Erro! A mensagem nor pode ser enviada!";
	$TEXT['mail-help1'] = "Notes da utilização do Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>O servidor Mercury necessita uma conexão externa na inicialização;</li>
	<li>Ao iniciar, o Mercury define o Domain Name Service (DNS) automaticamente como sendo o nome do servidor de seu provedor;</li>
	<li>Para todos os usuários de gateway: Por favor, configure seu DNS via TCP/IP (ex. via InterNic com um número de IP 198.41.0.4);</li>
	<li>O arquivo de configuração do Mercury é denominado MERCURY.INI;</li>
	<li>Por favor, envie uma mensagem para postmaster@localhost ou admin@localhost para testar e verifique o resultado nas pastas: xampp.../mailserver/MAIL/postmaster ou (...)/admin;</li>
	<li>Exixte um usuário teste denominado \"newuser\" (newuser@localhost) e com a Senha = wampp;</li>
	<li>Spam e outras obcenidades são totalmente proibidas com o Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "Servidor FTP FileZilla";
	$TEXT['filezilla-install'] = "O Apache <U>não</U> é um servidor de FTP ... mas o FTP FileZilla é. Por favor, considere as seguintes referências.";
	$TEXT['filezilla-install2'] = "No diretório principal do xampp, rodar o arquivo de lote \"filezilla_setup.bat\" para configurar. Atenção: Para o Windows NT, 2000 e XP Profissional, o FileZilla necessita ser instalado como um serviço.";
	$TEXT['filezilla-install3'] = "Configure o servidor de FTP \"FileZilla\". Utilize-se da interface do FileZilla para configurá-lo, que poder ser acessada pelo arquivo \"FileZilla Server Interface.exe\". Existem dois usuários neste exemplo:<br><br>
	A: O usuário padrão \"newuser\" com senha \"wampp\". O diretório principal dele é xampp\htdocs.<br>
	B: Um usuário anônimo \"anonymous\" sem senha definida. O diretório principal dele é xampp\anonymous.<br><br>
	A interface padrão é no endereço de loopback com IP 127.0.0.1.";
	$TEXT['filezilla-install4'] = "O servidor FTP será finaliza com o arquivo lote \"FileZillaFTP_stop.bat\". Para o servidor FTP FileZilla sendo utilizado como um serviço do Windows, por favor se utilize diretamente do arquivo \"FileZillaServer.exe\". Com ele você pode configurar todas as opções de inicialização.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Exportando para o Excel com PEAR (PHP)";
	$TEXT['pear-text'] = "Um <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manual</A> simples feito por Björn Schotte do <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (Somente em Alemão)";
	$TEXT['pear-cell'] = "O valor da célula";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Biblioteca Gráfica para PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Another DB access (PHP)";
	$TEXT['ADOdb-text'] = "
	ADOdb proporciona uma padronização dos comandos para acesso a banco de dados. Suporta atualmente MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite e ODBC genérico. Os drivers para Sybase, Informix, FrontBase e PostgreSQL são contribuições da comunidade.  Você o encontra em \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "Exemplo:";
	$TEXT['ADOdb-dbserver'] = "Tipo de Banco de dados (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Servidor do Banco de Dados (Nome ou IP)";
	$TEXT['ADOdb-user'] = "Usuário ";
	$TEXT['ADOdb-password'] = "Senha";
	$TEXT['ADOdb-database'] = "Atual banco de dados neste servidor";
	$TEXT['ADOdb-table'] = "Selecionar tabela do banco de dados";
	$TEXT['ADOdb-nottable'] = "<p><b>Tabela não encontrada!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>O driver para este tipo de servidor não existe ou talves seja um driver ODBC, ADO ou OLEDB!</b>";


	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Pacote";
	$TEXT['info-pages'] = "Páginas";
	$TEXT['info-extension'] = "Extensões";
	$TEXT['info-module'] = "Módulo Apache";
	$TEXT['info-description'] = "Descrição";
	$TEXT['info-signature'] = "Assinatura";
	$TEXT['info-docdir'] = "Raiz dos Documentos";
	$TEXT['info-port'] = "Porta padrão";
	$TEXT['info-service'] = "Serviços";
	$TEXT['info-examples'] = "Examplos";
	$TEXT['info-conf'] = "Arquivos de Configuração";
	$TEXT['info-requires'] = "Requer";
	$TEXT['info-alternative'] = "Alternativo(a)";
	$TEXT['info-tomcatwarn'] = "ATENÇÃO! O Tomcat foi iniciado na porta 8080.";
	$TEXT['info-tomcatok'] = "OK! Tomcat foi iniciado corretamente na porta 8080.";
	$TEXT['info-tryjava'] = "Exemplo java (JSP) com o Apache MOD_JK.";
	$TEXT['info-nococoon'] = "ATENÇÃO! Tomcat não pode iniciar na porta 8080. Não é possível instalar o \"Cocoon\" sem o servidor Tomcat!";
	$TEXT['info-okcocoon'] = "Ok! O servidor Tomcat está rodando normalmente. A instalação funcionará em alguns minutos! Para instalar agora o \"Cocoon\" clique aqui ...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 para XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>Atualmente ESTE XAMPP está rodando em ";
	$TEXT['switch-whatis'] = "<b>O que faz o PHP switch?</b><br>O PHP Switch do apachefriends para XAMPP permite a troca do PHP versão 4 para a versão 5 E (!) vice-versa. Você pode testar seus scripts em PHP 4 ou PHP 5.<p>";
	$TEXT['switch-find'] = "<b>Onde está o PHP Switch?</b><br>O PHP Switch para XAMPP irá executar um arquivo PHP (no diretório de instalação do XAMPP) com o nome \"php-switch.php\". Você também poderá utilizar este arquivo de lote para executar: ";
	$TEXT['switch-care'] = "<b>Está com dificuldades?</b><br>O PHP Switch não altera sua versão do PHP, quando a) O HTTPD Apache está rodando e/ou b) o arquivo \".phpversion\" no diretório de instalação está vazio ou tem um defeito. No arquivo \".phpversion\" foi escrito a atual versão do PHP utilizada pelo XAMPP tal como \"4\" ou \"5\". \"Desligue\" o servidor HTTPD Apache e então EXECUTE o arquivo \"php-switch.bat\".<p>";
	$TEXT['switch-where4'] = "<b>Depois disso onde estão meus arquivos de configuração antigos?</b><br><br>Para o PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>Para o PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>Quais são as alterações feitas no meus arquivos de configuração?</b><br><br>Eles estão para o PHP4 ou PHP5 em<br>";
	$TEXT['switch-make2'] = "<br><br> .. copia de segurança para o PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. copia de segurança para o PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>E estes arquivos retornarão com o PHP switch!!<p>";
	$TEXT['switch-not'] = "<b>Meu PHP está perfeito e NÃO UTILIZAREI o \"switch\" !!!</b><br>Super! Então esqueça tudo isso ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon em http://localhost/cocoon/";
	$TEXT['path-cocoon'] = "E a pasta atual no seu disco é: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Convidados atuais nesta release: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "Um editor HMTL ONLINE com mais que só JavaScript. Otimizado para o IE. Mas não funciona com o Mozilla FireFox.";
	$TEXT['guest1-text2'] = "Site do FCKeditor: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. Notas: A fonte Arial font NÃO funciona aqui, mas não se sabe o porque!";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">Um exemplo escrito com o FCKeditor.</A>";

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
