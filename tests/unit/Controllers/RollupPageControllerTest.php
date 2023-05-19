<?php

namespace Logicbrush\RollupPage\Tests;

use Logicbrush\RollupPage\Model\RollupPage;
use Page;
use SilverStripe\Dev\FunctionalTest;

class RollupPageControllerTest extends FunctionalTest
{
	protected $usesDatabase = true;

	protected function addChildPage($parent, $title = 'Child Page') {
		$page = Page::create();
		$page->Title = $title;
		$page->Content = "<p>{$title} Content.</p>";
		$page->ParentID = $parent->ID;
		$page->write();
		return $page;
	}

	public function testDisplayingRollupPageInline() {

		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->ShowLinksOnly = RollupPage::DISPLAY_INLINE;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$child1 = $this->addChildPage($rollupPage, 'Child 1');
		$child1->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertPartialMatchBySelector( 'h1', [
				'Rollup Page',
			] );
		$this->assertStringContainsString( 'Child 1', $response->getBody() );
		$this->assertStringNotContainsString( 'rollup-page-content', $response->getBody() );
	}

	public function testDisplayingRollupPageAsTabs() {
		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->ShowLinksOnly = RollupPage::DISPLAY_TABS;
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$child1 = $this->addChildPage($rollupPage, 'Child 1');
		$child1->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertPartialMatchBySelector( 'h1', [
				'Rollup Page',
			] );
		$this->assertStringContainsString( 'Child 1', $response->getBody() );
		$this->assertStringContainsString( 'rollup-page-content', $response->getBody() );
	}


	public function testBlockDefaultRollupPageCSS() {

		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );

		$this->assertStringContainsString( 'css/rolluppage.css', $response->getBody() );

		RollupPage::config()->update( 'block_default_rollup_page_css', true );

		$response = $this->get( $rollupPage->Link() );

		$this->assertStringNotContainsString( 'css/rolluppage.css', $response->getBody() );
	}


	public function testBlockDefaultRollupPageJavascript() {

		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );

		$this->assertStringContainsString( 'javascript/rolluppage.js', $response->getBody() );

		RollupPage::config()->update( 'block_default_rollup_page_js', true );

		$response = $this->get( $rollupPage->Link() );

		$this->assertStringNotContainsString( 'javascript/rolluppage.js', $response->getBody() );
	}


}
