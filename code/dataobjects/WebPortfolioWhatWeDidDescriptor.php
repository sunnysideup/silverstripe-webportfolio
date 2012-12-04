<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioWhatWeDidDescriptor extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Code" => "Varchar(255)",
		"Description" => "Text"
	);

	public static $belongs_many_many = array(
		"WebPortfolioItem" => "WebPortfolioItem"
	);

	public static $default_sort = "Name";

	public static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"Description" => "PartialMatchFilter"
	);

	public static $summary_fields = array(
		"Name",
		"Description"
	);

	public static $indexes = array(
		"Code" => true
	);

	public static $singular_name = "What We Did Descriptor";

	public static $plural_name = "What We Did Descriptors";

	function Link() {
		$link = '';
		if($page = DataObject::get_one("WebPortfolioPage")) {
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
			$dos = DataObject::get("WebPortfolioWhatWeDidDescriptor", "WebPortfolioWhatWeDidDescriptor.ID <> ".$this->ID);
			if($dos) {
				$dosArray = $dos->toDropDownMap("ID", "Name", "-- do not merge --");
				$fields->addFieldToTab("Root.Merge", new DropdownField("MergeID", "Merge <i>$this->Name</i> into:", $dosArray));
			}
		}
		$dos = DataObject::get("WebPortfolioItem");
		if($dos && $this->ID) {
			$dosArray = $dos->toDropDownMap();
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
				$this->mergeInto = DataObject::get_by_id("WebPortfolioWhatWeDidDescriptor", $mergeID);
			}
		}
		if($page = DataObject::get_one("WebPortfolioPage")) {
			$link = $page->Link().'show/'.$this->Code."/";
		}
		if($page = DataObject::get_one("WebPortfolioPage")) {
			$this->Code = $page->generateURLSegment($this->Name);
		}
	}


}
