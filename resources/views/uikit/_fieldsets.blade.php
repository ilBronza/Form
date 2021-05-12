

@if($form->mustDisplayAsSwitcher())

<ul class="uk-subnav uk-subnav-pill" uk-switcher>
	@foreach($form->fieldsets as $name => $fieldset)
    <li><a href="#">{{ $fieldset->getLegend() }}</a></li>
	@endforeach
</ul>

<ul class="uk-switcher uk-margin">
	@foreach($form->fieldsets as $name => $fieldset)
    <li>@include('form::uikit._fields', ['fields' => $fieldset->fields])</li>
	@endforeach
</ul>

@else

<div uk-grid class="uk-grid-divider">
    
    @foreach($form->fieldsets as $name => $fieldset)

    <div class="{{ $fieldset->getHtmlClasses() }}">

        <fieldset class="uk-fieldset uk-margin-small-bottom {{ Str::slug($name) }}" data-name="{{ Str::slug($name) }}" id="fieldset{{ Str::slug($name) }}">

            <legend class="uk-legend uk-margin-small-bottom">{!! $fieldset->getLegend() !!}
            </legend>

            <div uk-grid class="uk-child-width-1-{{ $fieldset->columns }} @if($fieldset->hasCollapse()) uk-grid-collapse @if($fieldset->hasDivider()) uk-grid-divider @endif @endif">
            	@include('form::uikit._fields', ['fields' => $fieldset->fields])
            </div>

        </fieldset>	

    </div>

    @endforeach

</div>

@endif