<?php
/*! Create a shopping cart with a line, necessary to send it to the payments method with a one-line approach */	
class HFshopcart{
	
	/*! Add an item to the cart.
		ID is important: it MUST be unique for each product! (numbers and letters only, no spaces)
		If you add a product with the same id the system will stack it (handy for client)
		ID will be used to remove a product from the cart so UNIQUE ID!
		*/
	function addToCart($name,$price,$quantity=1,$ID=1){
		
		$cart = array();
		
		if(isset($_SESSION['cart']) && $_SESSION['cart']!=null){
			$cart = json_decode($_SESSION['cart'],true);
		}

		$n = count($cart) + 1;
		$cart[$n]['item_name_'] = $name;
		$cart[$n]['amount_'] = $price;
		$cart[$n]['quantity_'] = $quantity;
		$cart[$n]['item_number_'] = $ID;
		
		
		$this->setCartFromArray($cart);
		
		return $this;
		
	}
	
	function removeFromCart($id){
		$products = $this->getCart();
		unset($products[$id]);
		$this->setCartFromArray($products);
		return $this;
	}
	
	function changeQuantity($id,$newQuantity){
		$products = $this->getCart();
		$products[$id]['quantity_'] = $newQuantity;
		$this->setCartFromArray($products);
		return $this;
	}
	
	private function setCartFromArray($array){
		$_SESSION['cart'] = json_encode($array);
		return $this;
	}
	
	function getCart(){
		if(isset($_SESSION['cart'])){
			return json_decode($_SESSION['cart'],true);
		}
		return array();
		
	}
	
	function emptyCart(){
		
		unset($_SESSION['cart']);
		return $this;
		
	}
	
	function sendCart($paymentCompany="paypal"){
		
		switch($paymentCompany){
			case "paypal" : HFpaypal::sendCart();
				break;
			default: HFpaypal::sendCart();
		}
		
		return $this;
		
	}
	
	
}