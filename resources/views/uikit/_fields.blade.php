@foreach($fields as $field)
	@if($form->isInFormDisplayMode())
		{{ $field->render() }}
	@elseif($form->isInShowDisplayMode())
		{{ $field->renderShow() }}
	@elseif($form->isInPdfDisplayMode())
		{{ $field->renderPdf() }}
	@else
	No display mode declared
	@endif
@endforeach