<?
class ImageComponent extends Object
{
	var  $contentType =  array('image/jpg','image/bmp','image/jpeg','image/gif','image/png','image/pjpg','image/pbmp','image/pjpeg','image/ppng','image/pgif');

	function  upload_image_and_thumbnail($fileData,$size,$subFolder,$prefix) {
		if  (strlen($fileData['name'])>4)
		{
			$error =  0;
			$destFolder =  WWW_ROOT.$subFolder;
			$realFileName  = $fileData['name'];
			if(!is_dir($destFolder))  mkdir($destFolder,true);
				$filetype = $this->getFileExtension($fileData['name']);
			$filetype  = strtolower($filetype);
			if(!in_array($fileData['type'],$this->contentType)){
				return false;exit();
			}else if($fileData['size'] > 700000 ){
				return false;exit();
			}else{
				$imgsize =  GetImageSize($fileData['tmp_name']);
			}
			if  (is_uploaded_file($fileData['tmp_name'])){
				if  (!copy($fileData['tmp_name'],$destFolder.'/'.$realFileName )){
					return false;
					exit();
				}else{
					$this->resize_img($destFolder.'/'.$realFileName, $size,  $destFolder.'/'.$prefix.$realFileName);
					unlink($destFolder.'/'.$realFileName);
				}
			}
			return  $fileData;
		}
	}
	
	function delete_image($filename)
	{
		unlink($filename);
	}
	
	function  resize_img($tempFile, $size, $newFile){
		$filetype =  $this->getFileExtension($tempFile);
		$filetype =  strtolower($filetype);
		switch($filetype) {
			case "jpeg":
			case "jpg":
				$img_src  = imagecreatefromjpeg($tempFile);
			break;
			case "gif":
				$img_src = imagecreatefromgif  ($tempFile);
			break;
			case "png":
				$img_src = imagecreatefrompng  ($tempFile);
			break;
			case "bmp":
				$img_src = imagecreatefromwbmp  ($tempFile);
			break;
		}
		$true_width  = imagesx($img_src);
		$true_height = imagesy($img_src);
	 
		$size = explode('x',strtolower($size));
		if  ($true_width>=$true_height)
		{
			$width=$size[0];
			$height =  ($width/$true_width)*$true_height;
		}else{
			$height=$size[1];
			$width =  ($height/$true_height)*$true_width;
		}
		$img_des =  imagecreatetruecolor($width,$height);
		imagecopyresampled  ($img_des, $img_src, 0, 0, 0, 0, $width, $height, $true_width,  $true_height);
		// Save the resized image
		switch($filetype)
		{
			case "jpeg":
			case "jpg":
				imagejpeg($img_des,$newFile,80);
			break;
			case "gif":
				imagegif($img_des,$newFile,80);
			break;
			case  "png":
				imagepng($img_des,$newFile,80);
			break;
			case "bmp":
				imagewbmp($img_des,$newFile,80);
			break;
		}
	}
	
	function  getFileExtension($str){
		$i  = strrpos($str,".");
		if (!$i) { return ""; }
		$l =  strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
}