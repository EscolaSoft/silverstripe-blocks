<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 11.08.17
 * Time: 13:45
 */

class BlocksAdmin extends ModelAdmin {
  public static $managed_models = array(
    'Block'
  );
  private static $menu_title = 'Blocks';
  private static $url_segment = 'blocks';

} 