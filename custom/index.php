<?php

if (!session::checkAccessControl('block_manip_allow')){
    return;
}

include_module ('block_manip');
block_manip::displayAll();

