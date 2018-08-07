<?php

namespace Sunnysideup\WebPortfolio;

use Page;






use PrettyPhoto;


use Sunnysideup\WebPortfolio\Dataobjects\WebPortfolioItem;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\View\Requirements;
use Sunnysideup\WebPortfolio\WebPortfolioPage;
use SilverStripe\Core\Convert;
use Sunnysideup\WebPortfolio\Dataobjects\WebPortfolioWhatWeDidDescriptor;
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

class WebPortfolioPage_Controller extends PageController
{
    private static $allowed_actions = array(
        "show"
    );

    public function init()
    {
        parent::init();
        Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
        if (class_exists("PrettyPhoto")) {
            PrettyPhoto::include_code();
        } else {
            user_error("It is recommended that you include the PrettyPhoto Module", E_USER_NOTICE);
        }
        Requirements::javascript("webportfolio/javascript/webportfolio.js");
        Requirements::themedCSS(WebPortfolioPage::class, "webportfolio");
    }

    protected $IDArray = array();
    protected $hasFilter = false;
    protected $currentCode = "";
    protected $currentDescription = "";

    public function index()
    {
        if (!$this->HighlightsOnly) {
            $this->Title .= " - Favourites";
        }
        return array();
    }

    public function show()
    {
        $this->hasFilter = true;
        $code = Convert::raw2sql($this->request->param("ID"));
        if (is_numeric($code) && intval($code) > 0) {
            $this->currentCode = $code;
            $item = WebPortfolioItem::get()->byID(intval($code));
            if ($item) {
                $this->IDArray = array($item->ID => $item->ID);
                $this->Title .= " - ".$item->getHeadLine();
                $this->currentDescription = $item->Notes;
            }
        } elseif ($code) {
            $this->currentCode = $code;
            $obj = WebPortfolioWhatWeDidDescriptor::get()->filter(array("Code" => $code))->first();
            $this->Title .= " - ".$obj->Name;
            if ($obj) {
                $this->currentDescription = $obj->Description;
                $components = $obj->getManyManyComponents(WebPortfolioItem::class);
                if ($components && $components->count()) {
                    $this->IDArray = $components->column("ID");
                }
            }
        }
        return array();
    }

    public function SelectedWebPortfolioItems()
    {
        if ($this->HighlightsOnly) {
            return $this->WebPortfolioItems()
                ->sort(array("Favourites" => "DESC", "RAND()" => "ASC"));
        }
        if ($this->hasFilter) {
        } else {
            $components = $this->getManyManyComponents('WebPortfolioItems');
            if ($components && $components->count()) {
                $this->IDArray = $components->column("ID");
            }
        }
        $reset = false;
        if (!$this->IDArray) {
            $reset = true;
        } elseif (!is_array($this->IDArray)) {
            $reset = true;
        } elseif (!count($this->IDArray)) {
            $reset = true;
        }
        if ($reset) {
            $this->IDArray = array(0 => 0);
        }
        $extraWhere = "";
        if (!$this->hasFilter) {
            $extraWhere = " AND \"Favourites\" = 1";
        }
        return WebPortfolioItem::get()
            ->where("\"WebPortfolioItem\".\"ID\" IN (".implode(",", $this->IDArray).") AND \"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioPageID\" = ".$this->ID.$extraWhere)
            ->sort(array("Favourites" => "DESC", "RAND()" => "ASC"))
            ->innerJoin("WebPortfolioPage_WebPortfolioItems", "\"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioItemID\" = \"WebPortfolioItem\".\"ID\"");
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
            ->innerJoin("WebPortfolioItem_WhatWeDid", " \"WebPortfolioItem_WhatWeDid\".\"WebPortfolioWhatWeDidDescriptorID\" = \"WebPortfolioWhatWeDidDescriptor\".\"ID\"");
        foreach ($items as $item) {
            if ($item->Code == $this->currentCode) {
                $item->LinkingMode = "current";
            } else {
                $item->LinkingMode = "link";
            }
        }
        return $items;
    }
}
