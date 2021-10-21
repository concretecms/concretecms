<?php

namespace Concrete\Core\File\Image\Svg;

class SanitizerOptions
{
    /**
     * The default space-separated list of unsafe XML elements.
     *
     * @var string
     */
    protected static $defaultUnsafeElements = 'script';

    /**
     * The default space-separated list of unsafe XML attributes.
     *
     * @var string
     *
     * @see https://www.w3.org/TR/SVG11/interact.html#SVGEvents
     * @see https://developer.mozilla.org/en-US/docs/Web/Events#Standard_events
     * @see https://developer.mozilla.org/en-US/docs/Web/Events#Non-standard_events
     */
    protected static $defaultUnsafeAttributes = <<<'EOT'
onabort onactivate onafterprint onafterscriptexecute onalerting onanimationend onanimationiteration onanimationstart
onappinstalled onaudioend onaudioprocess onaudiostart onbeforeinstallprompt onbeforeprint onbeforescriptexecute
onbeforeunload onbeginevent onblocked onblur onboundary onbusy oncached oncallschanged oncanplay oncanplaythrough
oncardstatechange oncfstatechange onchange onchargingchange onchargingtimechange onchecking onclick onclose oncomplete
oncompositionend oncompositionstart oncompositionupdate onconnected onconnecting onconnectioninfoupdate oncontextmenu
oncopy oncut ondatachange ondataerror ondblclick ondelivered ondevicechange ondevicelight ondevicemotion
ondeviceorientation ondeviceproximity ondialing ondisabled ondischargingtimechange ondisconnected ondisconnecting
ondomactivate ondomattributenamechanged ondomattrmodified ondomcharacterdatamodified ondomcontentloaded
ondomelementnamechanged ondomfocusin ondomfocusout ondommousescroll ondomnodeinserted ondomnodeinsertedintodocument
ondomnoderemoved ondomnoderemovedfromdocument ondomsubtreemodified ondownloading ondrag ondragdrop ondragend
ondragenter ondragexit ondraggesture ondragleave ondragover ondragstart ondrop ondurationchange onemptied onenabled
onend onended onendevent onerror onfocus onfocusin onfocusout onfullscreenchange onfullscreenerror ongamepadconnected
ongamepaddisconnected ongotpointercapture onhashchange onheld onholding onicccardlockerror oniccinfochange onincoming
oninput oninvalid onkeydown onkeypress onkeyup onlanguagechange onlevelchange onload onloadeddata onloadedmetadata
onloadend onloadstart onlocalized onlostpointercapture onmark onmessage onmessageerror onmousedown onmouseenter
onmouseleave onmousemove onmouseout onmouseover onmouseup onmousewheel onmozaudioavailable onmozbeforeresize
onmozbrowseractivitydone onmozbrowserasyncscroll onmozbrowseraudioplaybackchange onmozbrowsercaretstatechanged
onmozbrowserclose onmozbrowsercontextmenu onmozbrowserdocumentfirstpaint onmozbrowsererror onmozbrowserfindchange
onmozbrowserfirstpaint onmozbrowsericonchange onmozbrowserloadend onmozbrowserloadstart onmozbrowserlocationchange
onmozbrowsermanifestchange onmozbrowsermetachange onmozbrowseropensearch onmozbrowseropentab onmozbrowseropenwindow
onmozbrowserresize onmozbrowserscroll onmozbrowserscrollareachanged onmozbrowserscrollviewchange
onmozbrowsersecuritychange onmozbrowserselectionstatechanged onmozbrowsershowmodalprompt onmozbrowsertitlechange
onmozbrowserusernameandpasswordrequired onmozbrowservisibilitychange onmozgamepadbuttondown onmozgamepadbuttonup
onmozmousepixelscroll onmozorientation onmozscrolledareachanged onmoztimechange onmoztouchdown onmoztouchmove
onmoztouchup onmscontentzoom onmsmanipulationstatechanged onmspointerhover onnomatch onnotificationclick onnoupdate
onobsolete onoffline ononline onopen onorientationchange onoverflow onpagehide onpageshow onpaste onpause onplay
onplaying onpointercancel onpointerdown onpointerenter onpointerleave onpointerlockchange onpointerlockerror
onpointermove onpointerout onpointerover onpointerup onpopstate onprogress onpush onpushsubscriptionchange onratechange
onreadystatechange onreceived onrepeatevent onreset onresize onresourcetimingbufferfull onresult onresume onresuming
onscroll onseeked onseeking onselect onselectionchange onselectstart onsent onshow onslotchange onsmartcard-insert
onsmartcard-remove onsoundend onsoundstart onspeechend onspeechstart onstalled onstart onstatechange onstatuschange
onstkcommand onstksessionend onstorage onsubmit onsuccess onsuspend onsvgabort onsvgerror onsvgload onsvgresize
onsvgscroll onsvgunload onsvgzoom ontext ontimeout ontimeupdate ontouchcancel ontouchend ontouchenter ontouchleave
ontouchmove ontouchstart ontransitionend onunderflow onunload onupdateready onupgradeneeded onuploadprogress
onuserproximity onussdreceived onversionchange onvisibilitychange onvoicechange onvoiceschanged onvolumechange
onwaiting onwheel
EOT
    ;

