<input 
	class="uk-button uk-button-primary"
	type="submit"
	name="save"
	value="{{ $form->getSubmitButtonText() }}"
	>

@foreach($form->getClosureButtons() as $button)
{!! $button->render() !!}
@endforeach

@if($cancel = $form->getCancelButton())
	{!! $cancel->render() !!}
@endif