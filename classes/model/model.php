<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class addifyaddmodel extends ObjectModel {
    public $fname;
    public $lname;
	public $email;
	public $pass;
    public $switch;

    public $color;

  public $image;

  public static $definition = array(

    'table' => 'ps_Dataadds',
    'primary' => 'id',
    'multilang' => true,
    'fields' => array(
                  
                  'fname' => array('type' => self::TYPE_HTML),
                  'lanme' => array('type' => self::TYPE_HTML),
                  'email' => array('type' => self::TYPE_HTML),
                  'pass' => array('type' => self::TYPE_HTML),
                  'switch'=> array('type'=> self::TYPE_HTML),
                  'color'=> array('type'=> self::TYPE_HTML),
                  'image'=> array('type'=> self::TYPE_HTML),

                )
);
static function getListContent()
	{
          return DB::getInstance()->executes(
         'SELECT * FROM `' ._DB_PREFIX_. 'Dataadds`');
	}

}