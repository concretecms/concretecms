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
		&nbsp;
		<form method="post" action="getexcel.php">
			<table width="500" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480"><h1><?php echo $TEXT['pear-head']; ?></h1></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align=left width="20">&nbsp;</td><td align=left width="480"><?=$TEXT['pear-text']?></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align=left width="20">&nbsp;</td>
					<td align=left width="480"><?php echo $TEXT['pear-cell']; ?></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480"><input type="text" name="value" size="40"></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480"><input type="submit"> * <input type="reset"></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">
						<?php
							if (isset($_GET['source']) && ($_GET['source'] == "in")) {
								include "code.php";
								$beispiel = "excel.php";
								pagecode($beispiel);
							} else {
								echo "<p><br><br><h2><u><a href=\"$_SERVER[PHP_SELF]?source=in\">".$TEXT['srccode-in']."</a></u></h2>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td align="left" width="480">&nbsp;</td>
				</tr>
			</table>
		</form>
	</body>
</html>
