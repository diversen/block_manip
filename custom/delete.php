<?php

if (!session::checkAccessControl('block_manip_allow')){
    return; 
}

moduleloader::includeModule ('block_manip');
$block = new block_manip();

if (isset($_POST['submit'])) {
    if (empty(block_manip::$errors)) {
        $id = $block->getId();
        $res = $block->delete($id);
        if ($res) {
            http::locationHeader('/block_manip/custom/index', 
                    lang::translate('block_manip_confirm_deleted'));
        } else {
            cos_error_log('Should not happen');
        }        
    } else {
        view_form_errors(block_manip::$errors);
    }
}

$block->form('delete');
