<?php 

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Utilities
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */

/**
 * @access private
 */
class Search {
	
	/**
	 * field to sort by 
	 * @access public
	 * @var string
	*/
	var $sort;
	var $order;
	var $total;
	var $totalQuery;
	var $searchQuery;
	var $validSortColumns;
	var $filters;
	var $q;
	
	function qsReplace($variable_name) {
		// this function is born out of necessity and lack of something suitable in PHP
		// basically this takes an array of key/value variable/value pairs and
		// returns the existing query string, but with the matching variable equalling
		// the new variable value. If the variable does not exist within the query string, it
		// gets appended to it
		
		$qs = '?';
		
		foreach ($_GET as $key => $value) {
			if (is_array($value)) {
				// Then we're passinga bunch of var[] declarations through the query string
				// we can't change these - but we can at least output them
				
				foreach ($value as $_value) {
					$qs .= "{$key}[]={$_value}&";
				}
			} else {
				$qs .= $key . '=';					
				if (@array_key_exists($key, $variable_name)) {
					// then we know this variable name is within the list to be changed
					$qs .= $variable_name[$key];
					$matched[] = $key;
				} else { 
					$qs .= $value;
				}
				$qs .= '&';
			}
		}
			
		// now that we've gone through, we're going to iterate through the variable_name array to see if we've missed
		// any keys and need to append them
		
		foreach ($variable_name as $key => $value) {
			if (!@in_array($key, $matched)) {
				// not in the query string, so we append
				$qs .= $key . '=' . $value . '&';
			}
		}
				
		return $qs;
	}
	
	function paging($startAt) {
		$args = func_get_args();
		$chunk = ($args[2]) ? $args[2] : SEARCH_CHUNK_SIZE;
		$this->order = ($args[1] == 'desc') ? 'desc' : 'asc';
		$total = $this->total;
		$pOptions = array();
		
		$startAt = ($startAt < $chunk) ? '0' : $startAt;
		$pOptions['totalPages'] = ((int) ($total / $chunk));
		if (($total / $chunk) != $pOptions['totalPages']) {
			$pOptions['totalPages']++;
		}
		
		
		if ($startAt > 0) {
			$pOptions['current'] = ($startAt / $chunk) + 1;
		} else {
			$pOptions['current'] = '1';
		}
		
		$pOptions['previous'] = ($startAt >= $chunk) ? ($pOptions['current'] - 2) * $chunk : -1;
		$pOptions['next'] = (($total - $startAt) >= $chunk) ? $pOptions['current'] * $chunk : '';
		$pOptions['last'] = (($total - $startAt) >= $chunk) ? ($pOptions['totalPages'] - 1) * $chunk : '';
		$pOptions['currentRangeStart'] = ($pOptions['current'] > 1) ? ((($pOptions['current'] - 1) * $chunk) + 1) : '1';
		$pOptions['currentRangeEnd'] = ((($pOptions['current'] + $chunk) - 1) <= $pOptions['last']) ? ($pOptions['currentRangeStart'] + $chunk) - 1 : $this->total;			
		$pOptions['total'] = $total;
		$pOptions['chunk'] = $chunk;
		$pOptions['needPaging'] = ($total > $chunk) ? '1': false;
		
		return $pOptions;
	}
	
	function printHeader($fieldtext) {
		# it works like this: 
		# $fieldtext is required - it's what the header text says
		# if you offer an id as the second argument, then the header assumes it can be sorted
		# as that ID. If you offer a 1 as the third argument, we will not wrap the cell.
		
		$args = func_get_args();
		$cell = '<td';
		$cell .= ($args[2]) ? ' nowrap' : '';
		$defaultSort = ($args[3]) ? $args[3] : 'asc';
		if ($args[1]) {
			//$img = ($this->order == 'desc' && ($this->sort == $args[1])) ? 'sort_desc.gif' : 'sort_asc.gif';
			
			if ($this->sort == $args[1]) {
				$order = ($this->order != 'desc') ? 'desc' : 'asc';
				
				$variables['sort'] = $args[1];
				$variables['order'] = $order;
				$url = $_SERVER['PHP_SELF'] . $this->qsReplace($variables);
					
				$cell .= ' class="subheaderActive"><a href="' . htmlentities($url) . '">' . $fieldtext . '</a></td>';
			} else {
			
				$variables['sort'] = $args[1];
				$variables['order'] = $defaultSort;
				$url = $_SERVER['PHP_SELF'] . $this->qsReplace($variables);
				
				$cell .= ' class="subheader"><a href="' . htmlentities($url) . '">' . $fieldtext . '</a></td>';
			}
		} else {
			$cell .= ' class="subheader">' . $fieldtext . '</td>';
		}
		
		return $cell;
	}
	
	function printRow($value, $field) {
		# Before I forget, this is the order of arguments
		# printRow($field, $value [, $url, $is_new_window, $is_nowrap, $extraTags])
		# $url - gives the item a particular URL link
		# $isNewWindow - if is a url, opens the item in a new window
		# $isNowrap - appends nowrap to the table cell. Use with caution.
		# $extraTags - any extra tags
		
		$value = ($value == null) ? "&nbsp;" : $value;
		$args = func_get_args();
		$cell = '<td valign="top"';
		$cell .= ($args[4]) ? ' style="' . $args[4] . '"' : '';
		if ($args[2]) {
			# ugh. this is gettin ugly
			if ($args[3]) {
				$content = '<a href="' . $args[2] . '" target="_blank">' . $value . '</a>';
			} else {
				$content = '<a href="' . $args[2] . '">' . $value . '</a>';
			}
		} else {
			$content = $value;
		}
		
		if ($field == $this->sort) {
			$cell .= ' class="active">' . $content . '</td>';
		} else {
			$cell .= '>' . $content . '</td>';
		}
		return $cell;
	}
	
	function setLinkingWord() {
		$this->filters .= ($this->filters) ? ' and ' : ' where ';
	}
	
	function getTotal() {
		$db = Loader::db();
		if ($this->total > -1) {
			return $this->total;
		} else {
			if ($this->totalQuery) {
				$q = $this->totalQuery . $this->filters;
				$r = $db->query($q);
				if ($r) {
					$row = $r->fetchRow();
					$this->total = $row['total'];
					return $row['total'];
				} else {
					return 0;
				}
			} else {
				$q = $this->searchQuery . $this->filters;
				$r = $db->query($q);
				if ($r) {
					$this->total = $r->numRows();
				}
				return $this->total;
			}
		}
	}
	
	private function getSort($sort) {
		// first, we split the 'validSortColumns' variable into an array, if it exists
		// then we check to see if the provided column exists within the list. If not
		// then it gets the first column (the default.)		
		$sArray = preg_split('/,/', $this->validSortColumns);
		if (@in_array($sort, $sArray)) {
			$this->sort = $sort;
			return $sort;
		} else if (@is_array($sArray)) {
			return $sArray[0];
		} else {
			return false;
		}
	}
		
	function getResult($pSort) {
		$db = Loader::db();
		$args = func_get_args();
		$order = ($args[2] == 'desc') ? 'desc' : 'asc';
		$chunk = ($args[3]) ? $args[3] : SEARCH_CHUNK_SIZE;
		$this->q = $this->searchQuery . $this->filters;
		$this->sort = $this->getSort($pSort);
		
		$this->q .= ($this->sort) ? " order by {$this->sort} {$order} " : " ";
		// if the provided chunk is -1, then we don't limit it at all
		if ($chunk != -1) {
			$this->q .= ($args[1]) ? "limit {$args[1]}, $chunk" : "limit 0, $chunk";
		}
		
		return $db->query($this->q);
	}

}