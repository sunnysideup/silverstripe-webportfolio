<?php

namespace Sunnysideup\WebPortfolio\Admin;

use SilverStripe\Admin\ModelAdmin;
use Sunnysideup\WebPortfolio\Model\WebPortfolioItem;
use Sunnysideup\WebPortfolio\Model\WebPortfolioAgent;
use Sunnysideup\WebPortfolio\Model\WebPortfolioWhatWeDidDescriptor;

class WebPortfolioAdmin extends ModelAdmin
{
    private static $managed_models = [
        WebPortfolioItem::class,
        WebPortfolioAgent::class,
        WebPortfolioWhatWeDidDescriptor::class,
    ];

    private static $url_segment = 'webportfolio'; // will be linked as /admin/products

    private static $menu_title = 'Portfolio';
}
