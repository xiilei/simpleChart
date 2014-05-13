<?php
namespace SimpleChart;
/**
 * use the same as pDraw,based on GD2
 * this is a free software!
 * 
 * 支持line,arc,image,rect,text,eclipse,beziercurve(future features)
 * 支持阴影,反锯齿
 * 
 * @todo arc条纹问题,beziercurve,lineSize,textAlign
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author xilei
 */

class xDraw{
    
    public $AntialiasQuality = 0;//0-100
    //抗锯齿
    protected $Antialias = true;

    protected $UseAlpha = true;

    protected $Width = NULL;
    protected $Height = NULL;
    protected $Image = NULL;
    protected $TransparentBackground = false;
    
    protected $Shadow = false;
    
    protected $CommonFormat = array(
        "FontName"=>"",
        "FontSize"=>12,
        "FontColor"=>array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>100),
        "Color"=>array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>100),
        "BorderColor"=>false
    );
    
    protected $ShadowFormat = NULL;
    
    public function __construct($Width, $Height, $TransparentBackground = false) {
        $this->TransparentBackground = $TransparentBackground;
        $this->Width = $Width;
        $this->Height = $Height;
        $this->Image = imagecreatetruecolor($Width, $Height);
        if ($this->TransparentBackground) {
            imagealphablending($this->Image, false);
            imagefilledRectangle($this->Image, 0, 0, $Width, $Height, imagecolorallocatealpha($this->Image, 255, 255, 255, 127));
            imagealphablending($this->Image, true);
            imagesavealpha($this->Image, true);
        } else {
            $C_White = $this->allocateColor(255, 255, 255);
            imagefilledRectangle($this->Image, 0, 0, $Width, $Height, $C_White);
        }
    }
    
   /**
    * 设置阴影
    * @param type $Format
    */
    public function setShadow($Format = "") {
        if(is_bool($Format)){
            $this->Shadow = $Format;
            if($Format && empty($this->ShadowFormat)){
                $this->ShadowFormat=array(
                  'X'=>2,'Y'=>2,'Color'=> array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>10)
                );
            }
        }elseif(is_array($Format)){
            if($Format["X"] == 0 || $Format["Y"] == 0){
                $this->Shadow=false;
            }else{
                $this->Shadow=true;
            }
            $this->ShadowFormat= array(
                'X'=>isset($Format["X"]) ? $Format["X"] : 2,
                'Y'=>isset($Format["Y"]) ? $Format["Y"] : 2,
                'Color'=>isset($Format['Color'])? $Format['Color'] : array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>10)
            );
        }
    }
    
    /**
     * 设置格式
     * @param type $Key
     * @param type $Value
     */
    public function setFormat($Key,$Value){
        if(is_array($Key)){
            $this->CommonFormat=array(
                "FontName"=>isset($Key['FontName']) ? $Key['FontName'] :"",
                "FontSize"=>isset($Key["FontSize"]) ? $Key["FontSize"] : 12,
                "FontColor"=>isset($Key['FontColor']) ? $Key['FontColor'] : array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>100),
                "Color"=>isset($Key['Color']) ? $Key['Color'] : array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>100),
                "BorderColor"=>isset($Key['Color']) ? $Key['Color'] : array('R'=>0,'G'=>0,'B'=>0,'Alpha'=>100)
            );
        }elseif(is_string($Key)&&$Value!==''&&$Value!==NULL){
            $this->CommonFormat[$Key]=$Value;
        }
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
            imagesetthickness($this->Image, $thickness);
        }
    }
    
    /**
     * 图片 PNG
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromPNG($X, $Y, $FileName) {
        $this->drawFromImage(1, $FileName, $X, $Y);
    }
    
    /**
     * 图片 GIF
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromGIF($X, $Y, $FileName) {
        $this->drawFromImage(2, $FileName, $X, $Y);
    }
    
    /**
     * 图片 JPG
     * @param type $X
     * @param type $Y
     * @param type $FileName
     */
    public function drawFromJPG($X, $Y, $FileName) {
        $this->drawFromImage(3, $FileName, $X, $Y);
    }
    
    /**
     * 图片
     * @param type $PicType
     * @param type $FileName
     * @param type $X
     * @param type $Y
     * @return boolean
     */
    public function drawFromImage($PicType, $FileName, $X, $Y) {
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
        if ($this->Shadow) {
            $this->Shadow = false;
            if ($PicType == 3) {
                $this->drawFilledRectangle($X + $this->ShadowFormat['X'], $Y + $this->ShadowFormat['Y'], 
                        $X + $Width + $this->ShadowFormat['X'], $Y + $Height + $this->ShadowFormat['Y'], 
                        array('Color'=>$this->ShadowFormat['Color']));
            } else {
                imagecolortransparent($Raster);
                $ShadowI = $this->ShadowFormat['Color'];
                for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
                    for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
                        $RGBa = imagecolorat($Raster, $Xc, $Yc);
                        $Values = imagecolorsforindex($Raster, $RGBa);
                        if ($Values["alpha"] < 120) {
                            $ShadowI['Alpha'] = floor(($this->ShadowFormat['Color']['Alpha'] / 100) * ((100 / 127) * (127 - $Values["alpha"])));
                            $this->drawAlphaPixel($X + $Xc + $this->ShadowFormat['X'], $Y + $Yc + $this->ShadowFormat['Y'],$ShadowI);
                        }
                    }
                }
            }
        }
        $this->Shadow = $RestoreShadow;

        imagecopy($this->Image, $Raster, $X, $Y, 0, 0, $Width, $Height);
        imagedestroy($Raster);
    }   
    
    /**
     * 文字
     * @param type $X
     * @param type $Y
     * @param type $Text
     * @param type $Format
     */
    public function drawText($X, $Y, $Text, $Format = "") {
        $Color = isset($Format['Color']) ? $Format['Color'] :$this->CommonFormat['FontColor'];
        $FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->CommonFormat['FontName'];
        $FontSize = isset($Format['FontSize']) ? $Format['FontSize'] : $this->CommonFormat['FontSize'];
        $Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow) {
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $C_ShadowColor = $this->allocateColor($ShadowColor);
            imagettftext($this->Image, $FontSize, $Angle, $X + $this->ShadowFormat['X'], $Y + $this->ShadowFormat['Y'], $C_ShadowColor, $FontName, $Text);
            unset($ShadowColor,$C_ShadowColor);
        }

        $C_TextColor = $this->allocateColor($Color);
        imagettftext($this->Image, $FontSize, $Angle, $X, $Y, $C_TextColor, $FontName, $Text);

        $this->Shadow = $RestoreShadow;
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
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow) {
            $this->Shadow = false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawCircle($Xc + $this->ShadowFormat['X'], $Yc + $this->ShadowFormat['X'], $Width, $Height,array('Color'=>$ShadowColor));
            unset($ShadowColor);
        }
        
        if(!$this->Antialias){
            $C_Color = $this->allocateColor($Color);
            imageellipse($this->Image, $Xc, $Yc, $Width<<1, $Height<<1, $C_Color);
        }else{
            $Step = 360 / (2 * M_PI * max($Width,$Height));
            for($i=0;$i<=360;$i=$i+$Step){
              //这里把sin cos交换不影响绘图
              $Y = cos($i*M_PI/180) * $Height + $Yc;
              $X = sin($i*M_PI/180) * $Width + $Xc;
              $this->drawAntialiasPixel($X,$Y,$Color);
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
    public function drawFilledCircle($X, $Y,$Width,$Height,$Format = "") {
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $BorderColor = isset($Format['BorderColor']) ? $Format['BorderColor'] : $this->CommonFormat['BorderColor'];
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow) {
            $this->Shadow = false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawFilledCircle($X + $this->ShadowFormat['X'], $Y + $this->ShadowFormat['Y'], $Width, $Height,array('Color'=>$ShadowColor));
            unset($ShadowColor);
        }        
        
        $C_Color = $this->allocateColor($Color);
        imagefilledellipse($this->Image, $X, $Y, $Width<<1, $Height<<1, $C_Color);
        if ( $this->Antialias && empty($BorderColor)){
           $this->drawCircle($X,$Y,$Width,$Height,array('Color'=>$Color));
        }
        
        if (!empty($BorderColor)) {
            $this->drawCircle($X, $Y, $Width, $Height,array('Color'=>$BorderColor));
        }
        
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 填充圆弧
     * @param type $X
     * @param type $Y
     * @param type $Width
     * @param type $Height
     * @param type $Start
     * @param type $End
     * @param type $Format
     */
    public function drawFilledArc($X,$Y,$Width,$Height,$Start,$End,$Format=''){
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $BorderColor = isset($Format['BorderColor']) ? $Format['BorderColor'] : $this->CommonFormat['BorderColor'];
        
        $RestoreShadow = $this->Shadow;
        if($this->Shadow){
            $this->Shadow = false;
            //这里简单处理
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawFilledArc($X+$this->ShadowFormat['X'], $Y+$this->ShadowFormat['Y'], $Width, $Height, $Start, $End,
               array('Color'=>$ShadowColor,'BorderColor'=>false));
            unset($ShadowColor);
        }       
        
        $C_Color =  $this->allocateColor($Color);
        imagefilledarc($this->Image,$X,$Y,$Width<<1,$Height<<1,$Start,$End,$C_Color,IMG_ARC_PIE);
        
        if($this->Antialias && empty($BorderColor)){
            $this->drawArc($X,$Y,$Width,$Height,$Start,$End,array("Border"=>true,'Color'=>$Color));
        }
        
        if (!empty($BorderColor)) {
            $this->drawArc($X, $Y, $Width, $Height, $Start, $End,array('Color'=>$BorderColor));
        }
        
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 圆弧(顺时针方向)
     * @param type $Xc
     * @param type $Yc
     * @param type $Width
     * @param type $Height
     * @param type $Start
     * @param type $End
     * @param type $Format
     */
    public function drawArc($Xc,$Yc,$Width,$Height,$Start,$End,$Format=''){
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        //是否绘制边侧
        $Border = isset($Format['Border']) ? $Format['Border'] : false;
        
        $RestoreShadow = $this->Shadow;
        if($this->Shadow ){
            $this->Shadow = false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawArc($Xc+$this->ShadowFormat['X'], $Yc+$this->ShadowFormat['Y'],
                    $Width, $Height, $Start, $End,array('Color'=>$ShadowColor,'Border'=>$Border));
            unset($ShadowColor);
        }
       
        if($this->Antialias){
            $Step = 360 / (2 * M_PI * max($Width,$Height));
            $End = $End>=$Start ? $End : 360+$End;
            for($i=$Start;$i<=$End;$i=$i+$Step){
              $Y = sin($i*M_PI/180) * $Height + $Yc;
              $X = cos($i*M_PI/180) * $Width + $Xc;
              $this->drawAntialiasPixel($X,$Y,$Color);
            }
        }else{
            $C_Color = $this->allocateColor($Color);    
            imagearc($this->Image, $Xc, $Yc, $Width<<1, $Height<<1, $Start, $End, $C_Color);
        }
        
        if($Border){
            $this->drawLine($Xc, $Yc, cos($Start*M_PI/180) * $Width + $Xc, sin($Start*M_PI/180) * $Height + $Yc,array('Color'=>$Color));
            $this->drawLine($Xc, $Yc, cos($End*M_PI/180) * $Width + $Xc, sin($End*M_PI/180) * $Height + $Yc,array('Color'=>$Color));
        }
        $this->Shadow = $RestoreShadow; 
       
    }
    
    /**
     * @todo 贝塞尔曲线
     */
    public function drawBeziercurve(){
        
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
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $BorderColor = isset($Format['BorderColor']) ? $Format['BorderColor'] : $this->CommonFormat['BorderColor'];

        $RestoreShadow = $this->Shadow;
        if ($this->Shadow) {
            $this->Shadow = false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawFilledRectangle($X1 + $this->ShadowFormat['X'], $Y1 + $this->ShadowFormat['Y'], $X2 + $this->ShadowFormat['X'], $Y2 + $this->ShadowFormat['Y'], 
                    array('Color'=>$ShadowColor,'BorderColor'=>false));
            unset($ShadowColor);
        }
        $C_Color = $this->allocateColor( $Color);
        imagefilledrectangle($this->Image, ceil($X1), ceil($Y1), floor($X2), floor($Y2), $C_Color);
        if (!empty($BorderColor)) {
            $this->drawRectangle($X1, $Y1, $X2, $Y2, array('Color'=>$BorderColor));
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
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        
        $RestoreShadow = $this->Shadow;
        if($this->Shadow){
            $this->Shadow=false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawRectangle($X1 + $this->ShadowFormat['X'], $Y1 + $this->ShadowFormat['Y'], $X2 + $this->ShadowFormat['X'], $Y2 + $this->ShadowFormat['Y'],
                    array('Color'=>$ShadowColor));
            unset($ShadowColor);
        }
        $C_Color = $this->allocateColor($Color);
        imagerectangle($this->Image, $X1, $Y1, $X2, $Y2, $C_Color);
        
        $this->Shadow = $RestoreShadow;
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
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $Style = isset($Format["Style"]) ? $Format["Style"] : -1;//just for no Antialias
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow){
            $this->Shadow = false;
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            imageline($this->Image, $X1 + $this->ShadowFormat['X'], $Y1 + $this->ShadowFormat['Y'], 
                    $X2 + $this->ShadowFormat['X'], $Y2 + $this->ShadowFormat['Y'], $this->allocateColor($ShadowColor));
            unset($ShadowColor);
        }
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
              imagesetstyle($this->Image, $Style);
              imageline($this->Image, $X1, $Y1, $X2, $Y2, IMG_COLOR_STYLED);
           } elseif (is_resource($Style)) {
              imagesetbrush($this->Image, $Style);
              imageline($this->Image, $X1, $Y1, $X2, $Y2, IMG_COLOR_STYLEDBRUSHE);
           } else {
              $C_Color = $this->allocateColor($Color);
              imageline($this->Image, $X1, $Y1, $X2, $Y2, $C_Color);
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
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
       
        $count = count($Points);
        $RestoreShadow = $this->Shadow;
        if ($count < 6) return false;
        
        if ($this->Shadow) {
            $this->Shadow = false;
            $ShadowPoints = array();
            for ($i = 0; $i < $count; $i = $i + 2) {
                $ShadowPoints[] = $Points[$i] + $this->ShadowFormat['X'];
                $ShadowPoints[] = $Points[$i + 1] + $this->ShadowFormat['Y'];
            }
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawPolygon($ShadowPoints, array('Color'=>$ShadowColor));
            unset($ShadowPoints,$ShadowColor);
        }
        
        if($this->Antialias){
            for($i=0;$i < $count; $i = $i + 2){
                if(!isset($Points[$i+2])){
                    $this->drawLine($Points[$i], $Points[$i+1], $Points[0], $Points[1],array('Color'=>$Color));
                }else{
                    $this->drawLine($Points[$i], $Points[$i+1], $Points[$i+2], $Points[$i+3],array('Color'=>$Color));
                }
            }
        }else{
            imagepolygon($this->Image, $Points, $count/2, $this->allocateColor($Color));
        }
         
        $this->Shadow = $RestoreShadow;
    }
    
    public function drawFilledPolygon($Points,$Format=""){
        $Color = isset($Format['Color']) ? $Format['Color'] : $this->CommonFormat['Color'];
        $BorderColor = isset($Format['BorderColor']) ? $Format['BorderColor'] : $this->CommonFormat['BorderColor'];
        
        $count = count($Points);
        $RestoreShadow = $this->Shadow;
        if ($count < 6) return false;
        if ($this->Shadow) {
            $this->Shadow = false;
            $ShadowPoints = array();
            for ($i = 0; $i < $count; $i = $i + 2) {
                $ShadowPoints[] = $Points[$i] + $this->ShadowFormat['X'];
                $ShadowPoints[] = $Points[$i + 1] + $this->ShadowFormat['Y'];
            }
            $ShadowColor = $this->ShadowFormat['Color'];
            $ShadowColor['Alpha'] = ceil(($Color['Alpha'] / 100) * $this->ShadowFormat['Color']['Alpha']);
            $this->drawFilledPolygon($ShadowPoints, array('Color'=>$ShadowColor, "BorderColor" => false));
            unset($ShadowPoints,$ShadowColor);
        }
        
        $FillColor = $this->allocateColor($Color);
        imagefilledpolygon($this->Image, $Points, $count / 2, $FillColor);
        
        if($this->Antialias && empty($BorderColor)){
            $this->drawPolygon($Points,array('Color'=>$Color));
        }
        
        if (!empty($BorderColor)) {
            $this->drawPolygon($Points,array('Color'=>$BorderColor));
        }
        
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 像素点
     * @param type $X
     * @param type $Y
     * @param type $Color
     * @return boolean
     */
    public function drawAlphaPixel($X, $Y, $Color='') {
        $Color = !empty($Color) ? $Color :$this->CommonFormat['Color'];
        
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow){
            $this->Shadow = false;
            $L_Shadow = $this->ShadowFormat['Color'];
            $L_Shadow['Alpha'] = floor(($Color['Alpha'] / 100) *$this->ShadowFormat['Color']['Alpha']);
            imagesetpixel($this->Image, $X + $this->ShadowFormat['X'], $Y + $this->ShadowFormat['Y'], $this->allocateColor($L_Shadow));
            uset($L_Shadow);
        }
        $C_Color = $this->allocateColor($Color);
        imagesetpixel($this->Image, $X, $Y, $C_Color);
        $this->Shadow = $RestoreShadow;
    }
    
    /**
     * 抗锯齿像素点
     * @param type $X
     * @param type $Y
     * @param type $Color
     * @return boolean
     */
    public function drawAntialiasPixel($X,$Y,$Color=''){
        $Color = !empty($Color) ? $Color :$this->CommonFormat['Color'];
        $Alpha = $Color['Alpha'];
        $Xi   = floor($X);
        $Yi   = floor($Y);
        if( $Xi == $X && $Yi == $Y){
          $this->drawAlphaPixel($X,$Y,$Color);
        }else{
          $Alpha1 = (1 - ($X - $Xi)) * (1 - ($Y - $Yi)) * $Alpha;
          if ( $Alpha1 > $this->AntialiasQuality ) {
              $Color['Alpha']=$Alpha1;
              $this->drawAlphaPixel($Xi,$Yi,$Color); 
          }
          $Alpha2 = ($X - $Xi) * (1 - ($Y - $Yi)) * $Alpha;
          if ( $Alpha2 > $this->AntialiasQuality ) {
              $Color['Alpha']=$Alpha2;
              $this->drawAlphaPixel($Xi+1,$Yi,$Color);
          }
          $Alpha3 = (1 - ($X - $Xi)) * ($Y - $Yi)  * $Alpha;
          if ( $Alpha3 > $this->AntialiasQuality ) {
               $Color['Alpha']=$Alpha3;
              $this->drawAlphaPixel($Xi,$Yi+1, $Color); 
          }
          $Alpha4 = ($X - $Xi) * ($Y - $Yi) * $Alpha;
          if ( $Alpha4 > $this->AntialiasQuality ) {
               $Color['Alpha']=$Alpha4;
              $this->drawAlphaPixel($Xi+1,$Yi+1, $Color);
           }
         }
    }

    public function filter($filtertype, $params) {
        if ($filtertype == IMG_FILTER_COLORIZE) {
            $params = func_get_args();
            $R = isset($params[1]) ? $params[1] : 0;
            $G = isset($params[2]) ? $params[2] : 0;
            $B = isset($params[3]) ? $params[3] : 0;
            return imagefilter($this->Image, IMG_FILTER_COLORIZE, $R, $G, $B);
        } else {
            return imagefilter($this->Image, $filtertype, $params);
        }
    }

    public function render($FileName,$type="png") {
        if ($this->TransparentBackground) {
            imagealphablending($this->Image, false);
            imagesavealpha($this->Image, true);
        }
        imagepng($this->Image, $FileName);
        switch ($type){
            case 'jpeg':
                header('Content-type: image/jpeg');
                imagejpeg($this->Image,$FileName);
                break;
            default:
                header('Content-type: image/png');
                imagepng($this->Image,$FileName);
        }
    }

    public function stroke($BrowserExpire = false,$type="png") {
        if ($this->TransparentBackground) {
            imagealphablending($this->Image, false);
            imagesavealpha($this->Image, true);
        }

        if ($BrowserExpire) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }
        switch ($type){
            case 'jpeg'://Lost too much
                header('Content-Type: image/jpeg');
                imagejpeg($this->Image);
                break;
            default:
                header('Content-Type: image/png');
                imagepng($this->Image);
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
    public function allocateColor($Format, $G='', $B='', $Alpha = 100) {
        if(is_array($Format)){
            extract($Format,EXTR_OVERWRITE);
        }else{
            $R = $Format;
        }
        
        if ($R < 0) {$Format = 0;} if ($R > 255) {$R = 255;}
        if ($G < 0) {$G = 0;} if ($G > 255) {$G = 255;}
        if ($B < 0) {$B = 0;} if ($B > 255) {$B = 255;}
        if ($Alpha < 0) {$Alpha = 0;}
        if ($Alpha > 100) {$Alpha = 100;}

        $Alpha = (127 / 100) * (100 - $Alpha);
        return ($this->UseAlpha ? imagecolorallocatealpha($this->Image, $R, $G, $B, $Alpha)
            :  imagecolorallocate($this->Image, $R, $G, $B));
    }

    public function __destruct() {
        if (!empty($this->Image)){
            imagedestroy($this->Image);
        }
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
