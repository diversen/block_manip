<?php

template::setJs('/js/jquery.tr.js');
class block_manip {
    
    public static function loadAssets ($options) {
        if (config::getModuleIni('block_manip_markedit')) {
            moduleloader::includeTemplateCommon('jquery-markedit');
            jquery_markedit_load_assets($options);
        }
    }
    
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
        
        

        $str = self::getListStart();
        $unused = array ();
        foreach ($blocks as $val) {
            if (in_array($val, $valid_blocks)) {
                $values = config::getMainIni($val);
                
                // compute the difference between database entry and file entry
                // so we can keep on adding entries to file, which user
                // can add. 
                
                $diff = array_diff(config::getMainIniFromFile($val), $values);
                $unused = array_merge($unused, $diff);
                $str.= self::getOlBlock($values, $val);
            }
        }
        
        //print_r($unused);
        
        $values = config::getMainIni('blocks_unused');
        if (!$values) $values = array ();
        
        $values = array_merge($values, $unused);
        $values = array_unique($values);
        if (!$values) $values = array ();
        $str.= self::getOlBlock($values, 'blocks_unused');
        $str.= self::getListEnd();
        
        $success = lang::translate('block_manip_sort_success');
        $str.= "<div class = \"manip_success\">$success</div>\n";
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
        
        $str = "<h3>" . lang::translate($name) . "</h3>\n";
        $str.= "<ol id=\"$name\" class =\"connectedSortable\">\n";
        $num++;

        if (empty($values)) $values = array ();
        foreach ($values as $val) {
            
            // check for custom blocks
            if (is_numeric($val)) {
                //print_r($val);
                $val_str = $val;
                
                $row = self::getOne($val);
                //print_r($row);
                $name = $row['title'];
            } else {
                $val_str = str_replace('/', '-', $val);
                $name = lang::system($val . "-human");
            }
            
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
            
            $options = array ();
            $options['js'] = array ('reference' => 'block_manip', 'parent_id' => $id );
            block_manip::loadAssets($options);
            
            $vars = self::getOne($id);
            $legend = lang::translate('block_manip_label_edit_block');
        } else {
            $options['js'] = array ('reference' => 'block_manip', 'parent_id' => null );
            block_manip::loadAssets($options);
            $legend = lang::translate('block_manip_label_add_block');
        }
        
        
        html::init($vars);
        html::formStart('block_manip_add');
        html::legend($legend);
        html::label('title', lang::system('system_form_label_title'));
        html::text('title');

        $label = lang::system('system_form_label_content'). '<br />';
        $label.= moduleloader::getFiltersHelp(config::getModuleIni('block_manip_filters'));

        html::label('content_block', $label);
        html::textarea('content_block', null, array('class' => 'markdown'));

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
        
        if (empty($_POST['content_block'])) {
            self::$errors['content_block'] = lang::translate('block_manip_form_error_content');
        }
        
        if (!isset($_POST['show_title'])) {
            $_POST['show_title'] = 0;
        }
        
    }
    
    /**
     * inserts a block into block_manip table,
     * add the block to blocks_unused
     * @return boolean $res true on success and false on failure
     */
    public function insert () {
        
        db::$dbh->beginTransaction();
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);
        
        // we add to blocks_unused
        $res = $db->insert('block_manip', $values);

        if (!$res) {
            // should not happen
            db::$dbh->rollBack();
            return;
        }
        
        $insert_id = db::$dbh->lastInsertId();
        $unused = config::getMainIni('blocks_unused');
        
        if (!is_array($unused)) $unused = array();      
        array_push($unused, $insert_id);
            
        configdb::set('blocks_unused', $unused, 'main');  
        return db::$dbh->commit();
    }
    
    /**
     * delete from block_manip, 
     * traverse all blocks and remove id if set anywhere 
     * @param int $id the block id to delete
     * @return boolean $res true on success and false on failure
     */
    public function delete ($id) {
        db::$dbh->beginTransaction();
        
        try {
            $db = new db();
            $res = $db->delete('block_manip', 'id', $id);

            if (!$res) {
                // should not happen
                db::$dbh->rollBack();
                return;
            }

            // traverse blocks and remove element if set 
            //$data = array();
            $blocks = block_manip::getManipBlocks();
            foreach ($blocks as $val) {
                $data = config::getMainIni($val);

                foreach ($data as $in_key => $in_val) {
                    if ($in_val == $id) {
                        unset($data[$in_key]);
                    }
                }         
                //print_r($data); die;
                configdb::set($val, $data, 'main');
            }
        } catch (PDOException $e) {            
            db::$dbh->rollBack();
            log::error($e->getTraceAsString());
            return false;
        }
        return db::$dbh->commit();
    }
    
    /**
     * updates a row in block_manip table
     * @return boolean $res true on success and false on failure 
     */
    public function update () {
        
        $id = self::getId();
        
        db::begin();
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);
        $db->update('block_manip', $values, $id);
        
        //$insert_id = db::$dbh->lastInsertId();
        $unused = config::getMainIni('blocks_unused');
        
        if (!is_array($unused)) $unused = array();      
        if (!in_array ($id, $unused)) { 
            array_push($unused, $id);
            configdb::set('blocks_unused', $unused, 'main');
        }
        return db::commit();
    }
    
    /**
     * get all rows in block manip table
     * @return array $rows rows in table 
     */
    public static function getAll () {
        $db = new db();
        $rows = $db->selectAll('block_manip');
        return $rows = html::specialEncode($rows);
    }
    
    /**
     * get one row from block_manip
     * @param int $id
     * @return array $row 
     */
    public static function getOne ($id) {
        $db = new db();
        return $row = $db->selectOne('block_manip', 'id', $id);
    }
    
    /**
     *display all rows in block_manip 
     */
    public static function displayAll () {
        $all = self::getAll();
        echo view::get('block_manip', 'custom_all', $all);
    }
    
    /**
     * gets id from url. Used on update and delete. 
     * @return mixed $id should return int
     */
    public static function getId () {
        return $id = uri::getInstance()->fragment(3);
    }
    
    /**
     * used with sub module
     * @param int $id
     * @return string $url 
     */
    public static function getReturnUrlFromId ($id) {
        return "/block_manip/custom/edit/$id";
    }
    
    /**
     * gets link from id
     * @param int $id
     * @return string $link 
     */
    public static function getLinkFromId ($id) {
        $item = self::getOne($id);
        
        $url = self::getReturnUrlFromId($id);
        $title = html::specialEncode($item['title']);
        return html::createLink($url, $title);
    }
    
   
    
    /**
     * gets redirect 
     * @param int $id
     * @return string $url return url 
     */
    public static function getRedirect ($id) {
        return self::getReturnUrlFromId($id);
    }
    
    /**
     * includes sub modules
     * @param int $id
     * @return string $html html used with sub modules.  
     */
    public static function includeSubModules ($id) {
        
        $modules = config::getModuleIni('block_manip_modules');
        //print_r($modules);
        moduleloader::includeModules($modules);

        $return_url = self::getReturnUrlFromId($id);

        $options = array (
            'parent_id' => $id,
            'reference' => 'block_manip',
            'return_url' => $return_url
        );

        if ($modules) {
            $ary = moduleloader::subModuleGetAdminOptions($modules, $options);
            return implode(MENU_SUB_SEPARATOR, $ary);
        }
        
        $content = moduleloader::subModuleGetPreContent($modules, $options);
        return implode(MENU_SUB_SEPARATOR, $content);
    }

}

/**
 * alias. Needs to be blockManip when using sub modules.  
 */
class blockManip extends block_manip {
        
}