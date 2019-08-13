<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;

/**
 * @since 8.0.0
 */
interface SpecifiableHomePageRoutineInterface
{

    function setHomePage($page);

}
