<?php
use Concrete\Core\Block\BlockController;

/**
 * Class TestFormExternalFormBlockController
 * @todo Namespace this class, it currently isn't because ExternalFormBlockController is loading in a non-standard way.
 */
class TestFormExternalFormBlockController extends BlockController
{

    public function action_test_search()
    {
        $this->set('response', t('Thanks!'));
        return true;
    }

}
