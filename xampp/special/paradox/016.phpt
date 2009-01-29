--TEST--
Update a paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px016.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "L"), array("col2", "A", 10));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=-3; $i<=3; $i++) {
	$pxdoc->insert_record(array($i<0, "i=".$i));
}
$pxdoc->update_record(array("wrong", "updated"), 1);
print_r($pxdoc->retrieve_record(1));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px016.db");
?>
--EXPECT--
Array
(
    [col1] => 
    [col2] => updated
)

