<?php
require __DIR__.'/../src/xDraw.class.php';
require __DIR__.'/../src/xUtil.class.php';


$xdraw = new \SimpleChart\xDraw(400, 300);
$rgba = \SimpleChart\xUtil::hex2RGB('green');
$rgba['Alpha'] = 100;
//$xdraw->userAntialias(false);
$xdraw->setShadow(array('X'=>10,'Y'=>10,'Color'=>array('R'=>180,'G'=>180,'B'=>180,'Alpha'=>80)));
$xdraw->drawFilledPolygon(array(10,10,50,100,100,10),array('BorderColor'=>$rgba,'Color'=>array('R'=>150,'G'=>150,'B'=>150,'Alpha'=>80)));//,array('Color'=>xUtil::hex2RGB('green'))
$xdraw->stroke();

