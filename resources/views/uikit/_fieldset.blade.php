@if($fieldset->isVisible())
	<div {{ $fieldset->getContainerHtmlAttributesString() }} class="{{ $fieldset->getContainerHtmlClassesString() }}">

		<div>

			<fieldset class="{{ $fieldset->getUniqueId() }} {{ $fieldset->getHtmlClassesString() }}"
					  data-name="{{ $fieldset->getUniqueId() }}" id="fieldset{{ $fieldset->getUniqueId() }}">

				@if($fieldset->showLegend())
					<legend class="uk-legend {{ $fieldset->getLegendHtmlClassesString() }} @if(! $description = $fieldset->getDescription()) uk-margin-{{ $fieldset->getMarginSize() }}-bottom @endif">
						<span>{!! $fieldset->getLegend() !!}</span>
						<span class="toggler toggle{{ $fieldset->getUniqueId() }}"
							  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
							  uk-icon="chevron-up"></span>
						<span class="toggle{{ $fieldset->getUniqueId() }} uk-hidden"
							  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
							  uk-icon="chevron-down"></span>
					</legend>
				@endif

				<div class="toggle toggle{{ $fieldset->getUniqueId() }} {{ $fieldset->getBodyHtmlClassesString() }}">

					@if($description ?? false)
						<div class="uk-margin-medium-bottom">
							{!! $description !!}
						</div>
					@endif

					@if($fieldset->getView())
						<div {{ $fieldset->getHtmlAttributesString() }}>
							{!! $fieldset->renderView() !!}
						</div>
					@endif

					@if($buttons = $fieldset->getButtons())
						@foreach($buttons as $button)
							{!! $button->render() !!}
						@endforeach
					@endif

					<div>
						<div {{ $fieldset->getHtmlAttributesString() }} uk-grid
							 class="{{ $fieldset->getGridSizeHtmlClass() }} fields {{ $fieldset->getColumnsClass() }} {{ $fieldset->getCollapseDividerString() }}">
							@include('form::uikit._fields', ['fields' => $fieldset->fields])
						</div>
					</div>

					@if(count($fieldset->fieldsets))
						<div uk-grid
							 class="uk-padding-small {{ $fieldset->getGridSizeHtmlClass() }} @if($fieldset->hasDivider()) uk-grid-divider @endif">
							@foreach($fieldset->fieldsets as $fieldset)
								@include('form::uikit._fieldset')
							@endforeach
						</div>
					@endif

				</div>

			</fieldset>

		</div>

	</div>

@endif