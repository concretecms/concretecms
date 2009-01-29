<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP Windows版";
	$TEXT['global-showcode'] = "ソースコードの表示";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "ようこそ";
	$TEXT['navi-status'] = "ステータス";
	$TEXT['navi-security'] = "セキュリティ";
	$TEXT['navi-doc'] = "マニュアル";
	$TEXT['navi-components'] = "コンポーネント";
	$TEXT['navi-about'] = "XAMPPについて";

	$TEXT['navi-demos'] = "デモ";
	$TEXT['navi-cdcol'] = "ＣＤ コレクション";
	$TEXT['navi-bio'] = "バイオリズム";
	$TEXT['navi-guest'] = "ゲストブック";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "インスタントアート";
	$TEXT['navi-iart2'] = "フラッシュアート";
	$TEXT['navi-phonebook'] = "電話帳";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "ツール";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Current Guest";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "各国語";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP ステータス";
	$TEXT['status-text1'] = "このページでは稼働中のもの、そうでないものの情報を一覧で見ることができます。";
	$TEXT['status-text2'] = "設定の変更の仕方によっては、誤ったネガティブなステータス・レポートが出る場合があります。 SSL通信 (https://localhost)では、レポートが全て起動しない場合があります。";

	$TEXT['status-mysql'] = "MySQL データベース";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perlとmod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Pythonとmod_python";
	$TEXT['status-mmcache'] = "PHP extension ｻTurck MMCacheｫ";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp-en.html#mmcache";
	$TEXT['status-smtp'] = "SMTP Service";
	$TEXT['status-ftp'] = "FTP Service";
	$TEXT['status-tomcat'] = "Tomcat Service";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-oci8'] = "PHP extension ｻOCI8/Oracleｫ";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";

	$TEXT['status-lookfaq'] = "FAQを参照";
	$TEXT['status-ok'] = "開始";
	$TEXT['status-nok'] = "停止";

	$TEXT['status-tab1'] = "コンポーネント";
	$TEXT['status-tab2'] = "ステータス";
	$TEXT['status-tab3'] = "ヒント";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "XAMPP セキュリティ";
	$TEXT['security-text1'] = "ここでは、XAMPPインストールにおけるセキュリティ・ステータスについて、簡単なご説明をしています。(テーブルの後も続けてお読みください。)";
	$TEXT['security-text2'] = "緑のハイライトの表示は「安全」です。赤のハイライトの表示は、決定的に「要注意」、黄色のハイライトは「確認不能」(例えば、確認したいソフトウェアが稼動していないなど)です。<p>そのような問題をすべて修正するには、単純に次のツールを使ってください。</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[localhostからのみアクセスできます。]<br>&nbsp;<br>&nbsp;<br>
	そのほかの重要なメモ:<ul>
	<li>全てのテストページはlocalhost(127.0.0.1)のためだけに作成されています。</li>
	<li><i><b>FileZilla FTPとMercury Mailは、自分自身でセキュリティの問題点を解決してください。</b></i></li>
	<li>もし、あなたのコンピュータがオンラインでない場合、またはファイアウォールでブロックされている場合、あなたのサーバは外部から攻撃されることはありません。</li>
	<li>もし、サーバが起動していない場合、それらのサーバは安全です。</li></ul>";
	$TEXT['security-text3'] = "<b>よく検討してください:
	XAMPPのセキュリティを設定すると、いくつかのサンプルが実行できなくなります。もし、あなたがPHPを\"safe mode\"で使っていた場合、セキュリティ設定のいくつかの機能が動作しません。セキュリティを設定すると、機能が制限される場合があります。</b>";
	$TEXT['security-text4'] = "XAMPPデフォルトのポート:";

	$TEXT['security-ok'] = "安全";
	$TEXT['security-nok'] = "要注意";
	$TEXT['security-noidea'] = "不明";

	$TEXT['security-tab1'] = "対象";
	$TEXT['security-tab2'] = "ステータス";

	$TEXT['security-checkapache-nok'] = "これらのXAMPPページは一般的にネットワーク経由でアクセス可能です。";
	$TEXT['security-checkapache-ok'] = "これらのXAMPPページはネットワーク経由での一般的なアクセスが可能でなくなりました。";
	$TEXT['security-checkapache-text'] = "現在ご覧になっているすべてのXAMPPデモページは、ネットワーク上で一般的にアクセス可能です。あなたのIPアドレスを知っている人は誰でもこれらのページを見ることができます。";

	$TEXT['security-checkmysqlport-nok'] = "MySQL はネットワーク経由でアクセス可能です";
	$TEXT['security-checkmysqlport-ok'] = "MySQL はネットワーク上でアクセスできなくなりました";
	$TEXT['security-checkmysqlport-text'] = "セキュリティ・リークの可能性があります（少なくとも理論上のセキュリティ・リークです）。セキュリティについて不安がありましたら、MySQLのネットワーク・インターフェースを無効にすることをお勧めします。";

	$TEXT['security-checkpmamysqluser-nok'] = "phpMyAdmin ユーザpma にパスワードがありません";
	$TEXT['security-checkpmamysqluser-ok'] = "phpMyAdmin ユーザpma はパスワード無しの状態が解消されました";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdminは、あなたの追加MySQL databaseの選択を保存します。このデータにアクセスするのに、phpMyAdminでは特別なユーザpmaを使用します。このユーザは、デフォルトのインストールではパスワードが与えられていないので、セキュリティのトラブルを避けるためにはこのユーザにパスワードを与える必要があります。";

	$TEXT['security-checkmysql-nok'] = "MySQLユーザルートにパスワードがありません";
	$TEXT['security-checkmysql-ok'] = "MySQLユーザルートはパスワードが無しの状態が解消されました";
	$TEXT['security-checkmysql-out'] = "MySQLサーバは起動していないか、ファイアウォールでブロックされています!";
	$TEXT['security-checkmysql-text'] = "Windows Box上のローカルユーザであれば誰でもあなたのMySQLデータベースに管理者権限でアクセスできます。パスワードを設定してください。";

	$TEXT['security-pop-nok'] = "Mercury メールサーバ(POP3)のテストユーザ(newuser)は古いパスワードのままです(wampp)";
	$TEXT['security-pop-ok'] = "POP3サーバ(Mercury Mail)のテストユーザ \"newuser\" は存在しないか、新しいパスワードが設定されました。";
	$TEXT['security-pop-out'] = "MercuryメールサーバのようなPOP3サーバが起動していないか、ファイアウォールでブロックされています。";
	$TEXT['security-pop-notload'] = "<i>この安全テストに必要なIMAP機能が読み込まれていません(php.ini)。</i><br>";
	$TEXT['security-pop-text'] = "Mercury メールサーバのパスワードやユーザなど設定を確認してください。";

	$TEXT['security-checkftppassword-nok'] = "匿名のユーザのFTPパスワードが「wampp」のままです";
	$TEXT['security-checkftppassword-ok'] = "FTPパスワードが変更になりました。";
	$TEXT['security-checkftppassword-out'] = "FTPサーバは起動していないか、ファイアウォールでブロックされています!";
	$TEXT['security-checkftppassword-text'] = "もしFileZilla FTPサーバが起動していれば、デフォルトユーザ \"newuser\"、パスワード \"wampp\"でXAMPPのウェブサーバのファイルのアップロードや変更ができます。もしFileZilla FTPサーバを有効にする場合、\"newuser\" のパスワードを設定してください。";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdminはネットワーク上から自由にアクセスできてしまいます。";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdminのログインパスワードが有効になりました。";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin:'config.inc.php'ファイルが見つかりません。";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdminはパスワードなしでネットワークからアクセスできます。. \"config.inc.php\"ファイルの'httpd'か'cookie'が設定の助けになります。";

	$TEXT['security-checkphp-nok'] = "PHPは\"safe mode\"で起動していません。";
	$TEXT['security-checkphp-ok'] = "PHPは\"safe mode\"で起動しています。";
	$TEXT['security-checkphp-out'] = "PHPの設定で制御できません!";
	$TEXT['security-checkphp-text'] = "もし、公開サーバでPHPの動作を許可するのであれば、\"safe mode\"の設定を視野に入れてください。 しかし、スタンドアロンで開発する場合、\"safe mode\"ではいくつかの重要な機能が動作しない為、\"safe mode\"はおすすめしません。<a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>詳細情報</font></a>";


	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "MySQLのセキュリティコンソール & XAMPPのディレクトリ制御";
	$TEXT['mysql-rootsetup-head'] = "MYSQL 項目: \"ROOT\" パスワード";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "MySQLサーバが起動していないか、ファイアウォールでブロックされています! まず、この問題を確認してください。";
	$TEXT['mysql-rootsetup-passwdnotok'] = "新しいパスワードは同じものを入力します。両方のパスワードを入力してください。";
	$TEXT['mysql-rootsetup-passwdnull'] = "空のパスワードは設定できません。";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "成功: スーパーユーザ 'root' のパスワードが設定、または更新されました!
	要注意: \"root\"の新しいパスワードの初期化はMySQLの再起動が必要です。パスワードのデータは以下のファイルに格納されました:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "エラー: rootのパスワードは間違っています。MySQLは現在のrootパスワードでは、ログインを拒否します。";
	$TEXT['mysql-rootsetup-passwdold'] = "現在のパスワード:";
	$TEXT['mysql-rootsetup-passwd'] = "新しいパスワード:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "新しいパスワード(確認):";
	$TEXT['mysql-rootsetup-passwdchange'] = "パスワードを変更しました。";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdminを検出しました。:";

	$TEXT['xampp-setup-head'] = "XAMPPのディレクトリ制御 (.htaccess)";
	$TEXT['xampp-setup-user'] = "ユーザ:";
	$TEXT['xampp-setup-passwd'] = "パスワード:";
	$TEXT['xampp-setup-start'] = "安全なXAMPPディレクトリを作成してください。";
	$TEXT['xampp-setup-notok'] = "<br><br>エラー:ユーザ名の文字は、３文字以上１５文字未満で入力してください。特殊な文字や空白は認められていません。<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>rootのパスワードが変更されました。設定を有効にするために、MySQLを再起動してください。<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>成功: XAMPPのディレクトリ制御が設定されました!全ユーザのデータは、以下のファイルに格納されました:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>エラー: あなたのシステムでは、 \".htaccess\"と\"htpasswd.exe\"によるディレクトリ制御が有効ではありません。おそらく、PHPが\"Safe Mode\"で起動しています。 <br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "XAMPP Windows版へようこそ";

	$TEXT['start-subhead'] = "おめでとうございます:<br>システム上にXAMPPが正しくインストールされました！";

	$TEXT['start-text-newest'] = "";

	$TEXT['start-text1'] = "これで、Apacheを使い始めることができます。まず、左端のナビゲーションの≪ステータス≫から、すべて問題なく動作することを確認してください。";

	$TEXT['start-text2'] = "";

	$TEXT['start-text3'] = "";

	$TEXT['start-text4'] = "テスト証明書を使ったOpenSSLを<a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a>、もしくは<a href='https://localhost' target='_top'>https://localhost</a>がサポートしています。";

	$TEXT['start-text5'] = "これは、とても重要です! ヘルプやサポートに関してCarsten氏, Nemesis氏, KriS氏, Boppy氏,それにXAMPPの全てのユーザにとても感謝しています!";

	$TEXT['start-text6'] = "Good luck, Kay Vogelgesang, Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "オンライン・マニュアル";

	$TEXT['manuals-text1'] = "XAMPPは、いくつもの異なるソフトウェア・パッケージをひとまとめにします。 最も重要なソフトウェア・パッケージの標準的なドキュメントを掲載しました。";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/\">Apache 2 ドキュメント</a>
	<li><a href=\"http://www.php.net/manual/ja/\">PHP <b>リファレンス</b>ドキュメント</a>
	<li><a href=\"http://perldoc.perl.org/\">Perl ドキュメント</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">MySQL ドキュメント</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">FPDF Class ドキュメント</a>
	</ul>";

	$TEXT['manuals-text2'] = "チュートリアルのショートリストとApache Friendsドキュメントのページ：";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Apache Friends ドキュメント</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">PHP チュートリアル</a> by David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - 初心者向けインタラクティブ・チュートリアル</a> by Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Perl チュートリアル</a> by Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "グッドラック！ Have fun♪";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "XAMPP コンポーネント";

	$TEXT['components-text1'] = "XAMPPはたくさんのソフトウェアパッケージを組み合わせて構成されています。全てのソフトウェアパッケージを表示します。";

	$TEXT['components-text2'] = "これらのプログラムの開発者にとても感謝しています。";

	$TEXT['components-text3'] = "<b>\\xampp\licenses</b>ディレクトリで、これらのプログラムのライセンスについて確認することができます。";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "CDコレクション (PHP+MySQL+PDF Classの事例)";
	$TEXT['cds-head-fpdf'] = "CD コレクション (PHP+MySQL+FPDF Classの事例)";

	$TEXT['cds-text1'] = "シンプルなCDプログラム";

	$TEXT['cds-text2'] = "CDリスト<a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF ドキュメント</a>.";

	$TEXT['cds-error'] = "データベースを接続できません！<br>MySQLは稼動していますか？またはパスワードを変えませんでしたか？";
	$TEXT['cds-head1'] = "My CDs";
	$TEXT['cds-attrib1'] = "アーティスト";
	$TEXT['cds-attrib2'] = "タイトル";
	$TEXT['cds-attrib3'] = "年";
	$TEXT['cds-attrib4'] = "コマンド";
	$TEXT['cds-sure'] = "いいですか？";
	$TEXT['cds-head2'] = "CD追加";
	$TEXT['cds-button1'] = "CD削除";
	$TEXT['cds-button2'] = "CD追加";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "バイオリズム (PHP+GDの事例)";

	$TEXT['bio-by'] = "by";
	$TEXT['bio-ask'] = "誕生日を記入してください";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "日";
	$TEXT['bio-error2'] = "無効です";

	$TEXT['bio-birthday'] = "誕生日";
	$TEXT['bio-today'] = "今日";
	$TEXT['bio-intellectual'] = "知性的";
	$TEXT['bio-emotional'] = "感情的";
	$TEXT['bio-physical'] = "肉体的";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "インスタントアート (PHP+GD+FreeTypeの事例)";
	$TEXT['iart-text1'] = "フォント ≪AnkeCalligraph≫  by <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Example for PHP+MING)";
	$TEXT['flash-text1'] = "The MING project for win32 does not exist any longer and it is not complete.<br>Please read this: <a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - an SWF output library and PHP module</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "フラッシュアート (PHP+MINGの事例)";

	$TEXT['phonebook-text1'] = "簡単な電話帳スクリプトですが、最新のテクノロジーを駆使して実装されています：　SQLite、SQLデータベース、サーバは使用しません。";

	$TEXT['phonebook-error'] = "データベースを開けませんでした！";
	$TEXT['phonebook-head1'] = "マイ電話番号";
	$TEXT['phonebook-attrib1'] = "姓";
	$TEXT['phonebook-attrib2'] = "名";
	$TEXT['phonebook-attrib3'] = "電話番号";
	$TEXT['phonebook-attrib4'] = "コマンド";
	$TEXT['phonebook-sure'] = "いいですか?";
	$TEXT['phonebook-head2'] = "エントリ追加";
	$TEXT['phonebook-button1'] = "削除";
	$TEXT['phonebook-button2'] = "追加";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "XAMPPについて";

	$TEXT['about-subhead1'] = "アイディアと実現";

	$TEXT['about-subhead2'] = "デザイン";

	$TEXT['about-subhead3'] = "コラボレーション";

	$TEXT['about-subhead4'] = "コンタクト先";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "MercuryメールサーバによるSMTPとPOP3";
	$TEXT['mail-hinweise'] = "Mercuryを利用する際の重要なメモ!";
	$TEXT['mail-adress'] = "送信者:";
	$TEXT['mail-adressat'] = "受信者:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "件名:";
	$TEXT['mail-message'] = "メッセージ:";
	$TEXT['mail-sendnow'] = "メッセージを送信しています...";
	$TEXT['mail-sendok'] = "メッセージの送信が完了しました!";
	$TEXT['mail-sendnotok'] = "エラー!メッセージの送信が完了していません!";
	$TEXT['mail-help1'] = "Mercuryを利用する際のメモ:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercuryは起動時に外部ネットワークへの接続が必要です。;</li>
	<li>起動時、MercuryはプロバイダのDNSサーバを自動的に、DNSサーバとして定義します。;</li>
	<li>全ユーザ用ゲートウェイサーバ: TCP/IP経由でDNSサーバを設定してください(例： NICのIPアドレス 198.41.0.4);</li>
	<li>Mercuryの設定はMERCURY.INIを呼び出しています。;</li>
	<li>メッセージ送信のテストはpostmaster@localhostかadmin@localhostに送信して、次のフォルダにメッセージが格納されているか確認してください: xampp.../mailserver/MAIL/postmaster または、(...)/admin;</li>
	<li>テストユーザとして\"newuser\" (newuser@localhost) パスワード = wampp が存在します。;</li>
	<li>スパムや禁止用語は、Mercuryで全体的に許可されていません!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "FileZilla FTP Server";
	$TEXT['filezilla-install'] = "ApacheはFTPサーバでは<U>ありません</U>... しかしFileZillaはFTPサーバです。次を参照して検討してください。";
	$TEXT['filezilla-install2'] = "XAMPPのディレクトリに移動して、セットアップのために\"filezilla_setup.bat\"を実行してください。 注目: For Windows NT, 2000 と XP Professionalでは、FileZillaはサービスとしてインストールする必要があります。";
	$TEXT['filezilla-install3'] = "\"FileZilla FTP\"を設定します。 設定のために,FileZillaのインターフェイスである\"FileZilla Server Interface.exe\"を使ってください。次は二つのユーザについての例です:<br><br>
	A: デフォルトのユーザは\"newuser\", パスワードは\"wampp\"です。ホームディレクトリはxampp\htdocsです。<br>
	B: 匿名ユーザは\"anonymous\", パスワードはありません。ホームディレクトリはxampp\anonymousです。<br><br>
	デフォルトではループバックである127.0.0.1が指定されています。";
	$TEXT['filezilla-install4'] = "FTPサーバは\"FileZillaFTP_stop.bat\"で停止してください。FileZillaのサービスは、\"FileZillaServer.exe\"を直接使ってください。そして、あなたは全ての起動オプションを設定できます。";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "PEARを使ったExcelファイルのエクスポート (PHP)";
	$TEXT['pear-text'] = "簡単な<a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">マニュアル</A>が<a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (ドイツ語のみ)のBjn Schotte氏から提供されています。";
	$TEXT['pear-cell'] = "セルの値";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Graph Library for PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - そのほかのデータベースアクセス(PHP)";
	$TEXT['ADOdb-text'] = "ADOdbはActive Data Objectsデータベース用にできています。私たちは現在、MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite,そしてgeneric ODBCをサポートしています。 Sybase, Informix, FrontBase, PostgreSQLのドライバーはコミュニティーの貢献で作成されました。あなたは\(mini)xampp\php\pear\adodbで見ることができます。";
	$TEXT['ADOdb-example'] = "例:";
	$TEXT['ADOdb-dbserver'] = "データベースサーバ (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "データベースサーバのホスト (名前かIPアドレス)";
	$TEXT['ADOdb-user'] = "ユーザ名 ";
	$TEXT['ADOdb-password'] = "パスワード";
	$TEXT['ADOdb-database'] = "データベースサーバ上の現在のデータベース";
	$TEXT['ADOdb-table'] = "データベース上の選択されたテーブル";
	$TEXT['ADOdb-nottable'] = "<p><b>テーブルが見つかりません!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>データベースサーバ用のドライバが見つからない、もしくはドライバがODBC,ADOもしくはOLEDBドライバです!</b>";


	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "パッケージ";
	$TEXT['info-pages'] = "ページ";
	$TEXT['info-extension'] = "拡張";
	$TEXT['info-module'] = "Apache module";
	$TEXT['info-description'] = "説明";
	$TEXT['info-signature'] = "署名";
	$TEXT['info-docdir'] = "Document root";
	$TEXT['info-port'] = "デフォルトポート";
	$TEXT['info-service'] = "サービス";
	$TEXT['info-examples'] = "例";
	$TEXT['info-conf'] = "設定ファイル";
	$TEXT['info-requires'] = "必須";
	$TEXT['info-alternative'] = "選択";
	$TEXT['info-tomcatwarn'] = "警告! Tomcatはポート8080以外で起動しています。";
	$TEXT['info-tomcatok'] = "OK! Tomcatはポート8080で起動しました。";
	$TEXT['info-tryjava'] = "Apache MOD_JKとjava(JSP)の例。";
	$TEXT['info-nococoon'] = "警告! Tomcatはポート8080で起動していません。インストールできません。
	\"Cocoon\"はTomcatなしで起動しています!";
	$TEXT['info-okcocoon'] = "Ok!Tomcatは正常に起動しています。インストール作業はもう少しで完了します。 \"Cocoon\"のインストールはここをクリックしてください...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 for XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>現在このXAMPPには";
	$TEXT['switch-whatis'] = "<b>PHP switchで何か作れますか?</b><br>apachefriendsのPHP Switchは XAMPPのPHPのバージョン４から５に切り替えたり、戻したりします。あなたはPHP4かPHP5でスクリプトのテストをする事ができます。<p>";
	$TEXT['switch-find'] = "<b>PHP Switchはどこにありますか?</b><br>XAMPPのPHP Switchは\"php-switch.php\"という名前のPHPファイル（XAMPPインストールフォルダ）で実行できます。実行のためには、このファイルを使わなければなりません: ";
	$TEXT['switch-care'] = "<b>難しくありませんか?</b><br>PHP Switchは 次の場合PHPのバージョンが変更できません。a) Apache HTTPD が起動している。 または/かつ b) インストールフォルダの\".phpversion\"ファイルが空白、またはバグがある。 \".phpversion\"には, \"4\"か\"5\"のようにXAMPPの現在のバージョンのが書かれています。Apache HTTPDを\"shutdown\"してから\"php-switch.bat\"を実行してください。<p>";
	$TEXT['switch-where4'] = "<b>どこに(古い)設定ファイルがありますか?</b><br><br>PHP 4用:<br>";
	$TEXT['switch-where5'] = "<br><br>PHP 5用:<br>";
	$TEXT['switch-make1'] = "<b>設定ファイルの何が変更されますか?</b><br><br>There lives! For PHP4 or PHP5 in the<br>";
	$TEXT['switch-make2'] = "<br><br> .. PHP4用は安全です ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. PHP5用は安全です ...<br>";
	$TEXT['switch-make4'] = "<br><br>そして、これらのファイルはPHP switchingで戻すことができます。<p>";
	$TEXT['switch-not'] = "<b>PHPには問題ありませんが\"切り替え\"できません。</b><br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoonは次の、http://localhost/cocoon/にあります。";
	$TEXT['path-cocoon'] = "そして、次のフォルダに集めてください。: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "今のリリースの現在のゲスト: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "とてもよいHTMLオンラインエディタで、JavaScriptで実装されています。IEに最適化されています。しかし、MozillaFireFoxでは機能しません。";
	$TEXT['guest1-text2'] = "FCKeditor ホームページ: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>。 ";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">FCKeditorで作ったテストページ</A>";
	
	// ---------------------------------------------------------------------
	// NAVI SPECIALS SECTION
	// ---------------------------------------------------------------------
	
	$TEXT['navi-specials'] = "Specials";
	
	// ---------------------------------------------------------------------
	// PS AND PARADOX EXAMPLE
	// ---------------------------------------------------------------------

  $TEXT['navi-ps'] = "PHP PostScript";
	$TEXT['ps-head'] = "PostScript Module Example";
	$TEXT['ps-text1'] = "PostScript Module ｻphp_psｫ by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['ps-text2'] = "Tip: To convert PS files to PDF files on win32, you can use <a href=\"http://www.shbox.de/\" target=\"_new\">FreePDF</a> with <a href=\"http://www.ghostscript.com/awki/\" target=\"_new\">GhostScript</a>.";
	
	$TEXT['navi-paradox'] = "PHP Paradox";
	$TEXT['paradox-head'] = "Paradox Module Example";
	$TEXT['paradox-text1'] = "Paradox Module ｻphp_paradoxｫ by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['paradox-text2'] = "<h2>Reading and writing a paradox database</h2>";
	$TEXT['paradox-text3'] = "More examples you can find in the directory ";
	$TEXT['paradox-text4'] = "Further information to Paradox databases in <a href=\"http://en.wikipedia.org/wiki/Paradox\" target=\"_new\">WikiPedia</a>.";
?>
