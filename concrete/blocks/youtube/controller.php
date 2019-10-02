<?php

namespace Concrete\Block\Youtube;

use Concrete\Core\Block\BlockController;

class controller extends BlockController
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
        parse_str($url['query'], $params);
        $videoID = end($pathParts);
        $playListID = '';

        if (isset($url['query'])) {
            parse_str($url['query'], $query);

            if (isset($query['list'])) {
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

        if (false !== strpos($videoID, ',')) {
            $this->set('playlist', $videoID);
        }

        if (1 == $this->startTimeEnabled && ('0' === $this->startTime || $this->startTime)) {
            $this->set('startSeconds', $this->convertStringToSeconds($this->startTime));
        } elseif (!empty($params['t'])) {
            $this->set('startSeconds', $this->convertStringToSeconds($params['t']));
        }

        $this->set('videoID', $videoID);
        $this->set('playListID', $playListID);
    }

    public function convertStringToSeconds($string)
    {
        if (preg_match_all('/(\d+)(h|m|s)/i', $string, $matches)) {
            $h = (false !== ($key = array_search('h', $matches[2]))) ? (int) $matches[1][$key] : 0;
            $m = (false !== ($key = array_search('m', $matches[2]))) ? (int) $matches[1][$key] : 0;
            $s = (false !== ($key = array_search('s', $matches[2]))) ? (int) $matches[1][$key] : 0;
            $seconds = ($h * 3600) + ($m * 60) + $s;
        } else {
            $pieces = array_reverse(explode(':', $string));
            $seconds = 0;
            $multipliers = array(1, 60, 3600);
            foreach ($multipliers as $key => $multiplier) {
                if (\array_key_exists($key, $pieces)) {
                    $seconds += (int) $pieces[$key] * $multiplier;
                }
            }
        }
        if (0 === $seconds || $seconds > 0) {
            return $seconds;
        }

        return false;
    }

    public function save($data)
    {
        $data += array(
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
        );

        $args = array(
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
        );
        if ('fixed' === $args['sizing']) {
            $args += array(
                'vWidth' => trim($data['vWidth']),
                'vHeight' => trim($data['vHeight']),
            );
        } else {
            $args += array(
                'vWidth' => '',
                'vHeight' => '',
            );
        }

        parent::save($args);
    }
}
