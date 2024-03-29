<?php
/**
 * src/Controllers/RollupPageController.php
 *
 * @package default
 */


namespace Logicbrush\RollupPage\Controllers;

use PageController;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\FieldType\DBField;

class RollupPageController extends PageController
{

	/**
	 *
	 */
	public function init() {
		parent::init();

		$page = $this->data();

		if ( ! $page->config()->get( 'block_default_rollup_page_css' ) ) {
			Requirements::css( 'logicbrush/silverstripe-rolluppage:css/rolluppage.css' );
		}

		if ( ! $page->config()->get( 'block_default_rollup_page_js' ) ) {
			Requirements::javascript( 'logicbrush/silverstripe-rolluppage:javascript/rolluppage.js' );
		}
	}


	/**
	 *
	 * @return unknown
	 */
	public function index() {
		// return composite.
		return [
			'Content' => DBField::create_field( 'HTMLText', $this->Content() ),
		];
	}


}
