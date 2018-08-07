<?php

namespace Sunnysideup\WebPortfolio\Models;


use Sunnysideup\WebPortfolio\Models\WebPortfolioItem;
use Sunnysideup\WebPortfolio\Models\WebPortfolioAgent;
use Sunnysideup\WebPortfolio\Models\WebPortfolioWhatWeDidDescriptor;
use SilverStripe\Admin\ModelAdmin;



class WebPortfolioAdmin extends ModelAdmin
{

    private static $managed_models = array(
        WebPortfolioItem::class,
        WebPortfolioAgent::class,
        WebPortfolioWhatWeDidDescriptor::class,
    );

    private static $url_segment = 'webportfolio'; // will be linked as /admin/products

    private static $menu_title = 'Web Portfolio';
}
