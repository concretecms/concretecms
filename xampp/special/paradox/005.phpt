--TEST--
Set and read back parameters and values
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
@px_set_parameter($pxdoc, "tablename", "value1");
echo px_get_parameter($pxdoc, "tablename");
@px_set_value($pxdoc, "numprimkeys", 1.0);
echo px_get_value($pxdoc, "numprimkeys");
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
--EXPECT--
value10
