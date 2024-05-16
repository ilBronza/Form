
@if(isset($extraViews))
    <span class="uk-display-block uk-text-danger uk-h3">USE NEW EXTRA VIEWS SYSTEM</span>
    <span class="uk-display-block uk-text-danger uk-h3">Se alcune informazioni non sono visibili, contattare assistenza. <pre>{{ json_encode($extraViews) }}</pre></span>
@endif

@if(isset($buttons))
    <nav class="uk-navbar-container" uk-navbar>
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                @isset($backToListUrl)
                <li><a href="{{ $backToListUrl }}">@lang('crud::crud.backToList')</a></li>
                @endisset

                @foreach($buttons as $button)
                    @if($button)
                        <li>{!! $button->renderLink() !!}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </nav>
@endif


@if($form->hasExtraViewsPositions('outherLeft', 'outherRight'))
<div uk-grid>

    {!! $form->renderExtraViews('outherLeft') !!}

    <div class="uk-width-expand">
@endif

    {!! $form->renderExtraViews('outherTop') !!}

@if($form->isInFormDisplayMode())
    @include('form::uikit._opening')
@endif

@if($form->hasExtraViewsPositions('right', 'left'))
<div uk-grid>

    {!! $form->renderExtraViews('left') !!}

    <div class="uk-width-expand">
@endif

	@if($form->hasCard())

    <div class="uk-card uk-card-default {{ implode(' ', $form->getCardClasses()) }}">

        <div class="uk-card-header">

            <div uk-grid>
                
                <div class="uk-h3 uk-display-block uk-width-expand">{!! $form->getTitle() !!}</div>

                @if($backUrl = $form->getBackToListUrl())
                <div class="uk-width-auto uk-h5 uk-margin-large-right">
                    <a href="{{ $backUrl }}">@lang('crud::crud.backToList')</a>
                </div>
                @endif

                @if($form->hasButtonsNavbar())
                    {!! $form->getButtonsNavbar()->render() !!}

                @else
                            @if($form->isInFormDisplayMode())
                                        @if($showUrl = $form->getShowElementUrl())
                                            <span class="uk-h5"><a href="{{ $showUrl }}">@lang('crud::crud.showElement', ['element' => $form->getModel()?->getName()])</a> </span>
                                        @endif
                            @else
                                            @include('crud::utilities.editLink', ['element' => $form->getModel()])
                            @endif
                                
                @endif

            </div>

            @if($formIntro = $form->getIntro())
            <div class="uk-margin-top">
                {!! $formIntro !!}
            </div>
            @endif

        </div>

        <div class="uk-card-body">

            {!! $form->renderExtraViews('innerTop') !!}

            @include('form::uikit._content')

            {!! $form->renderExtraViews('innerBottom') !!}

        </div>
@if($form->isInFormDisplayMode())
        <div class="uk-card-footer">
            @include('form::uikit._closureButtons')
        </div>
@endif
    </div>

	@else

        {!! $form->renderExtraViews('innerTop') !!}
        @include('form::uikit._content')
        {!! $form->renderExtraViews('innerBottom') !!}

@if($form->isInFormDisplayMode())
		@include('form::uikit._closureButtons')
@endif

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