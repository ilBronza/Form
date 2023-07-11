<div class="{{ $fieldset->getContainerHtmlClassesString() }}">

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
                {!! $fieldset->renderView() !!}
                @endif


                <div>
                    <div uk-grid class="{{ $fieldset->getColumnsClass() }} @if($fieldset->hasCollapse()) uk-grid-collapse @if($fieldset->hasDivider()) uk-grid-divider @endif @endif">
                        @include('form::uikit._fields', ['fields' => $fieldset->fields])
                    </div>            
                </div>

                @if(count($fieldset->fieldsets))
                <div uk-grid class="uk-padding-small">
                    @foreach($fieldset->fieldsets as $fieldset)
                        @include('form::uikit._fieldset')
                    @endforeach
                </div>
                @endif

            </div>

        </fieldset>	

    </div>

</div>

