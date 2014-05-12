<?php
require __DIR__.'/../src/xDraw.class.php';
//exit(cos(M_PI/2));
$xdraw = new \SimpleChart\xDraw(400,300);

//$xdraw->userAntialias(false);

$xdraw->setShadow(true,array('X'=>10,'Y'=>10,'R'=>210,'G'=>210,'B'=>210,'Alpha'=>60));
$xdraw->drawArc(150, 150, 100, 50,-45,-90,array('Border'=>true));
//$xdraw->drawLine(0,0, 120,120);
//$xdraw->drawLine(0,0, 10,200);
$xdraw->stroke();
