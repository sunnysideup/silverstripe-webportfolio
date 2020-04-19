<?php

namespace Sunnysideup\WebPortfolio\Model;

use SilverStripe\ORM\DataObject;
use Sunnysideup\WebPortfolio\Model\WebPortfolioItem;

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioAgent extends DataObject
{
    private static $table_name = 'WebPortfolioAgent';

    private static $db = [
        'Name' => 'Varchar(255)',
        'AgentWebAddress' => 'Varchar(255)',
    ];

    private static $has_many = [
        'WebPortfolioItem' => WebPortfolioItem::class,
    ];

    private static $default_sort = 'Name';

    private static $searchable_fields = [
        'Name' => 'PartialMatchFilter',
        'AgentWebAddress',
    ];

    private static $singular_name = 'Web Portfolio Agent';

    private static $plural_name = 'Web Portfolio Agents';
}
