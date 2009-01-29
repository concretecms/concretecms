--TEST--
Converting a timestamp into a string
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$pxdoc = px_new();
echo px_timestamp2string($pxdoc, 5000000000000.0, "H:i:s d.m.Y");
echo "\n";
echo px_timestamp2string($pxdoc, 80000000000000.0, "H:i:s d.m.Y");
px_delete($pxdoc);
?>
--EXPECT--
08:53:20 11.06.0159
22:13:20 05.02.2536
