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
        'ClassName' => 'Type',
        'getIsActive' => 'Active',
        'CMSThumbnail' => 'Example'
    );
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
    public  function CMSThumbnailPath() {
        $filename = BASE_PATH.self::DIR_BLOCK. $this->class . '.png';
        return $filename;     
    }
    /**
     * 
     * @return URL image
     */
    public function CMSThumbnailURL() {
        $filename = $this->CMSThumbnailPath();
        
        if (is_file($filename)) {
            return BASE_URL.self::DIR_BLOCK. $this->class . '.png';
        }
        return FALSE;
    }
    /**
     * image thumbnail
     * @return HTML
     */
    public function CMSThumbnail(){
        $url = $this->CMSThumbnailURL();
        $output = HTMLText::create(); 
        $output ->setValue('<img title="'.$this->help.'" style="width:100px; height:auto;" src="'.$url.'" />'); 
        return $output;
    }
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Pages');
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
        'Active'
    );

    public function getIsActive() {
        return $this->Active ? 'Yes' : 'No';
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