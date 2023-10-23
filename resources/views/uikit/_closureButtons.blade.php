<div class="uk-text-{{ $form->getClosureAlignmentString() }} uk-margin-top ibform-closure-buttons">
	<div id="recaptcha"></div>
	<br />
	
	<input 


{{--
		data-sitekey="6LfvOKooAAAAAKuyQm3XDSxobDp5_q3OzpExDZja" 
        data-callback='onSubmit' 
        data-action='submit'
 --}}

		{{-- class="g-recaptcha uk-button uk-button-primary" --}}

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