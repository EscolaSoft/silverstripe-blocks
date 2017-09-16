<?php

/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 20.07.17
 * Time: 15:06
 */
class ContentBlocksModule extends DataExtension {

  private static $create_block_tab = true;
  private static $contentarea_rows = 12;
  private static $many_many = array(
    'Blocks' => 'Block'
  );
  private static $many_many_extraFields = array(
    'Blocks' => array(
      'SortOrder' => 'Int'
    )
  );

  public function updateCMSFields(FieldList $fields) {
    // Relation handler for Blocks
    $SConfig = GridFieldConfig_RelationEditor::create(125);
    if (class_exists('GridFieldOrderableRows')) {
      $SConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
    }

    $SConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
    $SConfig->addComponent(new GridFieldDuplicateBlocksButton("buttons-before-right"));

    $SConfig->addComponent(new GridFieldDeleteAction());

    // If the copy button module is installed, add copy as option
    if (class_exists('GridFieldCopyButton')) {
      $SConfig->addComponent(new GridFieldCopyButton(), 'GridFieldDeleteAction');
    }
    $gridField = new GridField("Blocks", "Content blocks", $this->owner->Blocks(), $SConfig);
    $classes = array_values(ClassInfo::subclassesFor($gridField->getModelClass()));
    $classes = array_splice($classes, 1); //Removing
    if (count($classes) > 1 && class_exists('GridFieldAddNewMultiClass')) {
      $gfanmc = new GridFieldAddNewMultiClass();
      $gfanmc->setClasses($classes);

      $SConfig->removeComponentsByType('GridFieldAddNewButton');
      $SConfig->addComponent($gfanmc);
    }
    $helpbutton = new Milkyway\SS\GridFieldUtils\HelpButton('buttons-before-right', 'Pomoc (lista typów bloków)');
    $content = $this->getDynamicHelpContent($classes);
    $helpbutton->setContent($content);

    //$helpbutton->setContent($this->getHelpContent());
    $SConfig->addComponent($helpbutton);
    if (self::$create_block_tab) {
      $fields->addFieldToTab("Root.Blocks", $gridField);
    } else {
      // Downsize the content field
      $fields->removeByName('Content');
      $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content')->setRows(self::$contentarea_rows), 'Metadata');

      $fields->addFieldToTab("Root.Main", $gridField, 'Metadata');
    }

    if (class_exists('GridFieldAddExistingSearchButton')) {
      $SConfig->addComponent(new GridFieldAddExistingSearchButton('buttons-before-left'));
    }


    return $fields;
  }


  public function getDynamicHelpContent($classes) {

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

  public function ActiveBlocks() {
    return $this->owner->Blocks()->filter(array('Active' => '1'))->sort('SortOrder');
  }

  public function OneBlock($id) {
    return Block::get()->byID($id);
  }

}
