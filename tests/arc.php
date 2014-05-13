<?php
require __DIR__.'/../src/xDraw.class.php';
require __DIR__.'/../src/xUtil.class.php';
//exit(cos(M_PI/2));
$xdraw = new \SimpleChart\xDraw(400,300);

//$xdraw->userAntialias(false);

$xdraw->setShadow(array('X'=>10,'Y'=>10,array('Color'=>\SimpleChart\xUtil::hex2RGB('red',100))));
$xdraw->drawFilledArc(150, 150, 100, 50,-45,-90,array(
    'Border'=>true,'Color'=> \SimpleChart\xUtil::hex2RGB('gray',100),'BorderColor'=> \SimpleChart\xUtil::hex2RGB('green',100)));
//$xdraw->drawLine(0,0, 120,120);
//$xdraw->drawLine(0,0, 10,200);
$xdraw->stroke();
