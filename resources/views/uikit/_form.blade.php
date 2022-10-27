
@if(isset($extraViews))
    <span class="uk-display-block uk-text-danger uk-h3">USE NEW EXTRA VIEWS SYSTEM</span>
    <span class="uk-display-block uk-text-danger uk-h3">Se alcune informazioni non sono visibili, contattare assistenza. <pre>{{ json_encode($extraViews) }}</pre></span>
@endif

@if($form->hasExtraViewsPositions('outherLeft', 'outherRight'))
<div uk-grid>

    {!! $form->renderExtraViews('outherLeft') !!}

    <div class="uk-width-expand">
@endif

    {!! $form->renderExtraViews('outherTop') !!}

@include('form::uikit._opening')

@if($form->hasExtraViewsPositions('right', 'left'))
<div uk-grid>

    {!! $form->renderExtraViews('left') !!}

    <div class="uk-width-expand">
@endif

	@if($form->hasCard())

    <div class="uk-card uk-card-default {{ implode(' ', $form->getCardClasses()) }}">
        <div class="uk-card-header">
            <span class="uk-h3 uk-display-block">{!! $form->getTitle() !!}</span>
            @if($backUrl = $form->getBackToListUrl())
            <span class="uk-h5"><a href="{{ $backUrl }}">@lang('crud::crud.backToList')</a> </span>
            @endif

            @if($formIntro = $form->getIntro())
            <br />
            {!! $formIntro !!}
            @endif
        </div>
        <div class="uk-card-body">

            {!! $form->renderExtraViews('innerTop') !!}

            @include('form::uikit._content')

            {!! $form->renderExtraViews('innerBottom') !!}

        </div>
        <div class="uk-card-footer">
            @include('form::uikit._closureButtons')
        </div>
    </div>

	@else

        {!! $form->renderExtraViews('innerTop') !!}
        @include('form::uikit._content')
        {!! $form->renderExtraViews('innerBottom') !!}

		@include('form::uikit._closureButtons')

	@endif

@if($form->hasExtraViewsPositions('right', 'left'))
    </div>

    {!! $form->renderExtraViews('right') !!}

</div>
@endif

@include('form::uikit._closure')

@if($form->hasExtraViewsPositions('outherRight', 'outherLeft'))

    </div>

    {!! $form->renderExtraViews('outherRight') !!}

</div>
@endif

{!! $form->renderExtraViews('outherBottom') !!}

@if (isset($errors)&&($errors->any()))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif