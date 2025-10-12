@extends('uikittemplate::app')

@section('content')

	{!! $form->_render() !!}

	@if(isset($relationshipManager))
		@include('crud::uikit.__relationships' . $relationshipManager->getDisplayType())
	@endif

@endsection