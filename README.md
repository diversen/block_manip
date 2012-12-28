### About

Module for manipulating blocks. 
Sort left and right blocks, and add custom blocks.

blocks you will be used with the sorting module must be secified in 

config/config.ini

Default configuration looks like this:

    blocks_top[0] = "/modules/system/blocks/system_admin_menu_top.inc"
    blocks[1] = "/modules/content/blocks/content_tree_keep_state.inc"
    blocks_all = "blocks,blocks_top"

The `blocks_all` specifies which blocks can be moved around. In your 
template you use this for getting a `block` of content and you can then
use to display what ever you like to be displayed. 
 
