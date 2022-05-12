<?php

namespace Concrete\Core\StyleCustomizer\Inline;

use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet as StyleSetEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;

class StyleSet
{
    /**
     * Get a StyleSet entity given its ID.
     *
     * @param int $issID
     *
     * @return \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet|null
     */
    public static function getByID($issID)
    {
        if (!$issID) {
            return null;
        }
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);

        return $em->find(StyleSetEntity::class, $issID);
    }

    /**
     * Import a StyleSet entity from an XML node.
     *
     * @param \SimpleXMLElement $node
     *
     * @return \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet
     */
    public static function import(SimpleXMLElement $node)
    {
        $o = new StyleSetEntity();
        $o->setBackgroundColor((string) $node->backgroundColor);
        $filename = (string) $node->backgroundImage;
        if ($filename) {
            $app = Application::getFacadeApplication();
            $inspector = $app->make('import/value_inspector');
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
        $o->setBoxShadowInset((bool) $node->boxShadowInset);
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet|null
     */
    public static function populateFromRequest(Request $request)
    {
        $post = $request->request;
        $set = new StyleSetEntity();
        $return = false;

        if ($post->has('backgroundColor') && trim($post->get('backgroundColor')) !== '') {
            $set->setBackgroundColor($post->get('backgroundColor'));
            $set->setBackgroundRepeat($post->get('backgroundRepeat'));
            $set->setBackgroundSize($post->get('backgroundSize'));
            $set->setBackgroundPosition($post->get('backgroundPosition'));
            $return = true;
        }

        $fID = (int) $post->get('backgroundImageFileID');
        if ($fID > 0) {
            $set->setBackgroundImageFileID($fID);
            $set->setBackgroundRepeat($post->get('backgroundRepeat'));
            $set->setBackgroundSize($post->get('backgroundSize'));
            $set->setBackgroundPosition($post->get('backgroundPosition'));
            $return = true;
        }

        $hod = $post->get('hideOnDevice');
        if (is_array($hod)) {
            if (!empty($hod[GridFramework::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL])) {
                $set->setHideOnExtraSmallDevice(true);
                $return = true;
            }
            if (!empty($hod[GridFramework::DEVICE_CLASSES_HIDE_ON_SMALL])) {
                $set->setHideOnSmallDevice(true);
                $return = true;
            }
            if (!empty($hod[GridFramework::DEVICE_CLASSES_HIDE_ON_MEDIUM])) {
                $set->setHideOnMediumDevice(true);
                $return = true;
            }
            if (!empty($hod[GridFramework::DEVICE_CLASSES_HIDE_ON_LARGE])) {
                $set->setHideOnLargeDevice(true);
                $return = true;
            }
        }

        $v = trim($post->get('linkColor', ''));
        if ($v !== '') {
            $set->setLinkColor($v);
            $return = true;
        }

        $v = trim($post->get('textColor', ''));
        if ($v !== '') {
            $set->setTextColor($v);
            $return = true;
        }

        $v = trim($post->get('baseFontSize', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBaseFontSize($v);
            $return = true;
        }

        $v = trim($post->get('marginTop', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginTop($v);
            $return = true;
        }

        $v = trim($post->get('marginRight', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginRight($v);
            $return = true;
        }

        $v = trim($post->get('marginBottom', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginBottom($v);
            $return = true;
        }

        $v = trim($post->get('marginLeft', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginLeft($v);
            $return = true;
        }

        $v = trim($post->get('paddingTop', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingTop($v);
            $return = true;
        }

        $v = trim($post->get('paddingRight', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingRight($v);
            $return = true;
        }

        $v = trim($post->get('paddingBottom', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingBottom($v);
            $return = true;
        }

        $v = trim($post->get('paddingLeft', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingLeft($v);
            $return = true;
        }

        $v = trim($post->get('borderWidth', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBorderWidth($v);
            $set->setBorderStyle($post->get('borderStyle'));
            $set->setBorderColor($post->get('borderColor'));
            $return = true;
        }

        $v = trim($post->get('borderRadius', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBorderRadius($v);
            $return = true;
        }

        $v = trim($post->get('alignment', ''));
        if ($v !== '') {
            $set->setAlignment($v);
            $return = true;
        }

        $v = $post->get('rotate');
        if ($v) {
            $set->setRotate($v);
            $return = true;
        }

        if ($post->has('boxShadowColor')) {
            $boxShadowHorizontal = trim($post->get('boxShadowHorizontal', '')) ?: '0px';
            $boxShadowVertical = trim($post->get('boxShadowVertical', '')) ?: '0px';
            $boxShadowBlur = trim($post->get('boxShadowBlur', '')) ?: '0px';
            $boxShadowSpread = trim($post->get('boxShadowSpread', '')) ?: '0px';
            $boxShadowInset = (bool) $post->get('boxShadowInset');
            if ($boxShadowHorizontal !== '0px' || $boxShadowVertical !== '0px' || $boxShadowBlur !== '0px' || $boxShadowSpread !== '0px') {
                $set->setBoxShadowColor($post->get('boxShadowColor'));
                $set->setBoxShadowBlur($boxShadowBlur);
                $set->setBoxShadowHorizontal($boxShadowHorizontal);
                $set->setBoxShadowVertical($boxShadowVertical);
                $set->setBoxShadowSpread($boxShadowSpread);
                $set->setBoxShadowInset($boxShadowInset);
                $return = true;
            }
        }

        $v = $post->get('customClass');
        if (is_array($v)) {
            $set->setCustomClass(implode(' ', $v));
            $return = true;
        }

        $v = trim($post->get('customID', ''));
        if ($v) {
            $set->setCustomID($v);
            $return = true;
        }

        $v = trim($post->get('customElementAttribute', ''));
        if ($v !== '') {
            // strip class attributes
            $pattern = '/(class\s*=\s*["\'][^\'"]*["\'])/i';
            $customElementAttribute = preg_replace($pattern, '', $v);
            // strip ID attributes
            $pattern = '/(id\s*=\s*["\'][^\'"]*["\'])/i';
            $customElementAttribute = preg_replace($pattern, '', $customElementAttribute);
            // don't save if there are odd numbers of single/double quotes
            $singleQuoteCount = preg_match_all('/([\'])/i', $customElementAttribute);
            $doubleQuoteCount = preg_match_all('/(["])/i', $customElementAttribute);

            if ($singleQuoteCount % 2 !== 0 || $doubleQuoteCount % 2 !== 0) {
                throw new UserMessageException(t('Custom Element Attribute input: unclosed quote(s)'));
            }
            $set->setCustomElementAttribute(trim($customElementAttribute));
            $return = true;
        }

        return $return ? $set : null;
    }
}
