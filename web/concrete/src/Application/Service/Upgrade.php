<?php

namespace Concrete\Core\Application\Service;
class Upgrade {

	public function getList($version) {
		$ugvs = array();
		if (version_compare($version, '5.0.0b1', '<')) {
			$ugvs[] = "version_500a1";
		}
		if (version_compare($version, '5.0.0b2', '<')) {
			$ugvs[] = "version_500b1";
		}
		if (version_compare($version, '5.0.0', '<')) {
			$ugvs[] = "version_500b2";
		}
		if (version_compare($version, '5.1.0', '<')) {
			$ugvs[] = "version_500";
		}
		if (version_compare($version, '5.2.0', '<')) {
			$ugvs[] = "version_510";
		}
		if (version_compare($version, '5.3.0', '<')) {
			$ugvs[] = "version_520";
		}
		if (version_compare($version, '5.3.2', '<')) {
			$ugvs[] = "version_530";
		}

		if (version_compare($version, '5.3.3', '<')) {
			$ugvs[] = "version_532";
		}

		if (version_compare($version, '5.3.3.1', '<')) {
			$ugvs[] = "version_533";
		}
		if (version_compare($version, '5.4.0', '<')) {
			$ugvs[] = "version_5331";
			$ugvs[] = "version_540";
		}
		if (version_compare($version, '5.4.1', '<')) {
			$ugvs[] = "version_5406";
			$ugvs[] = "version_541";
		}
		if (version_compare($version, '5.4.2', '<')) {
			$ugvs[] = "version_5411";
			$ugvs[] = "version_542";
		}
		if (version_compare($version, '5.4.2.1', '<')) {
			$ugvs[] = "version_5421";
		}

		if (version_compare($version, '5.5.0', '<')) {
			$ugvs[] = "version_550";
		}

		if (version_compare($sav, '5.5.1', '<')) {
			$ugvs[] = "version_551";
		}
		if (version_compare($version, '5.5.2', '<')) {
			$ugvs[] = "version_552";
		}
		if (version_compare($version, '5.6.0', '<')) {
			$ugvs[] = "version_560";
		}

		if (version_compare($version, '5.6.0.1', '<')) {
			$ugvs[] = "version_5601";
		}

		if (version_compare($version, '5.6.0.2', '<')) {
			$ugvs[] = "version_5602";
		}

		if (version_compare($version, '5.6.1', '<')) {
			$ugvs[] = "version_561";
		}

		if (version_compare($version, '5.6.2', '<')) {
			$ugvs[] = "version_562";
		}

		if (version_compare($version, '5.6.2.2', '<')) {
			$ugvs[] = "version_5622";
		}

		if (version_compare($version, '5.6.3', '<')) {
			$ugvs[] = "version_563";
		}

		if (version_compare($version, '5.6.4', '<')) {
			$ugvs[] = "version_564";
		}

		$upgrades = array();
		foreach($ugvs as $ugh) {
			$upgrades[] = Loader::helper('concrete/upgrade/' . $ugh);
		}

		return $upgrades;
	}

	public function refreshDatabaseTables($tables) {
		$dbxml = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');

		$output = new SimpleXMLElement("<schema></schema>");
		$output->addAttribute('version', '0.3');

		$th = Loader::helper("text");

		foreach($dbxml->table as $t) {
			$name = (string) $t['name'];
			if (in_array($name, $tables)) {
				$th->appendXML($output, $t);
			}
		}

		$xml = $output->asXML();

		if ($xml) {
			$file = Loader::helper('file')->getTemporaryDirectory() . '/tmpupgrade_' . time() . '.xml';
			@file_put_contents($file, $xml);
			if (file_exists($file)) {
				Package::installDB($file);
				@unlink($file);
			} else {
				throw new Exception(t('Unable to create temporary db xml file: %s', $file));
			}
		}
	}
}
