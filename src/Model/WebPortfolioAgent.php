<?php

namespace Sunnysideup\WebPortfolio\Models;


use Sunnysideup\WebPortfolio\Models\WebPortfolioItem;
use SilverStripe\ORM\DataObject;


/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioAgent extends DataObject
{

    private static $table_name = 'WebPortfolioAgent';

    private static $db = array(
        "Name" => "Varchar(255)",
        "AgentWebAddress" => "Varchar(255)"
    );

    private static $has_many = array(
        "WebPortfolioItem" => WebPortfolioItem::class
    );

    private static $default_sort = "Name";

    private static $searchable_fields = array(
        "Name" => "PartialMatchFilter",
        "AgentWebAddress"
    );

    private static $singular_name = "Web Portfolio Agent";

    private static $plural_name = "Web Portfolio Agents";
}
