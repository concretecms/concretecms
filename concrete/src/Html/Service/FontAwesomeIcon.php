<?php

namespace Concrete\Core\Html\Service;

use HtmlObject\Element;

/**
 * Helper class to render a Font Awesome 5 icon.
 * Accepts font awesome 4 icon name for backward compatibility.
 */
class FontAwesomeIcon
{
    public const PREFIX_SOLID = 'fas';
    public const PREFIX_BRANDS = 'fab';
    public const PREFIX_REGULAR = 'far';
    public const PREFIX_LIGHT = 'fal';
    public const PREFIX_DUOTONE = 'fad';

    /** @var string */
    protected $name;
    /** @var string */
    protected $prefix;
    /** @var string */
    protected $size;
    /** @var bool */
    protected $fixedWidth = false;
    /** @var int */
    protected $rotate;
    /** @var string */
    protected $flip;
    /** @var bool */
    protected $spin = false;
    /** @var bool */
    protected $pulse = false;
    /** @var bool */
    protected $bordered = false;
    /** @var string */
    protected $pull;
    /** @var bool */
    protected $pro = false;
    /** @var bool */
    protected $migrateOldName = true;
    /** @link https://fontawesome.com/v5.15/how-to-use/on-the-web/setup/upgrading-from-version-4#name-changes */
    protected $migrateList = [
        '500px' => ['name' => '500px', 'prefix' => 'fab'],
        'address-book-o' => ['name' => 'address-book', 'prefix' => 'far'],
        'address-card-o' => ['name' => 'address-card', 'prefix' => 'far'],
        'adn' => ['name' => 'adn', 'prefix' => 'fab'],
        'amazon' => ['name' => 'amazon', 'prefix' => 'fab'],
        'android' => ['name' => 'android', 'prefix' => 'fab'],
        'angellist' => ['name' => 'angellist', 'prefix' => 'fab'],
        'apple' => ['name' => 'apple', 'prefix' => 'fab'],
        'area-chart' => ['name' => 'chart-area', 'prefix' => 'fas'],
        'arrow-circle-o-down' => ['name' => 'arrow-alt-circle-down', 'prefix' => 'far'],
        'arrow-circle-o-left' => ['name' => 'arrow-alt-circle-left', 'prefix' => 'far'],
        'arrow-circle-o-right' => ['name' => 'arrow-alt-circle-right', 'prefix' => 'far'],
        'arrow-circle-o-up' => ['name' => 'arrow-alt-circle-up', 'prefix' => 'far'],
        'arrows' => ['name' => 'arrows-alt', 'prefix' => 'fas'],
        'arrows-alt' => ['name' => 'expand-arrows-alt', 'prefix' => 'fas'],
        'arrows-h' => ['name' => 'arrows-alt-h', 'prefix' => 'fas'],
        'arrows-v' => ['name' => 'arrows-alt-v', 'prefix' => 'fas'],
        'asl-interpreting' => ['name' => 'american-sign-language-interpreting', 'prefix' => 'fas'],
        'automobile' => ['name' => 'car', 'prefix' => 'fas'],
        'bandcamp' => ['name' => 'bandcamp', 'prefix' => 'fab'],
        'bank' => ['name' => 'university', 'prefix' => 'fas'],
        'bar-chart' => ['name' => 'chart-bar', 'prefix' => 'far'],
        'bar-chart-o' => ['name' => 'chart-bar', 'prefix' => 'far'],
        'bathtub' => ['name' => 'bath', 'prefix' => 'fas'],
        'battery' => ['name' => 'battery-full', 'prefix' => 'fas'],
        'battery-0' => ['name' => 'battery-empty', 'prefix' => 'fas'],
        'battery-1' => ['name' => 'battery-quarter', 'prefix' => 'fas'],
        'battery-2' => ['name' => 'battery-half', 'prefix' => 'fas'],
        'battery-3' => ['name' => 'battery-three-quarters', 'prefix' => 'fas'],
        'battery-4' => ['name' => 'battery-full', 'prefix' => 'fas'],
        'behance' => ['name' => 'behance', 'prefix' => 'fab'],
        'behance-square' => ['name' => 'behance-square', 'prefix' => 'fab'],
        'bell-o' => ['name' => 'bell', 'prefix' => 'far'],
        'bell-slash-o' => ['name' => 'bell-slash', 'prefix' => 'far'],
        'bitbucket' => ['name' => 'bitbucket', 'prefix' => 'fab'],
        'bitbucket-square' => ['name' => 'bitbucket', 'prefix' => 'fab'],
        'bitcoin' => ['name' => 'btc', 'prefix' => 'fab'],
        'black-tie' => ['name' => 'black-tie', 'prefix' => 'fab'],
        'bluetooth' => ['name' => 'bluetooth', 'prefix' => 'fab'],
        'bluetooth-b' => ['name' => 'bluetooth-b', 'prefix' => 'fab'],
        'bookmark-o' => ['name' => 'bookmark', 'prefix' => 'far'],
        'btc' => ['name' => 'btc', 'prefix' => 'fab'],
        'building-o' => ['name' => 'building', 'prefix' => 'far'],
        'buysellads' => ['name' => 'buysellads', 'prefix' => 'fab'],
        'cab' => ['name' => 'taxi', 'prefix' => 'fas'],
        'calendar' => ['name' => 'calendar-alt', 'prefix' => 'fas'],
        'calendar-check-o' => ['name' => 'calendar-check', 'prefix' => 'far'],
        'calendar-minus-o' => ['name' => 'calendar-minus', 'prefix' => 'far'],
        'calendar-o' => ['name' => 'calendar', 'prefix' => 'far'],
        'calendar-plus-o' => ['name' => 'calendar-plus', 'prefix' => 'far'],
        'calendar-times-o' => ['name' => 'calendar-times', 'prefix' => 'far'],
        'caret-square-o-down' => ['name' => 'caret-square-down', 'prefix' => 'far'],
        'caret-square-o-left' => ['name' => 'caret-square-left', 'prefix' => 'far'],
        'caret-square-o-right' => ['name' => 'caret-square-right', 'prefix' => 'far'],
        'caret-square-o-up' => ['name' => 'caret-square-up', 'prefix' => 'far'],
        'cc' => ['name' => 'closed-captioning', 'prefix' => 'far'],
        'cc-amex' => ['name' => 'cc-amex', 'prefix' => 'fab'],
        'cc-diners-club' => ['name' => 'cc-diners-club', 'prefix' => 'fab'],
        'cc-discover' => ['name' => 'cc-discover', 'prefix' => 'fab'],
        'cc-jcb' => ['name' => 'cc-jcb', 'prefix' => 'fab'],
        'cc-mastercard' => ['name' => 'cc-mastercard', 'prefix' => 'fab'],
        'cc-paypal' => ['name' => 'cc-paypal', 'prefix' => 'fab'],
        'cc-stripe' => ['name' => 'cc-stripe', 'prefix' => 'fab'],
        'cc-visa' => ['name' => 'cc-visa', 'prefix' => 'fab'],
        'chain' => ['name' => 'link', 'prefix' => 'fas'],
        'chain-broken' => ['name' => 'unlink', 'prefix' => 'fas'],
        'check-circle-o' => ['name' => 'check-circle', 'prefix' => 'far'],
        'check-square-o' => ['name' => 'check-square', 'prefix' => 'far'],
        'chrome' => ['name' => 'chrome', 'prefix' => 'fab'],
        'circle-o' => ['name' => 'circle', 'prefix' => 'far'],
        'circle-o-notch' => ['name' => 'circle-notch', 'prefix' => 'fas'],
        'circle-thin' => ['name' => 'circle', 'prefix' => 'far'],
        'clipboard' => ['name' => 'clipboard', 'prefix' => 'far'],
        'clock-o' => ['name' => 'clock', 'prefix' => 'far'],
        'clone' => ['name' => 'clone', 'prefix' => 'far'],
        'close' => ['name' => 'times', 'prefix' => 'fas'],
        'cloud-download' => ['name' => 'cloud-download-alt', 'prefix' => 'fas'],
        'cloud-upload' => ['name' => 'cloud-upload-alt', 'prefix' => 'fas'],
        'cny' => ['name' => 'yen-sign', 'prefix' => 'fas'],
        'code-fork' => ['name' => 'code-branch', 'prefix' => 'fas'],
        'codepen' => ['name' => 'codepen', 'prefix' => 'fab'],
        'codiepie' => ['name' => 'codiepie', 'prefix' => 'fab'],
        'comment-o' => ['name' => 'comment', 'prefix' => 'far'],
        'commenting' => ['name' => 'comment-dots', 'prefix' => 'fas'],
        'commenting-o' => ['name' => 'comment-dots', 'prefix' => 'far'],
        'comments-o' => ['name' => 'comments', 'prefix' => 'far'],
        'compass' => ['name' => 'compass', 'prefix' => 'far'],
        'connectdevelop' => ['name' => 'connectdevelop', 'prefix' => 'fab'],
        'contao' => ['name' => 'contao', 'prefix' => 'fab'],
        'copyright' => ['name' => 'copyright', 'prefix' => 'far'],
        'creative-commons' => ['name' => 'creative-commons', 'prefix' => 'fab'],
        'credit-card' => ['name' => 'credit-card', 'prefix' => 'far'],
        'credit-card-alt' => ['name' => 'credit-card', 'prefix' => 'fas'],
        'css3' => ['name' => 'css3', 'prefix' => 'fab'],
        'cutlery' => ['name' => 'utensils', 'prefix' => 'fas'],
        'dashboard' => ['name' => 'tachometer-alt', 'prefix' => 'fas'],
        'dashcube' => ['name' => 'dashcube', 'prefix' => 'fab'],
        'deafness' => ['name' => 'deaf', 'prefix' => 'fas'],
        'dedent' => ['name' => 'outdent', 'prefix' => 'fas'],
        'delicious' => ['name' => 'delicious', 'prefix' => 'fab'],
        'deviantart' => ['name' => 'deviantart', 'prefix' => 'fab'],
        'diamond' => ['name' => 'gem', 'prefix' => 'far'],
        'digg' => ['name' => 'digg', 'prefix' => 'fab'],
        'dollar' => ['name' => 'dollar-sign', 'prefix' => 'fas'],
        'dot-circle-o' => ['name' => 'dot-circle', 'prefix' => 'far'],
        'dribbble' => ['name' => 'dribbble', 'prefix' => 'fab'],
        'drivers-license' => ['name' => 'id-card', 'prefix' => 'fas'],
        'drivers-license-o' => ['name' => 'id-card', 'prefix' => 'far'],
        'dropbox' => ['name' => 'dropbox', 'prefix' => 'fab'],
        'drupal' => ['name' => 'drupal', 'prefix' => 'fab'],
        'edge' => ['name' => 'edge', 'prefix' => 'fab'],
        'eercast' => ['name' => 'sellcast', 'prefix' => 'fab'],
        'empire' => ['name' => 'empire', 'prefix' => 'fab'],
        'envelope-o' => ['name' => 'envelope', 'prefix' => 'far'],
        'envelope-open-o' => ['name' => 'envelope-open', 'prefix' => 'far'],
        'envira' => ['name' => 'envira', 'prefix' => 'fab'],
        'etsy' => ['name' => 'etsy', 'prefix' => 'fab'],
        'eur' => ['name' => 'euro-sign', 'prefix' => 'fas'],
        'euro' => ['name' => 'euro-sign', 'prefix' => 'fas'],
        'exchange' => ['name' => 'exchange-alt', 'prefix' => 'fas'],
        'expeditedssl' => ['name' => 'expeditedssl', 'prefix' => 'fab'],
        'external-link' => ['name' => 'external-link-alt', 'prefix' => 'fas'],
        'external-link-square' => ['name' => 'external-link-square-alt', 'prefix' => 'fas'],
        'eye' => ['name' => 'eye', 'prefix' => 'far'],
        'eye-slash' => ['name' => 'eye-slash', 'prefix' => 'far'],
        'eyedropper' => ['name' => 'eye-dropper', 'prefix' => 'fas'],
        'fa' => ['name' => 'font-awesome', 'prefix' => 'fab'],
        'facebook' => ['name' => 'facebook-f', 'prefix' => 'fab'],
        'facebook-f' => ['name' => 'facebook-f', 'prefix' => 'fab'],
        'facebook-official' => ['name' => 'facebook', 'prefix' => 'fab'],
        'facebook-square' => ['name' => 'facebook-square', 'prefix' => 'fab'],
        'feed' => ['name' => 'rss', 'prefix' => 'fas'],
        'file-archive-o' => ['name' => 'file-archive', 'prefix' => 'far'],
        'file-audio-o' => ['name' => 'file-audio', 'prefix' => 'far'],
        'file-code-o' => ['name' => 'file-code', 'prefix' => 'far'],
        'file-excel-o' => ['name' => 'file-excel', 'prefix' => 'far'],
        'file-image-o' => ['name' => 'file-image', 'prefix' => 'far'],
        'file-movie-o' => ['name' => 'file-video', 'prefix' => 'far'],
        'file-o' => ['name' => 'file', 'prefix' => 'far'],
        'file-pdf-o' => ['name' => 'file-pdf', 'prefix' => 'far'],
        'file-photo-o' => ['name' => 'file-image', 'prefix' => 'far'],
        'file-picture-o' => ['name' => 'file-image', 'prefix' => 'far'],
        'file-powerpoint-o' => ['name' => 'file-powerpoint', 'prefix' => 'far'],
        'file-sound-o' => ['name' => 'file-audio', 'prefix' => 'far'],
        'file-text' => ['name' => 'file-alt', 'prefix' => 'fas'],
        'file-text-o' => ['name' => 'file-alt', 'prefix' => 'far'],
        'file-video-o' => ['name' => 'file-video', 'prefix' => 'far'],
        'file-word-o' => ['name' => 'file-word', 'prefix' => 'far'],
        'file-zip-o' => ['name' => 'file-archive', 'prefix' => 'far'],
        'files-o' => ['name' => 'copy', 'prefix' => 'far'],
        'firefox' => ['name' => 'firefox', 'prefix' => 'fab'],
        'first-order' => ['name' => 'first-order', 'prefix' => 'fab'],
        'flag-o' => ['name' => 'flag', 'prefix' => 'far'],
        'flash' => ['name' => 'bolt', 'prefix' => 'fas'],
        'flickr' => ['name' => 'flickr', 'prefix' => 'fab'],
        'floppy-o' => ['name' => 'save', 'prefix' => 'far'],
        'folder-o' => ['name' => 'folder', 'prefix' => 'far'],
        'folder-open-o' => ['name' => 'folder-open', 'prefix' => 'far'],
        'font-awesome' => ['name' => 'font-awesome', 'prefix' => 'fab'],
        'fonticons' => ['name' => 'fonticons', 'prefix' => 'fab'],
        'fort-awesome' => ['name' => 'fort-awesome', 'prefix' => 'fab'],
        'forumbee' => ['name' => 'forumbee', 'prefix' => 'fab'],
        'foursquare' => ['name' => 'foursquare', 'prefix' => 'fab'],
        'free-code-camp' => ['name' => 'free-code-camp', 'prefix' => 'fab'],
        'frown-o' => ['name' => 'frown', 'prefix' => 'far'],
        'futbol-o' => ['name' => 'futbol', 'prefix' => 'far'],
        'gbp' => ['name' => 'pound-sign', 'prefix' => 'fas'],
        'ge' => ['name' => 'empire', 'prefix' => 'fab'],
        'gear' => ['name' => 'cog', 'prefix' => 'fas'],
        'gears' => ['name' => 'cogs', 'prefix' => 'fas'],
        'get-pocket' => ['name' => 'get-pocket', 'prefix' => 'fab'],
        'gg' => ['name' => 'gg', 'prefix' => 'fab'],
        'gg-circle' => ['name' => 'gg-circle', 'prefix' => 'fab'],
        'git' => ['name' => 'git', 'prefix' => 'fab'],
        'git-square' => ['name' => 'git-square', 'prefix' => 'fab'],
        'github' => ['name' => 'github', 'prefix' => 'fab'],
        'github-alt' => ['name' => 'github-alt', 'prefix' => 'fab'],
        'github-square' => ['name' => 'github-square', 'prefix' => 'fab'],
        'gitlab' => ['name' => 'gitlab', 'prefix' => 'fab'],
        'gittip' => ['name' => 'gratipay', 'prefix' => 'fab'],
        'glass' => ['name' => 'glass-martini', 'prefix' => 'fas'],
        'glide' => ['name' => 'glide', 'prefix' => 'fab'],
        'glide-g' => ['name' => 'glide-g', 'prefix' => 'fab'],
        'google' => ['name' => 'google', 'prefix' => 'fab'],
        'google-plus' => ['name' => 'google-plus-g', 'prefix' => 'fab'],
        'google-plus-circle' => ['name' => 'google-plus', 'prefix' => 'fab'],
        'google-plus-official' => ['name' => 'google-plus', 'prefix' => 'fab'],
        'google-plus-square' => ['name' => 'google-plus-square', 'prefix' => 'fab'],
        'google-wallet' => ['name' => 'google-wallet', 'prefix' => 'fab'],
        'gratipay' => ['name' => 'gratipay', 'prefix' => 'fab'],
        'grav' => ['name' => 'grav', 'prefix' => 'fab'],
        'group' => ['name' => 'users', 'prefix' => 'fas'],
        'hacker-news' => ['name' => 'hacker-news', 'prefix' => 'fab'],
        'hand-grab-o' => ['name' => 'hand-rock', 'prefix' => 'far'],
        'hand-lizard-o' => ['name' => 'hand-lizard', 'prefix' => 'far'],
        'hand-o-down' => ['name' => 'hand-point-down', 'prefix' => 'far'],
        'hand-o-left' => ['name' => 'hand-point-left', 'prefix' => 'far'],
        'hand-o-right' => ['name' => 'hand-point-right', 'prefix' => 'far'],
        'hand-o-up' => ['name' => 'hand-point-up', 'prefix' => 'far'],
        'hand-paper-o' => ['name' => 'hand-paper', 'prefix' => 'far'],
        'hand-peace-o' => ['name' => 'hand-peace', 'prefix' => 'far'],
        'hand-pointer-o' => ['name' => 'hand-pointer', 'prefix' => 'far'],
        'hand-rock-o' => ['name' => 'hand-rock', 'prefix' => 'far'],
        'hand-scissors-o' => ['name' => 'hand-scissors', 'prefix' => 'far'],
        'hand-spock-o' => ['name' => 'hand-spock', 'prefix' => 'far'],
        'hand-stop-o' => ['name' => 'hand-paper', 'prefix' => 'far'],
        'handshake-o' => ['name' => 'handshake', 'prefix' => 'far'],
        'hard-of-hearing' => ['name' => 'deaf', 'prefix' => 'fas'],
        'hdd-o' => ['name' => 'hdd', 'prefix' => 'far'],
        'header' => ['name' => 'heading', 'prefix' => 'fas'],
        'heart-o' => ['name' => 'heart', 'prefix' => 'far'],
        'hospital-o' => ['name' => 'hospital', 'prefix' => 'far'],
        'hotel' => ['name' => 'bed', 'prefix' => 'fas'],
        'hourglass-1' => ['name' => 'hourglass-start', 'prefix' => 'fas'],
        'hourglass-2' => ['name' => 'hourglass-half', 'prefix' => 'fas'],
        'hourglass-3' => ['name' => 'hourglass-end', 'prefix' => 'fas'],
        'hourglass-o' => ['name' => 'hourglass', 'prefix' => 'far'],
        'houzz' => ['name' => 'houzz', 'prefix' => 'fab'],
        'html5' => ['name' => 'html5', 'prefix' => 'fab'],
        'id-badge' => ['name' => 'id-badge', 'prefix' => 'far'],
        'id-card-o' => ['name' => 'id-card', 'prefix' => 'far'],
        'ils' => ['name' => 'shekel-sign', 'prefix' => 'fas'],
        'image' => ['name' => 'image', 'prefix' => 'far'],
        'imdb' => ['name' => 'imdb', 'prefix' => 'fab'],
        'inr' => ['name' => 'rupee-sign', 'prefix' => 'fas'],
        'instagram' => ['name' => 'instagram', 'prefix' => 'fab'],
        'institution' => ['name' => 'university', 'prefix' => 'fas'],
        'internet-explorer' => ['name' => 'internet-explorer', 'prefix' => 'fab'],
        'intersex' => ['name' => 'transgender', 'prefix' => 'fas'],
        'ioxhost' => ['name' => 'ioxhost', 'prefix' => 'fab'],
        'joomla' => ['name' => 'joomla', 'prefix' => 'fab'],
        'jpy' => ['name' => 'yen-sign', 'prefix' => 'fas'],
        'jsfiddle' => ['name' => 'jsfiddle', 'prefix' => 'fab'],
        'keyboard-o' => ['name' => 'keyboard', 'prefix' => 'far'],
        'krw' => ['name' => 'won-sign', 'prefix' => 'fas'],
        'lastfm' => ['name' => 'lastfm', 'prefix' => 'fab'],
        'lastfm-square' => ['name' => 'lastfm-square', 'prefix' => 'fab'],
        'leanpub' => ['name' => 'leanpub', 'prefix' => 'fab'],
        'legal' => ['name' => 'gavel', 'prefix' => 'fas'],
        'lemon-o' => ['name' => 'lemon', 'prefix' => 'far'],
        'level-down' => ['name' => 'level-down-alt', 'prefix' => 'fas'],
        'level-up' => ['name' => 'level-up-alt', 'prefix' => 'fas'],
        'life-bouy' => ['name' => 'life-ring', 'prefix' => 'far'],
        'life-buoy' => ['name' => 'life-ring', 'prefix' => 'far'],
        'life-ring' => ['name' => 'life-ring', 'prefix' => 'far'],
        'life-saver' => ['name' => 'life-ring', 'prefix' => 'far'],
        'lightbulb-o' => ['name' => 'lightbulb', 'prefix' => 'far'],
        'line-chart' => ['name' => 'chart-line', 'prefix' => 'fas'],
        'linkedin' => ['name' => 'linkedin-in', 'prefix' => 'fab'],
        'linkedin-square' => ['name' => 'linkedin', 'prefix' => 'fab'],
        'linode' => ['name' => 'linode', 'prefix' => 'fab'],
        'linux' => ['name' => 'linux', 'prefix' => 'fab'],
        'list-alt' => ['name' => 'list-alt', 'prefix' => 'far'],
        'long-arrow-down' => ['name' => 'long-arrow-alt-down', 'prefix' => 'fas'],
        'long-arrow-left' => ['name' => 'long-arrow-alt-left', 'prefix' => 'fas'],
        'long-arrow-right' => ['name' => 'long-arrow-alt-right', 'prefix' => 'fas'],
        'long-arrow-up' => ['name' => 'long-arrow-alt-up', 'prefix' => 'fas'],
        'mail-forward' => ['name' => 'share', 'prefix' => 'fas'],
        'mail-reply' => ['name' => 'reply', 'prefix' => 'fas'],
        'mail-reply-all' => ['name' => 'reply-all', 'prefix' => 'fas'],
        'map-marker' => ['name' => 'map-marker-alt', 'prefix' => 'fas'],
        'map-o' => ['name' => 'map', 'prefix' => 'far'],
        'maxcdn' => ['name' => 'maxcdn', 'prefix' => 'fab'],
        'meanpath' => ['name' => 'font-awesome', 'prefix' => 'fab'],
        'medium' => ['name' => 'medium', 'prefix' => 'fab'],
        'meetup' => ['name' => 'meetup', 'prefix' => 'fab'],
        'meh-o' => ['name' => 'meh', 'prefix' => 'far'],
        'minus-square-o' => ['name' => 'minus-square', 'prefix' => 'far'],
        'mixcloud' => ['name' => 'mixcloud', 'prefix' => 'fab'],
        'mobile' => ['name' => 'mobile-alt', 'prefix' => 'fas'],
        'mobile-phone' => ['name' => 'mobile-alt', 'prefix' => 'fas'],
        'modx' => ['name' => 'modx', 'prefix' => 'fab'],
        'money' => ['name' => 'money-bill-alt', 'prefix' => 'far'],
        'moon-o' => ['name' => 'moon', 'prefix' => 'far'],
        'mortar-board' => ['name' => 'graduation-cap', 'prefix' => 'fas'],
        'navicon' => ['name' => 'bars', 'prefix' => 'fas'],
        'newspaper-o' => ['name' => 'newspaper', 'prefix' => 'far'],
        'object-group' => ['name' => 'object-group', 'prefix' => 'far'],
        'object-ungroup' => ['name' => 'object-ungroup', 'prefix' => 'far'],
        'odnoklassniki' => ['name' => 'odnoklassniki', 'prefix' => 'fab'],
        'odnoklassniki-square' => ['name' => 'odnoklassniki-square', 'prefix' => 'fab'],
        'opencart' => ['name' => 'opencart', 'prefix' => 'fab'],
        'openid' => ['name' => 'openid', 'prefix' => 'fab'],
        'opera' => ['name' => 'opera', 'prefix' => 'fab'],
        'optin-monster' => ['name' => 'optin-monster', 'prefix' => 'fab'],
        'pagelines' => ['name' => 'pagelines', 'prefix' => 'fab'],
        'paper-plane-o' => ['name' => 'paper-plane', 'prefix' => 'far'],
        'paste' => ['name' => 'clipboard', 'prefix' => 'far'],
        'pause-circle-o' => ['name' => 'pause-circle', 'prefix' => 'far'],
        'paypal' => ['name' => 'paypal', 'prefix' => 'fab'],
        'pencil' => ['name' => 'pencil-alt', 'prefix' => 'fas'],
        'pencil-square' => ['name' => 'pen-square', 'prefix' => 'fas'],
        'pencil-square-o' => ['name' => 'edit', 'prefix' => 'far'],
        'photo' => ['name' => 'image', 'prefix' => 'far'],
        'picture-o' => ['name' => 'image', 'prefix' => 'far'],
        'pie-chart' => ['name' => 'chart-pie', 'prefix' => 'fas'],
        'pied-piper' => ['name' => 'pied-piper', 'prefix' => 'fab'],
        'pied-piper-alt' => ['name' => 'pied-piper-alt', 'prefix' => 'fab'],
        'pied-piper-pp' => ['name' => 'pied-piper-pp', 'prefix' => 'fab'],
        'pinterest' => ['name' => 'pinterest', 'prefix' => 'fab'],
        'pinterest-p' => ['name' => 'pinterest-p', 'prefix' => 'fab'],
        'pinterest-square' => ['name' => 'pinterest-square', 'prefix' => 'fab'],
        'play-circle-o' => ['name' => 'play-circle', 'prefix' => 'far'],
        'plus-square-o' => ['name' => 'plus-square', 'prefix' => 'far'],
        'product-hunt' => ['name' => 'product-hunt', 'prefix' => 'fab'],
        'qq' => ['name' => 'qq', 'prefix' => 'fab'],
        'question-circle-o' => ['name' => 'question-circle', 'prefix' => 'far'],
        'quora' => ['name' => 'quora', 'prefix' => 'fab'],
        'ra' => ['name' => 'rebel', 'prefix' => 'fab'],
        'ravelry' => ['name' => 'ravelry', 'prefix' => 'fab'],
        'rebel' => ['name' => 'rebel', 'prefix' => 'fab'],
        'reddit' => ['name' => 'reddit', 'prefix' => 'fab'],
        'reddit-alien' => ['name' => 'reddit-alien', 'prefix' => 'fab'],
        'reddit-square' => ['name' => 'reddit-square', 'prefix' => 'fab'],
        'refresh' => ['name' => 'sync', 'prefix' => 'fas'],
        'registered' => ['name' => 'registered', 'prefix' => 'far'],
        'remove' => ['name' => 'times', 'prefix' => 'fas'],
        'renren' => ['name' => 'renren', 'prefix' => 'fab'],
        'reorder' => ['name' => 'bars', 'prefix' => 'fas'],
        'repeat' => ['name' => 'redo', 'prefix' => 'fas'],
        'resistance' => ['name' => 'rebel', 'prefix' => 'fab'],
        'rmb' => ['name' => 'yen-sign', 'prefix' => 'fas'],
        'rotate-left' => ['name' => 'undo', 'prefix' => 'fas'],
        'rotate-right' => ['name' => 'redo', 'prefix' => 'fas'],
        'rouble' => ['name' => 'ruble-sign', 'prefix' => 'fas'],
        'rub' => ['name' => 'ruble-sign', 'prefix' => 'fas'],
        'ruble' => ['name' => 'ruble-sign', 'prefix' => 'fas'],
        'rupee' => ['name' => 'rupee-sign', 'prefix' => 'fas'],
        's15' => ['name' => 'bath', 'prefix' => 'fas'],
        'safari' => ['name' => 'safari', 'prefix' => 'fab'],
        'scissors' => ['name' => 'cut', 'prefix' => 'fas'],
        'scribd' => ['name' => 'scribd', 'prefix' => 'fab'],
        'sellsy' => ['name' => 'sellsy', 'prefix' => 'fab'],
        'send' => ['name' => 'paper-plane', 'prefix' => 'fas'],
        'send-o' => ['name' => 'paper-plane', 'prefix' => 'far'],
        'share-square-o' => ['name' => 'share-square', 'prefix' => 'far'],
        'shekel' => ['name' => 'shekel-sign', 'prefix' => 'fas'],
        'sheqel' => ['name' => 'shekel-sign', 'prefix' => 'fas'],
        'shield' => ['name' => 'shield-alt', 'prefix' => 'fas'],
        'shirtsinbulk' => ['name' => 'shirtsinbulk', 'prefix' => 'fab'],
        'sign-in' => ['name' => 'sign-in-alt', 'prefix' => 'fas'],
        'sign-out' => ['name' => 'sign-out-alt', 'prefix' => 'fas'],
        'signing' => ['name' => 'sign-language', 'prefix' => 'fas'],
        'simplybuilt' => ['name' => 'simplybuilt', 'prefix' => 'fab'],
        'skyatlas' => ['name' => 'skyatlas', 'prefix' => 'fab'],
        'skype' => ['name' => 'skype', 'prefix' => 'fab'],
        'slack' => ['name' => 'slack', 'prefix' => 'fab'],
        'sliders' => ['name' => 'sliders-h', 'prefix' => 'fas'],
        'slideshare' => ['name' => 'slideshare', 'prefix' => 'fab'],
        'smile-o' => ['name' => 'smile', 'prefix' => 'far'],
        'snapchat' => ['name' => 'snapchat', 'prefix' => 'fab'],
        'snapchat-ghost' => ['name' => 'snapchat-ghost', 'prefix' => 'fab'],
        'snapchat-square' => ['name' => 'snapchat-square', 'prefix' => 'fab'],
        'snowflake-o' => ['name' => 'snowflake', 'prefix' => 'far'],
        'soccer-ball-o' => ['name' => 'futbol', 'prefix' => 'far'],
        'sort-alpha-asc' => ['name' => 'sort-alpha-down', 'prefix' => 'fas'],
        'sort-alpha-desc' => ['name' => 'sort-alpha-up', 'prefix' => 'fas'],
        'sort-amount-asc' => ['name' => 'sort-amount-down', 'prefix' => 'fas'],
        'sort-amount-desc' => ['name' => 'sort-amount-up', 'prefix' => 'fas'],
        'sort-asc' => ['name' => 'sort-up', 'prefix' => 'fas'],
        'sort-desc' => ['name' => 'sort-down', 'prefix' => 'fas'],
        'sort-numeric-asc' => ['name' => 'sort-numeric-down', 'prefix' => 'fas'],
        'sort-numeric-desc' => ['name' => 'sort-numeric-up', 'prefix' => 'fas'],
        'soundcloud' => ['name' => 'soundcloud', 'prefix' => 'fab'],
        'spoon' => ['name' => 'utensil-spoon', 'prefix' => 'fas'],
        'spotify' => ['name' => 'spotify', 'prefix' => 'fab'],
        'square-o' => ['name' => 'square', 'prefix' => 'far'],
        'stack-exchange' => ['name' => 'stack-exchange', 'prefix' => 'fab'],
        'stack-overflow' => ['name' => 'stack-overflow', 'prefix' => 'fab'],
        'star-half-empty' => ['name' => 'star-half', 'prefix' => 'far'],
        'star-half-full' => ['name' => 'star-half', 'prefix' => 'far'],
        'star-half-o' => ['name' => 'star-half', 'prefix' => 'far'],
        'star-o' => ['name' => 'star', 'prefix' => 'far'],
        'steam' => ['name' => 'steam', 'prefix' => 'fab'],
        'steam-square' => ['name' => 'steam-square', 'prefix' => 'fab'],
        'sticky-note-o' => ['name' => 'sticky-note', 'prefix' => 'far'],
        'stop-circle-o' => ['name' => 'stop-circle', 'prefix' => 'far'],
        'stumbleupon' => ['name' => 'stumbleupon', 'prefix' => 'fab'],
        'stumbleupon-circle' => ['name' => 'stumbleupon-circle', 'prefix' => 'fab'],
        'sun-o' => ['name' => 'sun', 'prefix' => 'far'],
        'superpowers' => ['name' => 'superpowers', 'prefix' => 'fab'],
        'support' => ['name' => 'life-ring', 'prefix' => 'far'],
        'tablet' => ['name' => 'tablet-alt', 'prefix' => 'fas'],
        'tachometer' => ['name' => 'tachometer-alt', 'prefix' => 'fas'],
        'telegram' => ['name' => 'telegram', 'prefix' => 'fab'],
        'television' => ['name' => 'tv', 'prefix' => 'fas'],
        'tencent-weibo' => ['name' => 'tencent-weibo', 'prefix' => 'fab'],
        'themeisle' => ['name' => 'themeisle', 'prefix' => 'fab'],
        'thermometer' => ['name' => 'thermometer-full', 'prefix' => 'fas'],
        'thermometer-0' => ['name' => 'thermometer-empty', 'prefix' => 'fas'],
        'thermometer-1' => ['name' => 'thermometer-quarter', 'prefix' => 'fas'],
        'thermometer-2' => ['name' => 'thermometer-half', 'prefix' => 'fas'],
        'thermometer-3' => ['name' => 'thermometer-three-quarters', 'prefix' => 'fas'],
        'thermometer-4' => ['name' => 'thermometer-full', 'prefix' => 'fas'],
        'thumb-tack' => ['name' => 'thumbtack', 'prefix' => 'fas'],
        'thumbs-o-down' => ['name' => 'thumbs-down', 'prefix' => 'far'],
        'thumbs-o-up' => ['name' => 'thumbs-up', 'prefix' => 'far'],
        'ticket' => ['name' => 'ticket-alt', 'prefix' => 'fas'],
        'times-circle-o' => ['name' => 'times-circle', 'prefix' => 'far'],
        'times-rectangle' => ['name' => 'window-close', 'prefix' => 'fas'],
        'times-rectangle-o' => ['name' => 'window-close', 'prefix' => 'far'],
        'toggle-down' => ['name' => 'caret-square-down', 'prefix' => 'far'],
        'toggle-left' => ['name' => 'caret-square-left', 'prefix' => 'far'],
        'toggle-right' => ['name' => 'caret-square-right', 'prefix' => 'far'],
        'toggle-up' => ['name' => 'caret-square-up', 'prefix' => 'far'],
        'trash' => ['name' => 'trash-alt', 'prefix' => 'fas'],
        'trash-o' => ['name' => 'trash-alt', 'prefix' => 'far'],
        'trello' => ['name' => 'trello', 'prefix' => 'fab'],
        'tripadvisor' => ['name' => 'tripadvisor', 'prefix' => 'fab'],
        'try' => ['name' => 'lira-sign', 'prefix' => 'fas'],
        'tumblr' => ['name' => 'tumblr', 'prefix' => 'fab'],
        'tumblr-square' => ['name' => 'tumblr-square', 'prefix' => 'fab'],
        'turkish-lira' => ['name' => 'lira-sign', 'prefix' => 'fas'],
        'twitch' => ['name' => 'twitch', 'prefix' => 'fab'],
        'twitter' => ['name' => 'twitter', 'prefix' => 'fab'],
        'twitter-square' => ['name' => 'twitter-square', 'prefix' => 'fab'],
        'unsorted' => ['name' => 'sort', 'prefix' => 'fas'],
        'usb' => ['name' => 'usb', 'prefix' => 'fab'],
        'usd' => ['name' => 'dollar-sign', 'prefix' => 'fas'],
        'user-circle-o' => ['name' => 'user-circle', 'prefix' => 'far'],
        'user-o' => ['name' => 'user', 'prefix' => 'far'],
        'vcard' => ['name' => 'address-card', 'prefix' => 'fas'],
        'vcard-o' => ['name' => 'address-card', 'prefix' => 'far'],
        'viacoin' => ['name' => 'viacoin', 'prefix' => 'fab'],
        'viadeo' => ['name' => 'viadeo', 'prefix' => 'fab'],
        'viadeo-square' => ['name' => 'viadeo-square', 'prefix' => 'fab'],
        'video-camera' => ['name' => 'video', 'prefix' => 'fas'],
        'vimeo' => ['name' => 'vimeo-v', 'prefix' => 'fab'],
        'vimeo-square' => ['name' => 'vimeo-square', 'prefix' => 'fab'],
        'vine' => ['name' => 'vine', 'prefix' => 'fab'],
        'vk' => ['name' => 'vk', 'prefix' => 'fab'],
        'volume-control-phone' => ['name' => 'phone-volume', 'prefix' => 'fas'],
        'warning' => ['name' => 'exclamation-triangle', 'prefix' => 'fas'],
        'wechat' => ['name' => 'weixin', 'prefix' => 'fab'],
        'weibo' => ['name' => 'weibo', 'prefix' => 'fab'],
        'weixin' => ['name' => 'weixin', 'prefix' => 'fab'],
        'whatsapp' => ['name' => 'whatsapp', 'prefix' => 'fab'],
        'wheelchair-alt' => ['name' => 'accessible-icon', 'prefix' => 'fab'],
        'wikipedia-w' => ['name' => 'wikipedia-w', 'prefix' => 'fab'],
        'window-close-o' => ['name' => 'window-close', 'prefix' => 'far'],
        'window-maximize' => ['name' => 'window-maximize', 'prefix' => 'far'],
        'window-restore' => ['name' => 'window-restore', 'prefix' => 'far'],
        'windows' => ['name' => 'windows', 'prefix' => 'fab'],
        'won' => ['name' => 'won-sign', 'prefix' => 'fas'],
        'wordpress' => ['name' => 'wordpress', 'prefix' => 'fab'],
        'wpbeginner' => ['name' => 'wpbeginner', 'prefix' => 'fab'],
        'wpexplorer' => ['name' => 'wpexplorer', 'prefix' => 'fab'],
        'wpforms' => ['name' => 'wpforms', 'prefix' => 'fab'],
        'xing' => ['name' => 'xing', 'prefix' => 'fab'],
        'xing-square' => ['name' => 'xing-square', 'prefix' => 'fab'],
        'y-combinator' => ['name' => 'y-combinator', 'prefix' => 'fab'],
        'y-combinator-square' => ['name' => 'hacker-news', 'prefix' => 'fab'],
        'yahoo' => ['name' => 'yahoo', 'prefix' => 'fab'],
        'yc' => ['name' => 'y-combinator', 'prefix' => 'fab'],
        'yc-square' => ['name' => 'hacker-news', 'prefix' => 'fab'],
        'yelp' => ['name' => 'yelp', 'prefix' => 'fab'],
        'yen' => ['name' => 'yen-sign', 'prefix' => 'fas'],
        'yoast' => ['name' => 'yoast', 'prefix' => 'fab'],
        'youtube' => ['name' => 'youtube', 'prefix' => 'fab'],
        'youtube-play' => ['name' => 'youtube', 'prefix' => 'fab'],
        'youtube-square' => ['name' => 'youtube-square', 'prefix' => 'fab'],
    ];

