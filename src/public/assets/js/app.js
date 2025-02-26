$('form').submit(function(){
    $(this).find(':input[type=submit]').prop('disabled', true);
});

$('.matches__match__more[aria-controls]').click(function(){
    switch ($(this).text()) {
        case 'more': $(this).text('less'); break;
        case 'less': $(this).text('more'); break;
    }
});