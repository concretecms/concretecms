<?php

namespace Concrete\Core\Config;
use \Concrete\Core\Foundation\Object;
use Loader;
class ConfigStore {
	/**
	 * @var \Database
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $rows;

	public function __construct() {
		$this->load();
	}

	protected function load() {
		$this->rows = array();
		$this->db = Loader::db();
		if (!$this->db) {
			return;
		}
		$r = $this->db->Execute('select * from ConfigStore where uID = 0 order by cfKey asc');
		while ($row = $r->FetchRow()) {
			if (!$row['pkgID']) {
				$row['pkgID'] = 0;
			}
			$this->rows["{$row['cfKey']}.{$row['pkgID']}"] = $row;
		}
		$r->Close();
	}

	/**
	 * Get a config item
	 * @param string $cfKey
	 * @param int $pkgID optional
	 * @return ConfigValue|void
	 */
	public function get($cfKey, $pkgID = null) {
		if ($pkgID > 0 && isset($this->rows["{$cfKey}.{$pkgID}"])) {
			return $this->rowToConfigValue($this->rows["{$cfKey}.{$pkgID}"]);
		} else {
			foreach ($this->rows as $row) {
				if ($row['cfKey'] == $cfKey) {
					return $this->rowToConfigValue($row);
				}
			}
		}
		return null;
	}

	public function getListByPackage($pkgID) {
		$list = array();
		foreach ($this->rows as $row) {
			if ($row['pkgID'] == $pkgID) {
				$list[] = $row['cfKey'];
			}
		}
		return $list;
	}

	public function set($cfKey, $cfValue, $pkgID = 0) {
		$timestamp = date('Y-m-d H:i:s');
		if ($pkgID < 1) {
			$pkgID = 0;
		}
		$this->rows["{$cfKey}.{$pkgID}"] = array(
			'cfKey' => $cfKey,
			'timestamp' => $timestamp,
			'cfValue' => $cfValue,
			'uID' => 0,
			'pkgID' => $pkgID
		);
		$db = Loader::db();
		if (!$db) {
			return;
		}

		$db->query(
			"replace into ConfigStore (cfKey, timestamp, cfValue, pkgID) values (?, ?, ?, ?)",
			array($cfKey, $timestamp, $cfValue, $pkgID)
		);
	}

	public function delete($cfKey, $pkgID = null) {
		$db = Loader::db();
		if ($pkgID > 0) {
			unset($this->rows["{$cfKey}.{$pkgID}"]);
			$db->query(
				"delete from ConfigStore where cfKey = ? and pkgID = ?",
				array($cfKey, $pkgID)
			);
		} else {
			foreach ($this->rows as $key => $row) {
				if ($row['cfKey'] == $cfKey) {
					unset($this->rows[$key]);
				}
			}
			$db->query(
				"delete from ConfigStore where cfKey = ?",
				array($cfKey)
			);
		}
	}

}
