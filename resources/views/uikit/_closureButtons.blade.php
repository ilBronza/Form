<input 
	class="uk-button uk-button-primary"
	type="submit"
	name="save"
	value="{{ $form->getSubmitButtonText() }}"
	>

@foreach($form->getClosureButtons() as $button)
	@if($button->isSubmit())

	<input 
		class="uk-button uk-button-primary"
		type="submit"
		name="{{ $button->getName() }}"
		value="{{ $button->getText() }}"
		>
	@else
		{!! $button->render() !!}
	@endif
@endforeach

@if($cancel = $form->getCancelButton())
	{!! $cancel->render() !!}
@endif