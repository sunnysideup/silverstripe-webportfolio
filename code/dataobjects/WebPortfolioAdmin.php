<?php

class WebPortfolioAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'WebPortfolioItem',
        'WebPortfolioAgent',
        'WebPortfolioWhatWeDidDescriptor',
    );

    private static $url_segment = 'webportfolio'; // will be linked as /admin/products

    private static $menu_title = 'Web Portfolio';
}
