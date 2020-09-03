@extends('layouts.app')

@section('content')

@include('form::uikit._opening')

	@if($form->hasCard())

    <div class="uk-card uk-card-default {{ implode(' ', $form->getCardClasses()) }}">
        <div class="uk-card-header">
            <span class="uk-h3 uk-display-block">{!! $form->getTitle() !!}</span>
            @if($backUrl = $form->getBackToListUrl())
            <span class="uk-h5"><a href="{{ $backUrl }}">@lang('crud.backToList')</a> </span>
            @endif
        </div>
        <div class="uk-card-body">
            @include('form::uikit._content')
        </div>
        <div class="uk-card-footer">
            @include('form::uikit._closureButtons')
        </div>
    </div>

	@else
		@include('form::uikit._content')
		@include('form::uikit._closureButtons')

	@endif

@include('form::uikit._closure')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@endsection