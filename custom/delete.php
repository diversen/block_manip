<?php

if (!session::checkAccessControl('block_manip_allow')){
    return; 
}

include_module ('block_manip');
$block = new blockManip();

if (isset($_POST['submit'])) {
    if (empty(blockManip::$errors)) {
        $id = $block->getId();
        $res = $block->delete($id);
        if ($res) {
            http::locationHeader('/block_manip/custom/index', 
                    lang::translate('block_manip_confirm_deleted'));
        } else {
            cos_error_log('Should not happen');
        }        
    } else {
        view_form_errors(blockManip::$errors);
    }
}

$block->form('delete');
