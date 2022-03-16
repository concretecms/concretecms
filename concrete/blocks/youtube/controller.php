<?php

namespace Concrete\Block\Youtube;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $videoURL;

    /**
     * @var string|null
     */
    public $vWidth;

    /**
     * @var string|null
     */
    public $vHeight;

    protected $btTable = 'btYouTube';

    /**
     * @var string|int
     */
    protected $btInterfaceWidth = '400';

    /**
     * @var string|int
     */
    protected $btInterfaceHeight = '490';

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Embeds a YouTube Video in your web page.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('YouTube Video');
    }

    /**
     * @return void
     */
    public function edit()
    {
        if ($this->vWidth || $this->vHeight) {
            $this->set('sizing', 'fixed');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::VIDEO,
        ];
    }

    /**
     * @return void
     */
    public function view()
    {
        $playListID = '';
        $videoID = '';
        $query = [];

        $url = parse_url($this->videoURL ?? '');
        if (is_array($url) && isset($url['path'])) {
            $pathParts = explode('/', rtrim($url['path'], '/'));
            $videoID = end($pathParts);
            if (isset($url['query']) === true) {
                parse_str($url['query'], $query);

                if (isset($query['list']) === true) {
                    $playListID = $query['list'];
                    $videoID = '';
                } else {
                    $videoID = $query['v'] ?? $videoID;
                    $videoID = strtok($videoID, '?');
                }
            }
        }

        if (isset($this->noCookie) && $this->noCookie) {
            $this->set('youtubeDomain', 'www.youtube-nocookie.com');
        } else {
            $this->set('youtubeDomain', 'www.youtube.com');
        }

        if (is_string($videoID) && strpos($videoID, ',') !== false) {
            $this->set('playlist', $videoID);
        }

        if (isset($this->startTimeEnabled) && $this->startTimeEnabled == 1 && isset($this->startTime)) {
            $this->set('startSeconds', $this->convertStringToSeconds($this->startTime));
        } elseif (!empty($query) && isset($query['t']) === true && empty($query['t']) === false) {
            $this->set('startSeconds', $this->convertStringToSeconds($query['t']));
        }

        $this->set('videoID', (string) $videoID);
        $this->set('playListID', $playListID);
    }

    /**
     * @param string $string
     *
     * @return false|float|int
     */
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

    /**
     * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
     *
     * @param array<string,mixed> $data
     *
     * @return void
     */
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

            'lazyLoad' => false,
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

            'lazyLoad' => $data['lazyLoad'] ? 1 : 0,
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
