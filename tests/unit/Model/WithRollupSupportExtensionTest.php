<?php

namespace Logicbrush\RollupPage\Tests;

use Logicbrush\RollupPage\Model\RollupPage;
use Page;
use SilverStripe\Dev\SapphireTest;

class WithRollupSupportExtensionTest extends SapphireTest {

	protected $usesDatabase = true;

	public function testGetSettingsFields() {
		$page = Page::create();
		$page->write();

		$fields = $page->getSettingsFields();
		$this->assertNotNull( $fields );
		$this->assertNotNull( $fields->dataFieldByName( 'NeverRollup' ) );
	}


	public function testLink() {
		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->ShowLinksOnly = 1;
		$rollupPage->write();

		$page1 = Page::create();
		$page1->Title = 'Page 1';
		$page1->ParentID = $rollupPage->ID;
		$page1->write();

		$this->assertEquals( '/rollup-page/page-1/', $page1->Link() );

		$rollupPage->ShowLinksOnly = 0;
		$rollupPage->write();
		$page1->ParentID = $rollupPage->ID;
		$page1->write();

		$this->assertEquals( '/rollup-page/#page-1', $page1->Link() );

		$rollupPage->ShowLinksOnly = 2;
		$rollupPage->write();
		$page1->ParentID = $rollupPage->ID;
		$page1->write();

		$this->assertEquals( '/rollup-page/#page-1', $page1->Link() );
	}


}
