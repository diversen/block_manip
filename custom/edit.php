<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

moduleloader::includeModule ('block_manip');
$block = new block_manip();

if (isset($_POST['submit'])) {
    $block->sanitize();
    if (empty(block_manip::$errors)) {
        $res = $block->update();
        if ($res) {
            http::locationHeader('/block_manip/custom/index', 
                    lang::translate('block_manip_confirm_insert'));
        } else {
            log::error('Should not happen');
        }        
    } else {
        html::errors(block_manip::$errors);
    }
}

$id = block_manip::getId();
echo $block->includeSubModules($id);
$block->form('update');
