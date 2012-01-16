<?php

function block_block_manip_info () {
    include_module('block_manip');
    $text = get_module_ini('blocks_manip_info');

    
    $filters = get_module_ini('block_manip_filters');
    $filtered_content = get_filtered_content($filters, $text); 
    
    $str = "<div class=\"block\"><ul><li><hr />$filtered_content</li></ul></div>\n";
    return $str;
    
}