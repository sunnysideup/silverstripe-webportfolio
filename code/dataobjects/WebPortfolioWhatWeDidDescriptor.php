<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioWhatWeDidDescriptor extends DataObject {

	private static $db = array(
		"Name" => "Varchar(255)",
		"Code" => "Varchar(255)",
		"Description" => "Text"
	);

	private static $belongs_many_many = array(
		"WebPortfolioItem" => "WebPortfolioItem"
	);

	private static $default_sort = "Name";

	private static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"Description" => "PartialMatchFilter"
	);

	private static $summary_fields = array(
		"Name",
		"Description"
	);

	private static $indexes = array(
		"Code" => true
	);

	private static $singular_name = "What We Did Descriptor";

	private static $plural_name = "What We Did Descriptors";

	function Link() {
		$link = '';
		if($page = WebPortfolioPage::get()->first()) {
			if(!$this->Code) {
				$this->Code = $page->generateURLSegment($this->Name);
				$this->write();
			}
			$link = $page->Link().'show/'.$this->Code."/";
		}
		return $link;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Code");
		if($this->ID) {
			$dos = WebPortfolioWhatWeDidDescriptor::get()
				->exclude(array("ID" => $this->ID));
			if($dos->count()) {
				$dosArray = array("" => "--- do not merge ---");
				$dosArray += $dos->map("ID" => "Name")->toArray();
				$fields->addFieldToTab("Root.Merge", new DropdownField("MergeID", "Merge <i>$this->Name</i> into:", $dosArray));
			}
		}
		$dos = WebPortfolioItem::get();
		if($dos->count() && $this->ID) {
			$dosArray = $dos->map()->toArray();
			$fields->addFieldsToTab(
				"Root.WebPortfolioItem",
				array(
					new CheckboxSetField("WebPortfolioItem", "Carried out for", $dosArray)
				)
			);
		}
		if(class_exists("DataObjectOneFieldUpdateController")) {
			$link = DataObjectOneFieldUpdateController::popup_link(
				$ClassName = $this->ClassName,
				$FieldName = "Description",
				$where = '',
				$sort = 'Description DESC',
				$linkText = 'Edit Description'
			);
			$fields->addFieldToTab("Root.Main", new LiteralField("EditDescription", $link), "Description");
		}
		return $fields;
	}


	protected $mergeInto = null;

	function onAfterWrite(){
		parent::onAfterWrite();
		if($this->mergeInto) {
			DB::query("UPDATE \"WebPortfolioItem_WhatWeDid\" SET \"WebPortfolioWhatWeDidDescriptorID\" = ".$this->mergeInto->ID." WHERE \"WebPortfolioWhatWeDidDescriptorID\"  = ".$this->ID);
			$this->delete();
		}
		if(isset($_REQUEST["MergeID"])) {
			unset($_REQUEST["MergeID"]);
		}
		$this->mergeInto = null;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(isset($_REQUEST["MergeID"])) {
			$mergeID = intval($_REQUEST["MergeID"]);
			if($mergeID) {
				$this->mergeInto = WebPortfolioWhatWeDidDescriptor::get()->byID($mergeID);
			}
		}
		if($page = WebPortfolioPage::get()->first()) {
			$link = $page->Link().'show/'.$this->Code."/";
			$this->Code = $page->generateURLSegment($this->Name);
		}
	}


}
