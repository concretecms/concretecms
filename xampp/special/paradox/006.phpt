--TEST--
Reading a simple paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/simpletest.db", "r");
px_open_fp($pxdoc, $fp);
for($i=0; $i<px_numrecords($pxdoc); $i++) {
	print_r(px_get_record($pxdoc, $i, PX_KEYTOLOWER));
	print_r(px_get_record($pxdoc, $i, PX_KEYTOUPPER));
}
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
--EXPECT--
Array
(
    [col1] => 2
)
Array
(
    [COL1] => 2
)

