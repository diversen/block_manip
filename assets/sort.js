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

                    $('.manip_success').show().delay(1000).fadeOut();

                    
                }
            });
        } 
    }).disableSelection();
});