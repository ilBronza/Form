

@if($form->mustDisplayAsSwitcher())

<ul class="uk-subnav uk-subnav-pill" uk-switcher>
	@foreach($form->fieldsets as $name => $fieldset)
    <li><a href="#">{{ $name }}</a></li>
	@endforeach
</ul>

<ul class="uk-switcher uk-margin">
	@foreach($form->fieldsets as $name => $fieldset)
    <li>@include('form::uikit._fields', ['fields' => $fieldset->fields])</li>
	@endforeach
</ul>

@else

<div uk-grid>
    
    @foreach($form->fieldsets as $name => $fieldset)

    <div class="{{ $fieldset->getHtmlClasses() }}">

        <fieldset class="uk-fieldset uk-margin-bottom {{ Str::slug($name) }}" data-name="{{ Str::slug($name) }}" id="fieldset{{ Str::slug($name) }}">

            <legend class="uk-legend">@lang('fieldsets.' . Str::camel($name))</legend>

            <div uk-grid class="uk-child-width-1-{{ $fieldset->columns }}">
            	@include('form::uikit._fields', ['fields' => $fieldset->fields])
            </div>

        </fieldset>	

    </div>

    @endforeach

</div>

@endif