    /**
     * @param string $name
     * @param string $prefix
     */
    public function __construct(string $name, string $prefix = self::PREFIX_SOLID)
    {
        $this->name = $name;
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        $prefix = $this->prefix;
        if (in_array($prefix, [self::PREFIX_REGULAR, self::PREFIX_LIGHT, self::PREFIX_DUOTONE]) && !$this->isPro()) {
            $prefix = self::PREFIX_SOLID;
        }

        return $prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return (string)$this->size;
    }

    /**
     * @param string $size Available options are 'xs', 'sm', 'lg', '2x', '3x', '5x', '7x', '10x'
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return bool
     */
    public function isFixedWidth(): bool
    {
        return $this->fixedWidth;
    }

    /**
     * @param bool $fixedWidth
     */
    public function setFixedWidth(bool $fixedWidth): void
    {
        $this->fixedWidth = $fixedWidth;
    }

    /**
     * @return int
     */
    public function getRotate(): int
    {
        return (int)$this->rotate;
    }

    /**
     * @param int $rotate Available options are 90, 180, 270
     */
    public function setRotate(int $rotate): void
    {
        $this->rotate = $rotate;
    }

    /**
     * @return string
     */
    public function getFlip(): string
    {
        return (string)$this->flip;
    }

    /**
     * @param string $flip Available options are 'horizontal', 'vertical' and 'both'
     */
    public function setFlip(string $flip): void
    {
        $this->flip = $flip;
    }

