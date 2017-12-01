<?php
namespace SilverStripe_Blocks\Admin;

use SilverStripe\Admin\ModelAdmin;

class BlocksAdmin extends ModelAdmin
{

    private static $menu_title = 'Blocks';
    private static $url_segment = 'blocks';
    private static $managed_models = array('SilverStripe_Blocks\Block');
}
