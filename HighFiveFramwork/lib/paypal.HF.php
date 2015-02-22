<? dependencies("shopcart");
	
/*! Manage all the paypal stuff with this class*/
class HFpaypal{
	
	public $sandbox = 0;
	
	public $email = "setAnEmail@toTheClass.here";
	public $price = 4.99;
	public $name = 'Set name with $HF->paypal->setName()';
	public $currency = "USD";
	public $language = "US";
	public $quantity = 1;
	public $custom = 'Set Custom Var with $HF->paypal->setCustom()';
	public $returnPage= 'http://setRETURNPageinClass.$HF->paypal->setReturn()/paypal';
	public $cancelPage = 'http://setCANCELPageinClass.$HF->paypal->setCancel()/paypal';
	public $ipnPage = 'http://setIPNPageinClass.$HF->paypal->setIpn()/ipn';
	
	private $sandboxURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	private $liveURL = "https://www.paypal.com/cgi-bin/webscr";
	
	function ipn(){
		
				
		// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		// Set this to 0 once you go live or don't require logging.
		define("DEBUG_IPN", 0);

		define("LOG_FILE", "./ipn.log");
		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		if($this->sandbox == 1) {
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
			return FALSE;
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		if(DEBUG_IPN == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}
		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.
		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);
		$res = curl_exec($ch);
		if (curl_errno($ch) != 0) // cURL error
			{
			if(DEBUG_IPN == true) {	
				error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
			}
			curl_close($ch);
			exit;
		} else {
				// Log the entire HTTP response if debug is switched on.
				if(DEBUG_IPN == true) {
					error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
					error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
				}
				curl_close($ch);
		}
		// Inspect IPN validation result and act accordingly
		// Split response headers and payload, a better way for strcmp
		$tokens = explode("\r\n\r\n", trim($res));
		$res = trim(end($tokens));
		if (strcmp ($res, "VERIFIED") == 0) {
			// check whether the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment and mark item as paid.
			// assign posted variables to local variables
			//$item_name = $_POST['item_name'];
			//$item_number = $_POST['item_number'];
			//$payment_status = $_POST['payment_status'];
			//$payment_amount = $_POST['mc_gross'];
			//$payment_currency = $_POST['mc_currency'];
			//$txn_id = $_POST['txn_id'];
			//$receiver_email = $_POST['receiver_email'];
			//$payer_email = $_POST['payer_email'];


			return $_POST;
			if(DEBUG_IPN == true) {
				error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
			}
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			// Add business logic here which deals with invalid IPN messages
			if(DEBUG_IPN == true) {
				error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
			}
			
			return false;
			
		}
		
		
	}

	function setReturn($url){
		$this->returnPage = $url;
		return $this;
	}
	function setCancel($url){
		$this->cancelPage = $url;
		return $this;
	}
	function setIpn($url){
		$this->ipnPage = $url;
		return $this;
	}
	
	
	function useSandbox(){
		$this->sandbox = 1;
		return $this;
	}
	
	function setEmail($email){
		$this->email = $email;
		return $this;
	}
	
	function setPrice($price){
		$this->price = $price;
		return $this;
	}
	
	function setName($name){
		$this->name = $name;
		return $this;
	}
	
	function setCurrency($currency){
		$this->currency = $currency;
		return $this;
	}
	
	function setLanguage($language){
		$this->language = $language;
		return $this;
	}
	
	function setQuantity($quantity){
		$this->quantity = $quantity;
		return $this;
	}
	
	function setCustom($custom){
		$this->custom = $custom;
		return $this;
	}
	
	function button($button='<input class="btn btn-success" type="submit" value="Pay With Your Soul">'){

		$output ='	<form action="'.($this->sandbox==0?$this->liveURL:$this->sandboxURL).'" method="post" class="paypalForm">
					    <input type="hidden" name="cmd" value="_xclick">
					    <input type="hidden" name="business" value="'.$this->email.'">
					    <input type="hidden" name="item_name" value="'.$this->name.'">
					    <input type="hidden" name="item_number" value="'.$this->quantity.'">
					    <input type="hidden" name="amount" value="'.$this->price.'">
					    <input type="hidden" name="no_shipping" value="0">
					    <input type="hidden" name="no_note" value="1">
					    <input type="hidden" name="currency_code" value="'.$this->currency.'">
					    <input type="hidden" name="lc" value="'.$this->language.'">
					    <input type="hidden" name="bn" value="PP-BuyBF">
					    <input type="hidden" name="custom" value="'.addslashes($this->custom).'">

						<input type="hidden" name="return" value="'.$this->returnPage.'">
						<input type="hidden" name="cancel_return" value="'.$this->cancelPage.'">
						<input type="hidden" name="notify_url" value="'.$this->ipnPage.'">

					    '.$button.'
					</form>';
		
		echo $output;
		
	}
	
	/*! Must be used ONLY if a $HF->shopcart is used
	It's cool because it auto-send the cart to paypal
	*/
	function sendCart(){
		
		$products = HFshopcart::getCart();
		print_r($products);
		$output = ' <form action="'.($this->sandbox==0?$this->liveURL:$this->sandboxURL).'" method="post" id="paypalForm">';
		
		foreach($products as $k1 => $v1){
			foreach($v1 as $k => $v){
				$output .=' <input type="hidden" name="'.$k.$k1.'" value="'.$v.'">
				';
			}
		}

		$output.=	'   <input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="upload" value="1">
						<input type="hidden" name="tax_cart" value="0">
					    <input type="hidden" name="business" value="'.$this->email.'">
					    <input type="hidden" name="no_shipping" value="0">
					    <input type="hidden" name="no_note" value="1">
					    <input type="hidden" name="currency_code" value="'.$this->currency.'">
					    <input type="hidden" name="lc" value="'.$this->language.'">
					    <input type="hidden" name="bn" value="PP-BuyBF">
					    <input type="hidden" name="custom" value="'.addslashes($this->custom).'">
						<input type="hidden" name="return" value="'.$this->returnPage.'">
						<input type="hidden" name="cancel_return" value="'.$this->cancelPage.'">
						<input type="hidden" name="notify_url" value="'.$this->ipnPage.'">
						<input type="hidden" name="txn_type" value="cart"/>
					</form>';
					
					
		$output.=   '<script> $(document).ready(function(){ $("#paypalForm").submit();});</script>';
		echo $output;
	}
	
	
}