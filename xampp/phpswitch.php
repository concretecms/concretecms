<?php
	include "langsettings.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		<?php
			global $switchphp;
			$curdir = getcwd();
			list($partwampp, $directorwampp) = spliti ('\\\htdocs\\\xampp', $curdir);
			$switchphp = $partwampp."\\php-switch.bat";
			$switch = ereg_replace("\\\\", "/", $switchphp);
			$realswitch = "file:///".$switch;
			$php4ini = $partwampp."\\php\\php4\\php4.ini";
			$httpd4conf = $partwampp."\\php\\php4\\httpd4.conf";
			$php5ini = $partwampp."\\php\\php5.ini";
			$httpd5conf = $partwampp."\\php\\httpd5.conf";
			$httpdconf = $partwampp."\\apache\\bin\\httpd.conf";
			$phpini = $partwampp."\\apache\\bin\\php.ini";
			$version = phpversion();
		?>

		&nbsp;<p>

		<table width="90%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><h1><?php echo $TEXT['switch-head']; ?></h1></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><?php echo $TEXT['switch-phpversion']; ?><?php echo "PHP $version</b></i>"; ?></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><?php echo $TEXT['switch-whatis']; ?></td>
				<td width="6%">&nbsp;</td>
				</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><?php echo $TEXT['switch-find']; ?><?php echo "<br><br>=> $partwampp\\<b>php-switch.bat</b> <=<p>"; ?></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><img src="img/phpswitch4.gif" alt="" width="669" height="331" border="0"></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*">&nbsp;</td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><?php echo $TEXT['switch-care']; ?></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*">
					<?php echo $TEXT['switch-where4']; ?>
					<?php echo "$php4ini<br>$httpd4conf"; ?>
					<?php echo $TEXT['switch-where5']; ?>
					<?php echo "$php5ini<br>$httpd5conf<p>"; ?>
				</td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*">
					<?php echo $TEXT['switch-make1']; ?>
					<?php echo "<i>$phpini<br>$httpdconf</i>"; ?>
					<?php echo $TEXT['switch-make2']; ?>
					<?php echo "$php4ini<br>$httpd4conf"; ?>
					<?php echo $TEXT['switch-make3']; ?>
					<?php echo "$php5ini<br>$httpd5conf"; ?>
					<?php echo $TEXT['switch-make4']; ?>
				</td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><?php echo $TEXT['switch-not']; ?></td>
				<td width="6%">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%">&nbsp;</td>
				<td width="*"><p><br>© ApacheFriends 2002-2006<p></td>
				<td width="6%">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>
