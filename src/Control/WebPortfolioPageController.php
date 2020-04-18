<?php

namespace Sunnysideup\WebPortfolio\Control;

use PageController;

use SilverStripe\Core\Convert;

use SilverStripe\View\Requirements;
use Sunnysideup\PrettyPhoto\PrettyPhoto;
use Sunnysideup\WebPortfolio\Models\WebPortfolioItem;
use Sunnysideup\WebPortfolio\Models\WebPortfolioWhatWeDidDescriptor;
use Sunnysideup\WebPortfolio\Pages\WebPortfolioPage;

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 */

class WebPortfolioPageController extends PageController
{
    protected $IDArray = [];

    protected $hasFilter = false;

    protected $currentCode = '';

    protected $currentDescription = '';

    private static $allowed_actions = [
        'show',
    ];

    public function init()
    {
        parent::init();
        Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
        if (class_exists(PrettyPhoto::class)) {
            PrettyPhoto::include_code();
        } else {
            user_error('It is recommended that you include the PrettyPhoto Module', E_USER_NOTICE);
        }
        Requirements::javascript('webportfolio/javascript/webportfolio.js');
        Requirements::themedCSS(WebPortfolioPage::class, 'webportfolio');
    }

    public function index()
    {
        if (! $this->HighlightsOnly) {
            $this->Title .= ' - Favourites';
        }
        return [];
    }

    public function show()
    {
        $this->hasFilter = true;
        $code = Convert::raw2sql($this->request->param('ID'));
        if (is_numeric($code) && intval($code) > 0) {
            $this->currentCode = $code;
            $item = WebPortfolioItem::get()->byID(intval($code));
            if ($item) {
                $this->IDArray = [$item->ID => $item->ID];
                $this->Title .= ' - ' . $item->getHeadLine();
                $this->currentDescription = $item->Notes;
            }
        } elseif ($code) {
            $this->currentCode = $code;
            $obj = WebPortfolioWhatWeDidDescriptor::get()->filter(['Code' => $code])->first();
            $this->Title .= ' - ' . $obj->Name;
            if ($obj) {
                $this->currentDescription = $obj->Description;
                $components = $obj->getManyManyComponents(WebPortfolioItem::class);
                if ($components && $components->count()) {
                    $this->IDArray = $components->column('ID');
                }
            }
        }
        return [];
    }

    public function SelectedWebPortfolioItems()
    {
        if ($this->HighlightsOnly) {
            return $this->WebPortfolioItems()
                ->sort(['Favourites' => 'DESC', 'RAND()' => 'ASC']);
        }
        if ($this->hasFilter) {
        } else {
            $components = $this->getManyManyComponents('WebPortfolioItems');
            if ($components && $components->count()) {
                $this->IDArray = $components->column('ID');
            }
        }
        $reset = false;
        if (! $this->IDArray) {
            $reset = true;
        } elseif (! is_array($this->IDArray)) {
            $reset = true;
        } elseif (! count($this->IDArray)) {
            $reset = true;
        }
        if ($reset) {
            $this->IDArray = [0 => 0];
        }
        $extraWhere = '';
        if (! $this->hasFilter) {
            $extraWhere = ' AND "Favourites" = 1';
        }
        return WebPortfolioItem::get()
            ->where('"WebPortfolioItem"."ID" IN (' . implode(',', $this->IDArray) . ') AND "WebPortfolioPage_WebPortfolioItems"."WebPortfolioPageID" = ' . $this->ID . $extraWhere)
            ->sort(['Favourites' => 'DESC', 'RAND()' => 'ASC'])
            ->innerJoin('WebPortfolioPage_WebPortfolioItems', '"WebPortfolioPage_WebPortfolioItems"."WebPortfolioItemID" = "WebPortfolioItem"."ID"');
    }

    public function HasFilter()
    {
        return $this->hasFilter;
    }

    public function CurrentDescription()
    {
        return $this->currentDescription;
    }

    public function FilterList()
    {
        if ($this->HighlightsOnly) {
            return null;
        }
        $items = WebPortfolioWhatWeDidDescriptor::get()
            ->innerJoin('WebPortfolioItem_WhatWeDid', ' "WebPortfolioItem_WhatWeDid"."WebPortfolioWhatWeDidDescriptorID" = "WebPortfolioWhatWeDidDescriptor"."ID"');
        foreach ($items as $item) {
            if ($item->Code === $this->currentCode) {
                $item->LinkingMode = 'current';
            } else {
                $item->LinkingMode = 'link';
            }
        }
        return $items;
    }
}
