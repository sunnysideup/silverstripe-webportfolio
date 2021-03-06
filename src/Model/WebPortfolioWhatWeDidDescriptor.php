<?php

namespace Sunnysideup\WebPortfolio\Model;

use DataObjectOneFieldUpdateController;


use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use Sunnysideup\WebPortfolio\Pages\WebPortfolioPage;
use Sunnysideup\WebPortfolio\Model\WebPortfolioItem;

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioWhatWeDidDescriptor extends DataObject
{
    protected $mergeInto = null;

    private static $table_name = 'WebPortfolioWhatWeDidDescriptor';

    private static $db = [
        'Name' => 'Varchar(255)',
        'Code' => 'Varchar(255)',
        'Description' => 'Text',
    ];

    private static $belongs_many_many = [
        'WebPortfolioItem' => WebPortfolioItem::class,
    ];

    private static $default_sort = 'Name';

    private static $searchable_fields = [
        'Name' => 'PartialMatchFilter',
        'Description' => 'PartialMatchFilter',
    ];

    private static $summary_fields = [
        'Name',
        'Description',
    ];

    private static $indexes = [
        'Code' => true,
    ];

    private static $singular_name = 'What We Did Descriptor';

    private static $plural_name = 'What We Did Descriptors';

    public function Link()
    {
        $link = '';
        if ($page = WebPortfolioPage::get()->first()) {
            if (! $this->Code) {
                $this->Code = $page->generateURLSegment($this->Name);
                $this->write();
            }
            $link = $page->Link() . 'show/' . $this->Code . '/';
        }
        return $link;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Code');
        if ($this->ID) {
            $dos = self::get()
                ->exclude(['ID' => $this->ID]);
            if ($dos->count()) {
                $dosArray = ['' => '--- do not merge ---'];
                $dosArray += $dos->map('ID', 'Name')->toArray();
                $fields->addFieldToTab('Root.Merge', new DropdownField('MergeID', "Merge <i>{$this->Name}</i> into:", $dosArray));
            }
        }
        $dos = WebPortfolioItem::get();
        if ($dos->count() && $this->ID) {
            $dosArray = $dos->map()->toArray();
            $fields->addFieldsToTab(
                'Root.WebPortfolioItem',
                [
                    new CheckboxSetField(WebPortfolioItem::class, 'Carried out for', $dosArray),
                ]
            );
        }
        if (class_exists('DataObjectOneFieldUpdateController')) {
            $link = DataObjectOneFieldUpdateController::popup_link(
                $ClassName = $this->ClassName,
                $FieldName = 'Description',
                $where = '',
                $sort = 'Description DESC',
                $linkText = 'Edit Description'
            );
            $fields->addFieldToTab('Root.Main', new LiteralField('EditDescription', $link), 'Description');
        }
        return $fields;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->mergeInto) {
            DB::query('UPDATE "WebPortfolioItem_WhatWeDid" SET "WebPortfolioWhatWeDidDescriptorID" = ' . $this->mergeInto->ID . ' WHERE "WebPortfolioWhatWeDidDescriptorID"  = ' . $this->ID);
            $this->delete();
        }
        if (isset($_REQUEST['MergeID'])) {
            unset($_REQUEST['MergeID']);
        }
        $this->mergeInto = null;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (isset($_REQUEST['MergeID'])) {
            $mergeID = intval($_REQUEST['MergeID']);
            if ($mergeID) {
                $this->mergeInto = self::get()->byID($mergeID);
            }
        }
        if ($page = WebPortfolioPage::get()->first()) {
            $this->Code = $page->generateURLSegment($this->Name);
        }
    }
}
