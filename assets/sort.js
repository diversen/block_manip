$(function() 
{
    $("{block_manip_js_ids}").sortable(
    {
        connectWith: '.connectedSortable',
        update : function () 
        { 
            $.ajax(
            {
                type: "POST",
                url: "/block_manip/sort/sort",
                data: 
                {
                    {block_manip_js_data}
                },
                success: function(html)
                {
                    
                    //alert(html);
                    $('.manip_success').show();
                   $('.manip_success').fadeIn(500);
                    $('.manip_success').fadeOut(500);
                }
            });
        } 
    }).disableSelection();
});