    /**
     * The default space-separated list of XML elements to not consider as unsafe.
     *
     * @var string
     */
    protected static $defaultElementWhiteList = '';

    /**
     * The default space-separated list of XML attributes to not consider as unsafe.
     *
     * @var string
     */
    protected static $defaultAttributeWhiteList = '';

    /**
     * The list of unsafe XML elements.
     *
     * @var string[]
     */
    private $unsafeElements;

    /**
     * The list of unsafe XML attributes.
     *
     * @var string[]
     */
    private $unsafeAttributes;

    /**
     * The list of XML elements to not consider as unsafe.
     *
     * @var string[]
     */
    private $elementAllowlist;

    /**
     * The list of XML attributes to not consider as unsafe.
     *
     * @var string[]
     */
    private $attributeAllowlist;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this
            ->setUnsafeElements(static::$defaultUnsafeElements)
            ->setUnsafeAttributes(static::$defaultUnsafeAttributes)
            ->setElementAllowlist(static::$defaultElementWhiteList)
            ->setAttributeAllowlist(static::$defaultAttributeWhiteList)
        ;
    }

    /**
     * Get the list of unsafe XML elements.
     *
     * @return string[]
     */
    public function getUnsafeElements()
    {
        return $this->unsafeElements;
    }

    /**
     * Set the list of unsafe XML elements.
     *
     * @param string|string[] $value an array of strings, or a space-separated list of strings
     *
     * @return $this
     */
    public function setUnsafeElements($value)
    {
        $this->unsafeElements = $this->normalizeStringList($value);

        return $this;
    }

    /**
     * Get the list of unsafe XML attributes.
     *
     * @return string[]
     */
    public function getUnsafeAttributes()
    {
        return $this->unsafeAttributes;
    }

    /**
     * Set the list of unsafe XML attributes.
     *
     * @param string|string[] $value an array of strings, or a space-separated list of strings
     *
     * @return $this
     */
    public function setUnsafeAttributes($value)
    {
        $this->unsafeAttributes = $this->normalizeStringList($value);

        return $this;
    }

    /**
     * Get the list of XML elements to not consider as unsafe.
     *
     * @return string[]
     */
    public function getElementAllowlist()
    {
        return $this->elementAllowlist;
    }

    /**
     * Set the list of XML elements to not consider as unsafe.
     *
     * @param string|string[] $value an array of strings, or a space-separated list of strings
     *
     * @return $this
     */
    public function setElementAllowlist($value)
    {
        $this->elementAllowlist = $this->normalizeStringList($value);

        return $this;
    }

    /**
     * Get the list of XML attributes to not consider as unsafe.
     *
     * @return string[]
     */
    public function getAttributeAllowlist()
    {
        return $this->attributeAllowlist;
    }

    /**
     * Set the list of XML attributes to not consider as unsafe.
     *
     * @param string|string[] $value an array of strings, or a space-separated list of strings
     *
     * @return $this
     */
    public function setAttributeAllowlist($value)
    {
        $this->attributeAllowlist = $this->normalizeStringList($value);

        return $this;
    }

    /**
     * Takes an array, keeps only strings, makes them lowercase, and returns the unique values.
     *
     * @param string|array $value
     *
     * @return string[]
     */
    protected function normalizeStringList($value)
    {
        if (is_string($value)) {
            $strings = preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        } elseif (is_array($value)) {
            $strings = array_map('trim', array_map('strval', $value));
        } else {
            $strings = [];
        }

        return array_values(array_unique(array_map('strtolower', $strings)));
    }
}
