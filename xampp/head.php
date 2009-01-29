<html>
<head>
<meta name="author" content="Kai Oswald Seidler">
<link href="xampp.css" rel="stylesheet" type="text/css">
</head>

<body style="background: #ffffff; margin-top: 8px; margin-left: 8px;">

<table cellpadding=0 cellspacing=0 border=0>
<tr>
<td><img src="img/blank.gif" width=89 height=1></td>
<td><img src="img/xampp-logo-new.gif"></td>
<td><img src="img/blank.gif" width=5 height=1></td>
<? if(file_get_contents("lang.tmp")=="de") { ?>
<td><img src="img/head-fuer.gif"></td>
<? } else { ?>
<td><img src="img/head-for.gif"></td>
<? } ?>
<td><img src="img/blank.gif" width=10 height=1></td>
<td><img src="img/head-windows.gif"></td>
<td width="90%" id="langsel">
<div>
<?php
	include("lang/languages.php");
	$l=file_get_contents("lang.tmp");
        $i=0;
        while (list($key, $value) = each($languages))
        {
                if($i++)echo ' / ';
		$s="";
		if($l==$key)$s='style="font-weight: bold;"';
                echo '<a '.$s.' target="_parent" href="/xampp/lang.php?'.$key.'">'.$value.'</a>';
        }
?>
</div>
</td>
</tr>
</table>

</body>
</html>
