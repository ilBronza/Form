<div class="uk-text-{{ $form->getClosureAlignmentString() }} uk-margin-top">
	
	<input 
		class="uk-button uk-button-primary"
		type="submit"
		name="save"
		value="{{ $form->getSubmitButtonText() }}"
		>

	@foreach($form->getClosureButtons() as $button)
		@if($button->isSubmit())

			{!! $button->renderSubmit() !!}

	{{--
		<input 
			class="uk-button uk-button-primary"
			type="submit"
			name="{{ $button->getName() }}"
			value="{{ $button->getText() }}"
			>
	 --}}
		@else
			{!! $button->render() !!}
		@endif
	@endforeach

	@if($cancel = $form->getCancelButton())
		{!! $cancel->render() !!}
	@endif

</div>