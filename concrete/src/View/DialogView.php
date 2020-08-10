<?php
namespace Concrete\Core\View;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\Output\JavascriptFormatter;
use View as ConcreteView;
use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Asset\JavascriptInlineAsset;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Application;

class DialogView extends ConcreteView
{
    protected function onBeforeGetContents()
    {
        $this->markHeaderAssetPosition();
    }

    public function getViewTemplateFile()
    {
        return $this->template;
    }

    protected function loadViewThemeObject()
    {
        return null;
    }


    public function outputAssetIntoView($item)
    {
        if ($item instanceof Asset) {
            $formatter = new JavascriptFormatter();
            print $formatter->output($item);
        } else {
            print $item . "\n";
        }
    }

    public function getScopeItems()
    {
        $app = Application::getFacadeApplication();
        $items = parent::getScopeItems();
        $u = $app->make(User::class);
        $items['u'] = $u;

        return $items;
    }

    protected function getAssetsToOutput()
    {
        $ouput = parent::getAssetsToOutput();
        $return = array();
        foreach ($ouput as $position => $assets) {
            foreach ($assets as $asset) {
                if ($asset instanceof Asset) {
                    $asset->setAssetPosition(Asset::ASSET_POSITION_HEADER);
                }
                $return[$position][] = $asset;
            }
        }

        return $return;
    }

}
