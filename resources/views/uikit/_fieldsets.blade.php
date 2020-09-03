@foreach($form->fieldsets as $name => $fieldset)

<fieldset class="uk-fieldset">

    <legend class="uk-legend">{{ $name }}</legend>

    <div uk-grid class="uk-child-width-1-{{ $fieldset->columns }}">
    	@include('form::uikit._fields', ['fields' => $fieldset->fields])
    </div>

</fieldset>	

@endforeach