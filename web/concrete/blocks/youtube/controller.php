<?php

namespace Concrete\Block\Youtube;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = 'btYouTube';
    protected $btInterfaceWidth = "400";
    protected $btInterfaceHeight = "490";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public $title = '';
    public $videoURL = "";
    public $vHeight = "370";
    public $vWidth = "425";
    public $vPlayer = '1';
    public $mode = "youtube";

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Embeds a YouTube Video in your web page.");
    }

    public function getBlockTypeName()
    {
        return t("YouTube Video");
    }

    public function __construct($obj = null)
    {
        parent::__construct($obj);
        if (!$this->title) {
            $this->title = t("My Video");
        }
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('swfobject');
    }

    public function edit()
    {
        if ($this->vWidth || $this->vWidth) {
            $this->set('sizing', 'fixed');
        }
    }

    public function view()
    {
        $url       = parse_url($this->videoURL);
        $pathParts = explode('/', rtrim($url['path'], '/'));
        $videoID   = end($pathParts);
        $playListID = '';

        if (isset($url['query'])) {
            parse_str($url['query'], $query);

            if (isset($query['list'])) {
                $playListID = $query['list'];
                $videoID = '';
            } else {
                $videoID = (isset($query['v'])) ? $query['v'] : $videoID;
            }
        }

        if (strpos($videoID, ',') !== false) {
            $this->set('playlist', $videoID);
        }

        $this->set('videoID', $videoID);
        $this->set('playListID', $playListID);
    }

    public function save($data)
    {
        $args['title'] = isset($data['title']) ? trim($data['title']) : '';
        $args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';
        $args['vHeight'] = isset($data['vHeight']) ? trim($data['vHeight']) : '';
        $args['vWidth'] = isset($data['vWidth']) ? trim($data['vWidth']) : '';

        $args['rel'] = $data['rel'] ? 1 : 0;
        $args['showinfo'] = $data['showinfo'] ? 1 : 0;
        $args['autoplay'] = $data['autoplay'] ? 1 : 0;
        $args['loopEnd'] = $data['loopEnd'] ? 1 : 0;
        $args['modestbranding'] = $data['modestbranding'] ? 1 : 0;
        $args['iv_load_policy'] = $data['iv_load_policy'] ? 1 : 3;
        $args['color'] = $data['color'];
        $args['sizing'] = $data['sizing'];
        $args['iv_load_policy'] = $data['iv_load_policy'];
        $args['controls'] = $data['controls'];

        if ($data['sizing'] !='fixed') {
            $args['vHeight'] = '';
            $args['vWidth'] = '';
        }

        parent::save($args);
    }
}