    /**
     * @return bool
     */
    public function isSpin(): bool
    {
        return $this->spin;
    }

    /**
     * @param bool $spin
     */
    public function setSpin(bool $spin): void
    {
        $this->spin = $spin;
    }

    /**
     * @return bool
     */
    public function isPulse(): bool
    {
        return $this->pulse;
    }

    /**
     * @param bool $pulse
     */
    public function setPulse(bool $pulse): void
    {
        $this->pulse = $pulse;
    }

    /**
     * @return bool
     */
    public function isBordered(): bool
    {
        return $this->bordered;
    }

    /**
     * @param bool $bordered
     */
    public function setBordered(bool $bordered): void
    {
        $this->bordered = $bordered;
    }

    /**
     * @return string
     */
    public function getPull(): string
    {
        return (string)$this->pull;
    }

    /**
     * @param string $pull Available options are 'left' and 'right'
     */
    public function setPull(string $pull): void
    {
        $this->pull = $pull;
    }

    /**
     * @return bool
     */
    public function isPro(): bool
    {
        return $this->pro;
    }

    /**
     * @param bool $pro
     */
    public function setPro(bool $pro): void
    {
        $this->pro = $pro;
    }

    /**
     * @return bool
     */
    public function shouldMigrateOldName(): bool
    {
        return $this->migrateOldName;
    }

