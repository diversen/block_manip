<?php

template::setInlineJs(config::getModulePath('block_manip') . "/assets/sort.js");
//echo "hello world";
template::setInlineCss(config::getModulePath('block_manip') . "/assets/sort.css");

?>
<div id="sortable">
<ol id="sortable1" class ="connectedSortable">
    <li id="entry_1">1 bla</li>
    <li id="entry_2">2 bla</li>
</ol>

<ol id="sortable2" class = "connectedSortable">
    <li id="entry_3">3 bla</li>
    <li id="entry_4">4 bla</li>
</ol>
</div>