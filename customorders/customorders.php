<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomOrders extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'customorders';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Leiko Luhamaa';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom Orders');
        $this->description = $this->l('Something');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
		//Inserting all databases and starting values
		Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."co_users (id_user int not null AUTO_INCREMENT PRIMARY KEY, name varchar(256) not null)");
		Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."co_products (id_prod int not null AUTO_INCREMENT PRIMARY KEY, name varchar(256) not null, prod_price decimal(20,2) not null)");
		Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."co_orders (id_order int not null AUTO_INCREMENT PRIMARY KEY, id_user int not null, id_prod int not null, quantity int not null, total_cost decimal(20,2) not null, dt DATETIME not null)");
		
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_users (name) SELECT * FROM (SELECT 'John Hammond') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_users WHERE name = 'John Hammond') LIMIT 1");
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_users (name) SELECT * FROM (SELECT 'Ann Vaupere') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_users WHERE name = 'Ann Vaupere') LIMIT 1");
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_users (name) SELECT * FROM (SELECT 'Mesi Mumm') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_users WHERE name = 'Mesi Mumm') LIMIT 1");
		
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_products (name, prod_price) SELECT * FROM (SELECT 'Pepsi Cola', '1.50') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_products WHERE name = 'Pepsi Cola') LIMIT 1");
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_products (name, prod_price) SELECT * FROM (SELECT 'Fanta', '1.30') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_products WHERE name = 'Fanta') LIMIT 1");
		Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_products (name, prod_price) SELECT * FROM (SELECT 'Sprite', '1.70') AS tmp WHERE NOT EXISTS (SELECT name FROM "._DB_PREFIX_."co_products WHERE name = 'Sprite') LIMIT 1");
		
		
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

}
