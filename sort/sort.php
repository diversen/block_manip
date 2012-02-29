<?php


moduleLoader::includeModule('configdb');


//$_POST = html::specialEncode($_POST);

$data = array ();
db::$dbh->beginTransaction();
foreach ($_POST as $key => $val) {
    $data = array();
    foreach ($_POST[$key] as $in_val) {
        $data[] = str_replace('-', '/', $in_val);
    }
    configdb::set($key, $data, 'main');
}
print "suvces";
db::$dbh->commit();
    die;
