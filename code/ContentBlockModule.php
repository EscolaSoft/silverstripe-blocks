<?php

namespace SilverStripe_Blocks;

use SilverStripe\ORM\DataExtension;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe_Blocks\GridFieldDuplicateBlocksButton;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

class ContentBlocksModule extends DataExtension
{

    private static $create_block_tab = true;
    private static $contentarea_rows = 12;
    private static $many_many = [
        'Blocks' => Block::class
    ];
    private static $many_many_extraFields = [
        'Blocks' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function updateCMSFields(\SilverStripe\Forms\FieldList $fields)
    {
        // Relation handler for Blocks
        $SConfig = \SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor::create(125);
        if (class_exists('Symbiote\GridFieldExtensions\GridFieldOrderableRows')) {
            $SConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
        }

        $SConfig->removeComponentsByType('SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter');
        //$SConfig->addComponent(new GridFieldDuplicateBlocksButton("buttons-before-right"));

        $SConfig->addComponent(new \SilverStripe\Forms\GridField\GridFieldDeleteAction());

        // If the copy button module is installed, add copy as option
        if (class_exists('GridFieldCopyButton')) {
            $SConfig->addComponent(new GridFieldCopyButton(), 'GridFieldDeleteAction');
        }

        $gridField = new \SilverStripe\Forms\GridField\GridField("Blocks", "Content blocks", $this->owner->Blocks(), $SConfig);
        $classes = array_values(\SilverStripe\Core\ClassInfo::subclassesFor($gridField->getModelClass()));
        $classes = array_splice($classes, 1); //Removing
        $classes_tmp = array();
        foreach ($classes as $key => $value) {
            $classes_tmp[$value] = $value;
        }
        $classes = $classes_tmp;


        if (count($classes) > 1 && class_exists('\Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass')) {
            $gfanmc = new \Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass("buttons-before-left");
			$gfanmc->setClasses($classes);

            $SConfig->removeComponentsByType('SilverStripe\Forms\GridField\GridFieldAddNewButton');
            $SConfig->addComponent($gfanmc);
        }
        if (self::$create_block_tab) {
            $fields->addFieldToTab("Root.Blocks", $gridField);
        } else {
            // Downsize the content field
            $fields->removeByName('Content');
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content')->setRows(self::$contentarea_rows), 'Metadata');

            $fields->addFieldToTab("Root.Main", $gridField, 'Metadata');
        }

        if (class_exists('Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton')) {
            $SConfig->addComponent(new GridFieldAddExistingSearchButton('buttons-before-right'));
        }


        return $fields;
    }

    public function getDynamicHelpContent($classes)
    {

        $html = <<<HTML
<table class="blocks-help">
  <thead>
    <tr>
      <th class="name">Name</th>
      <th class="thumbnail">Example</th>
      <th class="description">Description</th>
    </tr>
  </thead>
<tbody>
HTML;
        foreach ($classes as $class_name) {
            $html .= '
            <tr>
                <td class="name">' . $class_name . '</td>
                <td class="thumbnail">' . $class_name::getCMSThumbnail() . '</td>
                <td class="description">' . $class_name::getCMSHelp() . '</td>
            </tr>
';
        }
        $html .= <<<HTML
            </tbody>
            </table>
HTML;
        return $html;
    }

    public function ActiveBlocks()
    {
        return $this->owner->Blocks()->filter(array('Active' => '1'))->sort('SortOrder');
    }

    public function OneBlock($id)
    {
        return \Tools\Blocks\Block::get()->byID($id);
    }
}
