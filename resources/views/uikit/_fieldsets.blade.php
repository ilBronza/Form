

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
        @include('form::uikit._fieldset')
    @endforeach

</div>

@endif