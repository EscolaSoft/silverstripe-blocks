<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 20.07.17
 * Time: 14:07
 */

class Block extends DataObject {

  private static $singular_name = 'Block';

  private static $plural_name = 'Blocks';

  private static $first_write = false;

  protected $help = '';

  const DIR_BLOCK = '/mysite/images/blocks/';

  private static $db = array(
    'Active' => 'Boolean',
    'SystemName' => 'Varchar',
  );
  private static $belongs_many_many = array(
    'Pages' => 'BlocksPage'
  );
  private static $defaults = array(
    'Active' => 1,
    'Page_Blocks[SortOrder]' => 999, // TODO: Fix sorting, new blocks should be added to the bottom of the list/gridfield
  );
  private static $casting = array(
    'createStringAsHTML' => 'HTMLText'
  );

  private static $summary_fields = array(
    'ID'=>'ID',
    'ClassName' => 'Type',
    'SystemName' => 'SystemName',
    'getIsActive' => 'Active',
    'CMSThumbnail' => 'Example',
    'PageTitles' => 'Appears on'
  );

  public function getPageTitles() {
    $titles = array();
    foreach($this->Pages() as $page) {
      $titles[] = $page->Title;
    }
    return implode(', ',$titles);
  }

  /**
   * Configuration style WYSIWYG (TinyMCE) for Title
   * Title- > HTMLVarchar
   *
   */
  public function __construct($record = null, $isSingleton = false, $model = null) {
    parent::__construct($record, $isSingleton, $model);
    $config = HtmlEditorConfig::get('reduced');
    $config->disablePlugins('table');
    $config->setButtonsForLine(2);
    $config->setButtonsForLine(3);
    $config->setButtonsForLine(1, 'bold');
    $config->setOption('forced_root_block', '');
    $config->setOption('force_br_newlines', true);
    $config->setOption('force_p_newlines', false);
    $config->setOption('invalid_elements', 'p');
  }

  public function createStringAsHTML($html) {
    $casted = HTMLText::create();
    $casted->setValue($html);

    return $casted;
  }



  /**
   * Path File Thumbnail
   * @return path file
   */
  public static function CMSThumbnailPath() {
    $filename = BASE_PATH.self::DIR_BLOCK. self::singleton()->class . '.png';
    return $filename;
  }
  /**
   *
   * @return URL image
   */
  public static function CMSThumbnailURL() {
    $filename = self::CMSThumbnailPath();

    if (is_file($filename)) {
      return BASE_URL.self::DIR_BLOCK. self::singleton()->class . '.png';
    }
    return FALSE;
  }

  public static function getCMSThumbnail() {
    $url = self::CMSThumbnailURL();
    $output = HTMLText::create();
    $output ->setValue('<img style="width:100px; height:auto;" src="'.$url.'" />');
    return $output;
  }

  public static function getCMSHelp() {
    return 'HELP please provide help!!!!';
  }

  public function CMSHelp(){
    return self::getCMSHelp();
  }

  public static function getCMSDescription() {
    return 'HELP please provide description!!!!';
  }

  public function CMSDescription(){
    return self::getCMSDescription();
  }

  /**
   * image thumbnail
   * @return HTML
   */
  public function CMSThumbnail(){
    return self::getCMSThumbnail();
  }


  public function getCMSFields() {
    $fields = parent::getCMSFields();
    $fields->removeByName('Pages');

    $help_content = '<p><strong>Description:</strong></p>';
    $help_content .= $this->getCMSHelp();
    $help_content .= '<hr/><p><strong>Instruction:</strong></p>';
    $help_content .= $this->getCMSDescription();
    $help_content .= '<hr/><p><strong>Thumbnail:</strong></p>';
    $help_content .= '<img style="width:100%; max-width:600px; height:auto;" src="'.self::CMSThumbnailURL().'" />';

    $fields->addFieldsToTab('Root.Help', new LiteralField('help', $help_content));

    if (isset($_GET['modal'])) {
      Requirements::customCSS(<<<CSS
            #cms-menu,
            #Form_ItemEditForm .cms-content-header.north,
            #Form_ItemEditForm .better-buttons-utils,
            #Form_ItemEditForm #Form_ItemEditForm_action_doSaveAndQuit
            {
              display: none;
            }
            #Form_ItemEditForm {
              position: relative !important;
              width: 100% !important;
              height: 100% !important;
              left: auto !important;
              top: auto !important;
            }
CSS
      );
    }
    return $fields;
  }

  public function canView($member = null) {
    return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_BlockAdmin') || Permission::check('CMS_ACCESS_LeftAndMain');
  }

  public function canEdit($member = null) {
    return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_BlockAdmin') || Permission::check('CMS_ACCESS_LeftAndMain');
  }

  public function canCreate($member = null) {
    return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_BlockAdmin') || Permission::check('CMS_ACCESS_LeftAndMain');
  }

  public function canPublish($member = null) {
    return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_BlockAdmin') || Permission::check('CMS_ACCESS_LeftAndMain');
  }


  private static $searchable_fields = array(
    'ID'     => 'PartialMatchFilter',
    'SystemName'   => 'PartialMatchFilter',
    'Active'
  );

  public function populateDefaults() {
    $this->SystemName = $this->getClassName();
    parent::populateDefaults();
  }

  public function getIsActive() {
    return $this->Active ? 'Yes' : 'No';
  }

  protected function onBeforeWrite() {
    if($this->SystemName == '') {
      $this->SystemName = $this->getClassName();
    }
    parent::onBeforeWrite();
  }

  function forTemplate() {

    // can we include the Parent page for rendering? Perhaps use a checkbox in the CMS on the block if we should include the Page data.
    // $page = Controller::curr();
    // return $this->customise(array('Page' => $page))->renderwith($this->Template);
    return $this->renderWith(array($this->class, 'Block')); // Fall back to Block if selected does not exist
  }

  /**
   * Returns the page object (SiteTree) that we are currently on
   * Allow us to loop on children of the page and other page related data
   *
   * @return SiteTree
   */
  public function CurrentPage() {
    return Director::get_current_page();
  }

  public function CurrentController() {
    return Controller::curr();
  }

  public function getEditLink() {
    return Director::baseURL() . 'admin/pages/edit/EditForm/field/Blocks/item/' . $this->ID . '/edit?modal=1';
  }

}