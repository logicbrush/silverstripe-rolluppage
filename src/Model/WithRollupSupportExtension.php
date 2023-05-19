<?php
/**
 * src/Model/WithRollupSupportExtension.php
 *
 * @package default
 */


namespace Logicbrush\RollupPage\Model;

use SilverStripe\CMS\Controllers\CMSPageEditController;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class WithRollupSupportExtension extends DataExtension
{
	private static $db = [
		'NeverRollup' => 'Boolean',
	];

	/**
	 *
	 * @param FieldList $fields
	 */
	public function updateSettingsFields( FieldList $fields ) {
		$fields->insertAfter( 'ShowInSearch', CheckboxField::create( 'NeverRollup', 'Never rollup this page?' ) );
	}


	/**
	 *
	 * @param unknown $link
	 * @param unknown $action
	 * @param unknown $relativeLink
	 */
	public function updateLink( &$link, &$action, &$relativeLink ) {
		if ( $action === null ) {
			if ( ! $this->owner->NeverRollup &&
				! ( Controller::has_curr() && Controller::curr() instanceof CMSPageEditController ) &&
				$this->owner->Parent() instanceof RollupPage &&
				$this->owner->Parent()->ShowLinksOnly !== 1
			) {
				$link = $this->owner->Parent()->Link() . '#' . $this->owner->URLSegment;
			}
		}
	}


}
