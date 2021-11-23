<?php

namespace Concrete\Core\Page;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Utility\Service\Validation\Strings;

class HandleGenerator
{
    /** @var Text */
    protected $text;
    /** @var Strings */
    protected $strings;
    /** @var Repository */
    protected $config;

    /**
     * HandleGenerator constructor.
     * @param Text $text
     * @param Strings $strings
     * @param Repository $config
     */
    public function __construct(Text $text, Strings $strings, Repository $config)
    {
        $this->text = $text;
        $this->strings = $strings;
        $this->config = $config;
    }

    /**
     * Generate collection handle from requested data.
     *
     * @param Page $page Generate handle for this page.
     * @param array $data Requested data. It may contain cName, cHandle
     * @return string
     */
    public function generate(Page $page, array $data)
    {
        $cName = $data['cName'] ?? $page->getCollectionName();
        $isHomePage = $page->isHomePage();
        if (!isset($data['cHandle']) && ($page->getCollectionHandle() !== '')) {
            // No passed cHandle, and there is an existing handle.
            $cHandle = $page->getCollectionHandle();
        } elseif (!$isHomePage && (!isset($data['cHandle']) || !$this->strings->notempty($data['cHandle']))) {
            // no passed cHandle, and no existing handle
            // make the handle out of the title
            $cHandle = $this->text->urlify($cName);
            $cHandle = str_replace('-', $this->config->get('concrete.seo.page_path_separator'), $cHandle);
        } else {
            // passed cHandle, no existing handle
            $cHandle = isset($data['cHandle']) ? $this->text->slugSafeString($data['cHandle']) : ''; // we DON'T run urlify
            $cHandle = str_replace('-', $this->config->get('concrete.seo.page_path_separator'), $cHandle);
        }

        return $cHandle;
    }
}