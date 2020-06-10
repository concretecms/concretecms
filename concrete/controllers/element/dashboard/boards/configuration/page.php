<?php
namespace Concrete\Controller\Element\Dashboard\Boards\Configuration;

use Concrete\Core\Board\DataSource\DataSourceElementController;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Page\Search\Field\Manager as SearchFieldManager;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Page extends DataSourceElementController
{
    
    public function getElement()
    {
        return 'dashboard/boards/configuration/page';
    }

    public function view()
    {
        $manager = $this->app->make(SearchFieldManager::class);
        $resolver = $this->app->make(ResolverManagerInterface::class);
        $addFieldAction = "#";
        $fieldSelector = new SearchFieldSelector($manager, $addFieldAction);
        $fieldSelector->setIncludeJavaScript(true);
        $fieldSelector->setAddFieldAction(
            $resolver->resolve(['/ccm/system/dialogs/page/advanced_search/add_field'])
        );
        if ($this->configuredDataSource) {
            $configuration = $this->configuredDataSource->getConfiguration();
            if ($configuration) {
                /**
                 * @var $configuration PageConfiguration
                 */
                $query = $configuration->getQuery();
                if ($query) {
                    $fieldSelector->setQuery($query);
                }
            }
        }
        $this->set('fieldSelector', $fieldSelector);
    }

}
