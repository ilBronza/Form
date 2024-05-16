@include('form::uikit._form')


<script type="text/javascript">
jQuery(document).ready(function($)
{
    $("#{{ $form->getId() }} :input").on('change input', function()
    {
        $(this).removeClass('uk-form-danger');
        $(this).closest('.uk-form-controls').find('.uk-text-danger').remove();

        $('#{{ $form->getId() }} .ibform-closure-buttons').fadeIn();
    });

    $("#{{ $form->getId() }}").submit(function(e)
    {
        e.preventDefault();

        var form = $(this);


        $(form).css('position', 'relative');

        $(form).append('<div class="spinner uk-overlay-primary uk-position-cover"><div class="uk-position-center"><span uk-spinner="ratio: 2"></span></div></div>');

        $.ajax(
        {
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),

            success: function(response)
            {
                if(response.success)
                {
                    $(form).find('.spinner').fadeOut(900).remove();

                    $('#{{ $form->getId() }} .ibform-closure-buttons').fadeOut();

                    window.addSuccessNotification("Elemento aggiornato con successo");
                }
            },
            error: function (reject) {

                $(form).find('.spinner').fadeOut(900).remove();

                window.addDangerNotification(reject.responseJSON.message);

                if( reject.status === 422 )
                {
                    let errorString = '';

                    $.each(reject.responseJSON.errors, function (key, val)
                    {
                        $('*[name=' + key + ']').addClass('uk-form-danger');

                        $('*[name=' + key + ']').closest('.uk-form-controls').append('<div class="uk-text-danger">' + val + '</div>');

                        errorString += key + ' ' + val + "<br />";
                    });

                    window.addDangerNotification(errorString);

                    // window.addDangerNotification(errorString);
                }
            }
        });
        
    });
});
</script>

