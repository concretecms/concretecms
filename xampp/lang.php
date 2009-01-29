<?
	$fp=fopen("lang.tmp","w");
	fwrite($fp,basename($_SERVER['QUERY_STRING']));
	fclose($fp);
	header("Location: index.php");
?>