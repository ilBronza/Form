

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

<div uk-grid class="uk-child-width-1-{{ count($form->fieldsets) }}">
    
    @foreach($form->fieldsets as $name => $fieldset)

    <fieldset class="uk-fieldset">

        <legend class="uk-legend">{{ $name }}</legend>

        <div uk-grid class="uk-child-width-1-{{ $fieldset->columns }}">
        	@include('form::uikit._fields', ['fields' => $fieldset->fields])
        </div>

    </fieldset>	

    @endforeach

</div>

@endif