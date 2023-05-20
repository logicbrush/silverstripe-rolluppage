<?php
/**
 * src/Model/RollupPage.php
 *
 * @package default
 */


namespace Logicbrush\RollupPage\Model;

use Logicbrush\RollupPage\Controllers\RollupPageController;
use Page;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\ArrayList;

class RollupPage extends Page
{
	const DISPLAY_INLINE = 0;
	const DISPLAY_LIST = 1;
	const DISPLAY_TABS = 2;

	const ROLLUP_PAGE_DISPLAY_TYPE = [
		self::DISPLAY_INLINE => 'content',
		self::DISPLAY_LIST => 'list',
		self::DISPLAY_TABS => 'tabs',
	];

	private static $icon = 'logicbrush/silverstripe-rolluppage:images/treeicons/rollup-page.png';
	private static $description = 'A page that rolls up content from its children.';

	private static $table_name = 'RollupPage';

	private static $db = [
		'ShowLinksOnly' => 'Int',
	];

	/**
	 * Set this to true to disable automatic inclusion of CSS files
	 *
	 * @config
	 * @var bool
	 */
	private static $block_default_rollup_page_css = false;

	/**
	 * Set this to true to disable automatic inclusion of Javascript files
	 *
	 * @config
	 * @var bool
	 */
	private static $block_default_rollup_page_js = false;

	/**
	 *
	 * @Metrics( crap = 1 )
	 * @return unknown
	 */
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


	/**
	 *
	 * @Metrics( crap = 2 )
	 * @return unknown
	 */
	public function Children() {
		if ( $this->ShowLinksOnly !== 1 ) {
			return ArrayList::create();
		}
		$children = parent::Children();

		return parent::Children()->exclude( ['Content' => ''] );
	}


	/**
	 *
	 * @Metrics( crap = 1 )
	 * @return unknown
	 */
	public function getRollupPageDisplayType() {
		return self::ROLLUP_PAGE_DISPLAY_TYPE[$this->ShowLinksOnly];
	}


	/**
	 *
	 * @Metrics( crap = 21 )
	 * @return unknown
	 */
	public function Content() {
		// original content.
		$content = $this->Content;

		if ( $this->ShowLinksOnly === self::DISPLAY_LIST || $this->ShowLinksOnly === self::DISPLAY_TABS ) {
			$content .= '<nav class="rollup-page-navigation-' . $this->getRollupPageDisplayType() . '"><ul>';
			foreach ( $this->AllChildren() as $index => $child ) {
				if ( ! $child->NeverRollup ) {
					$childContent = $child->hasMethod( 'Content' ) ? $child->Content() : $child->Content;

					if ( $child->ShowInMenus ) {
						$content .= '<li' . ( $index === 0 ? ' class="active"' : '' ) . '>';

						if ( $childContent || $this->ShowLinksOnly === self::DISPLAY_LIST ) {
							$content .= '<a href="' . $child->Link() . '" data-url-segment="' . $child->URLSegment . '">' . $child->MenuTitle . '</a>';
						} else {
							$content .= '<span>' . $child->MenuTitle . '</span>';
						}

						$content .= '</li>';
					}
				}
			}
			$content .= '</ul></nav>';
		}

		if ( $this->ShowLinksOnly === self::DISPLAY_INLINE || $this->ShowLinksOnly === self::DISPLAY_TABS ) {
			foreach ( $this->AllChildren() as $index => $child ) {
				if ( ! $child->NeverRollup ) {
					$childContent = $child->hasMethod( 'Content' ) ? $child->Content() : $child->Content;
					if ( $childContent ) {

						if ( $this->ShowLinksOnly > 0 ) {
							$content .= '<div class="rollup-page-content' . ( $index === 0 ? ' active' : '' ) . '" id="rollup-page-content-' . $child->URLSegment . '">';
						}

						// The class may implement a 'BeforeRollup'
						// method that allows some content to be
						// inserted before the main content.
						if ( $child->hasMethod( 'BeforeRollup' ) ) {
							$content .= $child->BeforeRollup();
						}

						if ( $this->ShowLinksOnly != self::DISPLAY_TABS ) {
							// For tabs, the display of the header is redundant.
							$content .= '<h2><a name="' . $child->URLSegment . '"></a>' . $child->Title . '</h2>';
						}
						$content .= $childContent;

						// Likewise, there is an 'AfterRollup' method.
						if ( $child->hasMethod( 'AfterRollup' ) ) {
							$content .= $child->AfterRollup();
						}

						if ( $this->ShowLinksOnly > 0 ) {
							$content .= '</div>';
						}
					}
				}
			}
		}

		return $content;
	}


	/**
	 *
	 * @Metrics( crap = 1 )
	 * @return unknown
	 */
	public function getControllerName() {
		return RollupPageController::class;
	}


}
