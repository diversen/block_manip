<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

moduleloader::includeModule ('block_manip');
block_manip::displayAll();

