<?php

namespace Logicbrush\RollupPage\Tasks;

use SilverStripe\ORM\DB;
use Logicbrush\RollupPage\Model\RollupPage;
use SilverStripe\Dev\MigrationTask;

class UpdateRollupPageReferencesTask extends MigrationTask {

    private static $segment = 'UpdateRollupPageReferencesTask';

    protected $title = "Update RollupPage DB References";
    
    protected $description = "Updates the references to the class name in the datbase to the FQCN."; 

    public function up() {
        $this->runSql('RollupPage', RollupPage::class);
    }

    public function down() {
        $this->runSql(RollupPage::class, 'RollupPage');
    }

    private function runSql($from, $to) {
        
		$from = \SilverStripe\Core\Convert::raw2sql($from);
		$to = \SilverStripe\Core\Convert::raw2sql($to);

        DB::query("update `SiteTree` set ClassName = '{$to}' where ClassName = '{$from}'");
        
        DB::query("update `SiteTree_Live` set ClassName = '{$to}' where ClassName = '{$from}'");
        
        DB::query("update `SiteTree_Versions` set ClassName = '{$to}' where ClassName = '{$from}'");
        
        DB::query("update `SiteTreeLink` set ParentClass = '{$to}' where ParentClass = '{$from}'");
        
    }
}
