<?php

function block_block_manip_info () {
    moduleloader::includeModule('block_manip');
    $text = config::getModuleIni('blocks_manip_info');

    $filters = config::getModuleIni('block_manip_filters');
    $filtered_content = moduleloader::getFilteredContent($filters, $text); 
    
    $str = "<div class=\"block\">\n<ul><li>$filtered_content</li></ul></div>\n";
    return $str;
}
