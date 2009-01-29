--TEST--
Reading a simple paradox database with a memo field
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/memo.db", "r");
px_open_fp($pxdoc, $fp);
px_set_blob_file($pxdoc, $dirname."/memo.mb");
for($i=0; $i<px_numrecords($pxdoc); $i++) {
	$data = px_get_record($pxdoc, $i, PX_KEYTOLOWER);
	echo $data["memo"]."\n";
}
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
--EXPECT--
This is a memo with less than 40 chars.
This memo is hopefuly large enough to go into the .MB file. This is only the case if it does not fit into 40 bytes.
