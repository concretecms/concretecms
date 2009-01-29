<html>
<head><title>XAMPP</title>
<link href="xampp.css" rel="stylesheet" type="text/css">
</head>

<body class=white bgcolor=#ffffff>
<center>
<img src=img/blank.gif height=180 width=1><br>
<!--
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
     codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
     ID=anim WIDTH=400 HEIGHT=100><PARAM NAME=movie VALUE="splash-swf.php"> <PARAM NAME=loop VALUE=false> <PARAM NAME=quality VALUE=high> <EMBED src="splash-swf.php" loop=false quality=high WIDTH=400 HEIGHT=100 TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></EMBED></OBJECT>
-->
<img src="img/xampp-logo.jpg">
<p>
<?php
	include("lang/languages.php");
	$i=0;
	while (list($key, $value) = each($languages))
	{
		if($i++)echo ' / ';
		echo '<a href="/xampp/lang.php?'.$key.'">'.$value.'</a>';
	}
?>
<p>
</center>
</body>
</html>
