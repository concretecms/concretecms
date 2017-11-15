<?php
namespace Concrete\Core\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class KeywordsField extends AbstractField
{
    /**
     * Initialize the instance.
     *
     * @param string|null $keywords the keywords to be searched
     */
    public function __construct($keywords = null)
    {
        if ($keywords) {
            $this->data['keywords'] = $keywords;
        }
    }

    /**
     * The list of all the request variables.
     *
     * @var array
     */
    protected $requestVariables = [
        'keywords',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getKey()
     */
    public function getKey()
    {
        return 'keywords';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Keywords');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::filterList()
     */
    public function filterList(ItemList $list)
    {
        $keywords = $this->data['keywords'];
        $list->filterByKeywords($keywords);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\AbstractField::renderSearchField()
     */
    public function renderSearchField()
    {
        $form = \Core::make('helper/form');

        return $form->text('keywords', isset($this->data['keywords']) ? $this->data['keywords'] : '');
    }
}
