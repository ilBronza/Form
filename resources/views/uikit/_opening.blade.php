<form
	method="{{ $methodAttribute = $form->getMethodAttribute() }}"
	action="{{ $form->getAction() }}"
	role="form" 

	@if($methodAttribute == 'POST')
	enctype="multipart/form-data" 
	@endif

	class="uk-form {{ $form->getFormOrientationClass() }}"
>

	@if(! in_array($method = $form->getMethod(), ['GET', 'POST']) )
		@method($method)
	@endif

	{{ csrf_field() }}
