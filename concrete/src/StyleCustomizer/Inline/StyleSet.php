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
        $values = app(StyleValues::class);
        $o = new StyleSetEntity();
        $o->setCustomClass($values->sanitizeClasses(preg_split('/\s+/', trim((string) $node->customClass), -1, PREG_SPLIT_NO_EMPTY) ?: []));
        $o->setCustomID(trim((string) $node->customID));
        try {
            $customElementAttribute = $values->sanitizeElementAttribute((string) $node->customElementAttribute);
        } catch (UserMessageException $x) {
            // a CIF is read for what holds in it, and an attribute whose quotes are not closed does not
            $customElementAttribute = '';
        }
        $o->setCustomElementAttribute($customElementAttribute);
        $o->setBackgroundColor($values->sanitizeColor($node->backgroundColor));
        $filename = (string) $node->backgroundImage;
        if ($filename) {
            $inspector = app('import/value_inspector');
            $result = $inspector->inspect($filename);
            $fID = $result->getReplacedValue();
            if ($fID) {
                $o->setBackgroundImageFileID($fID);
            }
        }
        $o->setBackgroundRepeat($values->sanitizeBackgroundRepeat($node->backgroundRepeat));
        $o->setBackgroundSize($values->sanitizeBackgroundSize($node->backgroundSize));
        $o->setBackgroundPosition($values->sanitizeBackgroundPosition($node->backgroundPosition));
        $o->setBorderColor($values->sanitizeColor($node->borderColor));
        $o->setBorderStyle($values->sanitizeBorderStyle($node->borderStyle));
        $o->setBorderWidth($values->sanitizeLength($node->borderWidth, false));
        $o->setBorderRadius($values->sanitizeLength($node->borderRadius, false));
        $o->setBaseFontSize($values->sanitizeLength($node->baseFontSize, false));
        $o->setAlignment($values->sanitizeAlignment($node->alignment));
        $o->setTextColor($values->sanitizeColor($node->textColor));
        $o->setLinkColor($values->sanitizeColor($node->linkColor));
        $o->setMarginTop($values->sanitizeLength($node->marginTop));
        $o->setMarginBottom($values->sanitizeLength($node->marginBottom));
        $o->setMarginLeft($values->sanitizeLength($node->marginLeft));
        $o->setMarginRight($values->sanitizeLength($node->marginRight));
        $o->setPaddingTop($values->sanitizeLength($node->paddingTop, false));
        $o->setPaddingBottom($values->sanitizeLength($node->paddingBottom, false));
        $o->setPaddingLeft($values->sanitizeLength($node->paddingLeft, false));
        $o->setPaddingRight($values->sanitizeLength($node->paddingRight, false));
        $o->setRotate($values->sanitizeRotation($node->rotate));
        $o->setBoxShadowHorizontal($values->sanitizeLength($node->boxShadowHorizontal));
        $o->setBoxShadowVertical($values->sanitizeLength($node->boxShadowVertical));
        $o->setBoxShadowBlur($values->sanitizeLength($node->boxShadowBlur, false));
        $o->setBoxShadowSpread($values->sanitizeLength($node->boxShadowSpread));
        $o->setBoxShadowColor($values->sanitizeColor($node->boxShadowColor));
        $o->setBoxShadowInset($xmlService->getBool($node->boxShadowInset));
        $o->setHideOnExtraSmallDevice($xmlService->getBool($node->hideOnExtraSmallDevice));
        $o->setHideOnSmallDevice($xmlService->getBool($node->hideOnSmallDevice));
        $o->setHideOnMediumDevice($xmlService->getBool($node->hideOnMediumDevice));
        $o->setHideOnLargeDevice($xmlService->getBool($node->hideOnLargeDevice));

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
        $values = app(StyleValues::class);
        $set = new StyleSetEntity();
        $return = false;

        $backgroundRepeat = $values->sanitizeBackgroundRepeat($post->get('backgroundRepeat')) ?: 'no-repeat';
        $backgroundSize = $values->sanitizeBackgroundSize($post->get('backgroundSize')) ?: 'auto';
        $backgroundPosition = $values->sanitizeBackgroundPosition($post->get('backgroundPosition')) ?: 'left top';

        $v = $values->sanitizeColor($post->get('backgroundColor'));
        if ($v !== '') {
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

        $v = $values->sanitizeColor($post->get('linkColor', ''));
        if ($v !== '') {
            $set->setLinkColor($v);
            $return = true;
        }

        $v = $values->sanitizeColor($post->get('textColor', ''));
        if ($v !== '') {
            $set->setTextColor($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('baseFontSize', ''), false, false);
        if ($v !== '') {
            $set->setBaseFontSize($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('marginTop', ''), true, false);
        if ($v !== '') {
            $set->setMarginTop($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('marginRight', ''), true, false);
        if ($v !== '') {
            $set->setMarginRight($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('marginBottom', ''), true, false);
        if ($v !== '') {
            $set->setMarginBottom($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('marginLeft', ''), true, false);
        if ($v !== '') {
            $set->setMarginLeft($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('paddingTop', ''), false, false);
        if ($v !== '') {
            $set->setPaddingTop($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('paddingRight', ''), false, false);
        if ($v !== '') {
            $set->setPaddingRight($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('paddingBottom', ''), false, false);
        if ($v !== '') {
            $set->setPaddingBottom($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('paddingLeft', ''), false, false);
        if ($v !== '') {
            $set->setPaddingLeft($v);
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('borderWidth', ''), false, false);
        if ($v !== '') {
            $set->setBorderWidth($v);
            $set->setBorderStyle($values->sanitizeBorderStyle($post->get('borderStyle')));
            $set->setBorderColor($values->sanitizeColor($post->get('borderColor')));
            $return = true;
        }

        $v = $values->sanitizeLength($post->get('borderRadius', ''), false, false);
        if ($v !== '') {
            $set->setBorderRadius($v);
            $return = true;
        }

        $v = $values->sanitizeAlignment($post->get('alignment', ''));
        if ($v !== '') {
            $set->setAlignment($v);
            $return = true;
        }

        $v = $values->sanitizeRotation($post->get('rotate'), false);
        if ($v !== '') {
            $set->setRotate($v);
            $return = true;
        }

        if ($post->has('boxShadowColor')) {
            $boxShadowHorizontal = $values->sanitizeLength($post->get('boxShadowHorizontal', '')) ?: '0px';
            $boxShadowVertical = $values->sanitizeLength($post->get('boxShadowVertical', '')) ?: '0px';
            $boxShadowBlur = $values->sanitizeLength($post->get('boxShadowBlur', ''), false) ?: '0px';
            $boxShadowSpread = $values->sanitizeLength($post->get('boxShadowSpread', '')) ?: '0px';
            $boxShadowInset = (bool) $post->get('boxShadowInset');
            if ($boxShadowHorizontal !== '0px' || $boxShadowVertical !== '0px' || $boxShadowBlur !== '0px' || $boxShadowSpread !== '0px') {
                $set->setBoxShadowColor($values->sanitizeColor($post->get('boxShadowColor')));
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
            $customClasses = $values->sanitizeClasses($v);
            if ($customClasses !== '') {
                $set->setCustomClass($customClasses);
                $return = true;
            }
        }

        $v = trim($post->get('customID', ''));
        if ($v) {
            $set->setCustomID($v);
            $return = true;
        }

        $v = $values->sanitizeElementAttribute($post->get('customElementAttribute', ''));
        if ($v !== '') {
            $set->setCustomElementAttribute($v);
            $return = true;
        }

        return $return ? $set : null;
    }

    /**
     * @deprecated use \Concrete\Core\StyleCustomizer\Inline\StyleValues::sanitizeClasses()
     *
     * @param string[] $cssClasses
     */
    protected static function sanitizeCssClasses(array $cssClasses): ?string
    {
        return self::orNull(app(StyleValues::class)->sanitizeClasses($cssClasses));
    }

    /**
     * @deprecated use \Concrete\Core\StyleCustomizer\Inline\StyleValues::sanitizeColor()
     */
    protected static function sanitizeCssColor($value): ?string
    {
        return self::orNull(app(StyleValues::class)->sanitizeColor($value));
    }

    /**
     * @deprecated use \Concrete\Core\StyleCustomizer\Inline\StyleValues::sanitizeLength()
     */
    protected static function sanitizeCssLength($value, bool $allowNegative = true): ?string
    {
        return self::orNull(app(StyleValues::class)->sanitizeLength($value, $allowNegative));
    }

    /**
     * @deprecated use \Concrete\Core\StyleCustomizer\Inline\StyleValues::sanitizeRotation()
     */
    protected static function sanitizeCssRotation($value): ?string
    {
        return self::orNull(app(StyleValues::class)->sanitizeRotation($value));
    }

    /**
     * @deprecated use \Concrete\Core\StyleCustomizer\Inline\StyleValues::sanitizeEnum()
     *
     * @param string[] $allowed
     */
    protected static function sanitizeCssEnum($value, array $allowed): ?string
    {
        return self::orNull(app(StyleValues::class)->sanitizeEnum($value, $allowed));
    }

    private static function orNull(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
