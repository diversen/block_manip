<?php

$db_config = array (
    array ('name' => 'blocks_manip_info', 
           'description' => lang::translate('blocks_manip_info_description'),
           'type' => 'textarea', 
           'value' => lang::translate('blocks_manip_info_value'),
           'auth' => 'admin',
           'filters' => array ('markdown')),
);