    /**
     * @param bool $migrateOldName
     */
    public function setMigrateOldName(bool $migrateOldName): void
    {
        $this->migrateOldName = $migrateOldName;
    }

    protected function migrateOldIconName(): void
    {
        $name = $this->getName();
        if (array_key_exists($name, $this->migrateList)) {
            $this->setName($this->migrateList[$name]['name']);
            $this->setPrefix($this->migrateList[$name]['prefix']);
        }
    }

    /**
     * Get helper class from full class name list like 'far fa-address'.
     * Also supports just icon name like 'address'
     *
     * @param string $classname
     * @return FontAwesomeIcon
     */
    public static function getFromClassNames(string $classname): FontAwesomeIcon
    {
        $name = '';
        $prefix = self::PREFIX_SOLID;
        $classes = explode(' ', $classname);
        if (count($classes) > 1) {
            foreach ($classes as $key => $class) {
                if (substr($class, 0, 3) === 'fa-') {
                    $name = str_replace('fa-', '', $class);
                    unset($classes[$key]);
                }
                if ($class === 'fa') {
                    $prefix = self::PREFIX_SOLID;
                    unset($classes[$key]);
                }
                if (in_array($class, [self::PREFIX_BRANDS, self::PREFIX_LIGHT, self::PREFIX_REGULAR, self::PREFIX_LIGHT, self::PREFIX_DUOTONE])) {
                    $prefix = $class;
                    unset($classes[$key]);
                }
            }
        } else {
            $name = $classname;
        }

        return new self($name, $prefix);
    }

