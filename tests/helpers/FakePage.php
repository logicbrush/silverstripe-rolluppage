<?php

namespace Logicbrush\RollupPage\Tests;

use Page;

class FakePage extends Page {

	public function BeforeRollup() {
		return '<div>' . $this->Title . ' Before Content</div>';
	}


	public function AfterRollup() {
		return '<div>' . $this->Title . ' After Content</div>';
	}


}
