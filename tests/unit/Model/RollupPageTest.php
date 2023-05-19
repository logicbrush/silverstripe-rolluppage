<?php
/**
 * tests/unit/Model/RollupPageTest.php
 *
 * @package default
 */


namespace Logicbrush\RollupPage\Tests;

use Logicbrush\RollupPage\Model\RollupPage;
use Page;
use SilverStripe\Dev\SapphireTest;

class RollupPageTest extends SapphireTest
{
	protected $usesDatabase = true;

	/**
	 *
	 */
	public function testCreateRollupPage() {
		$rollupPage = new RollupPage();
		$rollupPage->write();
		$this->assertNotNull( $rollupPage );
	}


	/**
	 *
	 */
	public function testGetCMSFields() {
		$rollupPage = RollupPage::create();
		$rollupPage->write();

		$fields = $rollupPage->getCMSFields();
		$this->assertNotNull( $fields );
		$this->assertNotNull( $fields->dataFieldByName( 'ShowLinksOnly' ) );
	}


	/**
	 *
	 */
	public function testChildren() {
		$rollupPage = RollupPage::create();
		$rollupPage->ShowLinksOnly = 1;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertEquals( 0, $rollupPage->Children()->count() );

		$page1 = Page::create();
		$page1->Content = '<p>Page 1</p>';
		$page1->ParentID = $rollupPage->ID;
		$page1->write();
		$page1->publish( 'Stage', 'Live' );

		$page2 = Page::create();
		$page2->Content = '';
		$page2->ParentID = $rollupPage->ID;
		$page2->write();
		$page2->publish( 'Stage', 'Live' );

		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertEquals( 1, $rollupPage->Children()->count() );

		$page2->Content = '<p>Page 2</p>';
		$page2->write();
		$page2->publish( 'Stage', 'Live' );

		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertEquals( 2, $rollupPage->Children()->count() );

		$rollupPage->ShowLinksOnly = 2;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertEquals( 0, $rollupPage->Children()->count() );

		$rollupPage->ShowLinksOnly = 0;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertEquals( 0, $rollupPage->Children()->count() );
	}


	/**
	 *
	 */
	public function testGetRollupPageDisplayType() {
		$rollupPage = RollupPage::create();
		$rollupPage->ShowLinksOnly = 0;
		$rollupPage->write();

		$this->assertEquals( 'content', $rollupPage->getRollupPageDisplayType() );

		$rollupPage->ShowLinksOnly = 1;
		$rollupPage->write();

		$this->assertEquals( 'list', $rollupPage->getRollupPageDisplayType() );

		$rollupPage->ShowLinksOnly = 2;
		$rollupPage->write();

		$this->assertEquals( 'tabs', $rollupPage->getRollupPageDisplayType() );
	}


	/**
	 *
	 */
	public function testContent() {
		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->ShowLinksOnly = 0;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );


		$page1 = Page::create();
		$page1->Title = 'Page 1';
		$page1->Content = '<p>Page 1 content</p>';
		$page1->ParentID = $rollupPage->ID;
		$page1->write();
		$page1->publish( 'Stage', 'Live' );

		$page2 = FakePage::create();
		$page2->Title = 'Page 2';
		$page2->Content = '<p>Page 2 content</p>';
		$page2->ParentID = $rollupPage->ID;
		$page2->write();
		$page2->publish( 'Stage', 'Live' );

		$this->assertStringContainsString( '<p>Page 1 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<p>Page 2 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<div>Page 2 Before Content</div>', $rollupPage->Content() );
		$this->assertStringContainsString( '<div>Page 2 After Content</div>', $rollupPage->Content() );
		$this->assertStringNotContainsString( '<a href="' . $page1->Link() . '">Page 1</a>', $rollupPage->Content() );

		$rollupPage->ShowLinksOnly = 1;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$page1->write();
		$page1->publish( 'Stage', 'Live' );

		$page2->write();
		$page2->publish( 'Stage', 'Live' );

		$this->assertStringNotContainsString( '<p>Page 1 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<nav class="rollup-page-navigation-list">', $rollupPage->Content() );
		$this->assertStringContainsString(
			'<a href="' . $page1->Link() . '" data-url-segment="' . $page1->URLSegment . '">Page 1</a>',
			$rollupPage->Content()
		);

		$rollupPage->ShowLinksOnly = 2;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$page1->write();
		$page1->publish( 'Stage', 'Live' );

		$page2->Content = '';
		$page2->write();
		$page2->publish( 'Stage', 'Live' );

		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$this->assertStringContainsString( '<p>Page 1 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<nav class="rollup-page-navigation-tabs">', $rollupPage->Content() );
		$this->assertStringContainsString(
			'<a href="' . $page1->Link() . '" data-url-segment="' . $page1->URLSegment . '">Page 1</a>',
			$rollupPage->Content()
		);
		$this->assertStringContainsString(
			'<span>Page 2</span>',
			$rollupPage->Content()
		);
	}


}