    public function getTag(): Element
    {
        if ($this->shouldMigrateOldName()) {
            $this->migrateOldIconName();
        }

        $i = new Element('i');
        $i->addClass($this->getPrefix());
        $i->addClass('fa-' . $this->getName());
        if ($this->getSize()) {
            $i->addClass('fa-' . $this->getSize());
        }
        if ($this->isFixedWidth()) {
            $i->addClass('fa-fw');
        }
        if ($this->getRotate() > 0) {
            if ($this->getFlip()) {
                $span = new Element('span');
                $span->addClass('fa-rotate-' . $this->getRotate());
                $i->addClass('fa-flip-' . $this->getFlip());
            } else {
                $i->addClass('fa-rotate-' . $this->getRotate());
            }
        } elseif ($this->getFlip()) {
            $i->addClass('fa-flip-' . $this->getFlip());
        }
        if ($this->isSpin()) {
            $i->addClass('fa-spin');
        }
        if ($this->isPulse()) {
            $i->addClass('fa-pulse');
        }
        if ($this->isBordered()) {
            $i->addClass('fa-border');
        }
        if ($this->getPull()) {
            $i->addClass('fa-pull-' . $this->getPull());
        }

        if (isset($span)) {
            $span->appendChild($i);
            return $span;
        }

        return $i;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return (string)$this->getTag();
    }
}
