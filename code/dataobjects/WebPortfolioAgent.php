<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioAgent extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"AgentWebAddress" => "Varchar(255)"
	);

	public static $has_many = array(
		"WebPortfolioItem" => "WebPortfolioItem"
	);

	public static $default_sort = "Name";

	public static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"AgentWebAddress"
	);

	public static $singular_name = "Web Portfolio Agent";

	public static $plural_name = "Web Portfolio Agents";

}