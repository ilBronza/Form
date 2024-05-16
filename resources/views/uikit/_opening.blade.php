<form
	method="{{ $methodAttribute = $form->getMethodAttribute() }}"
	action="{{ $form->getAction() }}"
	role="form" 

	id="{{ $form->getId() }}"

	@if($methodAttribute == 'POST')
	enctype="multipart/form-data" 
	@endif

	class="uk-form {{ $form->getFormOrientationClass() }} {{ $form->getFormHtmlClassesString() }}"
>

	@if(! in_array($method = $form->getMethod(), ['GET', 'POST']) )
		@method($method)
	@endif

	{{ csrf_field() }}

	@if(isset($callertablename))
	<input type="hidden" name="callertablename" value="{{ $callertablename }}" />
	@endif