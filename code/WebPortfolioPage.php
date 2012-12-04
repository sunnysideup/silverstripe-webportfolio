<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 *
 *
 *
 */

class WebPortfolioPage extends Page {

	static $icon = "webportfolio/images/treeicons/WebPortfolioPage";

	public static $db = array();

	public static $has_one = array();

	public static $many_many = array(
		"WebPortfolioItems" => "WebPortfolioItem"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$itemOptionSet = DataObject::get("WebPortfolioItem");
		$itemOptionSetMap = ($itemOptionSet) ? $itemOptionSet->map('ID', 'Title') : array();
		$fields->addFieldsToTab("Root.Content.Portfolio",
			array(
				new LiteralField("UpdatePortfolio", "<h3>Update Portfolio</h3>"),
				new LiteralField("EditPortfolio", "<p><a href=\"/admin/webportfolio\" target=\"_blank\">edit portfolio</a></p>"),
				new LiteralField("RefreshPortfolio", "<p><a href=\"".$this->Link("json/?flush=json")."\" target=\"_blank\">clear portfolio cache</a> (portfolio data is cached to incrase loading speed)</p>"),
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

class WebPortfolioPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		if(class_exists("PrettyPhoto")) {
			PrettyPhoto::include_code();
		}
		else {
			user_error("It is recommended that you include the PrettyPhoto Module", E_USER_NOTICE);
		}
		Requirements::javascript("webportfolio/javascript/webportfolio.js");
		Requirements::themedCSS("WebPortfolioPage");

	}

	protected $IDArray = array();
	protected $hasFilter = false;
	protected $currentCode = "";

	function index(){
		$this->MetaTitle .= " - Favourites";
		$this->Title .= " - Favourites";
		return Array();
	}

	function show(){
		$this->hasFilter = true;
		$code = Convert::raw2sql($this->request->param("ID"));
		if(is_numeric($code) && intval($code) > 0) {
			$this->currentCode = $code;
			$item = DataObject::get_by_id("WebPortfolioItem", intval($code));
			if($item) {
				$this->IDArray = array($item->ID => $item->ID);
				$this->Title .= " - ".$item->getHeadLine();
				$this->MetaTitle .= " - ".$item->getHeadLine();
			}
		}
		elseif($code) {
			$this->currentCode = $code;
			$obj = DataObject::get_one("WebPortfolioWhatWeDidDescriptor", "\"Code\" = '$code'");
			$this->Title .= " - ".$obj->Name;
			$this->MetaTitle .= " - ".$obj->Name;
			if($obj) {
				$components = $obj->getManyManyComponents('WebPortfolioItem');
				if($components && $components->count()) {
					$this->IDArray = $components->column("ID");
				}
			}
		}
		return array();
	}

	function SelectedWebPortfolioItems(){
		if($this->hasFilter) {

		}
		else {
			$components = $this->getManyManyComponents('WebPortfolioItems');
			if($components && $components->count()) {
				$this->IDArray = $components->column("ID");
			}
		}
		$reset = false;
		if(!$this->IDArray) {
			$reset = true;
		}
		elseif(!is_array($this->IDArray)) {
			$reset = true;
		}
		elseif(!count($this->IDArray)) {
			$reset = true;
		}
		if($reset) {
			$this->IDArray = array(0 => 0);
		}
		$extraWhere = "";
		if(!$this->hasFilter) {
			$extraWhere = " AND \"Favourites\" = 1";
		}
		return DataObject::get(
			"WebPortfolioItem",
			"\"WebPortfolioItem\".\"ID\" IN (".implode(",", $this->IDArray).") AND \"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioPageID\" = ".$this->ID.$extraWhere,
			"\"Favourites\" DESC, RAND()",
			" INNER JOIN \"WebPortfolioPage_WebPortfolioItems\" ON \"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioItemID\" = \"WebPortfolioItem\".\"ID\""
		);
	}

	function HasFilter(){
		return $this->hasFilter;
	}

	function FilterList() {
		$items = DataObject::get("WebPortfolioWhatWeDidDescriptor", null, null, " INNER JOIN \"WebPortfolioItem_WhatWeDid\" ON \"WebPortfolioItem_WhatWeDid\".\"WebPortfolioWhatWeDidDescriptorID\" = \"WebPortfolioWhatWeDidDescriptor\".\"ID\"");
		foreach($items as $item) {
			if($item->Code == $this->currentCode ) {
				$item->LinkingMode = "current";
			}
			else {
				$item->LinkingMode = "link";
			}
		}
		return $items;
	}

}
