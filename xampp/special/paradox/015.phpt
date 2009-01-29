--TEST--
Delete Record from a paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px015.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "S"), array("col2", "I"));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=-3; $i<=3; $i++) {
	$pxdoc->insert_record(array($i, $i));
}
$pxdoc->delete_record(1);
print_r($pxdoc->retrieve_record(1));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px015.db");
?>
--EXPECT--
Array
(
    [col1] => -1
    [col2] => -1
)

