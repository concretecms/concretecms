<?php
namespace Concrete\Core\Gathering\Item;
use Loader;
class Twitter extends Item {

	public function loadDetails() {}
	public function canViewGatheringItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = GatheringDataSource::getByHandle('twitter');
		return GatheringItem::getListByKey($ags, $mixed->get_link());
	}

	public static function add(GatheringDataSourceConfiguration $configuration, $tweet) {
		$gathering = $configuration->getGatheringObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($gathering, $configuration->getGatheringDataSourceObject(), date('Y-m-d H:i:s', strtotime($tweet->created_at)), $tweet->text, $tweet->id);
		} catch(Exception $e) {}

		if (is_object($item)) {
			$item->assignFeatureAssignments($tweet);
			if(count($tweet->entities->media) > 0 && $tweet->entities->media[0]->type == 'photo') {
				$item->setAutomaticGatheringItemTemplate();
			} else {
				$type = GatheringItemTemplateType::getByHandle('tile');
				$template = GatheringItemTemplate::getByHandle('tweet');
				$item->setGatheringItemTemplate($type, $template);
			}
			return $item;
		}
	}

	public function assignFeatureAssignments($tweet) {
		$userMentions = $tweet->entities->user_mentions;
		if(count($userMentions) > 0) {  // link mentions
			foreach($tweet->entities->user_mentions as $mention) {
				$tweet->text = str_replace('@'.$mention->screen_name, '<a target="_blank" href="http://www.twitter.com/'.$mention->screen_name.'">@'.$mention->screen_name.'</a>', $tweet->text);
			}
		}
		if(count($tweet->entities->hashtags) > 0) {   //link hashtags
			foreach($tweet->entities->hashtags as $hash) {
				$tweet->text = str_replace('#'.$hash->text, '<a target="_blank" href="http://www.twitter.com/search/%23'.$hash->text.'">#'.$hash->text.'</a>', $tweet->text);
			}
		}
		if(count($tweet->entities->urls) > 0) {
			foreach($tweet->entities->urls as $url) {
				$tweet->text = str_replace($url->url, '<a target="_blank" href="'.$url->url.'">'.$url->url.'</a>', $tweet->text);
			}
		}
		if(count($tweet->entities->media) > 0) {
			foreach($tweet->entities->media as $medium) {
				if ($medium->type == 'photo') {
					$this->addFeatureAssignment('image', $medium->media_url);
				}
			}
		}
		$this->addFeatureAssignment('description', $tweet->text);
		$this->addFeatureAssignment('date_time', $tweet->created_at);
		$this->addFeatureAssignment('author', $tweet->user->name);
		$this->addFeatureAssignment('link', 'http://www.twitter.com/' . $tweet->user->name . '/status/' . $tweet->id);
	}

}
