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
require_once(_PS_MODULE_DIR_ . '/addifyadd/classes/model/model.php'); 
class addifyadd extends Module
{
    public function __construct()
    {
        $this->name = 'addifyadd';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'awais aezad';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('addifyadd');
        $this->description = $this->trans('Description of addifyadd');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?');

        if (!Configuration::get('addifyadd')) {
            $this->warning = $this->trans('No name provided');
        }
    }
    public function install()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "Dataadds (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fname VARCHAR(255),
            lname VARCHAR(255),
            email VARCHAR(255),
            pass VARCHAR(255),
            switch TINYINT(1),
            color VARCHAR(255),
            image VARCHAR(255)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install()
        );
    }
    public function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'Dataadds`';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return (
            parent::uninstall()
            && Configuration::deleteByName('addifyadd')
        );
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit' . $this->name)) {

            $this->updateconfigval();

            $output = $this->displayConfirmation($this->l('Data updated'));
        }
        if (Tools::isSubmit('addsubmit' . $this->name)) {

            $this->addValuesToDatabase();
            $output = $this->displayConfirmation($this->l('Data Added'));
            return $output . $this->displayForm() . $this->renderList();
        }
        if (Tools::isSubmit('update'. $this->name)) {
            $this->updateValuesInDatabase();
            $output = $this->displayConfirmation($this->l('Data updated'));
            return $output . $this->displayForm() . $this->renderList();
        }
        if ((Tools::isSubmit('addaddifyadd'))== true|| (Tools::isSubmit('updateDataadds'))== true) {
            
            $output = $this->addNewForm();
            return $output;
        }
        if((Tools::isSubmit('deleteDataadds'))== true){
            $this->deleteFromDatabase();
            $output = $this->displayConfirmation($this->l('Data Deleted'));
            return $output . $this->displayForm() . $this->renderList();
        }

        return $output . $this->displayForm() . $this->renderList();
    }

    public function displayForm()
    {
        $image = "";
        $get_image = Configuration::get('image');
        $baseUrl = $this->context->shop->getBaseURL(true);
        if ($get_image) {
            $this->context->smarty->assign(array(
                'baseUrl' => $baseUrl, 'get_image' => $get_image
            ));
            $image = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/addifyadd/views/templates/admin/adminimage.tpl');
        }
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add user data'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Firstname'),
                        'name' => 'fname',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Lastname'),
                        'name' => 'lname',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email'),
                        'name' => 'email',
                        'required' => true,
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Password'),
                        'name' => 'pass',
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Swtich'),
                        'name' => 'switch',
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'on',
                                'value' => 1,
                                'label' => 'on',
                            ),

                            array(
                                'id' => 'off',
                                'value' => 0,
                                'label' => 'off',
                            )
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Pick Color'),
                        'name' => 'color',
                        'required' => true,
                    ),
                    array(
                        'type' => 'file',
                        'image' => $image,
                        'display_image' => true,
                        'label' => $this->l('Image'),
                        'name' => 'image',
                        'required' => true,
                        'desc' => $this->l('"'),
                    ),
                ),
                
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->submit_action = 'submit' . $this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getformvalues()
        );

        return $helper->generateForm(array($form));
    }

    public function getformvalues()
    {
        $configval = array();
        if (Tools::isSubmit('submit' . $this->name)||Tools::isSubmit('addsubmit' . $this->name) || Tools::isSubmit('updateDataadds') == true) {
            $configval['fname'] = Tools::getValue('fname');
            $configval['lname'] = Tools::getValue('lname');
            $configval['email'] = Tools::getValue('email');
            $configval['pass'] = Tools::getValue('pass');
            $configval['switch'] = Tools::getValue('switch');
            $configval['color'] = Tools::getValue('color');
            $configval['image'] = Tools::getValue('image');
        }
        // else {
        //     $configval['fname'] = Configuration::get('fname');
        //     $configval['lname'] = Configuration::get('lname');
        //     $configval['email'] = Configuration::get('email');
        //     $configval['pass'] = Configuration::get('pass');
        //     $configval['switch'] = Configuration::get('switch');
        //     $configval['color'] = Configuration::get('color');
        //     $configval['image'] = Configuration::get('image');
        // }
        if (isset($_FILES['image'])) {
            $target_dir = _PS_MODULE_DIR_ . 'addifyadd/views/img/';
            $without_extension = Tools::substr(basename($_FILES['image']["name"]), 0, strrpos(basename($_FILES['image']["name"]), "."));
            $img_path_ext = Tools::substr(basename($_FILES['image']["name"]), strrpos(basename($_FILES['image']["name"]), '.'));
            $target_file = $target_dir . $without_extension . $img_path_ext;
            move_uploaded_file($_FILES['image']["tmp_name"], $target_file);
        }
        return $configval;
    }
    public function updateconfigval()
    {
        $update = $this->getformvalues();

        Configuration::updateValue('fname', $update['fname']);
        Configuration::updateValue('lname', $update['lname']);
        Configuration::updateValue('email', $update['email']);
        Configuration::updateValue('pass', $update['pass']);
        Configuration::updateValue('switch', $update['switch']);
        Configuration::updateValue('color', $update['color']);
        Configuration::updateValue('image', $update['image']);
    }
    public function addValuesToDatabase()
    {
        $update = $this->getformvalues();
        Db::getInstance()->insert('Dataadds', $update);
    }
    public function getValuesFromDatabase()
    {
        // Retrieve values from the new table
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "Dataadds";
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }
    public function updateValuesInDatabase()
    {

         $id = (int) Tools::getValue('id');
        $fname = Tools::getValue('fname');
        $lname = Tools::getValue('lname');
        $email = Tools::getValue('email');
        $pass = Tools::getValue('pass');
        $switch = Tools::getValue('switch');
        $color = Tools::getValue('color');
        $image = Tools::getValue('image');
        $update = array(
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'pass' => $pass,
            'switch' => $switch,
            'color' => $color,
            'image' => $image
        );

        Db::getInstance()->update('Dataadds', $update, 'id = ' . $id);
    }
    public function deleteFromDatabase()
{
    $id = (int) Tools::getValue('id');
    $sql = "DELETE FROM " . _DB_PREFIX_ . "Dataadds WHERE id = '$id'";
    Db::getInstance()->execute($sql);
}

    public function renderList()
    {
        // Retrieve values from the database
        $data = $this->getValuesFromDatabase();

        // Prepare columns for HelperList
        $fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'fname' => array(
                'title' => $this->l('First Name'),
                'type' => 'text',
            ),
            'lname' => array(
                'title' => $this->l('Last Name'),
                'type' => 'text',
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'type' => 'text',
            ),
            'pass' => array(
                'title' => $this->l('Password'),
                'type' => 'text',
            ),
            'switch' => array(
                'title' => $this->l('Switch'),
                'type' => 'bool',
                'active' => 'switch',
            ),
            'color' => array(
                'title' => $this->l('Color'),
                'type' => 'color',
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type' => 'file',
            ),
        );

        // Prepare options for HelperList
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array('href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
        'desc' => $this->l('Add new'));
        $helper->actions = array('edit', 'delete', 'view');
        $helper->module = $this;
        $helper->title = $this->l('Data Adds');
        $helper->table = 'Dataadds';
        $helper->identifier = 'id';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Load list values
        $helper->listTotal = count($data);
        $helper->identifier = 'id';
        $helper->tpl_vars['fields_list'] = $fields_list;
        $helper->tpl_vars['base_url'] = $this->_path;

        // Load list values
        $helper->listTotal = count($data);
        $helper->listTotal = count($data);
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateList($data, $fields_list);
    }
    public function addNewForm()
    {
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Firstname'),
                        'name' => 'fname',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Lastname'),
                        'name' => 'lname',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email'),
                        'name' => 'email',
                        'required' => true,
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Password'),
                        'name' => 'pass',
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Swtich'),
                        'name' => 'switch',
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'on',
                                'value' => 1,
                                'label' => 'on',
                            ),

                            array(
                                'id' => 'off',
                                'value' => 0,
                                'label' => 'off',
                            )
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Pick Color'),
                        'name' => 'color',
                        'required' => true,
                    ),
                    array(
                        'type' => 'file',
                        'display_image' => true,
                        'label' => $this->l('Image'),
                        'name' => 'image',
                        'required' => true,
                        'desc' => $this->l('"'),
                    ),
                ),

                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ),
                'buttons' => array(
                    '0' => array(
                        'type' => 'submit',
                        'title' => $this->l('Go Back'),
                        'name' => 'goback',
                        'icon' => 'process-icon-back',
                        'class' => 'pull-left',
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                    )
                    ),
            ),
        );
        $helper = new HelperForm();
        if((Tools::isSubmit('updateDataadds'))== true){
            $helper->submit_action = 'update' .$this->name;
        }
        else{
        $helper->submit_action = 'addsubmit' . $this->name;
        }
        $helper->tpl_vars = array(
            'fields_value' => $this->getformvalues()
        );

        return $helper->generateForm(array($form));
    }
}
