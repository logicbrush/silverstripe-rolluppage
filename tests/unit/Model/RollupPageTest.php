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
	 * The list of children for the rollup page can vary depending on the value
	 * of `ShowLinksOnly`.
	 */
	public function testChildrenWithShowLinksOnlyOptions() {

		$rollupPage = RollupPage::create();
		$rollupPage->ShowLinksOnly = RollupPage::DISPLAY_INLINE;
		$rollupPage->write();
		$rollupPage->publishSingle();

		$childPage = Page::create();
		$childPage->ParentID = $rollupPage->ID;
		$childPage->write();
		$childPage->publishSingle();

		// ShowLinksOnly == DISPLAY_INLINE -> should have no children.
		$this->assertEquals( 0, $rollupPage->Children()->count() );

		$rollupPage->ShowLinksOnly = RollupPage::DISPLAY_LIST;
		$rollupPage->write();
		$rollupPage->publishSingle();

		// ShowLinksOnly == DISPLAY_LIST -> should have one child.
		$this->assertEquals( 1, $rollupPage->Children()->count() );

		$rollupPage->ShowLinksOnly = RollupPage::DISPLAY_TABS;
		$rollupPage->write();
		$rollupPage->publishSingle();

		// ShowLinksOnly == DISPLAY_TABS -> should have no children.
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
		$rollupPage->publishSingle();


		$page1 = Page::create();
		$page1->Title = 'Page 1';
		$page1->Content = '<p>Page 1 content</p>';
		$page1->ParentID = $rollupPage->ID;
		$page1->write();
		$page1->publishSingle();

		$page2 = FakePage::create();
		$page2->Title = 'Page 2';
		$page2->Content = '<p>Page 2 content</p>';
		$page2->ParentID = $rollupPage->ID;
		$page2->write();
		$page2->publishSingle();

		$this->assertStringContainsString( '<p>Page 1 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<p>Page 2 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<div>Page 2 Before Content</div>', $rollupPage->Content() );
		$this->assertStringContainsString( '<div>Page 2 After Content</div>', $rollupPage->Content() );
		$this->assertStringNotContainsString( '<a href="' . $page1->Link() . '">Page 1</a>', $rollupPage->Content() );

		$rollupPage->ShowLinksOnly = 1;
		$rollupPage->write();
		$rollupPage->publishSingle();

		$page1->write();
		$page1->publishSingle();

		$page2->write();
		$page2->publishSingle();

		$this->assertStringNotContainsString( '<p>Page 1 content</p>', $rollupPage->Content() );
		$this->assertStringContainsString( '<nav class="rollup-page-navigation-list">', $rollupPage->Content() );
		$this->assertStringContainsString(
			'<a href="' . $page1->Link() . '" data-url-segment="' . $page1->URLSegment . '">Page 1</a>',
			$rollupPage->Content()
		);

		$rollupPage->ShowLinksOnly = 2;
		$rollupPage->write();
		$rollupPage->publishSingle();

		$page1->write();
		$page1->publishSingle();

		$page2->Content = '';
		$page2->write();
		$page2->publishSingle();

		$rollupPage->write();
		$rollupPage->publishSingle();

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
