@if($form->hasSubmit())
<input class="uk-button uk-button-primary" type="submit" name="save" value="{{ __('forms.save') }}">
@endif

@foreach($form->getExtraSubmitButtons() as $extraSubmit)
	{!! $extraSubmit->renderSubmit() !!}
@endforeach

@if($form->hasCancel())
<a class="uk-button uk-button-secondary" href="{{ $form->getCancelUrl() }}">Cancella</a>
@endif