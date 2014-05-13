<?php
require __DIR__.'/../src/xDraw.class.php';
require __DIR__.'/../src/xUtil.class.php';
$xdraw = new \SimpleChart\xDraw(400,300);


$xdraw->setShadow(array('X'=>10,'Y'=>10,array('Color'=>array('R'=>210,'G'=>210,'B'=>210,'Alpha'=>60))));
$xdraw->drawFilledRectangle(10, 10, 100,100,array('Color'=> \SimpleChart\xUtil::hex2RGB('gray',60),'BorderColor'=>  \SimpleChart\xUtil::hex2RGB('green')));
$xdraw->stroke();
