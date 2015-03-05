<?php
class HFfile{
	
	public $resizeSize = false;
	public $quality = 90;
	public $oldFile = "";
	
	/*! Setting this will automagically resize the image you'll upload! (max width/height)
		Set it to false to remove the auto-resize */
	function setResizeSize($pixel=500){
		$this->resizeSize = $pixel;
		return $this;
	}
	
	/*! If the image will be resized so this value will set the quality, 90 by default
		Lower number, less file weight BUT really bad quality! Choose it wisely */
	function setResizeQuality($quality){
		$this->quality = $quality;
		return $this;
	}
	
	/*! This one WILL DELETE THE FILE YOU'RE PASSING THE NAME!
		Will be triggered only on the actual successful upload of the file new file
		Use this ONLY if you want to delete the old file in the same directory of the new one uploaded.
	*/
	function deleteOldFile($fileName){
		$this->oldFile = $fileName;
		return $this;
	}
	
	
	/*! Upload a file named param1 in directory param2 (IT MUST BE WRITABLE!), max filesize in byte (so 1000=1mb) with extensions in array,
		not resize if false and $rewriteIfExists if true add numbers at the end of the file, no rewrite.
		Array with file data is give if upload is success, string with an error message if not so check is_array() to know if the upload is fine */
	function uploadFile($fileFieldName,$directory="upload/",$maxFileSize=3500,$extensions=array('doc','pdf','txt'),$rewriteIfExists=false){
		
		 if(isset($_FILES[$fileFieldName]))
	       {
	           $file = $_FILES[$fileFieldName];
	           
	           //check for / at the end of the upload directory
	           if($directory[(strlen($directory-1))]!="/") $directory .="/";
	           
	           if($file['error'] == UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])){
		           
		           
		        	if($_FILES[$fileFieldName]['size'] > ($maxFileSize * 1000)){
					    return LANG_FILE_BIG.' '.($maxFileSize/1000).'MB';
					}
			    	$name = pathinfo($directory.$_FILES[$fileFieldName]['name'], PATHINFO_FILENAME);
					$extension = pathinfo($directory.$_FILES[$fileFieldName]['name'], PATHINFO_EXTENSION);
					
					# ====================================== #
					# ==== CHECK EXTENSION OF THE IMAGE ==== #
					# ====================================== #
	
					if(!in_array(strtolower($extension), $extensions)){
						foreach($extensions as $ex){
							$ext .= ".".$ex;
						}
						return "($extension)! ".LANG_EXTENSION_NOT_VALID.": ".$ext;
					}
					
					$increment = ''; //start with no suffix
					
					if(!$rewriteIfExists){
						while(file_exists($directory.$name . $increment . '.' . $extension)) {
						    $increment++;
						}
					}
					
					$basename = $name . $increment . '.' . $extension;
					
					
					move_uploaded_file($file['tmp_name'], $directory.$basename);
					//Delete the old file if its name is passed then reset the name for safety
					if($this->oldFile!=""){
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$directory.$this->oldFile)){
							unlink($directory.$this->oldFile);
							$this->oldFile = "";
						}
					}
					return array("fileName"=>$basename,"extension"=>$extension,"nameWithoutExtension"=>$name.$increment);
					
	           }
	       }
	}
	
	/*! Upload an image named param1 in directory param2 (IT MUST BE WRITABLE!), max filesize in byte (so 1000=1mb) with extensions in array,
		NOT resize if false and $rewriteIfExists if true add numbers at the end of the file, no rewrite.
		Array with file data is give if upload is success, string with an error message if not so check is_array() to know if the upload is fine
		RESIZE? Just set '$this->setResizeSize = 100;' where 100 is the pixel integer, change it as you like */
	function uploadImage($imageFieldName,$directory="upload/",$maxFileSize=3500,$extensions=array('jpg','jpeg','png','gif'),$rewriteIfExists=false){
		
		global $HF;
		
		 if(isset($_FILES[$imageFieldName]))
	       {
	           $file = $_FILES[$imageFieldName];
	           $directory = trim($directory);
	           //check for / at the end of the upload directory
	           if($directory[(strlen($directory)-1)]!="/") $directory .="/";
	           
	           if($file['error'] == UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])){
		           
		           
		        	if($_FILES[$imageFieldName]['size'] > ($maxFileSize * 1000)){
					    return LANG_FILE_BIG.' '.($maxFileSize/1000).'MB';
					}
			    	$name = pathinfo($directory.$_FILES[$imageFieldName]['name'], PATHINFO_FILENAME);
					$extension = pathinfo($directory.$_FILES[$imageFieldName]['name'], PATHINFO_EXTENSION);
					
					# ====================================== #
					# ==== CHECK EXTENSION OF THE IMAGE ==== #
					# ====================================== #
	
					if(!in_array(strtolower($extension), $extensions)){
						foreach($extensions as $ex){
							$ext .= ".".$ex;
						}
						return "($extension)! ".LANG_EXTENSION_NOT_VALID.": ".$ext;
					}
					
					$increment = ''; //start with no suffix
					
					if(!$rewriteIfExists){
						while(file_exists($directory.$name . $increment . '.' . $extension)) {
						    $increment++;
						}
					}
					
					$basename = $name . $increment . '.' . $extension;
		           
		           
					move_uploaded_file($file['tmp_name'], $directory.$basename);
					
					//Delete the old file if its name is passed then reset the name for safety
					if($this->oldFile!="") $this->deleteFile($this->oldFile,$directory);
					
					
					if($this->resizeSize!=false && $this->resizeSize!="") $this->compressImage($directory.$basename,$directory.$basename,90,$this->resizeSize);
					
					return array("imageName"=>$basename,"extension"=>$extension,"nameWithoutExtension"=>$name.$increment);
					
	           }
	       }
	}
	
	
	/*!	Compress a passed image, OVERWRITING THE ORIGINAL FILE! So no double! */
	function compressImage($source, $destination="") {
	
		$quality = $this->quality;
		if($this->resizeSize === false || !is_numeric($this->resizeSize)){
			echo "You MUST set a 'HFfile::resizeSize' value to know how you want to resize this image! 1000 will be used but SET IT!";
			$resizeSize = 1000;
		}else{
			$resizeSize = $this->resizeSize;
		}
	
		if($destination=="") $destination = $source;
	
		$info = getimagesize($source);
	
		if ($info['mime'] == 'image/jpeg') 
			$image = imagecreatefromjpeg($source);
	
		elseif ($info['mime'] == 'image/gif') 
			$image = imagecreatefromgif($source);
	
		elseif ($info['mime'] == 'image/png') 
			$image = imagecreatefrompng($source);
	
	
		//RESIZE PART
		$width = $resizeSize;
		$height = $resizeSize;
	
		list($width_orig, $height_orig) = $info;
	
		$ratio_orig = $width_orig/$height_orig;
		if ($width/$height > $ratio_orig) {
			$width = $height*$ratio_orig;
		} else {
			$height = $width/$ratio_orig;
		}
		$image_p = imagecreatetruecolor($width, $height);
		
		if ($info['mime'] == 'image/png' || $info['mime'] == 'image/gif'){
			//Maintain the Alpha of gif/png images
			//imagealphablending( $image_p, false );
			//imagesavealpha( $image_p, true );
			/*
			imagealphablending($image_p, false);
			imagesavealpha($image_p,true);
			$transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
			imagefilledrectangle($image_p, 0, 0, $width, $height, $transparent);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
			*/
		}
		
		//AUTO ROTATING PART
		if ($info['mime'] == 'image/jpeg'){
			$exif = exif_read_data($source);
			if(!empty($exif['Orientation'])) {
			    switch($exif['Orientation']) {
			        case 8:
			            $image = imagerotate($image,90,0);
			            break;
			        case 3:
			            $image = imagerotate($image,180,0);
			            break;
			        case 6:
			            $image = imagerotate($image,-90,0);
			            break;
			    }
			}
		}
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	
		if ($info['mime'] == 'image/png' || $info['mime'] == 'image/gif'){
			imagegif($image_p, $destination);
			//PNG imagepng($image_p, $destination, $quality/10);
		}else{
			imagejpeg($image_p, $destination, $quality);
		}
	}

	/*! Directory is the folder name FROM THE ROOT! where the file is */
	function deleteFile($imgName,$directory){
		$directoryFull = $directory;
		if($directoryFull[0]!="/") $directoryFull = "/".$directoryFull;
		if($imgName[0]!="/" && $directory[strlen($directory)-1]!="/"){
			$directory.="/";
			$directoryFull.="/";
		}

		if(file_exists($_SERVER['DOCUMENT_ROOT'].$directoryFull.$imgName)){
			if(unlink($directory.$imgName)){
				return true;
			}else{
				return false;
			}
		}
		
		return false;
							
	}
	
}
