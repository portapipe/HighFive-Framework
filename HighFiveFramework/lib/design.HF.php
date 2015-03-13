<?php
/*! Most of the component of a webpage should be done with this class! */
class HFdesign{
	
	private $corner = 2;
	
	function __constructDisabled(){
		echo "<script>
		/* Add vertical to head */
		$('head').append('".$this->verticalAlign()."');
		/* Add horizontal to head */
		$('head').append('".$this->horizontalAlign()."');
		/* Add absolute to head */
		$('head').append('".$this->absoluteAlign()."');
		</script>";
	}
	
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
	
	private $useVerticalAlign = false;
	
	function verticalAlign(){
		if(!$this->useVerticalAlign){
			$this->useVerticalAlign = true;
			$return = '<script>
						(function ($) {
			// VERTICALLY ALIGN FUNCTION
			$.fn.HFvAlign = function() {
			    return this.each(function(i){
			    var ah = $(this).height();
			    var ph = $(this).parent().height();
			    var mh = Math.ceil((ph-ah) / 2);
			    $(this).css(\'margin-top\', mh);
			    });
			};
			})(jQuery);
			
			
			$(document).ready(function() { 
				setTimeout(function() {
			  $(".HF-vAlign").HFvAlign();
			  $(".HF-valign").HFvAlign();
			  $( window ).resize(function() {
				  $(".HF-vAlign").HFvAlign();
				  $(".HF-valign").HFvAlign();
				});
				},200);
			});

			</script>';
			
			echo $return;return "";
			echo "<script>$('head').append('".$return."');</script>";
		}
	}
	
	private $useHorizontalAlign = false;
	
	function horizontalAlign(){
		if(!$this->useHorizontalAlign){
			$this->useHorizontalAlign = true;
			$return = '<script>
						(function ($) {
			// VERTICALLY ALIGN FUNCTION
			$.fn.HFhAlign = function() {
			    return this.each(function(i){
			    var aw = $(this).width();
			    var pw = $(this).parent().width();
			    var mw = Math.ceil((pw-aw) / 2);
			    $(this).css(\'margin-left\', mw);
			    });
			};
			})(jQuery);
			
			$(document).ready(function() { 
				setTimeout(function() {
			  $(".HF-hAlign").HFhAlign();
			  $(".HF-halign").HFhAlign();
			  $( window ).resize(function() {
				  $(".HF-hAlign").HFhAlign();
				  $(".HF-halign").HFhAlign();
				});
				},200);
			});

			</script>';
			
			echo $return;return "";
			echo "<script>$('head').append('".$return."');</script>";
		}
	}
	
	private $useAbsoluteAlign = false;
	
	function absoluteAlign(){
		if(!$this->useAbsoluteAlign){
			$this->useAbsoluteAlign = true;
			$return = '<script>
						(function ($) {
			// VERTICALLY ALIGN FUNCTION
			$.fn.HFcentered = function() {
			    return this.each(function(i){
			    var ah = $(this).height();
			    var ph = $(this).parent().height();
			    var aw = $(this).width();
			    var pw = $(this).parent().width();
			    var mh = Math.ceil((ph-ah) / 2);
			    var mw = Math.ceil((pw-aw) / 2);
			    $(this).css("margin-top", mh);
			    $(this).css("margin-left", mw);
			    });
			};
			})(jQuery);
			
			$(document).ready(function() { 
				setTimeout(function() {
			  $(".HF-Centered").HFcentered();
			  $(".HF-centered").HFcentered();
			  $( window ).resize(function() {
				  $(".HF-Centered").HFcentered();
				  $(".HF-centered").HFcentered();
				});
				},200);
			});

			</script>';
			echo $return;return "";
			
			echo "<script>$(document).ready(function() { 
							$('head').append('".$return."');
						});</script>";
		}
	}
	
}