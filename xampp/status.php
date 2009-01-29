<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		<?php include "lang/".file_get_contents("lang.tmp").".php"; ?>
		&nbsp;<p>

		<h1><?php echo $TEXT['status-head']; ?></h1>

		<?php echo $TEXT['status-text1']; ?><p>

		<?php
			$curdir = getcwd();
		 list($partwampp, $directorwampp) = spliti ('\\\htdocs\\\xampp', $curdir);
			$i = 0;
			$up = "";
			
			if ($_SERVER['SERVER_PORT'] == 443) {
				$prot = "https";
			} else {
				$prot = "http";
			}

			if (isset($_SERVER['PHP_AUTH_USER'])) {
				$up = "$_SERVER[PHP_AUTH_USER]:$_SERVER[PHP_AUTH_PW]@";
			}
			if ('localhost' == strtolower($_SERVER['SERVER_NAME'])) { // Fix by Wiedmann
				$server = '127.0.0.1';
			} else {
				$server = $_SERVER['SERVER_NAME'];
			}
			$b = "$prot://$up".$server;
			if ($_SERVER['SERVER_PORT'] != 80) {
				$b = "$prot://$up".$server.":".$_SERVER['SERVER_PORT']; // Fix by Wiedmann
			}

			function line($text, $status, $link = "") {
				global $i, $TEXT;

				if ($i > 0) {
					echo "<tr valign='bottom'>";
					echo "<td bgcolor='#ffffff' height='1' style='background-image:url(img/strichel.gif)' colspan='5'></td>";
					echo "</tr>";
				}
				echo "<tr bgcolor='#ffffff'>";
				echo "<td bgcolor='#ffffff'><img src='img/blank.gif' alt='' width='1' height='20'></td>";
				echo "<td class='tabval'>";
				echo "$text";
				echo "</td>";
				if (trim($status) == "OK") {
					echo "<td><span class='green'>&nbsp;".$TEXT['status-ok']."&nbsp;</span></td>";
					echo "<td></td>";
					$i++;
				} else {
					echo "<td><span class='red'>&nbsp;".$TEXT['status-nok']."&nbsp;</span></td>";
					if ($link == "") {
						echo "<td></td>";
					} else {
						echo "<td><a target='faq' href='$link'><span style='font-size: 10pt'>".$TEXT['status-lookfaq']."</span></a></td>";
					}
				}
				echo "<td bgcolor='#ffffff'></td>";
				echo "</tr>";
				$i++;
			}

			$a = @file("$b/xampp/php.php");
			$php = $a[0];
			$a = @file("$b/xampp/mysql.php");
			$mysql = $a[0];
			$a = @file("$b/cgi-bin/cgi.cgi");
			$cgi = $a[0];
			$a = @file("$b/xampp/ssi.shtml");
			$ssi = $a[0];

			$host = "127.0.0.1";
			$timeout= 1;

			if (($handle = @fsockopen($host, 443, $errno, $errstr, $timeout)) == false) {
				$ssl="NOK";
			} else {
				$ssl="OK";
			}
			@fclose($handle);

			echo "<table border='0' cellpadding='0' cellspacing='0'>";
			echo "<tr valign='top'>";
			echo "<td bgcolor='#fb7922' valign='top'><img src='img/blank.gif' alt='' width='10' height='0'></td>";
			echo "<td bgcolor='#fb7922' class='tabhead'><img src='img/blank.gif' alt='' width='250' height='6'><br>".$TEXT['status-tab1']."</td>";
			echo "<td bgcolor='#fb7922' class='tabhead'><img src='img/blank.gif' alt='' width='100' height='6'><br>".$TEXT['status-tab2']."</td>";
			echo "<td bgcolor='#fb7922' class='tabhead'><img src='img/blank.gif' alt='' width='100' height='6'><br>".$TEXT['status-tab3']."</td>";
			echo "<td bgcolor='#fb7922' valign='top'><br><img src='img/blank.gif' alt='' width='1' height='10'></td>";
			echo "</tr>";
			line($TEXT['status-mysql'], $mysql);
			line($TEXT['status-php'], $php);
			line($TEXT['status-ssl'], $ssl);
			line($TEXT['status-cgi'], $cgi);
			line($TEXT['status-ssi'], $ssi);
		

			if ((file_exists("$partwampp\htdocs\python\\xa.py")) && (file_exists("$partwampp\apache\conf\python.conf"))) {
				$a = @file("$b/python/xa.py");
				$python = $a[0];
				line($TEXT['status-python'], $python);
			}

			if ((file_exists("$partwampp\htdocs\modperl\perl.pl")) && (file_exists("$partwampp\apache\conf\extra\perl.conf"))) {
				$a = @file("$b/modperl/perl.pl");
				$perl = $a[0];
				line($TEXT['status-perl'], $perl);
			}

			if (file_exists("$partwampp\MercuryMail\MERCURY.INI")) {
				if (($handle = @fsockopen($host, 25, $errno, $errstr, $timeout)) == false) {
					$smtp = "NOK";
				} else {
					$smtp = "OK";
				}
				@fclose($handle);
				line($TEXT['status-smtp'], $smtp);
			}

			if (file_exists("$partwampp\FileZillaFTP\FzGSS.dll")) {
				if (($handle = @fsockopen($host, 21, $errno, $errstr, $timeout)) == false) {
					$ftp = "NOK";
				} else {
					$ftp = "OK";
				}
				@fclose($handle);
				line($TEXT['status-ftp'], $ftp);
			}

			if (file_exists("$partwampp\\tomcat\conf\\server.xml")) {
				if (($handle = @fsockopen($host, 8080, $errno, $errstr, $timeout)) == false) {
					$tomcat = "NOK";
				} else {
					$tomcat = "OK";
				}
				@fclose($handle);
				line($TEXT['status-tomcat'], $tomcat);
			}

			if (file_exists("$partwampp\PosadisDNS\posadis.exe")) {
				if (($handle = @fsockopen($host, 53, $errno, $errstr, $timeout)) == false) {
					$named = "NOK";
				} else {
					$named = "OK";
				}
				@fclose($handle);
				line($TEXT['status-named'], $named);
			}

			echo "<tr valign='bottom'>";
			echo "<td bgcolor='#fb7922'></td>";
			echo "<td bgcolor='#fb7922' colspan='3'><img src='img/blank.gif' alt='' width='1' height='8'></td>";
			echo "<td bgcolor='#fb7922'></td>";
			echo "</tr>";
			echo "</table>";

			echo "<p>";
		?>

		<?php echo $TEXT['status-text2']; ?><p>

	</body>
</html>
