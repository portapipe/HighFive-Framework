<?

class HFmail{
	
	public $host = "localhost";
	public $username = "root";
	public $password = "root";
	public $sender = "your@email.com";
	public $senderName = "";
	public $smtpAuth = true; //if SMTP requires authorization to send emails
	public $port = 587;
	public $html = true;
	public $designPage = "";
	
	
	function set($host,$username,$password,$senderEmail="your@email.com",$senderName=""){
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->sender = $senderEmail;
		$this->senderName = $senderName;
		return $this;
	}
	
	
	
	
	/*!
		Send an email to the passed $recipent with the $subject and the $body in html
		RETURN VOID
	*/
	
	public function send($recipient,$subject,$body){
		
		require_once HF_FULL_ADDON_DIR.'PHPMailerAutoload.php';
	
		$mail = new PHPMailer;
		
		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $this->host;							  // Specify main and backup SMTP servers
		$mail->SMTPAuth = $this->smtpAuth;                        // Enable SMTP authentication
		$mail->Username = $this->username;  		          // SMTP username
		$mail->Password = $this->password;                       // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $this->port; //587; 465                 // TCP port to connect to
		
		$mail->From = $this->sender;
		$mail->FromName = $this->senderName;
		
		$mail->addAddress($recipient);			               // Name is optional ..($recipient,'The Name');
	/*	$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');
		
		$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name */
		$mail->isHTML($this->html);                           // Set email format to HTML
		
		$mail->Subject = $subject;
		
		if($designPage!=""){
			$mail->Body = basicDesign($body);
		}else{
			$mail->Body = $body;
		}
		
		if($this->html){
			$mail->AltBody = "A MODERN BROWSER IS NEEDED TO SEE HTML EMAILS! <p>".$body."</p>" ;
		}
		
		if(!$mail->send()) {
		    echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		    return false;
		} else {
		    return true;
		}
	}
	
	
	/*! Use an HTML page with styles and all the cool stuff you can image and put the body into it.
		You can use a simple html page and you must put $body as is, so a simple string.
		This function will replace the $body with the body you'll pass as arg in the send() function!
	*/
	function basicDesign($body){
		$html = file_get_contents($this->designPage);
		$html = str_replace('$body', $body, $html);
		return $html;
	}
	
}