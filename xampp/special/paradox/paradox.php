<?php
	      $lang=@file_get_contents("../../lang.tmp");
        @include("../../lang/languages.php");
        @include("../../lang/en.php");
        @include("../../lang/$lang.php");
        if($lang=="zh")
        {
                header("Content-Type: text/html; charset=gb2312");
        }
        else if($lang=="jp")
        {
                header("Content-Type: text/html; charset=shift-jis");
        }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="../../xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		&nbsp;<p>
		<h1><?php echo $TEXT['paradox-head']; ?></h1>

			 <p class=small>
			<?=$TEXT['paradox-text1']?>
		
			<p>
			<?=$TEXT['paradox-text2']?>
<?php
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/simpletest.db", "r");
px_open_fp($pxdoc, $fp);
$info = px_get_info($pxdoc);

if ( getenv('REMOTE_ADDR') == "127.0.0.1") {
         echo "<b>Database: ".$dirname."/simpletest.db</b><br /><br />";
         }
echo "Database info: <pre>";
print_r($info);
echo "</pre><br />Number of fields: ".px_numfields($pxdoc)."<br />\n";
echo "Number of records: ".px_numrecords($pxdoc)."<br />\n";
echo "Database schema:<br /><pre>\n";
print_r(px_get_schema($pxdoc));
echo "</pre>\n";
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
<p>
<?php
if ( getenv('REMOTE_ADDR') == "127.0.0.1") {
         echo $TEXT['paradox-text3'].$dirname.". ";
         }
?>
			<?=$TEXT['paradox-text4']?>
		
	    <?php include("../../showcode.php"); ?>
	</body>
</html>
