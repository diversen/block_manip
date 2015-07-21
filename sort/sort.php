<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

moduleloader::includeModule('configdb');
moduleloader::includeModule ('block_manip');

$blocks = block_manip::getManipBlocks();
$data = array ();

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
        configdb::set($key, $data, 'main');
    }
} catch (PDOException $e) {            
    q::rollBack();
    log::error($e->getTraceAsString());
    //return false;
}
q::commit();
die;
