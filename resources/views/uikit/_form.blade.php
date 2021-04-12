@if((isset($extraViews['outerLeft']))||(isset($extraViews['outerRight'])))
<div uk-grid>

    @foreach($extraViews['outerLeft'] ?? [] as $name => $parameters)
        @include($name, $parameters)
    @endforeach

    <div class="uk-width-expand">
@endif

@foreach($extraViews['outherTop'] ?? [] as $name => $parameters)
    @include($name, $parameters)
@endforeach


@include('form::uikit._opening')

@if((isset($extraViews['innerLeft']))||(isset($extraViews['innerRight'])))
<div uk-grid>

    @foreach($extraViews['innerLeft'] ?? [] as $name => $parameters)
        @include($name, $parameters)
    @endforeach

    <div class="uk-width-expand">
@endif

	@if($form->hasCard())

    <div class="uk-card uk-card-default {{ implode(' ', $form->getCardClasses()) }}">
        <div class="uk-card-header">
            <span class="uk-h3 uk-display-block">{!! $form->getTitle() !!}</span>
            @if($backUrl = $form->getBackToListUrl())
            <span class="uk-h5"><a href="{{ $backUrl }}">@lang('crud.backToList')</a> </span>
            @endif
        </div>
        <div class="uk-card-body">
            @foreach($extraViews['innerTop'] ?? [] as $name => $parameters)
                @include($name, $parameters)
            @endforeach

            @include('form::uikit._content')

            @foreach($extraViews['innerBottom'] ?? [] as $name => $parameters)
                @include($name, $parameters)
            @endforeach
        </div>
        <div class="uk-card-footer">
            @include('form::uikit._closureButtons')
        </div>
    </div>

	@else
		@include('form::uikit._content')
		@include('form::uikit._closureButtons')

	@endif

@if((isset($extraViews['innerLeft']))||(isset($extraViews['innerRight'])))
    </div>

    @foreach($extraViews['innerRight'] ?? [] as $name => $parameters)
        @include($name, $parameters)
    @endforeach

</div>
@endif

@include('form::uikit._closure')

@if((isset($extraViews['outerLeft']))||(isset($extraViews['outerRight'])))
    </div>

    @foreach($extraViews['outerRight'] ?? [] as $name => $parameters)
        @include($name, $parameters)
    @endforeach

</div>
@endif

@foreach($extraViews['outerBottom'] ?? [] as $name => $parameters)
    @include($name, $parameters)
@endforeach


@if (isset($errors)&&($errors->any()))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif