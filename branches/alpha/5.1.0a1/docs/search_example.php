<?

Loader::library('3rdparty/Zend/Search/Lucene');
try {
	$index = Zend_Search_Lucene::open(DIR_FILES_CACHE . '/lucene.cache');
} catch(Exception $e) {
	$index = Zend_Search_Lucene::create(DIR_FILES_CACHE . '/lucene.cache');
}

Zend_Search_Lucene_Analysis_Analyzer::setDefault(
    new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num());


/*$id = 5;
$_id = md5($id);
$doc = new Zend_Search_Lucene_Document();
$doc->addField(Zend_Search_Lucene_Field::Keyword('page_id', $_id));
$doc->addField(Zend_Search_Lucene_Field::Text('url', BASE_URL . DIR_REL . '/index.php?cID=' . $id));
$doc->addField(Zend_Search_Lucene_Field::Text('description', 'der!!!' . $id));
$index->addDocument($doc);

print $index->numDocs();
 

$term = new Zend_Search_Lucene_Index_Term($id, 'page_id');
$pageID = new Zend_Search_Lucene_Search_Query_Term($term);
$page = $index->find($pageID);
print $page[0]->url;
*/

$term = new Zend_Search_Lucene_Index_Term(md5(5), 'page_id');
$pageID = new Zend_Search_Lucene_Search_Query_Term($term);
$page = $index->find($pageID);
print $page[0]->url;
