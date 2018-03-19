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

    public function edit()
    {
        if ($this->vWidth || $this->vWidth) {
            $this->set('sizing', 'fixed');
        }
    }

    public function view()
    {
        $url = parse_url($this->videoURL);
        $pathParts = explode('/', rtrim($url['path'], '/'));
        parse_str($url['query'], $params);
        $videoID = end($pathParts);
        $playListID = '';

        if (isset($url['query'])) {
            parse_str($url['query'], $query);

            if (isset($query['list'])) {
                $playListID = $query['list'];
                $videoID = '';
            } else {
                $videoID = (isset($query['v'])) ? $query['v'] : $videoID;
                $videoID = strtok($videoID, '?');
            }
        }

        if (strpos($videoID, ',') !== false) {
            $this->set('playlist', $videoID);
        }

        if ($this->startTimeEnabled == 1 && ($this->startTime === '0' || $this->startTime)) {
            $this->set('startSeconds', $this->convertStringToSeconds($this->startTime));
        }
        elseif ($params['t']) {
            $this->set('startSeconds', $this->convertStringToSeconds($params['t']));
        }

        $this->set('videoID', $videoID);
        $this->set('playListID', $playListID);
    }

    public function convertStringToSeconds($string)
    {
        if (preg_match_all('/(\d+)(h|m|s)/i', $string, $matches)) {
            $h = (($key = array_search('h', $matches[2])) !== false) ? (int)$matches[1][$key] : 0;
            $m = (($key = array_search('m', $matches[2])) !== false) ? (int)$matches[1][$key] : 0;
            $s = (($key = array_search('s', $matches[2])) !== false) ? (int)$matches[1][$key] : 0;
            $seconds = ($h * 3600) + ($m * 60) + $s;
        }
        else {
            $pieces = array_reverse(explode(':', $string));
            $seconds = 0;
            $multipliers = [1, 60, 3600];
            foreach ($multipliers as $key => $multiplier) {
                if (array_key_exists($key, $pieces)) {
                    $seconds += (int)$pieces[$key] * $multiplier;
                }
            }
        }
        if ($seconds === 0 || $seconds > 0) {
            return $seconds;
        }
        return false;
    }

    public function save($data)
    {
        $args['title'] = isset($data['title']) ? trim($data['title']) : '';
        $args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';
        $args['vHeight'] = isset($data['vHeight']) ? trim($data['vHeight']) : '';
        $args['vWidth'] = isset($data['vWidth']) ? trim($data['vWidth']) : '';
        $args['startTime'] = isset($data['startTime']) ? trim($data['startTime']) : '';

        $args['rel'] = $data['rel'] ? 1 : 0;
        $args['showinfo'] = $data['showinfo'] ? 1 : 0;
        $args['autoplay'] = $data['autoplay'] ? 1 : 0;
        $args['loopEnd'] = $data['loopEnd'] ? 1 : 0;
        $args['startTimeEnabled'] = $data['startTimeEnabled'] ? 1 : 0;

        $args['modestbranding'] = $data['modestbranding'] ? 1 : 0;
        $args['iv_load_policy'] = $data['iv_load_policy'] ? 3 : 1;
        $args['color'] = $data['color'];
        $args['sizing'] = $data['sizing'];
        $args['controls'] = $data['controls'] ? 1 : 0;

        if ($data['sizing'] != 'fixed') {
            $args['vHeight'] = '';
            $args['vWidth'] = '';
        }

        parent::save($args);
    }
}
