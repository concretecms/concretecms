<?php
namespace Concrete\Core\Gathering\DataSource;

use Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
use Concrete\Core\Url\Url;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Memory;
use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth1\Token\StdOAuth1Token;

class TwitterDataSource extends DataSource
{

    const TWITTER_SEARCH_URL = 'http://api.flickr.com/services/feeds/photos_public.gne';

    public function createConfigurationObject(Gathering $ag, $post)
    {
        $o = new TwitterGatheringDataSourceConfiguration();
        $o->setTwitterUsername($post['twitterUsername']);
        return $o;
    }

    private function getTwitterService()
    {
        $key = TWITTER_APP_CONSUMER_KEY;
        $secret = TWITTER_APP_CONSUMER_SECRET;
        $access_token = TWITTER_APP_ACCESS_TOKEN;
        $access_secret = TWITTER_APP_ACCESS_SECRET;

        /** @var \OAuth\ServiceFactory $factory */
        $factory = \Core::make('oauth/factory/service');

        // Initialize the token
        $token = new StdOAuth1Token($access_token);
        $token->setAccessTokenSecret($access_secret);

        // Store the token in memory
        $storage = new Memory();
        $storage->storeAccessToken('Twitter', $token);

        // Create the twitter service
        return $factory->createService('twitter', new Credentials($key, $secret, ''), $storage);
    }

    public function createGatheringItems(GatheringDataSourceConfiguration $configuration)
    {
        $twitter = $this->getTwitterService();

        $url = Url::createFromUrl('');
        $url->setPath('/statuses/user_timeline.json');
        $url->setQuery(array(
            'screen_name' => $configuration->getTwitterUsername(),
            'count' => 50
        ));

        $tweets = json_decode($twitter->request($url));
        if (!empty($tweets->errors[0])) {
            throw new Exception($tweets->errors[0]->message);
        }

        $gathering = $configuration->getGatheringObject();
        $lastupdated = 0;
        if ($gathering->getGatheringDateLastUpdated()) {
            $lastupdated = strtotime($gathering->getGatheringDateLastUpdated());
        }

        $items = array();
        foreach ($tweets as $tweet) {
            $item = TwitterGatheringItem::add($configuration, $tweet);

            if (is_object($item)) {
                $items[] = $item;
            }
        }
        return $items;
    }

}
