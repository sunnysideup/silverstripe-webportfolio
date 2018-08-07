<?php

namespace Sunnysideup\WebPortfolio\Control;

use Page;

use HtmlEditorField;







use PrettyPhoto;

use Sunnysideup\WebPortfolio\Models\WebPortfolioItem;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Core\Config\Config;
use SilverStripe\View\SSViewer;
use SilverStripe\Core\Convert;
use SilverStripe\View\Requirements;
use Sunnysideup\WebPortfolio\Pages\WebPortfolioPageTimeLine;
use SilverStripe\Control\Director;
use PageController;



class WebPortfolioPageTimeLineController extends PageController
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
        Requirements::themedCSS(WebPortfolioPageTimeLine::class, "webportfolio");
    }

    public function json($request)
    {
        Config::inst()->set(SSViewer::class, 'source_file_comments', false);
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
