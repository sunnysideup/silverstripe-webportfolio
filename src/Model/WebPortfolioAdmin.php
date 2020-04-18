<?php

namespace Sunnysideup\WebPortfolio\Models;

use SilverStripe\Admin\ModelAdmin;

class WebPortfolioAdmin extends ModelAdmin
{
    private static $managed_models = [
        WebPortfolioItem::class,
        WebPortfolioAgent::class,
        WebPortfolioWhatWeDidDescriptor::class,
    ];

    private static $url_segment = 'webportfolio'; // will be linked as /admin/products

    private static $menu_title = 'Web Portfolio';
}
