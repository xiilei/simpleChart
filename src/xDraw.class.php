<?php
namespace SimpleChart;
/**
 * use the same as pChart2
 * 
 * @author xilei
 */

define("PI", 3.14159265);

class xDraw{
    
    //抗锯齿
    public $Antialias = true;
    public $AntialiasQuality = 0;//
    
    protected $UseAlpha = true;

    protected $XSize = NULL;
    protected $YSize = NULL;
    protected $Picture = NULL;
    protected $TransparentBackground = false;
    //字体
    protected $FontName = "";
    protected $FontSize = 12;
    protected $FontColorR = 0;
    protected $FontColorG = 0;
    protected $FontColorB = 0;
    protected $FontColorA = NULL;
    //阴影
    protected $Shadow = false;
    protected $ShadowX = NULL;
    protected $ShadowY = NULL;
    protected $ShadowR = NULL;
    protected $ShadowG = NULL;
    protected $ShadowB = NULL;
    protected $Shadowa = NULL;
    //extra
    protected $LineSize = 1;

    public function __construct($XSize, $YSize, $TransparentBackground = false) {
        $this->TransparentBackground = $TransparentBackground;
        $this->XSize = $XSize;
        $this->YSize = $YSize;
        $this->Picture = imagecreatetruecolor($XSize, $YSize);
        if ($this->TransparentBackground) {
            imagealphablending($this->Picture, false);
            imagefilledRectangle($this->Picture, 0, 0, $XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 127));
            imagealphablending($this->Picture, true);
            imagesavealpha($this->Picture, true);
        } else {
            $C_White = $this->allocateColor(255, 255, 255);
            imagefilledRectangle($this->Picture, 0, 0, $XSize, $YSize, $C_White);
        }
    }
    
    /**
     * 设置阴影
     * @param type $Enabled
     * @param type $Format
     */
    public function setShadow($Enabled = true, $Format = "") {
        $X = isset($Format["X"]) ? $Format["X"] : 2;
        $Y = isset($Format["Y"]) ? $Format["Y"] : 2;
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 10;

        $this->Shadow = $Enabled;
        $this->ShadowX = $X;
        $this->ShadowY = $Y;
        $this->ShadowR = $R;
        $this->ShadowG = $G;
        $this->ShadowB = $B;
        $this->Shadowa = $Alpha;
    }
    
    /**
     * 启用/禁用alpha
     * @param type $enable
     */
    public function useAlpha($enable=true){
        if(!$enable){
            $this->Antialias = false;//disabled antialias
            $this->UseAlpha=false;
        }else{
            $this->UseAlpha=true;
        }
    }
    
    /**
     * 使用/禁用 反锯齿
     * @param type $enable
     */
    public function userAntialias($enable=true){
        $this->Antialias = ($enable == true);
    }


    /**
     * 设置线宽
     * @param type $thickness
     * @return boolean
     */
    public function setLineSize($thickness) {
        if (is_numeric($thickness) && $thickness > 0) {
            $this->LineSize = $thickness;
            imagesetthickness($this->Picture, $thickness);
        }
    }
    
    /**
     * 图片 PNG
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromPNG($X, $Y, $FileName) {
        $this->drawFromPicture(1, $FileName, $X, $Y);
    }
    
    /**
     * 图片 GIF
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromGIF($X, $Y, $FileName) {
        $this->drawFromPicture(2, $FileName, $X, $Y);
    }
    
    /**
     * 图片 JPG
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromJPG($X, $Y, $FileName) {
        $this->drawFromPicture(3, $FileName, $X, $Y);
    }
    
    /**
     * 图片
     * @param type $PicType
     * @param type $FileName
     * @param type $X
     * @param type $Y
     * @return boolean
     */
    public function drawFromPicture($PicType, $FileName, $X, $Y) {
        if (!file_exists($FileName)){
            return false;
        }
        list($Width, $Height) = self::getPicInfo($FileName);
        if ($PicType == 1) {
            $Raster = imagecreatefrompng($FileName);
        } elseif ($PicType == 2) {
            $Raster = imagecreatefromgif($FileName);
        } elseif ($PicType == 3) {
            $Raster = imagecreatefromjpeg($FileName);
        } else {
            return false;
        }

        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            if ($PicType == 3) {
                $this->drawFilledRectangle($X + $this->ShadowX, $Y + $this->ShadowY, $X + $Width + $this->ShadowX, $Y + $Height + $this->ShadowY, array("R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa));
            } else {
                $TranparentID = imagecolortransparent($Raster);
                for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
                    for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
                        $RGBa = imagecolorat($Raster, $Xc, $Yc);
                        $Values = imagecolorsforindex($Raster, $RGBa);
                        if ($Values["alpha"] < 120) {
                            $AlphaFactor = floor(($this->Shadowa / 100) * ((100 / 127) * (127 - $Values["alpha"])));
                            $this->drawAlphaPixel($X + $Xc + $this->ShadowX, $Y + $Yc + $this->ShadowY,array('Alpha'=>$AlphaFactor, 'R'=>$this->ShadowR, 'G'=>$this->ShadowG,'B'=>$this->ShadowB));
                        }
                    }
                }
            }
        }
        $this->Shadow = $RestoreShadow;

        imagecopy($this->Picture, $Raster, $X, $Y, 0, 0, $Width, $Height);
        imagedestroy($Raster);
    }
    
    /**
     * 设置字体属性
     * @param type $Format
     */
    public function setFontProperties($Format) {
        $R = isset($Format["R"]) ? $Format["R"] : -1;
        $G = isset($Format["G"]) ? $Format["G"] : -1;
        $B = isset($Format["B"]) ? $Format["B"] : -1;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $FontName = isset($Format["FontName"]) ? $Format["FontName"] : NULL;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : NULL;

        if ($R != -1) {$this->FontColorR = $R;}
        if ($G != -1) {$this->FontColorG = $G;}
        if ($B != -1) {$this->FontColorB = $B;}
        if ($Alpha != NULL) {$this->FontColorA = $Alpha;}
        if ($FontName != NULL){$this->FontName = $FontName;}    
        if ($FontSize != NULL){$this->FontSize = $FontSize;}
    }
    
    /**
     * 文字
     * @param type $X
     * @param type $Y
     * @param type $Text
     * @param type $Format
     */
    public function drawText($X, $Y, $Text, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : $this->FontColorR;
        $G = isset($Format["G"]) ? $Format["G"] : $this->FontColorG;
        $B = isset($Format["B"]) ? $Format["B"] : $this->FontColorB;
        $FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->FontName;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $this->FontColorA;
        $Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
        $FontSize = isset($Format['FontSize']) ? $Format['FontSize'] : $this->FontSize;
        $Shadow = $this->Shadow;

        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $C_ShadowColor = $this->allocateColor( $this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
            imagettftext($this->Picture, $FontSize, $Angle, $X + $this->ShadowX, $Y + $this->ShadowY, $C_ShadowColor, $FontName, $Text);
        }

        $C_TextColor = $this->allocateColor( $R, $G, $B, $Alpha);
        imagettftext($this->Picture, $FontSize, $Angle, $X, $Y, $C_TextColor, $FontName, $Text);

        $this->Shadow = $Shadow;
    }
    
    /**
     * 圆/椭圆
     * @param type $Xc
     * @param type $Yc
     * @param type $Width
     * @param type $Height
     * @param type $Format
     */
    public function drawCircle($Xc, $Yc, $Width, $Height, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            $this->drawCircle($Xc + $this->ShadowX, $Yc + $this->ShadowY, $Width, $Height, array("R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa));
        }
        
        if(!$this->Antialias){
            $C_color = $this->allocateColor( $R, $G, $B, $Alpha);
            imageellipse($this->Picture, $Xc, $Yc, $Width<<1, $Height<<1, $C_color);
        }else{
            if ( $Width == 0 ) { $Width = $Height; }
            if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
            if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
            if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

            $Step = 360 / (2 * PI * max($Width,$Height));
            for($i=0;$i<=360;$i=$i+$Step){
              $Y = cos($i*PI/180) * $Height + $Yc;
              $X = sin($i*PI/180) * $Width + $Xc;
              $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
            }
        }
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 填充圆/椭圆
     * @param type $X
     * @param type $Y
     * @param type $Radius
     * @param type $Format
     */
    public function drawFilledCircle($X, $Y,$Width,$Height, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha;
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            $this->drawFilledCircle($X + $this->ShadowX, $Y + $this->ShadowY, $Width, $Height, array("R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa));
        }
        $Color = $this->allocateColor( $R, $G, $B, $Alpha);

        imagefilledellipse($this->Picture, $X, $Y, $Width<<1, $Height<<1, $Color);
        if ( $this->Antialias ){
           $this->drawCircle($X,$Y,$Width,$Height,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
        }
        
        if ($BorderR != -1) {
            $this->drawCircle($X, $Y, $Width, $Height, array("R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha));
        }
        $this->Shadow = $RestoreShadow;
    }
    
    public function drawFilledArc($X,$Y,$Width,$Height,$start,$end,$Format=''){
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
       // $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
       // $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
       // $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
       // $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha;
        imagefilledarc($this->Picture,$X,$Y,$Width,$Height,$start,$end,$this->allocateColor( $R, $G, $B,$Alpha),IMG_ARC_PIE);
    }
    
    public function drawArc(){
        
    }
    
    /**
     * 填充矩形
     * @param type $X1
     * @param type $Y1
     * @param type $X2
     * @param type $Y2
     * @param type $Format
     */
    public function drawFilledRectangle($X1, $Y1, $X2, $Y2, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha;

        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            $this->drawFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, array("R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa));
        }
        $Color = $this->allocateColor( $R, $G, $B, $Alpha);
        imagefilledrectangle($this->Picture, ceil($X1), ceil($Y1), floor($X2), floor($Y2), $Color);
        if ($BorderR != -1) {
            $this->drawRectangle($X1, $Y1, $X2, $Y2, array("R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha));
        }
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 矩形
     * @param type $X1
     * @param type $Y1
     * @param type $X2
     * @param type $Y2
     * @param type $Format
     */
    public function drawRectangle($X1, $Y1, $X2, $Y2, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Color = $this->allocateColor( $R, $G, $B, $Alpha);
        imagerectangle($this->Picture, $X1, $Y1, $X2, $Y2, $Color);
    }
    
    /**
     * 线
     * @param type $X1
     * @param type $Y1
     * @param type $X2
     * @param type $Y2
     * @param type $Format
     */
    public function drawLine($X1, $Y1, $X2, $Y2, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Style = isset($Format["Style"]) ? $Format["Style"] : -1;//just for no Antialias
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            $ShadowColor = $this->allocateColor( $this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
            imageline($this->Picture, $X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $ShadowColor);
        }
        
        $Color = $this->allocateColor( $R, $G, $B, $Alpha);
        //仅仅在斜线的时候需要
        if($this->Antialias && !($X1==$X2 || $Y1 == $Y2)){
           $distance = sqrt(pow($X2-$X1,2)+pow($Y2-$Y1,2));
           $xstep = ($X2-$X1)/$distance;
           $ystep = ($Y2-$Y1)/$distance;
           for($i=0;$i<$distance;$i++){
              $X = $i*$xstep + $X1;
              $Y = $i*$ystep + $Y1;
              $this->drawAntialiasPixel($X,$Y,$Color);
           }
        }else{
           if (is_array($Style)) {
              imagesetstyle($this->Picture, $Style);
              imageline($this->Picture, $X1, $Y1, $X2, $Y2, IMG_COLOR_STYLED);
           } elseif (is_resource($Style)) {
              imagesetbrush($this->Picture, $Style);
              imageline($this->Picture, $X1, $Y1, $X2, $Y2, IMG_COLOR_STYLEDBRUSHE);
           } else {                
              imageline($this->Picture, $X1, $Y1, $X2, $Y2, $Color);
           }
        }
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 多边形
     * @param type $Points
     * @param type $Format
     * @return boolean
     */
    public function drawPolygon($Points, $Format = "") {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : false;
        $NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
        $BorderAlpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $Alpha / 2;

        $Backup = $Points;
        $count = count($Points);
        $RestoreShadow = $this->Shadow;
        if ($count < 6) return false;
        if (!$NoFill) {
            if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
                $this->Shadow = false;
                for ($i = 0; $i < $count; $i = $i + 2) {
                    $Shadow[] = $Points[$i] + $this->ShadowX;
                    $Shadow[] = $Points[$i + 1] + $this->ShadowY;
                }
                $this->drawPolygon($Shadow, array("R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa, "NoBorder" => true));
            }
            $FillColor = $this->allocateColor( $R, $G, $B, $Alpha);
            imagefilledpolygon($this->Picture, $Points, $count / 2, $FillColor);
        }
        if (!$NoBorder) {
            $Points = $Backup;
            if($this->Antialias){
                $LFormat = array('Alpha'=>$BorderAlpha,'R'=>$BorderR,'G'=>$BorderG,'B'=>$BorderB);
                for($i=0;$i < $count; $i = $i + 2){
                    if(!isset($Points[$i+2])){
                        $this->drawLine($Points[$i], $Points[$i+1], $Points[0], $Points[1],$LFormat);
                        break;
                    }
                    $this->drawLine($Points[$i], $Points[$i+1], $Points[$i+2], $Points[$i+3],$LFormat);
                }
            }else{
                $BorderColor = $NoFill ? $this->allocateColor( $R, $G, $B, $Alpha) :
                $this->allocateColor( $BorderR, $BorderG, $BorderB, $BorderAlpha);
                imagepolygon($this->Picture, $Points, $count / 2, $BorderColor);
            }
        }
        
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 像素点
     * @param type $X
     * @param type $Y
     * @param type $Format
     * @return boolean
     */
    public function drawAlphaPixel($X, $Y, $Format='') {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize)
            return false;
        if ($R < 0) {$R = 0;} if ($R > 255) {$R = 255;}
        if ($G < 0) { $G = 0;} if ($G > 255) {$G = 255;}
        if ($B < 0) {$B = 0;} if ($B > 255) {$B = 255;}
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $AlphaFactor = floor(($Alpha / 100) * $this->Shadowa);
            $ShadowColor = $this->allocateColor( $this->ShadowR, $this->ShadowG, $this->ShadowB, $AlphaFactor);
            imagesetpixel($this->Picture, $X + $this->ShadowX, $Y + $this->ShadowY, $ShadowColor);
        }
        $C_Aliased = $this->allocateColor( $R, $G, $B, $Alpha);
        imagesetpixel($this->Picture, $X, $Y, $C_Aliased);
    }
    
    /**
     * 抗锯齿像素点
     * @param type $X
     * @param type $Y
     * @param type $Format
     * @return boolean
     */
    public function drawAntialiasPixel($X,$Y,$Format=''){
        $R     = isset($Format["R"]) ? $Format["R"] : 0;
        $G     = isset($Format["G"]) ? $Format["G"] : 0;
        $B     = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        if ( $X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize ){
            return false;
        }
        if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
        if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
        if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }
        
        $Xi   = floor($X);
        $Yi   = floor($Y);
        if( $Xi == $X && $Yi == $Y){
          $this->drawAlphaPixel($X,$Y,array('Alpha'=>$Alpha,'R'=>$R,'G'=>$G,'B'=>$B));
        }else{
          $Alpha1 = (1 - ($X - $Xi)) * (1 - ($Y - $Yi)) * $Alpha;
          if ( $Alpha1 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi,array('Alpha'=>$Alpha1,'R'=>$R,'G'=>$G,'B'=>$B)); }

          $Alpha2 = ($X - $Xi) * (1 - ($Y - $Yi)) * $Alpha;
          if ( $Alpha2 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi,array('Alpha'=>$Alpha2,'R'=>$R,'G'=>$G,'B'=>$B));}

          $Alpha3 = (1 - ($X - $Xi)) * ($Y - $Yi)  * $Alpha;
          if ( $Alpha3 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi+1,array('Alpha'=>$Alpha3,'R'=>$R,'G'=>$G,'B'=>$B)); }

          $Alpha4 = ($X - $Xi) * ($Y - $Yi) * $Alpha;
          if ( $Alpha4 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi+1,array('Alpha'=>$Alpha4,'R'=>$R,'G'=>$G,'B'=>$B)); }
         }
    }

    public function filter($filtertype, $params) {
        if ($filtertype == IMG_FILTER_COLORIZE) {
            $params = func_get_args();
            $R = isset($params[1]) ? $params[1] : 0;
            $G = isset($params[2]) ? $params[2] : 0;
            $B = isset($params[3]) ? $params[3] : 0;
            return imagefilter($this->Picture, IMG_FILTER_COLORIZE, $R, $G, $B);
        } else {
            return imagefilter($this->Picture, $filtertype, $params);
        }
    }

    public function render($FileName,$type="png") {
        if ($this->TransparentBackground) {
            imagealphablending($this->Picture, false);
            imagesavealpha($this->Picture, true);
        }
        imagepng($this->Picture, $FileName);
        switch ($type){
            case 'jpeg':
                header('Content-type: image/jpeg');
                imagejpeg($this->Picture,$FileName);
                break;
            default:
                header('Content-type: image/png');
                imagepng($this->Picture,$FileName);
        }
    }

    public function stroke($BrowserExpire = false,$type="png") {
        if ($this->TransparentBackground) {
            imagealphablending($this->Picture, false);
            imagesavealpha($this->Picture, true);
        }

        if ($BrowserExpire) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }
        switch ($type){
            case 'jpeg'://Lost too much
                header('Content-Type: image/jpeg');
                imagejpeg($this->Picture);
                break;
            default:
                header('Content-Type: image/png');
                imagepng($this->Picture);
        }
        
    }
    
     /**
     * alpha颜色
     * @param type $R
     * @param type $G
     * @param type $B
     * @param type $Alpha
     * @return type
     */
    public function allocateColor($R, $G, $B, $Alpha = 100) {
        if ($R < 0) {$R = 0;} if ($R > 255) {$R = 255;}
        if ($G < 0) {$G = 0;} if ($G > 255) {$G = 255;}
        if ($B < 0) {$B = 0;} if ($B > 255) {$B = 255;}
        if ($Alpha < 0) {$Alpha = 0;}
        if ($Alpha > 100) {$Alpha = 100;}

        $Alpha = (127 / 100) * (100 - $Alpha);
        return ($this->UseAlpha ? imagecolorallocatealpha($this->Picture, $R, $G, $B, $Alpha)
            :  imagecolorallocate($this->Picture, $R, $G, $B));
    }

    public function __destruct() {
        if (!empty($this->Picture)){
            imagedestroy($this->Picture);
        }
    }
    
    /**
     * hex to rgb
     */
    static public function toRGB(){
        
    }
    
    /**
     * 图片信息
     * @param type $FileName
     * @return type
     */
    static public function getPicInfo($FileName) {
        $Infos = getimagesize($FileName);
        $Width = $Infos[0];
        $Height = $Infos[1];
        $Type = $Infos["mime"];

        if ($Type == "image/png") {
            $Type = 1;
        }
        if ($Type == "image/gif") {
            $Type = 2;
        }
        if ($Type == "image/jpeg ") {
            $Type = 3;
        }

        return(array($Width, $Height, $Type));
    }
    
    /**
     * 文字信息
     */
    static public function getTextBox($Text, $FontSize, $FontFile, $FontAngle) {
        $Rect = imagettfbbox($FontSize, $FontAngle, $FontFile, $Text);
        $MinX = min(array($Rect[0], $Rect[2], $Rect[4], $Rect[6]));
        $MaxX = max(array($Rect[0], $Rect[2], $Rect[4], $Rect[6]));
        $MinY = min(array($Rect[1], $Rect[3], $Rect[5], $Rect[7]));
        $MaxY = max(array($Rect[1], $Rect[3], $Rect[5], $Rect[7]));

        return array(
            "left" => abs($MinX) - 1,
            "top" => abs($MinY) - 1,
            "width" => $MaxX - $MinX,
            "height" => $MaxY - $MinY,
            "box" => $Rect
        );
    }  
   

}
