<?php

namespace Logicbrush\RollupPage\Model;

use Logicbrush\RollupPage\Controllers\RollupPageController;
use Page;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\ArrayList;

class RollupPage extends Page
{
	const ROLLUP_PAGE_DISPLAY_TYPE = [
		0 => 'content',
		1 => 'list',
		2 => 'tabs',
	];

	private static $icon = 'logicbrush/silverstripe-rolluppage:images/treeicons/rollup-page.png';
	private static $description = 'A page that rolls up content from its children.';

	private static $table_name = 'RollupPage';

	private static $db = [
		'ShowLinksOnly' => 'Int',
	];

	/**
	 * Set this to true to disable automatic inclusion of CSS files
	 * @config
	 * @var bool
	 */
	private static $block_default_rollup_page_css = false;

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->insertBefore(
			OptionsetField::create(
				'ShowLinksOnly',
				'Rollup Display',
				[
					0 => 'Show children inline',
					2 => 'Show children in tabs',
					1 => 'Show children as links',
				]
			),
			'Content' );
		$contentField = $fields->dataFieldByName( 'Content' );
		$contentField->setTitle( 'Introduction' );

		return $fields;
	}


	public function Children() {
		if ( ! $this->ShowLinksOnly ) {
			return ArrayList::create();
		}
		$children = parent::Children();

		return parent::Children()->exclude( ['Content' => ''] );
	}


	public function getRollupPageDisplayType() {
		return self::ROLLUP_PAGE_DISPLAY_TYPE[$this->ShowLinksOnly];
	}


	public function Content() {
		// original content.
		$content = $this->Content;

		if ( $this->ShowLinksOnly ) {
			$content .= '<ul class="rollup-page-' . $this->getRollupPageDisplayType() . '">';
			foreach ( $this->AllChildren() as $child ) {
				if ( ! $child->NeverRollup ) {
					$childContent = $child->hasMethod( 'Content' ) ? $child->Content() : $child->Content;
					if ( $child->ShowInMenus ) {
						if ( $childContent ) {
							$content .= '<li><a href="' . $child->Link() . '">' . $child->MenuTitle . '</a></li>';
						} else {
							$content .= '<li><span>' . $child->MenuTitle . '</span></li>';
						}
					}
				}
			}
			$content .= '</ul>';
		} else {
			foreach ( $this->AllChildren() as $child ) {
				if ( ! $child->NeverRollup ) {
					$childContent = $child->hasMethod( 'Content' ) ? $child->Content() : $child->Content;
					if ( $childContent ) {

						// The class may implement a 'BeforeRollup'
						// method that allows some content to be
						// inserted before the main content.
						if ( $child->hasMethod( 'BeforeRollup' ) ) {
							$content .= $child->BeforeRollup();
						}

						$content .= '<h2><a name="' . $child->URLSegment . '"></a>' . $child->Title . '</h2>';
						$content .= $childContent;

						// Likewise, there is an 'AfterRollup' method.
						if ( $child->hasMethod( 'AfterRollup' ) ) {
							$content .= $child->AfterRollup();
						}
					}
				}
			}
		}

		return $content;
	}


	public function getControllerName() {
		return RollupPageController::class;
	}


}
