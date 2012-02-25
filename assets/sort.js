$(function() 
{
    $("#sortable1, #sortable2").sortable(
    {
        connectWith: '.connectedSortable',
        update : function () 
        { 
            $.ajax(
            {
                type: "POST",
                url: "sort.php",
                data: 
                {
                    sort1:$("#sortable1").sortable('serialize'),
                    sort2:$("#sortable2").sortable('serialize')
                },
                success: function(html)
                {
                    
                    alert(html);
                    $('.success').fadeIn(500);
                    $('.success').fadeOut(500);
                }
            });
        } 
    }).disableSelection();
});