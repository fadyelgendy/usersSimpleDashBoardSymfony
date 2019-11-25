$(document).ready(function () {
    $('.delete-todo').on('click', function (e) {
        e.preventDefault();

        var $link = $(e.currentTarget);

        $.ajax({
            method: 'DELETE',
            url: $link.attr('href')
        }).done(function(data){
            console.log(data);
        });
    })
});