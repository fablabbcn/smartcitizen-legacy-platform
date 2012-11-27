<?php
class ImgComponent extends Component  {
	
	function redim($img,$dest,$largeur=0,$hauteur=0){
		$dimension=getimagesize($img);
        $ratio = $dimension[0] / $dimension[1];
        // Création des miniatures
        if($largeur==0 && $hauteur==0){ $largeur = $dimension[0]; $hauteur = $dimension[1]; }
		else if($hauteur==0){ $hauteur = round($largeur / $ratio); }
        else if($largeur==0){ $largeur = round($hauteur * $ratio); }
		if($dimension[0]>($largeur/$hauteur)*$dimension[1] ){ $dimY=$hauteur; $dimX=round($hauteur*$dimension[0]/$dimension[1]);}
		if($dimension[0]<($largeur/$hauteur)*$dimension[1]){ $dimX=$largeur; $dimY=round($largeur*$dimension[1]/$dimension[0]);}
		if($dimension[0]==($largeur/$hauteur)*$dimension[1]){ $dimX=$largeur; $dimY=$hauteur;}
        if(Configure::read('Config.imagemagick')){
            $cmd = '/usr/bin/convert -resize '.$dimX.'x'.$dimY.' "'.$img.'" "'.$dest.'"';
            shell_exec($cmd);

            $cmd = '/usr/bin/convert -gravity Center -quality 80 -crop '.$largeur.'x'.$hauteur.'+0+0 -page '.$largeur.'x'.$hauteur.' "'.$dest.'" "'.$dest.'"';
            shell_exec($cmd);
            
            shell_exec('chown 1003 "'.$dest.'"');

            return true;
        }
        if($dimension[0]>($largeur/$hauteur)*$dimension[1] ){ $dimY=$hauteur; $dimX=round($hauteur*$dimension[0]/$dimension[1]); $decalX=($dimX-$largeur)/2; $decalY=0;}
        if($dimension[0]<($largeur/$hauteur)*$dimension[1]){ $dimX=$largeur; $dimY=round($largeur*$dimension[1]/$dimension[0]); $decalY=($dimY-$hauteur)/2; $decalX=0;}
        if($dimension[0]==($largeur/$hauteur)*$dimension[1]){ $dimX=$largeur; $dimY=$hauteur; $decalX=0; $decalY=0;}
        $miniature =imagecreatetruecolor ($largeur,$hauteur);
        if(substr($img,-4)==".jpg" || substr($img,-4)==".JPG"){$image = imagecreatefromjpeg($img); }
        if(substr($img,-4)==".png" || substr($img,-4)==".PNG"){$image = imagecreatefrompng($img); }
        if(substr($img,-4)==".gif" || substr($img,-4)==".GIF"){$image = imagecreatefromgif($img); }

        imagecopyresampled($miniature,$image,-$decalX,-$decalY,0,0,$dimX,$dimY,$dimension[0],$dimension[1]);
        imagejpeg($miniature,$dest,90);
        
        return true;

	}
}
?>