<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 *
 *
 *
 */

class WebPortfolioPageTimeLine extends Page
{

    private static $icon = "webportfolio/images/treeicons/WebPortfolioPageTimeLine";

    private static $db = array(
        "TimeLineHeader" => "Varchar",
        "TimeLineIntro" => "HTMLText",
        "JSON" => "Text"
    );

    private static $many_many = array(
        "WebPortfolioItems" => "WebPortfolioItem"
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab("Root.Portfolio", new TextField("TimeLineHeader", "Time line header"));
        $fields->addFieldToTab("Root.Portfolio", new HtmlEditorField("TimeLineIntro", "Time line intro"));
        $itemOptionSet = WebPortfolioItem::get();
        $itemOptionSetMap = ($itemOptionSet->count()) ? $itemOptionSet->map('ID', 'Title')->toArray() : array();
        $fields->addFieldsToTab("Root.Portfolio",
            array(
                new LiteralField("UpdatePortfolio", "<h3>Update Portfolio</h3>"),
                new LiteralField("EditPortfolio", "<p><a href=\"/admin/webportfolio\" target=\"_blank\">edit portfolio</a></p>"),
                new LiteralField("RefreshPortfolio", "<p><a href=\"".$this->Link("json/?flush=json")."\" target=\"_blank\">clear portfolio cache</a> (portfolio data is cached to incrase loading speed)</p>"),
                new LiteralField("SelectPortfolio", "<h3>Select Portfolio</h3>"),
                new CheckboxSetField(
                    $name = "WebPortfolioItems",
                    $title = "Items shown",
                    $source = $itemOptionSetMap
                )
            )
        );
        return $fields;
    }

    public function createJSON()
    {
        SSViewer::set_source_file_comments(false);
        $json = '
{
		"timeline":
		{
				"headline":'.$this->html2json($this->TimeLineHeader).',
				"type":"default",
				"text": '.$this->html2json($this->TimeLineIntro).',
				"date": [';
                //'.$this->html2json($this->TimeLineIntro).'';//
                //"asset": {
                //    "media":"http://yourdomain_or_socialmedialink_goes_here.jpg",
                //    "credit":"Credit Name Goes Here",
                //    "caption":"Caption text goes here"
                //},

        $data = $this->WebPortfolioItems();
        if ($data && $data->count()) {
            $dayExistsArray = array();
            foreach ($data as $site) {
                if ($site->StartDate) {
                    $startDateRaw = $site->StartDate;
                } else {
                    $startDateRaw = $this->Created;
                }
                $startDateArray = explode("-", $startDateRaw);
                $startDate = intval($startDateArray[0]). ",".intval($startDateArray[1]). ",".intval($startDateArray[2]);
                $headLine = $this->html2json($site->getHeadLine());
                $text = $this->html2json($site->renderWith("WebPortfolioPageOneItemTimeline")); // //
                $json .= '
						{
								"startDate":"'.$startDate.'",
								"headline": '.$headLine.',
								"text": '.$text.'
						}
				';
                if (!$site->Last()) {
                    $json .= ",";
                }
            }
        }
/*
                "era": [
                        {
                                "startDate":"2011,12,10",
                                "endDate":"2011,12,11",
                                "headline":"Headline Goes Here",
                                "text":"<p>Body text goes here, some HTML is OK</p>",
                                "tag":"This is Optional"
                        }

                ]
                */
        $json .='
			]
		}
}';
        if ($json) {
            $this->JSON = $json;
            $this->writeToStage('Stage');
            $this->Publish('Stage', 'Live');
            $this->Status = "Published";
            $this->flushCache();
        }
        return $json;
    }

    protected function html2json($html)
    {
        $html = preg_replace('!\s+!', ' ', $html);
        if (!trim($html)) {
            $html = "&nbsp;";
        }
        $json = Convert::raw2json($html);
        return $json;
    }
}

class WebPortfolioPageTimeLine_Controller extends Page_Controller
{

    private static $allowed_actions = array(
        "json"
    );

    private static $ajax_file_location = "webportfolio/javascript/timeline-executive.js";

    public function init()
    {
        parent::init();
        Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
        if (class_exists("PrettyPhoto")) {
            PrettyPhoto::include_code();
        } else {
            user_error("It is recommended that you include the PrettyPhoto Module", E_USER_NOTICE);
        }
        Requirements::javascript($this->Config()->get("ajax_file_location"));
        Requirements::javascript("webportfolio/thirdparty/TimelineJS/compiled/js/storyjs-embed.js");
        Requirements::themedCSS("WebPortfolioPageTimeLine", "webportfolio");
    }

    public function json($request)
    {
        SSViewer::set_source_file_comments(false);
        if (isset($_GET['flush']) || !$this->JSON) {
            return $this->createJSON();
        }
        return $this->JSON;
    }

    public function JSONLink()
    {
        return Director::absoluteURL($this->Link("json/"));
    }
}
