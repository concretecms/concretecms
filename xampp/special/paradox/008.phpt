--TEST--
Reading a simple paradox database with a graphic field
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/picture.db", "r");
px_open_fp($pxdoc, $fp);
px_set_blob_file($pxdoc, $dirname."/picture.mb");
for($i=0; $i<px_numrecords($pxdoc); $i++) {
	$data = px_get_record($pxdoc, $i, PX_KEYTOLOWER);
	echo md5($data["picture"])."\n";
}
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
--EXPECT--
1187c3b0365cdba557503607bde26a04
