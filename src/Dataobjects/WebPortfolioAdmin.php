<?php

namespace Sunnysideup\WebPortfolio\Dataobjects;


use Sunnysideup\WebPortfolio\Dataobjects\WebPortfolioItem;
use Sunnysideup\WebPortfolio\Dataobjects\WebPortfolioAgent;
use Sunnysideup\WebPortfolio\Dataobjects\WebPortfolioWhatWeDidDescriptor;
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
