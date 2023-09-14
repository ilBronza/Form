<div {{ $fieldset->getContainerHtmlAttributesString() }} class="{{ $fieldset->getContainerHtmlClassesString() }}">

    <div>

        <fieldset class="{{ $fieldset->getUniqueId() }} {{ $fieldset->getHtmlClassesString() }}" data-name="{{ $fieldset->getUniqueId() }}" id="fieldset{{ $fieldset->getUniqueId() }}">

            <legend class="uk-legend @if(! $description = $fieldset->getDescription()) uk-margin-medium-bottom @endif">
                <span>{!! $fieldset->getLegend() !!}</span>
                <span class="toggler toggle{{ $fieldset->getUniqueId() }}" uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden" uk-icon="chevron-up"></span>
                <span class="toggle{{ $fieldset->getUniqueId() }} uk-hidden" uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden" uk-icon="chevron-down"></span>
            </legend>

            <div class="toggle toggle{{ $fieldset->getUniqueId() }}">
                
                @if($description)
                <div class="uk-margin-medium-bottom">
                    {!! $description !!}
                </div>
                @endif

                @if($fieldset->getView())
                <div {{ $fieldset->getHtmlAttributesString() }}>
                    {!! $fieldset->renderView() !!}
                </div>
                @endif


                <div>
                    <div {{ $fieldset->getHtmlAttributesString() }} uk-grid class="uk-grid-small {{ $fieldset->getColumnsClass() }} @if($fieldset->hasCollapse()) uk-grid-collapse @if($fieldset->hasDivider()) uk-grid-divider @endif @endif">
                        @include('form::uikit.fields.show', ['fields' => $fieldset->fields])
                    </div>
                </div>

                @if(count($fieldset->fieldsets))
				<div uk-grid uk-height-match class="uk-grid-divider uk-grid-small">
                    @foreach($fieldset->fieldsets as $fieldset)
                        @include('form::uikit.fieldsets.show')
                    @endforeach
                </div>
                @endif

            </div>

        </fieldset>	

    </div>

</div>

