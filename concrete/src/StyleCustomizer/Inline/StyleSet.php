<?php
namespace Concrete\Core\StyleCustomizer\Inline;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Database;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Mapping as ORM;

class StyleSet
{
    /**
     * @param $issID
     *
     * @return \Concrete\Core\StyleCustomizer\Set
     */
    public static function getByID($issID)
    {
        $em = \ORM::entityManager();
        return $em->find('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $issID);
    }

    public static function import(\SimpleXMLElement $node)
    {
        $o = new \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet();
        $o->setBackgroundColor((string) $node->backgroundColor);
        $filename = (string) $node->backgroundImage;
        if ($filename) {
            $inspector = \Core::make('import/value_inspector');
            $result = $inspector->inspect($filename);
            $fID = $result->getReplacedValue();
            if ($fID) {
                $o->setBackgroundImageFileID($fID);
            }
        }

        $o->setBackgroundRepeat((string) $node->backgroundRepeat);
        $o->setBackgroundSize((string) $node->backgroundSize);
        $o->setBackgroundPosition((string) $node->backgroundPosition);
        $o->setBorderWidth((string) $node->borderWidth);
        $o->setBorderColor((string) $node->borderColor);
        $o->setBorderStyle((string) $node->borderStyle);
        $o->setBorderRadius((string) $node->borderRadius);
        $o->setBaseFontSize((string) $node->baseFontSize);
        $o->setAlignment((string) $node->alignment);
        $o->setTextColor((string) $node->textColor);
        $o->setLinkColor((string) $node->linkColor);
        $o->setPaddingTop((string) $node->paddingTop);
        $o->setPaddingBottom((string) $node->paddingBottom);
        $o->setPaddingLeft((string) $node->paddingLeft);
        $o->setPaddingRight((string) $node->paddingRight);
        $o->setMarginTop((string) $node->marginTop);
        $o->setMarginBottom((string) $node->marginBottom);
        $o->setMarginLeft((string) $node->marginLeft);
        $o->setMarginRight((string) $node->marginRight);
        $o->setRotate((string) $node->rotate);
        $o->setBoxShadowHorizontal((string) $node->boxShadowHorizontal);
        $o->setBoxShadowVertical((string) $node->boxShadowVertical);
        $o->setBoxShadowSpread((string) $node->boxShadowSpread);
        $o->setBoxShadowBlur((string) $node->boxShadowBlur);
        $o->setBoxShadowColor((string) $node->boxShadowColor);
        $o->setCustomClass((string) $node->customClass);
        $o->setCustomID((string) $node->customID);
        $o->setCustomElementAttribute((string) $node->customElementAttribute);

        $o->save();

        return $o;
    }

    /**
     * If the request contains any fields that are valid to save as a style set, we return the style set object
     * pre-save. If it's not (e.g. there's a background repeat but no actual background image, empty strings, etc...)
     * then we return null.
     * return \Concrete\Core\StyleCustomizer\Inline\StyleSet|null.
     *
     * @param Request $request
     */
    public static function populateFromRequest(Request $request)
    {
        $r = $request->request->all();
        $set = new \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet();
        $return = false;
        if (isset($r['backgroundColor']) && trim($r['backgroundColor']) != '') {
            $set->setBackgroundColor($r['backgroundColor']);
            $set->setBackgroundRepeat($r['backgroundRepeat']);
            $set->setBackgroundSize($r['backgroundSize']);
            $set->setBackgroundPosition($r['backgroundPosition']);
            $return = true;
        }

        if (isset($r['backgroundImageFileID'])) {
            $fID = intval($r['backgroundImageFileID']);
            if ($fID > 0) {
                $set->setBackgroundImageFileID($fID);
                $set->setBackgroundRepeat($r['backgroundRepeat']);
                $set->setBackgroundSize($r['backgroundSize']);
                $set->setBackgroundPosition($r['backgroundPosition']);
                $return = true;
            }
        }

        if (isset($r['hideOnDevice'])) {
            if (isset($r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL]) && $r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL] == 1) {
                $set->setHideOnExtraSmallDevice(true);
                $return = true;
            }
            if (isset($r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_SMALL]) && $r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_SMALL] == 1) {
                $set->setHideOnSmallDevice(true);
                $return = true;
            }
            if (isset($r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_MEDIUM]) && $r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_MEDIUM] == 1) {
                $set->setHideOnMediumDevice(true);
                $return = true;
            }
            if (isset($r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_LARGE]) && $r['hideOnDevice'][GridFramework::DEVICE_CLASSES_HIDE_ON_LARGE] == 1) {
                $set->setHideOnLargeDevice(true);
                $return = true;
            }
        }
        if (isset($r['linkColor']) && trim($r['linkColor']) != '') {
            $set->setLinkColor($r['linkColor']);
            $return = true;
        }

        if (isset($r['textColor']) && trim($r['textColor']) != '') {
            $set->setTextColor($r['textColor']);
            $return = true;
        }

        if (isset($r['baseFontSize']) && trim($r['baseFontSize']) && trim($r['baseFontSize']) != '0px') {
            $set->setBaseFontSize($r['baseFontSize']);
            $return = true;
        }

        if (isset($r['marginTop']) && trim($r['marginTop']) && trim($r['marginTop']) != '0px') {
            $set->setMarginTop($r['marginTop']);
            $return = true;
        }

        if (isset($r['marginRight']) && trim($r['marginRight']) && trim($r['marginRight']) != '0px') {
            $set->setMarginRight($r['marginRight']);
            $return = true;
        }

        if (isset($r['marginBottom']) && trim($r['marginBottom']) && trim($r['marginBottom']) != '0px') {
            $set->setMarginBottom($r['marginBottom']);
            $return = true;
        }

        if (isset($r['marginLeft']) && trim($r['marginLeft']) && trim($r['marginLeft']) != '0px') {
            $set->setMarginLeft($r['marginLeft']);
            $return = true;
        }

        if (isset($r['paddingTop']) && trim($r['paddingTop']) && trim($r['paddingTop']) != '0px') {
            $set->setPaddingTop($r['paddingTop']);
            $return = true;
        }

        if (isset($r['paddingRight']) && trim($r['paddingRight']) && trim($r['paddingRight']) != '0px') {
            $set->setPaddingRight($r['paddingRight']);
            $return = true;
        }

        if (isset($r['paddingBottom']) && trim($r['paddingBottom']) && trim($r['paddingBottom']) != '0px') {
            $set->setPaddingBottom($r['paddingBottom']);
            $return = true;
        }

        if (isset($r['paddingLeft']) && trim($r['paddingLeft']) && trim($r['paddingLeft']) != '0px') {
            $set->setPaddingLeft($r['paddingLeft']);
            $return = true;
        }

        if (isset($r['borderWidth']) && trim($r['borderWidth']) && trim($r['borderWidth']) != '0px') {
            $set->setBorderWidth($r['borderWidth']);
            $set->setBorderStyle($r['borderStyle']);
            $set->setBorderColor($r['borderColor']);
            $return = true;
        }
        if (isset($r['borderRadius']) && trim($r['borderRadius']) && trim($r['borderRadius']) != '0px') {
            $set->setBorderRadius($r['borderRadius']);
            $return = true;
        }

        if (isset($r['alignment']) && trim($r['alignment']) != '') {
            $set->setAlignment($r['alignment']);
            $return = true;
        }

        if (isset($r['rotate']) && $r['rotate']) {
            $set->setRotate($r['rotate']);
            $return = true;
        }

        if (isset($r['boxShadowHorizontal'])) {
            if ((trim($r['boxShadowHorizontal']) && trim($r['boxShadowHorizontal']) != '0px')
            || (trim($r['boxShadowVertical']) && trim($r['boxShadowVertical']) != '0px')) {
                $set->setBoxShadowBlur($r['boxShadowBlur']);
                $set->setBoxShadowColor($r['boxShadowColor']);
                $set->setBoxShadowHorizontal($r['boxShadowHorizontal']);
                $set->setBoxShadowVertical($r['boxShadowVertical']);
                $set->setBoxShadowSpread($r['boxShadowSpread']);
                $return = true;
            }
        }

        if (isset($r['customClass']) && is_array($r['customClass'])) {
            $set->setCustomClass(implode(' ', $r['customClass']));
            $return = true;
        }

        if (isset($r['customID']) && trim($r['customID'])) {
            $set->setCustomID(trim($r['customID']));
            $return = true;
        }

        if (isset($r['customElementAttribute']) && trim($r['customElementAttribute'])) {
            // strip class attributes
            $pattern = '/(class\s*=\s*["\'][^\'"]*["\'])/i';
            $customElementAttribute = preg_replace($pattern, '', $r['customElementAttribute']);
            // strip ID attributes
            $pattern = '/(id\s*=\s*["\'][^\'"]*["\'])/i';
            $customElementAttribute = preg_replace($pattern, '', $customElementAttribute);
            // don't save if there are odd numbers of single/double quotes
            $singleQuoteCount = preg_match_all('/([\'])/i', $customElementAttribute);
            $doubleQuoteCount = preg_match_all('/(["])/i', $customElementAttribute);

            if ($singleQuoteCount % 2 == 0 && $doubleQuoteCount % 2 == 0 ) {
                $set->setCustomElementAttribute(trim($customElementAttribute));
                $return = true;
            } else {
                throw new Exception(t('Custom Element Attribute input: unclosed quote(s)'));
            }
        }

        if ($return) {
            return $set;
        }

        return null;
    }
}
