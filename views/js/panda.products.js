$(document).ready(function () {
    $('#category').chosen();
});

let exportProduct = function () {
    $.ajax({
        type: 'POST',
        // url: 'ajax.php',
        async: true,
        dataType: 'json',
        data: $('#filter').serialize()+'&ajax=true&exportList=1',
        success: function(jsonData) {
            if (jsonData.status === 200) {
                window.location = baseLink + jsonData.download;
            } else {
                alert('Export error');
            }
        },
        error: function() {
            alert('Connection error');
        }
    });
};