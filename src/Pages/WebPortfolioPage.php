<?php

namespace Sunnysideup\WebPortfolio\Pages;

use Page;






use PrettyPhoto;


use Sunnysideup\WebPortfolio\Models\WebPortfolioItem;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\View\Requirements;
use Sunnysideup\WebPortfolio\Pages\WebPortfolioPage;
use SilverStripe\Core\Convert;
use Sunnysideup\WebPortfolio\Models\WebPortfolioWhatWeDidDescriptor;
use PageController;



/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 *
 *
 *
 */

class WebPortfolioPage extends Page
{


    private static $table_name = 'WebPortfolioPage';

    private static $icon = "webportfolio/images/treeicons/WebPortfolioPage";

    private static $db = array(
        'HighlightsOnly' => 'Boolean'
    );

    private static $has_one = array();

    private static $many_many = array(
        "WebPortfolioItems" => WebPortfolioItem::class
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $itemOptionSet = WebPortfolioItem::get();
        $itemOptionSetMap = ($itemOptionSet->count()) ? $itemOptionSet->map('ID', 'Title')->toArray() : array();
        $fields->addFieldsToTab(
            "Root.Portfolio",
            array(
                CheckboxField::create(
                    'HighlightsOnly',
                    'Highlights Only'
                ),
                LiteralField::create("UpdatePortfolio", "<h3>Update Portfolio</h3>"),
                LiteralField::create("EditPortfolio", "<p><a href=\"/admin/webportfolio\" target=\"_blank\">edit portfolio</a></p>"),
                LiteralField::create("RefreshPortfolio", "<p><a href=\"".$this->Link("json/?flush=json")."\" target=\"_blank\">clear portfolio cache</a> (portfolio data is cached to increase loading speed)</p>"),
                CheckboxSetField::create(
                    $name = "WebPortfolioItems",
                    $title = "Items shown",
                    $source = $itemOptionSetMap
                )
            )
        );
        return $fields;
    }
}
