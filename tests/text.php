<?php
require __DIR__.'/../src/xDraw.class.php';
require __DIR__.'/../src/xUtil.class.php';
$xdraw = new \SimpleChart\xDraw(400,300);

$xdraw->setShadow(array('X'=>10,'Y'=>10,'Color'=>array('R'=>180,'G'=>180,'B'=>180,'Alpha'=>60)));
$xdraw->setFormat('FontName', '/usr/share/fonts/wenquanyi/wqy-zenhei/wqy-zenhei.ttc');
$xdraw->setFormat('FontSize', 18);

$xdraw->drawFromPNG(0, 0, __DIR__.'/../chart/l-success.png');
$xdraw->drawText(250, 250, '过');



$xdraw->stroke();
