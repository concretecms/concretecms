<?php
namespace Concrete\Core\Page;
use \Symfony\Component\EventDispatcher\GenericEvent;

class FeedEvent extends Event
{

	protected $feed;
	protected $writer;

	public function getFeedObject()
	{
		return $this->feed;
	}

	public function setFeedObject($feed)
	{
		return $this->feed = $feed;
	}

	public function setWriterObject($writer)
	{
		return $this->writer = $writer;
	}

	public function getWriterObject()
	{
		return $this->writer;
	}


}
