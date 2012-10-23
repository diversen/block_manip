<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

include_module ('block_manip');

$block = new block_manip();

if (isset($_POST['submit'])) {
    $block->sanitize();
    if (empty(block_manip::$errors)) {
        $res = $block->insert();
        if ($res) {
            http::locationHeader('/block_manip/custom/index', 
                    lang::translate('block_manip_confirm_insert'));
        } else {
            cos_error_log('Should not happen');
        }        
    } else {
        view_form_errors(block_manip::$errors);
    }
}

$block->form('add');
