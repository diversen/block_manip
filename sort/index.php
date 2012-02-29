<?php

include_module ('block_manip');
$block_manip_js = config::getModulePath('block_manip') . "/assets/sort.js";
template::setInlineCss(config::getModulePath('block_manip') . "/assets/sort.css");;

$search = array ();
$search[] = '{block_manip_js_ids}';
$search[] = '{block_manip_js_data}';

$replace = array ();
$replace[] = block_manip::getJsIds();
$replace[] = block_manip::getJsData();

            //$replace = $code;
            template::setInlineJs(
                $block_manip_js, 
                // load last or close to. 
                10000, 
                array ('no_cache'   => 1, 
                       'search'     => $search, 
                       'replace'    => $replace)
            );


block_manip::getBlocksFull();
return;
