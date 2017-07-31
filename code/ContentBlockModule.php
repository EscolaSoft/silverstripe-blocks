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
        $SConfig = GridFieldConfig_RelationEditor::create(25);
        if (class_exists('GridFieldOrderableRows')) {
            $SConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
        }
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
        //$helpbutton->setContent($this->getDynamicHelpContent(ClassInfo::allClasses()));
        $helpbutton->setContent($this->getHelpContent());
        $SConfig->addComponent($helpbutton);
        if (self::$create_block_tab) {
            $fields->addFieldToTab("Root.Blocks", $gridField);
        } else {
            // Downsize the content field
            $fields->removeByName('Content');
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content')->setRows(self::$contentarea_rows), 'Metadata');

            $fields->addFieldToTab("Root.Main", $gridField, 'Metadata');
        }

        Requirements::customCSS($this->getHelpCSS());

        return $fields;
    }

    public function getHelpCSS() {
        $css = <<<CSS
table.blocks-help { width: 100%; }
table.blocks-help tr > * { border-bottom: 1px solid #d0d3d5; }
table.blocks-help tbody tr:nth-child(odd) { background: #f6f7f8; }
table.blocks-help td { padding: 10px; }
table.blocks-help th { padding: 10px; }
table.blocks-help th { font-weight: bold; }
table.blocks-help img { width: 100%; height: auto; display: block; }
table.blocks-help .name { width: 15%; }
table.blocks-help .thumbnail { width: 60%; }
table.blocks-help .description { width: 25%; }
CSS;
        return $css;
    }

    public function getHelpContent() {
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
    <tr>
      <td>Banner</td>
      <td><img src="mysite/images/blocks/banner.png" alt="banner"/></td>
      <td>This's Simple Block Banner to add title, image, description.</td>
    </tr>

    <tr>
      <td>Logo Block</td>
      <td><img src="mysite/images/blocks/logosBlock.png" alt="logo Block"/></td>
      <td>Add company logo and link url eg. "http://www.bosch-home.pl". </td>
    </tr>

    <tr>
      <td>Map Block</td>
      <td><img src="mysite/images/blocks/mapBlock.png" alt="map block"/></td>
      <td>Add map with google. All points realization.</td>
    </tr>

    <tr>
      <td>Testimonial Block</td>
      <td><img src="mysite/images/blocks/testimonialBlock.png" alt="testimonial block"/></td>
      <td>Add review your company.</td>
    </tr>

    <tr>
      <td>Image Block</td>
      <td><img src="mysite/images/blocks/imageBlock.png" alt="image block"/></td>
      <td>Add Image.</td>
    </tr>

    <tr>
      <td>Slider Block</td>
      <td><img src="mysite/images/blocks/sliderBlock.png" alt="slider block"/></td>
      <td>Add Images and it create carousel. The carousel is a slideshow for cycling through a series of images.</td>
    </tr>

    <tr>
      <td>Diagram Block</td>
      <td><img src="mysite/images/blocks/diagramBlock.png" alt="diagram Block"/></td>
      <td>Add power diagram.</td>
    </tr>

    <tr>
      <td>Definition Block</td>
      <td><img src="mysite/images/blocks/definitionBlock.png" alt="definition block"/></td>
      <td>Add banner "Description of the operation of the photovoltaic system". </td>
    </tr>

    <tr>
      <td>Golden Block</td>
      <td><img src="mysite/images/blocks/goldenBlock.png" alt="golden Block"/></td>
      <td>Add titles in the block.</td>
    </tr>

    <tr>
      <td>Box Block</td>
      <td><img src="mysite/images/blocks/boxBlock.png" alt="box block"/></td>
      <td>Add banner sunsol services. Services list have got field: title, description and 
          image *.SVG.</td>
    </tr>
    <tr>
      <td>Column Block</td>
      <td><img src="mysite/images/blocks/columnBlock.png" alt="column block"/></td>
      <td>References list to page. At most 2.</td>
    </tr>
    <tr>
      <td>List Block</td>
      <td><img src="mysite/images/blocks/listBlock.png" alt="list block"/></td>
      <td>Create table. By means of add item to table.</td>
    </tr>
    <tr>
      <td>Realization Category Block</td>
      <td><img src="mysite/images/blocks/realizationCategoryBlock.png" alt="realization category block"/></td>
      <td>Realizations list in the block. Choose with list. At most 3.</td>
    </tr>
    <tr>
      <td>Product Category Block</td>
      <td><img src="mysite/images/blocks/selectedProductCategories.png" alt="post category block"/></td>
      <td>Categories list in the block. Choose with list. At most 4.</td>
    </tr>
    <tr>
      <td>Video Block</td>
      <td><img src="mysite/images/blocks/banner.png" alt="video block"/></td>
      <td>Add Video. At most *.mp4. </td>
    </tr>
    <tr>
      <td>Posts Banner</td>
      <td><img src="mysite/images/blocks/selectedPostsBanner.png" alt="posts banner"/></td>
      <td>Posts list on the page.</td>
    </tr>
  </tbody>
</table>
HTML;
        return $html;
    }
    //TODO: Problem with $classes
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
        foreach ($classes as $class) {
            $html .= <<<HTML
            <tr>
                <td class="name">'.$class.'</td>
                <td class="thumbnail">'.$class::CMSThumbnail().'</td>
                <td class="description">'.$class::CMSHelp().'</td>
            </tr>
HTML;
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
