<?php

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

    private static $db = array();

    private static $has_one = array();

    private static $many_many = array(
        "WebPortfolioItems" => "WebPortfolioItem"
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $itemOptionSet = WebPortfolioItem::get();
        $itemOptionSetMap = ($itemOptionSet->count()) ? $itemOptionSet->map('ID', 'Title')->toArray() : array();
        $fields->addFieldsToTab("Root.Portfolio",
            array(
                new LiteralField("UpdatePortfolio", "<h3>Update Portfolio</h3>"),
                new LiteralField("EditPortfolio", "<p><a href=\"/admin/webportfolio\" target=\"_blank\">edit portfolio</a></p>"),
                new LiteralField("RefreshPortfolio", "<p><a href=\"".$this->Link("json/?flush=json")."\" target=\"_blank\">clear portfolio cache</a> (portfolio data is cached to increase loading speed)</p>"),
                new CheckboxSetField(
                    $name = "WebPortfolioItems",
                    $title = "Items shown",
                    $source = $itemOptionSetMap
                )
            )
        );
        return $fields;
    }
}

class WebPortfolioPage_Controller extends Page_Controller
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
        Requirements::themedCSS("WebPortfolioPage", "webportfolio");
    }

    protected $IDArray = array();
    protected $hasFilter = false;
    protected $currentCode = "";
    protected $currentDescription = "";

    public function index()
    {
        $this->Title .= " - Favourites";
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
                $components = $obj->getManyManyComponents('WebPortfolioItem');
                if ($components && $components->count()) {
                    $this->IDArray = $components->column("ID");
                }
            }
        }
        return array();
    }

    public function SelectedWebPortfolioItems()
    {
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
