<?php
/**
 * Description of xUtil
 *
 * 绘图工具类,xDraw并不对此依赖,仅作为工具使用
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author xilei
 */
class xUtil {
    
    /**
     * name color map
     * @var type 
     */
    static  $NameColors =array('aliceblue'=>array('R'=>240,'G'=>248,'B'=>255),'antiquewhite'=>array('R'=>250,'G'=>235,'B'=>215),'aqua'=>array('R'=>0,'G'=>255,'B'=>255),'aquamarine'=>array('R'=>127,'G'=>255,'B'=>212),'azure'=>array('R'=>240,'G'=>255,'B'=>255),'beige'=>array('R'=>245,'G'=>245,'B'=>220),'bisque'=>array('R'=>255,'G'=>228,'B'=>196),'black'=>array('R'=>0,'G'=>0,'B'=>0),'blanchedalmond'=>array('R'=>255,'G'=>235,'B'=>205),'blue'=>array('R'=>0,'G'=>0,'B'=>255),'blueviolet'=>array('R'=>138,'G'=>43,'B'=>226),'brown'=>array('R'=>165,'G'=>42,'B'=>42),'burlywood'=>array('R'=>222,'G'=>184,'B'=>135),'cadetblue'=>array('R'=>95,'G'=>158,'B'=>160),'chartreuse'=>array('R'=>127,'G'=>255,'B'=>0),'chocolate'=>array('R'=>210,'G'=>105,'B'=>30),'coral'=>array('R'=>255,'G'=>127,'B'=>80),'cornflowerblue'=>array('R'=>100,'G'=>149,'B'=>237),'cornsilk'=>array('R'=>255,'G'=>248,'B'=>220),'crimson'=>array('R'=>220,'G'=>20,'B'=>60),'cyan'=>array('R'=>0,'G'=>255,'B'=>255),'darkblue'=>array('R'=>0,'G'=>0,'B'=>139),'darkcyan'=>array('R'=>0,'G'=>139,'B'=>139),'darkgoldenrod'=>array('R'=>184,'G'=>134,'B'=>11),'darkgray'=>array('R'=>169,'G'=>169,'B'=>169),'darkgrey'=>array('R'=>169,'G'=>169,'B'=>169),'darkgreen'=>array('R'=>0,'G'=>100,'B'=>0),'darkkhaki'=>array('R'=>189,'G'=>183,'B'=>107),'darkmagenta'=>array('R'=>139,'G'=>0,'B'=>139),'darkolivegreen'=>array('R'=>85,'G'=>107,'B'=>47),'darkorange'=>array('R'=>255,'G'=>140,'B'=>0),'darkorchid'=>array('R'=>153,'G'=>50,'B'=>204),'darkred'=>array('R'=>139,'G'=>0,'B'=>0),'darksalmon'=>array('R'=>233,'G'=>150,'B'=>122),'darkseagreen'=>array('R'=>143,'G'=>188,'B'=>143),'darkslateblue'=>array('R'=>72,'G'=>61,'B'=>139),'darkslategray'=>array('R'=>47,'G'=>79,'B'=>79),'darkslategrey'=>array('R'=>47,'G'=>79,'B'=>79),'darkturquoise'=>array('R'=>0,'G'=>206,'B'=>209),'darkviolet'=>array('R'=>148,'G'=>0,'B'=>211),'deeppink'=>array('R'=>255,'G'=>20,'B'=>147),'deepskyblue'=>array('R'=>0,'G'=>191,'B'=>255),'dimgray'=>array('R'=>105,'G'=>105,'B'=>105),'dimgrey'=>array('R'=>105,'G'=>105,'B'=>105),'dodgerblue'=>array('R'=>30,'G'=>144,'B'=>255),'firebrick'=>array('R'=>178,'G'=>34,'B'=>34),'floralwhite'=>array('R'=>255,'G'=>250,'B'=>240),'forestgreen'=>array('R'=>34,'G'=>139,'B'=>34),'fuchsia'=>array('R'=>255,'G'=>0,'B'=>255),
        'gainsboro'=>array('R'=>220,'G'=>220,'B'=>220),'ghostwhite'=>array('R'=>248,'G'=>248,'B'=>255),'gold'=>array('R'=>255,'G'=>215,'B'=>0),'goldenrod'=>array('R'=>218,'G'=>165,'B'=>32),'gray'=>array('R'=>128,'G'=>128,'B'=>128),'grey'=>array('R'=>128,'G'=>128,'B'=>128),'green'=>array('R'=>0,'G'=>128,'B'=>0),'greenyellow'=>array('R'=>173,'G'=>255,'B'=>47),'honeydew'=>array('R'=>240,'G'=>255,'B'=>240),'hotpink'=>array('R'=>255,'G'=>105,'B'=>180),'indianred'=>array('R'=>205,'G'=>92,'B'=>92),'indigo'=>array('R'=>75,'G'=>0,'B'=>130),'ivory'=>array('R'=>255,'G'=>255,'B'=>240),'khaki'=>array('R'=>240,'G'=>230,'B'=>140),'lavender'=>array('R'=>230,'G'=>230,'B'=>250),'lavenderblush'=>array('R'=>255,'G'=>240,'B'=>245),'lawngreen'=>array('R'=>124,'G'=>252,'B'=>0),'lemonchiffon'=>array('R'=>255,'G'=>250,'B'=>205),'lightblue'=>array('R'=>173,'G'=>216,'B'=>230),'lightcoral'=>array('R'=>240,'G'=>128,'B'=>128),'lightcyan'=>array('R'=>224,'G'=>255,'B'=>255),'lightgoldenrodyellow'=>array('R'=>250,'G'=>250,'B'=>210),'lightgray'=>array('R'=>211,'G'=>211,'B'=>211),'lightgrey'=>array('R'=>211,'G'=>211,'B'=>211),'lightgreen'=>array('R'=>144,'G'=>238,'B'=>144),'lightpink'=>array('R'=>255,'G'=>182,'B'=>193),'lightsalmon'=>array('R'=>255,'G'=>160,'B'=>122),'lightseagreen'=>array('R'=>32,'G'=>178,'B'=>170),'lightskyblue'=>array('R'=>135,'G'=>206,'B'=>250),'lightslategray'=>array('R'=>119,'G'=>136,'B'=>153),'lightslategrey'=>array('R'=>119,'G'=>136,'B'=>153),'lightsteelblue'=>array('R'=>176,'G'=>196,'B'=>222),'lightyellow'=>array('R'=>255,'G'=>255,'B'=>224),'lime'=>array('R'=>0,'G'=>255,'B'=>0),'limegreen'=>array('R'=>50,'G'=>205,'B'=>50),'linen'=>array('R'=>250,'G'=>240,'B'=>230),'magenta'=>array('R'=>255,'G'=>0,'B'=>255),'maroon'=>array('R'=>128,'G'=>0,'B'=>0),'mediumaquamarine'=>array('R'=>102,'G'=>205,'B'=>170),'mediumblue'=>array('R'=>0,'G'=>0,'B'=>205),'mediumorchid'=>array('R'=>186,'G'=>85,'B'=>211),'mediumpurple'=>array('R'=>147,'G'=>112,'B'=>216),'mediumseagreen'=>array('R'=>60,'G'=>179,'B'=>113),'mediumslateblue'=>array('R'=>123,'G'=>104,'B'=>238),'mediumspringgreen'=>array('R'=>0,'G'=>250,'B'=>154),'mediumturquoise'=>array('R'=>72,'G'=>209,'B'=>204),'mediumvioletred'=>array('R'=>199,'G'=>21,'B'=>133),'midnightblue'=>array('R'=>25,'G'=>25,'B'=>112),
        'mintcream'=>array('R'=>245,'G'=>255,'B'=>250),'mistyrose'=>array('R'=>255,'G'=>228,'B'=>225),'moccasin'=>array('R'=>255,'G'=>228,'B'=>181),'navajowhite'=>array('R'=>255,'G'=>222,'B'=>173),'navy'=>array('R'=>0,'G'=>0,'B'=>128),'oldlace'=>array('R'=>253,'G'=>245,'B'=>230),'olive'=>array('R'=>128,'G'=>128,'B'=>0),'olivedrab'=>array('R'=>107,'G'=>142,'B'=>35),'orange'=>array('R'=>255,'G'=>165,'B'=>0),'orangered'=>array('R'=>255,'G'=>69,'B'=>0),'orchid'=>array('R'=>218,'G'=>112,'B'=>214),'palegoldenrod'=>array('R'=>238,'G'=>232,'B'=>170),'palegreen'=>array('R'=>152,'G'=>251,'B'=>152),'paleturquoise'=>array('R'=>175,'G'=>238,'B'=>238),'palevioletred'=>array('R'=>216,'G'=>112,'B'=>147),'papayawhip'=>array('R'=>255,'G'=>239,'B'=>213),'peachpuff'=>array('R'=>255,'G'=>218,'B'=>185),'peru'=>array('R'=>205,'G'=>133,'B'=>63),'pink'=>array('R'=>255,'G'=>192,'B'=>203),'plum'=>array('R'=>221,'G'=>160,'B'=>221),'powderblue'=>array('R'=>176,'G'=>224,'B'=>230),'purple'=>array('R'=>128,'G'=>0,'B'=>128),'red'=>array('R'=>255,'G'=>0,'B'=>0),'rosybrown'=>array('R'=>188,'G'=>143,'B'=>143),'royalblue'=>array('R'=>65,'G'=>105,'B'=>225),'saddlebrown'=>array('R'=>139,'G'=>69,'B'=>19),'salmon'=>array('R'=>250,'G'=>128,'B'=>114),'sandybrown'=>array('R'=>244,'G'=>164,'B'=>96),'seagreen'=>array('R'=>46,'G'=>139,'B'=>87),'seashell'=>array('R'=>255,'G'=>245,'B'=>238),'sienna'=>array('R'=>160,'G'=>82,'B'=>45),'silver'=>array('R'=>192,'G'=>192,'B'=>192),'skyblue'=>array('R'=>135,'G'=>206,'B'=>235),'slateblue'=>array('R'=>106,'G'=>90,'B'=>205),'slategray'=>array('R'=>112,'G'=>128,'B'=>144),'slategrey'=>array('R'=>112,'G'=>128,'B'=>144),'snow'=>array('R'=>255,'G'=>250,'B'=>250),'springgreen'=>array('R'=>0,'G'=>255,'B'=>127),'steelblue'=>array('R'=>70,'G'=>130,'B'=>180),'tan'=>array('R'=>210,'G'=>180,'B'=>140),'teal'=>array('R'=>0,'G'=>128,'B'=>128),'thistle'=>array('R'=>216,'G'=>191,'B'=>216),'tomato'=>array('R'=>255,'G'=>99,'B'=>71),'turquoise'=>array('R'=>64,'G'=>224,'B'=>208),'violet'=>array('R'=>238,'G'=>130,'B'=>238),'wheat'=>array('R'=>245,'G'=>222,'B'=>179),'white'=>array('R'=>255,'G'=>255,'B'=>255),'whitesmoke'=>array('R'=>245,'G'=>245,'B'=>245),'yellow'=>array('R'=>255,'G'=>255,'B'=>0),'yellowgreen'=>array('R'=>154,'G'=>205,'B'=>50));
    
    /**
     * 转为RBG格式
     * @param type $hex
     * @return array
     */
    static public function hex2RGB($hex){
        if(empty($hex)){
            return array('R'=>0,'G'=>0,'B'=>0);
        }
        if(isset(static::$NameColors[$hex])){
            return static::$NameColors[$hex];
        }
        $hex = str_replace('#','',$hex);
        $len = strlen($hex);
        $hexarr = array();
        if($len == 3){
            $hexarr = str_split($hex,1);
            foreach ($hexarr as $k=>$v){
                $hexarr[$k] = $v.$v;
            }
        }else{
            if($len <6){
                $hex = str_pad($hex,6,'0',STR_PAD_RIGHT);
            }
            $hexarr = str_split($hex,2);
        }
        return array('R'=>hexdec($hexarr[0]),
        'G'=>hexdec($hexarr[1]),'B'=>hexdec($hexarr[2]));
    }
}
