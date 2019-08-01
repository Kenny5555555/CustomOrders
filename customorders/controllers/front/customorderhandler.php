<?php
class CustomOrdersCustomOrderHandlerModuleFrontController extends ModuleFrontController
{

	//Queries from database
	public function getUsers() {
		$usrs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT id_user, name FROM "._DB_PREFIX_."co_users;");
		return $usrs;
	}
	
	public function getProducts() {
		$prods = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT id_prod, prod_name, prod_price FROM "._DB_PREFIX_."co_products;");
		return $prods;
	}
	
	//Order data is joined from multiple databases
	public function getOrders() {
		$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT "._DB_PREFIX_."co_orders.id_order, "._DB_PREFIX_."co_users.name, "._DB_PREFIX_."co_products.prod_name,"._DB_PREFIX_."co_products.prod_price, "._DB_PREFIX_."co_orders.quantity, "._DB_PREFIX_."co_orders.total_cost, "._DB_PREFIX_."co_orders.dt 
		FROM "._DB_PREFIX_."co_orders 
		LEFT JOIN "._DB_PREFIX_."co_users ON "._DB_PREFIX_."co_orders.id_user = "._DB_PREFIX_."co_users.id_user 
		LEFT JOIN "._DB_PREFIX_."co_products ON "._DB_PREFIX_."co_orders.id_prod = "._DB_PREFIX_."co_products.id_prod 
		ORDER BY "._DB_PREFIX_."co_orders.dt DESC;");
		return $orders;
	}

	//New order
	public function addOrder() {
		$id_user = isset($_POST['select_user']) ? $_POST['select_user'] : null;
		$id_product = isset($_POST['select_products']) ? $_POST['select_products'] : null;
		$quantity = Tools::getValue('quantity');
		//Check if all necessary fields are entered
		if($id_user!=0 AND $id_product!=0 AND $quantity!=0){
			$product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT prod_name, prod_price FROM "._DB_PREFIX_."co_products WHERE id_prod=".$id_product.";");
			$totalcost = $product[0]['prod_price']*$quantity;
			//Special calculation for Pepsi Cola
			if($id_product == 1 AND $quantity >= 3){
				$totalcost = $totalcost*0.8;
			}
			Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."co_orders (id_user, id_prod, quantity, total_cost, dt) VALUES (".intval($id_user).", ".intval($id_product).", ".intval($quantity).", ".$totalcost.", FROM_UNIXTIME(".time()."))");
		}
		else{
			echo "<script type='text/javascript'>alert('".json_encode("Missing information")."');</script>";
		}
	}
	
	//Delete order
	public function deleteOrder($oid) {
		Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_."co_orders WHERE id_order=".$oid);
	}
	
	//Change order
	public function changeOrder($oid) {
		$id_product = isset($_POST['select_products-'.$oid]) ? $_POST['select_products-'.$oid] : null;
		$quantity = Tools::getValue('quantity-'.$oid);
		//Check if all necessary fields are entered
		if($id_product!=0 AND $quantity!=0){
			$product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT prod_name, prod_price FROM "._DB_PREFIX_."co_products WHERE id_prod=".$id_product.";");
			$totalcost = $product[0]['prod_price']*$quantity;
			//Special calculation for Pepsi Cola
			if($id_product == 1 AND $quantity >= 3){
				$totalcost = $totalcost*0.8;
			}
			Db::getInstance()->execute("UPDATE "._DB_PREFIX_."co_orders SET id_prod=".intval($id_product).", quantity=".intval($quantity).", total_cost=".$totalcost.", dt=FROM_UNIXTIME(".time().") WHERE id_order=".$oid.";");
		}
		else{
			echo "<script type='text/javascript'>alert('".json_encode("Missing information")."');</script>";
		}
	}
	
	//Catches all post functions
	public function postProcess(){
        if (Tools::isSubmit('addOrder')) {
            $this->addOrder();
        }
		if (Tools::isSubmit('deleteOrder')) {
			$this->deleteOrder($_POST['deleteOrder']);
        }
		if (Tools::isSubmit('changeOrder')) {
			$this->changeOrder($_POST['changeOrder']);
        }
    }
	
	public function initContent()
    {
        parent::initContent();
		$smarty = $this->context->smarty;
		$smarty->assign("users", $this->getUsers());
		$smarty->assign("products", $this->getProducts());
		$smarty->assign("orders", $this->getOrders());
        $this->setTemplate('module:customorders/views/templates/front/customorderhandler.tpl');
    }

}