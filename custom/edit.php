<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

include_module ('block_manip');
$block = new blockManip();

if (isset($_POST['submit'])) {
    $block->sanitize();
    if (empty(blockManip::$errors)) {
        $res = $block->update();
        if ($res) {
            http::locationHeader('/block_manip/custom/index', 
                    lang::translate('block_manip_confirm_insert'));
        } else {
            cos_error_log('Should not happen');
        }        
    } else {
        view_form_errors(blockManip::$errors);
    }
}

$id = blockManip::getId();
echo $block->includeSubModules($id);
$block->form('update');
