<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

include_module ('block_manip');
$block_manip_js = config::getModulePath('block_manip') . "/assets/sort.js";
template::setInlineCss(config::getModulePath('block_manip') . "/assets/sort.css");;

$search = array ();
$search[] = '{block_manip_js_ids}';
$search[] = '{block_manip_js_data}';

$replace = array ();
$replace[] = blockManip::getJsIds();
$replace[] = blockManip::getJsData();

            //$replace = $code;
            template::setInlineJs(
                $block_manip_js, 
                // load last or close to. 
                10000, 
                array ('no_cache'   => 1, 
                       'search'     => $search, 
                       'replace'    => $replace)
            );


blockManip::getBlocksFull();
return;
