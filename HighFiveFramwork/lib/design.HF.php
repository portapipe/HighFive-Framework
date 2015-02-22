<?
/*! Most of the component of a webpage should be done with this class! */
class HFdesign{
	
	private $corner = 2;
	
	
	
	function flatCorner(){
		$this->corner = 0;
		return $this;
	}
	
	function setCorner($radius=2){
		$this->corner = $radius;
		return $this;
	}
	
	function getCorner(){
		echo '-webkit-border-radius: '.$this->corner.';-moz-border-radius: '.$this->corner.';border-radius: '.$this->corner.';';
		return $this;
	}
	
	
}