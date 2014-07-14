<?

class PageStyleTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('PageStyleSets');

    public function testPageStyles()
    {
        $ps = new \Concrete\Core\Page\Style\Set();
        $ps->setBackgroundColor('#ffffff');
        $ps->save();

        $psx = \Concrete\Core\Page\Style\Set::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Page\Style\Set', $psx);
        $this->assertEquals(1, $psx->getID());
        $this->assertEquals('#ffffff', $psx->getBackgroundColor());
    }

}
