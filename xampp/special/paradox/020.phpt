--TEST--
Insert Auto increment fields
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px020.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "+"));
$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=-3; $i<=3; $i++) {
	$pxdoc->insert_record(array(NULL));
}
print_r($pxdoc->retrieve_record(5));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px020.db");
?>
--EXPECT--
Array
(
    [col1] => 6
)

