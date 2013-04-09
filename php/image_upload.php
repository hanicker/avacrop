<?php
##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################
class SimpleImage {
 
   var $image;
   var $image_type;
 
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      
   
	static function resizeImage($image,$width,$height,$scale) {
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
			case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
		}
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		
		switch($imageType) {
			case "image/gif":
				imagegif($newImage,$image); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				imagejpeg($newImage,$image,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);  
				break;
		}
		
		//chmod($image, 0777);
		return $image;
	}

	static function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = floor($width * $scale);
		$newImageHeight = floor($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
			case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
		}
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
				imagegif($newImage,$thumb_image_name); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
		}
		//chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}

	static function getImageHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}
	
	static function getImageWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}   
 
}



##########################################################################################################
##########################################################################################################
##########################################################################################################

$upload_dir="../upload/temp/";

if(isset($_GET['opt'])){
	if(!isset($_GET['image']))
		die();
	$image=$upload_dir.basename($_GET['image']);
	if(!file_exists($image))
		die();
	$image2=$image;
	$check=array('w','h','x','y');
	foreach($check as $c){
		$c=$_GET[$c];
		if(!isset($c)||!is_numeric($c))
			die('Error');
	}
	
	$exactsize=120;
	
	$imwidth=SimpleImage::getImageWidth($image);
	$imheight=SimpleImage::getImageHeight($image);
	$width=$imwidth*$_GET['w'];
	$height=$imheight*$_GET['h'];
	$imx=$imwidth*$_GET['x'];
	$imy=$imheight*$_GET['y'];
	
	//Crop
	$scale=$exactsize/$width;
	SimpleImage::resizeThumbnailImage($image2,$image,$width,$width,$imx,$imy,$scale);
	echo basename($_GET['image']);
	die();
}

/**Delete old files**/
if (is_dir($upload_dir)) {
   if ($dh = opendir($upload_dir)) {
      while ($file = readdir($dh)) {
         if(!is_dir($upload_dir.$file)) {
            if (filemtime($upload_dir.$file) < strtotime('-2 hours')) { 
               unlink($upload_dir.$file);
            } 
         }
      }
   } else {
      
   }
} else {
}
/**/



if(!isset($_FILES["upload"]))
	die();
$ns=$_FILES["upload"]["name"];
$ns=preg_replace("/[^A-Za-z0-9\.]/", '', $ns);
$newname=time().md5(time().rand(0,1000000000).'s').'_'.$ns;
move_uploaded_file($_FILES["upload"]["tmp_name"],$upload_dir . $newname);

$image = new SimpleImage();
$image->load($upload_dir . $newname);

$image->resizeToWidth(600);
$image->save($upload_dir."t_" . $newname);		

@unlink($upload_dir . $newname);

if(file_exists($upload_dir."t_" . $newname)){
	echo "t_".$newname;
}
	
