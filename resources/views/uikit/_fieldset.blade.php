<div class="{{ $fieldset->getContainerHtmlClassesString() }}">

    <div>
            
        <fieldset class="{{ Str::slug($name) }} {{ $fieldset->getHtmlClassesString() }}" data-name="{{ Str::slug($name) }}" id="fieldset{{ Str::slug($name) }}">

            <legend class="uk-legend uk-margin-medium-bottom">
                <u>
                    {!! $fieldset->getLegend() !!}
                </u>
            </legend>

            @if($description = $fieldset->getDescription())
            <div class="uk-margin-bottom">
                {!! $description !!}
            </div>
            @endif

            <div>
                <div uk-grid class="uk-child-width-1-{{ $fieldset->columns }}@m @if($fieldset->hasCollapse()) uk-grid-collapse @if($fieldset->hasDivider()) uk-grid-divider @endif @endif">
                    @include('form::uikit._fields', ['fields' => $fieldset->fields])
                </div>            
            </div>

        </fieldset>	

    </div>

</div>

