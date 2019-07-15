<?php

namespace Logicbrush\RollupPage\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Control\Controller;
use SilverStripe\CMS\Controllers\CMSPageEditController;

class WithRollupSupportExtension extends DataExtension {

    private static $db = [
        'NeverRollup' => 'Boolean',
    ];

    public function updateCMSFields( FieldList $fields) {
        
		$fields->insertAfter( 'ShowInSearch', CheckboxField::create( 'NeverRollup', 'Never rollup this page?' ) );

    }

    public function updateLink( &$link, &$action, &$relativeLink ) {
		if ( $action === null ) {
			if ( ! $this->owner->NeverRollup && ! ( Controller::curr() instanceof CMSPageEditController ) && $this->owner->Parent() instanceof RollupPage && ! $this->owner->Parent()->ShowLinksOnly ) {
				$link = $this->owner->Parent()->Link() . '#' . $this->owner->URLSegment;
			}
		}
        
    }

}

