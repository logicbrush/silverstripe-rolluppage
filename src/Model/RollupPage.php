<?php

namespace Logicbrush\RollupPage\Model;
    
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBField;

class RollupPage extends \Page {

	private static $icon = 'logicbrush/silverstripe-rolluppage:images/treeicons/rollup-page.png';
	private static $description = "A page that rolls up content from its children.";

    private static $table_name = "RollupPage";

	private static $db = [
		'ShowLinksOnly' => 'Boolean',
	];

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->insertBefore( OptionsetField::create( 'ShowLinksOnly', 'Rollup Display', [ 0 => 'Show Full Content', 1 =>'Show Links Only' ] ),
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
		return parent::Children()->exclude( [ 'Content' => '' ] );
	}


	public function Content() {
		// original content.
		$content = $this->Content;

		if ( $this->ShowLinksOnly ) {
			$content .= '<ul>';
			foreach ( $this->AllChildren() as $child ) {
				if ( ! $child->NeverRollup ) {
					$childContent = $child->hasMethod( 'Content' ) ? $child->Content() : $child->Content;
					if ( $child->ShowInMenus ) {
						if ( $childContent ) {
							$content .= '<li><a href="' . $child->Link() . '">' . $child->MenuTitle . '</a></li>';
						} else {
							$content .= '<li>' . $child->MenuTitle . '</li>';
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
                        if ($child->hasMethod('BeforeRollup'))
                            $content .= $child->BeforeRollup();
                        
						$content .= '<h2><a name="' . $child->URLSegment . '"></a>' . $child->Title . '</h2>';
						$content .= $childContent;

                        // Likewise, there is an 'AfterRollup' method.
                        if ($child->hasMethod('AfterRollup'))
                            $content .= $child->AfterRollup();
					}
				}
			}
		}

		return $content;
	}


}


class RollupPageController extends \PageController {

	public function index() {

		// return composite.
		return [
			'Content' => DBField::create_field( 'HTMLText', $this->Content() )
		];
	}


}
