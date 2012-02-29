<?php

class block_manip {
    /**
     *
     * @return array $blocks all blocks set in main config 
     */
    public static function getBlocks () {
        $blocks = config::getMainIni('blocks_all');
        $blocks = explode(',' , $blocks);
        return $blocks;
    }
    
    /**
     *
     * @return array $blocks all valid blocks that can be moved 
     */
    public static function getManipBlocks () {
        $blocks = config::getModuleIni('block_manip_blocks');
        $blocks = explode(',' , $blocks);
        return $blocks;
    }

    /**
     * echo full html blocks  
     */
    public static function getBlocksFull () {
        $blocks = self::getBlocks();
        $valid_blocks = self::getManipBlocks();
        
        $str = "<div class = \"manip_success\">Done</div>\n";

        $str.= self::getListStart();
        foreach ($blocks as $val) {
            if (in_array($val, $valid_blocks)) {
                $values = config::getMainIni($val);
                $str.= self::getOlBlock($values, $val);
            }
        }
        
        $values = config::getMainIni('blocks_unused');
        if (!$values) $values = array ();
        $str.= self::getOlBlock($values, 'blocks_unused');
        $str.= self::getListEnd();
        echo $str;
    }
    
    /**
     * 
     * @return string $str returns id to be used with javacript 
     */
    public static function getJsIds () {
        $blocks = self::getManipBlocks();
        $str = '';
        foreach ($blocks as $val) {
            $str.="#$val, ";
        }
        $str = rtrim($str, ', ');
        return $str;
    }
    
    /**
     * 
     * @return string 
     */
    public static function getJsData () {
        $blocks = self::getManipBlocks();
        $str = '';
        foreach ($blocks as $val) {
            $str.="$val:$(\"#$val\").sortable('toArray'), ";
        }
        $str = rtrim($str, ', ');
        return $str;
    }


    /**
     * @param array $values the values of the block 
     * @param string $name the name of the block
     * @return string $str the ol of the block 
     */
    public static function getOlBlock ($values, $name) {
        static $num = 1;
        static $count = 0;
        
        $str = "<h3>" . lang::system($name) . "</h3>\n";
        $str.= "<ol id=\"$name\" class =\"connectedSortable\">\n";
        $num++;
        foreach ($values as $val) {
            $val_str = str_replace('/', '-', $val);
            $name = lang::system($val . "-human");
            $str.= "<li id=\"$val_str\">$name</li>";
            $count++;
        }
        $str.="</ol>\n";
        return $str;
    }

    /**
     * 
     * @return string $str the div start 
     */
    public static function getListStart () {
        $str = '';
        $str.= "<div id=\"sortable\">\n";
        return $str;
    }

    /**
     * 
     * @return string $str the div end 
     */
    public static function getListEnd () {
        $str = "</div>\n";
        return $str;
    }
}
