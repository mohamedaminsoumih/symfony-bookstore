// avoid ctrl click on multi select
$(document).on("mousedown", ".multi-select-option:not(disabled)", function (e) {
    e.preventDefault();
    $(this).prop('selected', !$(this).prop('selected'));
    return false;
});