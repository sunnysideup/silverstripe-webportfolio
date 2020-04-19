<?php

namespace Sunnysideup\WebPortfolio\Pages;

use Page;








use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\LiteralField;
use Sunnysideup\WebPortfolio\Model\WebPortfolioItem;

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 */

class WebPortfolioPage extends Page
{
    private static $table_name = 'WebPortfolioPage';

    private static $icon = 'webportfolio/images/treeicons/WebPortfolioPage';

    private static $db = [
        'HighlightsOnly' => 'Boolean',
    ];

    private static $has_one = [];

    private static $many_many = [
        'WebPortfolioItems' => WebPortfolioItem::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $itemOptionSet = WebPortfolioItem::get();
        $itemOptionSetMap = $itemOptionSet->count() ? $itemOptionSet->map('ID', 'Title')->toArray() : [];
        $fields->addFieldsToTab(
            'Root.Portfolio',
            [
                CheckboxField::create(
                    'HighlightsOnly',
                    'Highlights Only'
                ),
                LiteralField::create('UpdatePortfolio', '<h3>Update Portfolio</h3>'),
                LiteralField::create('EditPortfolio', '<p><a href="/admin/webportfolio" target="_blank">edit portfolio</a></p>'),
                LiteralField::create('RefreshPortfolio', '<p><a href="' . $this->Link('json/?flush=json') . '" target="_blank">clear portfolio cache</a> (portfolio data is cached to increase loading speed)</p>'),
                CheckboxSetField::create(
                    $name = 'WebPortfolioItems',
                    $title = 'Items shown',
                    $source = $itemOptionSetMap
                ),
            ]
        );
        return $fields;
    }
}
