var item_type = 'logs';

$(function()
{

    $('img[src="images/delete-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one log to delete');
            return;
        }

        if( confirm('Are you sure you want to delete the selected logs?') )
        {

            //console.log(selected.join(','));

            $.ajax({
                    async: true,
                    toolbarIcon: this,
                    data: 'r=_xLogsDeleteBulk&logs=' + selected.join(','),
                    success: function()
                             {
                                 $.each(selected, function(index, item)
                                                  {
                                                      $('table.item-table tbody tr[id="item-'+item+'"]')
                                                      .remove();

                                                      $('#num-items')
                                                      .decrementText();
                                                  });
                             }});
        }
    });
});