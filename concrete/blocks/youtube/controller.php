<?php

namespace Concrete\Block\Youtube;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = 'btYouTube';
    protected $btInterfaceWidth = '400';
    protected $btInterfaceHeight = '490';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t('Embeds a YouTube Video in your web page.');
    }

    public function getBlockTypeName()
    {
        return t('YouTube Video');
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
        $videoID = end($pathParts);
        $playListID = '';

        if (isset($url['query']) === true) {
            parse_str($url['query'], $query);
            
            if (isset($query['list']) === true) {
                $playListID = $query['list'];
                $videoID = '';
            } else {
                $videoID = isset($query['v']) ? $query['v'] : $videoID;
                $videoID = strtok($videoID, '?');
            }
        }

        if ($this->noCookie) {
            $this->set('youtubeDomain', 'www.youtube-nocookie.com');
        } else {
            $this->set('youtubeDomain', 'www.youtube.com');
        }

        if (strpos($videoID, ',') !== false) {
            $this->set('playlist', $videoID);
        }

        if ($this->startTimeEnabled == 1 && ($this->startTime === '0' || $this->startTime)) {
            $this->set('startSeconds', $this->convertStringToSeconds($this->startTime));
        } elseif (isset($query['t']) === true && empty($query['t']) === false) {
            $this->set('startSeconds', $this->convertStringToSeconds($query['t']));
        }

        $this->set('videoID', $videoID);
        $this->set('playListID', $playListID);
    }

    public function convertStringToSeconds($string)
    {
        if (preg_match_all('/(\d+)(h|m|s)/i', $string, $matches)) {
            $h = (($key = array_search('h', $matches[2])) !== false) ? (int) $matches[1][$key] : 0;
            $m = (($key = array_search('m', $matches[2])) !== false) ? (int) $matches[1][$key] : 0;
            $s = (($key = array_search('s', $matches[2])) !== false) ? (int) $matches[1][$key] : 0;
            $seconds = ($h * 3600) + ($m * 60) + $s;
        } else {
            $pieces = array_reverse(explode(':', $string));
            $seconds = 0;
            $multipliers = [1, 60, 3600];
            foreach ($multipliers as $key => $multiplier) {
                if (array_key_exists($key, $pieces)) {
                    $seconds += (int) $pieces[$key] * $multiplier;
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
        $data += [
            'title' => '',

            'videoURL' => '',

            'sizing' => null,
            'vHeight' => null,
            'vWidth' => null,

            'controls' => false,
            'modestbranding' => false,
            'showCaptions' => false,

            'color' => null,

            'rel' => false,
            'iv_load_policy' => false,
            'autoplay' => false,
            'loopEnd' => false,
            'startTimeEnabled' => false,
            'startTime' => '',

            'noCookie' => false,

            'lazyLoad' => false
        ];

        $args = [
            'title' => trim($data['title']),

            'videoURL' => trim($data['videoURL']),

            'sizing' => $data['sizing'],

            'controls' => $data['controls'] ? 1 : 0,
            'modestbranding' => $data['modestbranding'] ? 1 : 0,
            'showCaptions' => $data['showCaptions'] ? 1 : 0,

            'color' => $data['color'],

            'rel' => $data['rel'] ? 1 : 0,
            'iv_load_policy' => $data['iv_load_policy'] ? 3 : 1,
            'autoplay' => $data['autoplay'] ? 1 : 0,
            'loopEnd' => $data['loopEnd'] ? 1 : 0,

            'startTimeEnabled' => $data['startTimeEnabled'] ? 1 : 0,
            'startTime' => trim($data['startTime']),

            'noCookie' => $data['noCookie'] ? 1 : 0,

            'lazyLoad' => $data['lazyLoad'] ? 1 : 0
        ];
        if ($args['sizing'] === 'fixed') {
            $args += [
                'vWidth' => trim($data['vWidth']),
                'vHeight' => trim($data['vHeight']),
            ];
        } else {
            $args += [
                'vWidth' => '',
                'vHeight' => '',
            ];
        }

        parent::save($args);
    }
}
