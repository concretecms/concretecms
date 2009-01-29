<?
	if(file_get_contents("lang.tmp")=="")
	{
		header("Location: splash.php");	
		exit();
	}
?>
<html>
<head>
<meta name="author" content="Kai Oswald Seidler">
<meta http-equiv="cache-control" content="no-cache">
<?include("lang/".file_get_contents("lang.tmp").".php"); ?>
<title>XAMPP <?include('.version');?></title>

<frameset rows="74,*" marginwidth="0" marginheight="0" frameborder="0" border="0" borderwidth="0">
    <frame name="head" src="head.php" scrolling=no>
<frameset cols="150,*" marginwidth="0" marginheight="0" frameborder="0" border="0" borderwidth="0">
    <frame name="navi" src="navi.php" scrolling=no>
    <frame name="content" src="start.php" marginwidth=20>
</frameset>
</frameset>
</head>
<body bgcolor=#ffffff>
</body>
</html>
