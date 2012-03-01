<?php


moduleLoader::includeModule('configdb');
include_module ('block_manip');

$blocks = block_manip::getManipBlocks();

$data = array ();
db::$dbh->beginTransaction();

try {
    foreach ($blocks as $key) {
        $data = array();
        
        if (!isset($_POST[$key])) {
            $data = array ();
        } else {
        
            foreach ($_POST[$key] as $in_val) {
                $data[] = str_replace('-', '/', $in_val);
            }
        }
        //cos_error_log($key);
        //cos_error_log(print_r($data, 1));
        configdb::set($key, $data, 'main');
    }
} catch (PDOException $e) {            
    db::$dbh->rollBack();
    cos_error_log($e->getTraceAsString());
    //return false;
}
db::$dbh->commit();
die;
