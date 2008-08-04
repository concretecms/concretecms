<?

// lets take in some variables here
	/*		
	orderBy
	displayPages
	displaySubPages
	displaySubPageLevels
	*/
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/controller.php');

	$tmpbid->orderBy = $_REQUEST[orderBy];
	$tmpbid->displayPages = $_REQUEST[displayPages];
	$tmpbid->displaySubPages = $_REQUEST[displaySubPages];
	$tmpbid->displaySubPageLevels = $_REQUEST[displaySubPageLevels];
	$tmpbid->displaySubPageLevelsNum = $_REQUEST[displaySubPageLevelsNum];
	$tmpbid->displayUnavailablePages = $_REQUEST[displayUnavailablePages];
	if($tmpbid->displayPages == "custom") {
		$tmpbid->displayPagesCID = $_REQUEST[displayPagesCID];
		$tmpbid->displayPagesIncludeSelf = $_REQUEST[displayPagesIncludeSelf];
	}
	$tmpbid->cParentIDArray = array();
	$tmpbid->cID = $_REQUEST[cID];
	$obj = Page::getByID($_REQUEST[cID]);
	$tmpbid->cParentID = $obj->getCollectionParentID();
	// this is ripped from BlockAutoNavigation -- facking ugly stuff doing this

		function generateNav() {
			global $tmpbid;
			$db = Loader::db();

			// now we proceed, with information obtained either from the database, or passed manually from
			$orderBy = "";
			/*switch($tmpbid->orderBy) {
			switch($tmpbid->orderBy) {
				case 'display_asc':
					$orderBy = "order by Collections.cDisplayOrder asc";
					break;
				case 'display_desc':
					$orderBy = "order by Collections.cDisplayOrder desc";
					break;
				case 'chrono_asc':
					$orderBy = "order by cvDatePublic asc";
					break;
				case 'chrono_desc':
					$orderBy = "order by cvDatePublic desc";
					break;
				case 'alpha_desc':
					$orderBy = "order by cvName desc";
					break;
				default:
					$orderBy = "order by cvName asc";
					break;
			}*/
			switch($tmpbid->orderBy) {
				case 'display_asc':
					$orderBy = "order by Pages.cDisplayOrder asc";
					break;
				case 'display_desc':
					$orderBy = "order by Pages.cDisplayOrder desc";
					break;
				default:
					$orderBy = '';
					break;
			}
			$level = 0;
			$cParentID = 0;
			switch($tmpbid->displayPages) {
				case 'current':
					$cParentID = $tmpbid->cParentID;
					break;
				case 'top':
					// top level actually has ID 1 as its parent, since the home page is effectively alone at the top
					$cParentID = 1;
					break;
				case 'above':
					$cParentID = getParentParentID();
					break;
				case 'below':
					$cParentID = $tmpbid->cID;
					break;
				case 'second_level':
					$cParentID = getParentAtLevel(2);
					break;
				case 'third_level':
					$cParentID = getParentAtLevel(3);
					break;
				case 'custom':
					$cParentID = $tmpbid->displayPagesCID;
					break;
				default:
					$cParentID = 1;
					break;
			}
			if ($tmpbid->displayPagesIncludeSelf) {
				$q = "select Pages.cID from Pages where Pages.cID = '{$cParentID}' and cIsTemplate = 0";
				$r = $db->query($q);
				if ($r) {
					$row = $r->fetchRow();
					$displayPage = true;
					if ($tmpbid->displayUnapproved) {
						$tc = Page::getByID($row['cID'], "RECENT");
					} else {
						$tc = Page::getByID($row['cID'], "ACTIVE");
					}
					$tcv = $tc->getVersionObject();
					if (!$tcv->isApproved() && !$tmpbid->displayUnapproved) {
						$displayPage = false;
					}

					if ($tmpbid->displayUnavailablePages == false) {
						$tcp = new Permissions($tc);
						if (!$tcp->canRead()) {
							$displayPage = false;
						}
					}
					
					if ($displayPage) {
						$niRow = array();
						$niRow['cvName'] = $tc->getCollectionName();
						$niRow['cID'] = $row['cID'];
						$niRow['cvDescription'] = $tc->getCollectionDescription();
						$niRow['cPath'] = $tc->getCollectionPath();
						
						$ni = new AutonavBlockItem($niRow, $level);
						$level++;
						$ni->setCollectionObject($tc);
						//$tmpbid->navArray[] = $ni;
						$tmpbid->navSort[$niRow[cID]] = $dateKey;
						$tmpbid->sorted_array[$niRow[cID]] = $ni;

						$_c = $ni->getCollectionObject();
						$object_name = $_c->getCollectionName();
						$tmpbid->navObjectNames[$niRow[cID]] = $object_name;
					}
				}
			}

			if ($tmpbid->displaySubPages == 'relevant' || $tmpbid->displaySubPages == 'relevant_breadcrumb') {
				populateParentIDArray($tmpbid->cID);
			}
			
			getNavigationArray($cParentID, $orderBy, $level);
		
			return $tmpbid->navArray;
		}
		
		function getNavigationArray($cParentID, $orderBy, $currentLevel) {
			global $tmpbid;
			$db = Loader::db();

			$navSort = $tmpbid->navSort;
			$sorted_array = $tmpbid->sorted_array;
			$navObjectNames = $tmpbid->navObjectNames;

			$allowedParentIDs = ($allowedParentIDs) ? $allowedParentIDs : array();
			$q = "select Pages.cID from Pages where cFilename is null and cIsTemplate = 0 and cParentID = '{$cParentID}' {$orderBy}";
			$r = $db->query($q);
				if ($r) {
				while ($row = $r->fetchRow()) {
					if ($tmpbid->displaySubPages != 'relevant_breadcrumb' || (in_array($row['cID'], $tmpbid->cParentIDArray) || $row['cID'] == $tmpbid->cID)) {
						/*
						if ($tmpbid->haveRetrievedSelf) {
							// since we've already retrieved self, and we're going through again, we set plus 1
							$tmpbid->haveRetrievedSelfPlus1 = true;
						} else 
						*/
						
						if ($tmpbid->haveRetrievedSelf && $cParentID == $tmpbid->cID) {
							$tmpbid->haveRetrievedSelfPlus1 = true;
						} else if ($row['cID'] == $tmpbid->cID) {
							$tmpbid->haveRetrievedSelf = true;
						}
						
						$displayPage = true;
						if ($tmpbid->displayUnapproved) {
							$tc = Page::getByID($row['cID'], "RECENT");
						} else {
							$tc = Page::getByID($row['cID'], "ACTIVE");
						}
						$tcv = $tc->getVersionObject();
						if (!$tcv->isApproved() && !$tmpbid->displayUnapproved) {
							$displayPage = false;
						}
						
						if ($tmpbid->displayUnavailablePages == false) {
							$tcp = new Permissions($tc);
							if (!$tcp->canRead()) {
								$displayPage = false;
							}
						}

						if ($displayPage) {
							$niRow = array();
							$niRow['cvName'] = $tc->getCollectionName();
							$niRow['cID'] = $row['cID'];
							$niRow['cvDescription'] = $tc->getCollectionDescription();
							$niRow['cPath'] = $tc->getCollectionPath();
							$dateKey = strtotime($tc->getCollectionDatePublic());

							$ni = new AutonavBlockItem($niRow, $currentLevel);
							$ni->setCollectionObject($tc);
							// $tmpbid->navArray[] = $ni;
							$navSort[$niRow[cID]] = $dateKey;
							$sorted_array[$niRow[cID]] = $ni;

							$_c = $ni->getCollectionObject();
							$object_name = $_c->getCollectionName();
							$navObjectNames[$niRow[cID]] = $object_name;
						}

					}
				}
				// end while -- sort navSort

				// Joshua's Huge Sorting Crap
				if($navSort) {
					$sortit=0;
					if($tmpbid->orderBy == "chrono_asc") { asort($navSort); $sortit=1; }
					if($tmpbid->orderBy == "chrono_desc") { arsort($navSort); $sortit=1; }

					if($sortit) {
						foreach($navSort as $sortCID => $sortdatewhocares) {
							// create sorted_array
							$tmpbid->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($tmpbid->displaySubPages == 'all') {
								if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($tmpbid->displaySubPages == "relevant" || $tmpbid->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $tmpbid->cParentIDArray) || $sortCID == $tmpbid->cID)) {
								if ($tmpbid->displaySubPageLevels == "enough" && $tmpbid->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == "enough_plus1" && $tmpbid->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}

					$sortit=0;
					if($tmpbid->orderBy == "alpha_desc") { arsort($navObjectNames); $sortit=1; }
					if($tmpbid->orderBy == "alpha_asc") { asort($navObjectNames); $sortit=1; }

					if($sortit) {
						foreach($navObjectNames as $sortCID => $sortnameaction) {
							// create sorted_array
							$tmpbid->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($tmpbid->displaySubPages == 'all') {
								if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($tmpbid->displaySubPages == "relevant" || $tmpbid->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $tmpbid->cParentIDArray) || $sortCID == $tmpbid->cID)) {
								if ($tmpbid->displaySubPageLevels == "enough" && $tmpbid->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == "enough_plus1" && $tmpbid->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}

					$sortit=0;
					if($tmpbid->orderBy == "display_desc") { $sortit=1; }
					if($tmpbid->orderBy == "display_asc") { $sortit=1; }

					if($sortit) {
						// for display order? this stuff is already sorted...
						foreach($navObjectNames as $sortCID => $sortnameaction) {
							// create sorted_array
							$tmpbid->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($tmpbid->displaySubPages == 'all') {
								if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($tmpbid->displaySubPages == "relevant" || $tmpbid->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $tmpbid->cParentIDArray) || $sortCID == $tmpbid->cID)) {
								if ($tmpbid->displaySubPageLevels == "enough" && $tmpbid->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == "enough_plus1" && $tmpbid->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($tmpbid->displaySubPageLevels == 'all' || ($tmpbid->displaySubPageLevels == 'custom' && $tmpbid->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}
				}
				// End Joshua's Huge Sorting Crap

			}
		}

		function populateParentIDArray($cID) {
			// returns an array of collection IDs going from the top level to the current item
			global $tmpbid;
			$db = Loader::db();

			$q = "select cParentID from Pages where cID = '$cID'";
			$cParentID = $db->getOne($q);
			if ($cParentID) {
				if ($cParentID != $stopAt) {
					if (!in_array($cParentID, $tmpbid->cParentIDArray)) {
						$tmpbid->cParentIDArray[] = $cParentID;
					}
					populateParentIDArray($cParentID);
				}
			}

		}
		
		function getParentParentID() {
			// this has to be the stupidest name of a function I've ever created. sigh
			global $tmpbid;
			$db = Loader::db();

			$q = "select cParentID from Pages where cID = '{$tmpbid->cParentID}'";
			$cParentID = $db->getOne($q);
			return (!$cParentID) ? $cParentID : 0;
		}

		
		function getParentAtLevel($level) {
			// this function works in the following way
			// we go from the current collection up to the top level. Then we find the parent Id at the particular level specified, and begin our
			// autonav from that point
			global $tmpbid;
			populateParentIDArray($tmpbid->cID);

			$idArray = array_reverse($tmpbid->cParentIDArray);
			$tmpbid->cParentIDArray = array();

			if ($level - count($idArray) == 0) {
				// This means that the parent ID array is one less than the item
				// we're trying to grab - so we return our CURRENT page as the item to get
				// things under
				return $tmpbid->cID;
			}
			
			if (isset($idArray[$level])) {
				return $idArray[$level];
			} else {
				return null;
			}
		}
		
?>
<center>Auto-Nav Preview</center>

<?
$myTmpBlocks = generateNav();
if(is_array($myTmpBlocks)) {
	echo("<ul id=\"autonav-preview\">");
	foreach($myTmpBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		$thisLevel = $ni->getLevel();
		if ($thisLevel > $lastLevel) {
			echo("<ul>");
		} else if ($thisLevel < $lastLevel) {
			for ($j = $thisLevel; $j < $lastLevel; $j++) {
				echo("</ul>");
			}
		}
		echo('<li>');
		if (!$ni->isActive($c)) {
			if ($_c->getCollectionTypeHandle() == 'critique') { 
				echo('<a href="' . $ni->getURL() . '?mode=review" target="_blank">' . $ni->getName() . '</a>');
			} else {
				echo('<a href="' . $ni->getURL() . '" target="_blank">' . $ni->getName() . '</a>');
			}
		} else {
			echo($ni->getName());
		}
		echo('</li>');
		$lastLevel = $thisLevel;
	}
	
	$thisLevel = 0;
	for ($i = $thisLevel; $i <= $lastLevel; $i++) {
		echo("</ul>");
	}
} // end_is_array
?>