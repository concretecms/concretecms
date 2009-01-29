<?
	echo '&nbsp;<p>&nbsp;<p>';
	if($_REQUEST['showcode']!=1)
	{
		echo '<a href="'.$_SERVER['PHP_SELF'].'?showcode=1">'.$TEXT['global-showcode'].'</a>';
	}
	else
	{
		if($file=="")$file=$_SERVER['PHP_SELF'];
		$f=htmlentities(file_get_contents(basename($file)));
		echo "<h2>".$TEXT['global-sourcecode']."</h2>";
		echo "<form><textarea cols=100 rows=10>";
		echo $f;
		echo "</textarea></form>";
		echo "&nbsp;<p>";
		echo "&nbsp;<p>";
	}
?>
