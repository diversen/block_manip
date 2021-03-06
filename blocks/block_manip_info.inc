<?php

function block_blocks_info () {
    moduleloader::includeModule('blocks');
    $text = conf::getModuleIni('blocks_manip_info');

    $filters = conf::getModuleIni('blocks_filters');
    $filtered_content = moduleloader::getFilteredContent($filters, $text); 
    
    $str = "<div class=\"block\">\n<ul><li>$filtered_content</li></ul></div>\n";
    return $str;
}
