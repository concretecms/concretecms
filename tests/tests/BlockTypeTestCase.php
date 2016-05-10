<?php

use \Concrete\Core\Block\View\BlockView;

abstract class BlockTypeTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('BlockTypes', 'Blocks', 'Pages', 'CollectionVersionBlocks', 'Collections', 'PagePaths');

    public function testInstall()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        if ($this->assertTrue($bt->getBlockTypeHandle() == $this->btHandle) && $btx->getBlockTypeID() == 1);
    }

    public function testSave()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $class = $btx->getBlockTypeClass();
        $btc = new $class();
        $bID = 1;
        foreach ($this->requestData as $type => $requestData) {
            $nb = $bt->add($requestData);
            $data = $this->expectedRecordData[$type];
            $db = Loader::db();
            $r = $db->GetRow('select * from `' . $btc->getBlockTypeDatabaseTable() . '` where bID = ?', array($bID));
            foreach ($data as $key => $value) {
                $this->assertTrue($r[$key] == $value, 'Key `' . $key . '` did not equal expected value `' . $value . '` instead equalled `' . $r[$key] . '` (type `' . $type . '`)');
            }
            ++$bID;

            ob_start();
            $bv = new BlockView($nb);
            $bv->render('view');
            $contents = ob_get_contents();
            ob_end_clean();

            $contents = trim($contents);

            if (isset($this->expectedOutput[$type])) {
                $this->assertTrue($this->expectedOutput[$type] == $contents, 'Output `' . $contents . '` did not equal expected output `' . $this->expectedOutput[$type] . '` (type `' . $type . '`)');
            }
        }
    }
}
