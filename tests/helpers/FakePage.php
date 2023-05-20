<?php
/**
 * tests/helpers/FakePage.php
 *
 * @package default
 */


namespace Logicbrush\RollupPage\Tests;

use Page;

class FakePage extends Page {

	/**
	 *
	 * @return unknown
	 */
	public function BeforeRollup() {
		return '<div>' . $this->Title . ' Before Content</div>';
	}


	/**
	 *
	 * @return unknown
	 */
	public function AfterRollup() {
		return '<div>' . $this->Title . ' After Content</div>';
	}


}
