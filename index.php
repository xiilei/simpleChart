<?php
require __DIR__.'/src/xDraw.class.php';

$xdraw = new \SimpleChart\xDraw(400,300);
//$xdraw->Antialias = FALSE;

$X=1.6;
$Y=1.2;

$Xi  = floor($X);
$Yi  = floor($Y);


$R=76;$G=152;$B=220;$Alpha=100;

if( $Xi == $X && $Yi == $Y){
    $xdraw->drawFilledCircle($X*100,$Y*100,50,50,array('Alpha'=>$Alpha1,'R'=>$R,'G'=>$G,'B'=>$B));
}else{
    $Alpha1 = (1 - ($X - $Xi)) * (1 - ($Y - $Yi)) * $Alpha;
    $xdraw->drawFilledCircle($Xi*100,$Yi*100,50,50,array('Alpha'=>$Alpha1,'R'=>$R,'G'=>$G,'B'=>$B)); 
    
    $Alpha2 = ($X - $Xi) * (1 - ($Y - $Yi)) * $Alpha;
    $xdraw->drawFilledCircle(($Xi+1)*100,$Yi*100,50,50,array('Alpha'=>$Alpha2,'R'=>$R,'G'=>$G,'B'=>$B));
    
    $Alpha3 = (1 - ($X - $Xi)) * ($Y - $Yi)  * $Alpha;
    $xdraw->drawFilledCircle($Xi*100,($Yi+1)*100,50,50,array('Alpha'=>$Alpha3,'R'=>$R,'G'=>$G,'B'=>$B)); 
    
    $Alpha4 = ($X - $Xi) * ($Y - $Yi) * $Alpha;
    $xdraw->drawFilledCircle(($Xi+1)*100,($Yi+1)*100,50,50,array('Alpha'=>$Alpha4,'R'=>$R,'G'=>$G,'B'=>$B));
}
$xdraw->drawFilledCircle($X*100, $Y*100, 50,50,array('R'=>76,'G'=>152,'B'=>220,'Alpha'=>$Alpha));
$xdraw->stroke();
