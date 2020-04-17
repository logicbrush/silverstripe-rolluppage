<?php

namespace Logicbrush\RollupPage\Tests;

use Logicbrush\RollupPage\Model\RollupPage;
use SilverStripe\Dev\FunctionalTest;

class RollupPageControllerTest extends FunctionalTest
{
	protected $usesDatabase = true;

	public function testDisplayingRollupPage() {
		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertPartialMatchBySelector( 'h1', [
				'Rollup Page',
			] );
	}


	public function testBlockDefaultRollupPageCSS() {

		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );

		$this->assertContains( 'css/rolluppage.css', $response->getBody() );

		RollupPage::config()->update( 'block_default_rollup_page_css', true );

		$response = $this->get( $rollupPage->Link() );

		$this->assertNotContains( 'css/rolluppage.css', $response->getBody() );
	}


	public function testBlockDefaultRollupPageJavascript() {

		$rollupPage = RollupPage::create();
		$rollupPage->Title = 'Rollup Page';
		$rollupPage->Content = '<p>Rollup</p>';
		$rollupPage->write();
		$rollupPage->publish( 'Stage', 'Live' );

		$response = $this->get( $rollupPage->Link() );

		$this->assertContains( 'javascript/rolluppage.js', $response->getBody() );

		RollupPage::config()->update( 'block_default_rollup_page_js', true );

		$response = $this->get( $rollupPage->Link() );

		$this->assertNotContains( 'javascript/rolluppage.js', $response->getBody() );
	}


}
