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
    
    public static function form ($action = 'add', $vars = array()) {
        self::sanitize();
        
        if ($action == 'delete') {
            html::formStart('content_article_form');
            html::legend(lang::translate('block_manip_label_delete_block'));
            html::submit('submit', lang::system('system_submit_delete'));
            html::formEnd();
            echo html::getStr();
            return;
        }
   
        html::$autoLoadTrigger = 'submit';
        if ($action == 'update') {
            $id = self::getId();
            $vars = self::getOne($id);
            $legend = lang::translate('block_manip_label_edit_block');
        } else {
            $legend = lang::translate('block_manip_label_add_block');
        }
        
        
        html::init($vars);
        html::formStart('block_manip_add');
        html::legend($legend);
        html::label('title', lang::system('system_form_label_title'));
        html::text('title');

        $label = lang::system('system_form_label_content'). '<br />';
        $label.= moduleLoader::getFiltersHelp(config::getModuleIni('block_manip_filters'));

        html::label('content', $label);
        html::textarea('content', null, array('id' => 'markdown'));

        html::label('show_title', lang::translate('block_manip_form_show_title') );
        html::checkbox('show_title');

        html::submit('submit', lang::system('system_submit'));
        html::formEnd();
        
        echo html::getStr();
    }
    
    public static $errors = null;
    
    public static function sanitize () {
        if (isset($_POST['submit'])) {
            $_POST = html::specialEncode($_POST);
        } 
            
        if (empty($_POST['title'])) {
            self::$errors['title'] = lang::translate('block_manip_form_error_title');
        } 
        
        if (empty($_POST['content'])) {
            self::$errors['content'] = lang::translate('block_manip_form_error_content');
        }
        
        if (!isset($_POST['show_title'])) {
            $_POST['show_title'] = 0;
        }
        
    }
    
    public function insert () {
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);
        return $db->insert('block_manip', $values);
    }
    
    public function delete ($id) {
        $db = new db();
        return $db->delete('block_manip', 'id', $id);
    }
    
    public function update () {
        
        $id = self::getId();
        
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);
        return $db->update('block_manip', $values, $id);
    }
    
    public static function getAll () {
        $db = new db();
        $rows = $db->selectAll('block_manip');
        return $rows = html::specialEncode($rows);
    }
    
    public static function getOne ($id) {
        $db = new db();
        return $row = $db->selectOne('block_manip', 'id', $id);
    }
    
    public static function displayAll () {
        $all = self::getAll();
        echo templateView::get('block_manip', 'custom_all', $all);
    }
    
    public static function getId () {
        return $id = uri::getInstance()->fragment(3);
    }
}
