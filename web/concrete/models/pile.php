<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* Essentially a user's scrapbook, a pile is an object used for clumping bits of content together around a user account.
* Piles currently only contain blocks but they could also contain collections. Any bit of content inside a user's pile
* can be reordered, etc... although no public interface makes use of much of this functionality.
* @package Utilities
*
*/
class Pile extends Concrete5_Model_Pile {}
class PileContent extends Concrete5_Model_PileContent {}