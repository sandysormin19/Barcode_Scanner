function showAlert(message)
{
    alert(message);
}


function resetInput(selector)
{
    $(selector).val('').focus();
}


function isEmpty(value)
{
    return value === null || value === '';
}


function formatNumber(number)
{
    return new Intl.NumberFormat('id-ID')
        .format(number);
}
