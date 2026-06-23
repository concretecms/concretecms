<?php

namespace Concrete\Core\StyleCustomizer\Inline;

use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet as StyleSetEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Xml;
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
        $xmlService = app(Xml::class);
        $o = new StyleSetEntity();
        $o->setCustomClass((string) $node->customClass);
        $o->setCustomID((string) $node->customID);
        $o->setCustomElementAttribute((string) $node->customElementAttribute);
        $o->setBackgroundColor((string) $node->backgroundColor);
        $filename = (string) $node->backgroundImage;
        if ($filename) {
            $inspector = app('import/value_inspector');
            $result = $inspector->inspect($filename);
            $fID = $result->getReplacedValue();
            if ($fID) {
                $o->setBackgroundImageFileID($fID);
            }
        }
        $o->setBackgroundRepeat((string) $node->backgroundRepeat);
        $o->setBackgroundSize((string) $node->backgroundSize);
        $o->setBackgroundPosition((string) $node->backgroundPosition);
        $o->setBorderColor((string) $node->borderColor);
        $o->setBorderStyle((string) $node->borderStyle);
        $o->setBorderWidth((string) $node->borderWidth);
        $o->setBorderRadius((string) $node->borderRadius);
        $o->setBaseFontSize((string) $node->baseFontSize);
        $o->setAlignment((string) $node->alignment);
        $o->setTextColor((string) $node->textColor);
        $o->setLinkColor((string) $node->linkColor);
        $o->setMarginTop((string) $node->marginTop);
        $o->setMarginBottom((string) $node->marginBottom);
        $o->setMarginLeft((string) $node->marginLeft);
        $o->setMarginRight((string) $node->marginRight);
        $o->setPaddingTop((string) $node->paddingTop);
        $o->setPaddingBottom((string) $node->paddingBottom);
        $o->setPaddingLeft((string) $node->paddingLeft);
        $o->setPaddingRight((string) $node->paddingRight);
        $o->setRotate((string) $node->rotate);
        $o->setBoxShadowHorizontal((string) $node->boxShadowHorizontal);
        $o->setBoxShadowVertical((string) $node->boxShadowVertical);
        $o->setBoxShadowBlur((string) $node->boxShadowBlur);
        $o->setBoxShadowSpread((string) $node->boxShadowSpread);
        $o->setBoxShadowColor((string) $node->boxShadowColor);
        $o->setBoxShadowInset($xmlService->getBool($node->boxShadowInset));
        $o->setHideOnExtraSmallDevice($xmlService->getBool($node->hideOnExtraSmallDevice));
        $o->setHideOnSmallDevice($xmlService->getBool($node->hideOnSmallDevice));
        $o->setHideOnMediumDevice($xmlService->getBool($node->hideOnMediumDevice));
        $o->setHideOnLargeDevice($xmlService->getBool($node->hideOnLargeDevice));

        $o->save();

        return $o;
    }

    /**
     * @param string[] $cssClasses
     */
    protected static function sanitizeCssClasses(array $cssClasses): ?string
    {
        $cssClasses = array_filter($cssClasses, function ($class) {
            return preg_match('/^[^<>\'"]+$/', $class);
        });
        if (count($cssClasses) > 0) {
            return implode(' ', $cssClasses);
        }
        return null;
    }

    protected static function sanitizeCssColor($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value)) {
            return $value;
        }
        if (preg_match('/^(?:rgb|hsl)a?\(\s*[-\d.%\s,\/]+\)$/i', $value)) {
            return preg_replace('/\s+/', ' ', $value);
        }

        return in_array(strtolower($value), ['transparent', 'currentcolor'], true) ? $value : null;
    }

    protected static function sanitizeCssLength($value, bool $allowNegative = true): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $pattern = $allowNegative
            ? '/^-?(?:\d+|\d*\.\d+)(?:px|%|em|rem|vh|vw|vmin|vmax)?$/i'
            : '/^(?:\d+|\d*\.\d+)(?:px|%|em|rem|vh|vw|vmin|vmax)?$/i';

        return preg_match($pattern, $value) ? $value : null;
    }

    protected static function sanitizeCssRotation($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return preg_match('/^-?(?:\d+|\d*\.\d+)$/', $value) ? $value : null;
    }

    protected static function sanitizeCssEnum($value, array $allowed): ?string
    {
        $value = trim((string) $value);

        return in_array($value, $allowed, true) ? $value : null;
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

        $backgroundRepeat = self::sanitizeCssEnum($post->get('backgroundRepeat'), ['no-repeat', 'repeat-x', 'repeat-y', 'repeat']) ?? 'no-repeat';
        $backgroundSize = self::sanitizeCssEnum($post->get('backgroundSize'), ['auto', 'contain', 'cover', '10%', '25%', '50%', '75%', '100%']) ?? 'auto';
        $backgroundPosition = self::sanitizeCssEnum($post->get('backgroundPosition'), [
            'left top',
            'left center',
            'left bottom',
            'center top',
            'center center',
            'center bottom',
            'right top',
            'right center',
            'right bottom',
        ]) ?? 'left top';

        $v = self::sanitizeCssColor($post->get('backgroundColor'));
        if ($v !== null) {
            $set->setBackgroundColor($v);
            $set->setBackgroundRepeat($backgroundRepeat);
            $set->setBackgroundSize($backgroundSize);
            $set->setBackgroundPosition($backgroundPosition);
            $return = true;
        }

        $fID = (int) $post->get('backgroundImageFileID');
        if ($fID > 0) {
            $set->setBackgroundImageFileID($fID);
            $set->setBackgroundRepeat($backgroundRepeat);
            $set->setBackgroundSize($backgroundSize);
            $set->setBackgroundPosition($backgroundPosition);
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

        $v = self::sanitizeCssColor($post->get('linkColor', ''));
        if ($v !== null) {
            $set->setLinkColor($v);
            $return = true;
        }

        $v = self::sanitizeCssColor($post->get('textColor', ''));
        if ($v !== null) {
            $set->setTextColor($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('baseFontSize', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBaseFontSize($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('marginTop', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginTop($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('marginRight', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginRight($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('marginBottom', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginBottom($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('marginLeft', ''));
        if (!in_array($v, ['', '0px'], true)) {
            $set->setMarginLeft($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('paddingTop', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingTop($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('paddingRight', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingRight($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('paddingBottom', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingBottom($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('paddingLeft', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setPaddingLeft($v);
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('borderWidth', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBorderWidth($v);
            $set->setBorderStyle(self::sanitizeCssEnum($post->get('borderStyle'), ['', 'solid', 'dotted', 'dashed', 'double', 'groove', 'ridge', 'inset', 'outset']));
            $set->setBorderColor(self::sanitizeCssColor($post->get('borderColor')));
            $return = true;
        }

        $v = self::sanitizeCssLength($post->get('borderRadius', ''), false);
        if (!in_array($v, ['', '0px'], true)) {
            $set->setBorderRadius($v);
            $return = true;
        }

        $v = self::sanitizeCssEnum($post->get('alignment', ''), ['', 'left', 'center', 'right']);
        if ($v !== null && $v !== '') {
            $set->setAlignment($v);
            $return = true;
        }

        $v = self::sanitizeCssRotation($post->get('rotate'));
        if ($v !== null && $v !== '0') {
            $set->setRotate($v);
            $return = true;
        }

        if ($post->has('boxShadowColor')) {
            $boxShadowHorizontal = self::sanitizeCssLength($post->get('boxShadowHorizontal', '')) ?: '0px';
            $boxShadowVertical = self::sanitizeCssLength($post->get('boxShadowVertical', '')) ?: '0px';
            $boxShadowBlur = self::sanitizeCssLength($post->get('boxShadowBlur', ''), false) ?: '0px';
            $boxShadowSpread = self::sanitizeCssLength($post->get('boxShadowSpread', '')) ?: '0px';
            $boxShadowInset = (bool) $post->get('boxShadowInset');
            if ($boxShadowHorizontal !== '0px' || $boxShadowVertical !== '0px' || $boxShadowBlur !== '0px' || $boxShadowSpread !== '0px') {
                $set->setBoxShadowColor(self::sanitizeCssColor($post->get('boxShadowColor')));
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
            $customClasses = self::sanitizeCssClasses($v);
            if ($customClasses !== null) {
                $set->setCustomClass($customClasses);
                $return = true;
            }
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
