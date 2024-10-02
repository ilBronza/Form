<div {{ $fieldset->getContainerHtmlAttributesString() }} class="{{ $fieldset->getContainerHtmlClassesString() }}">

	<div>

		<fieldset class="{{ $fieldset->getUniqueId() }} {{ $fieldset->getHtmlClassesString() }}"
				  data-name="{{ $fieldset->getUniqueId() }}" id="fieldset{{ $fieldset->getUniqueId() }}">

			@if($fieldset->showLegend())
				<legend class="uk-legend @if(! $description = $fieldset->getDescription()) uk-margin-medium-bottom @endif">
					<span>{!! $fieldset->getLegend() !!}</span>
					<span class="toggler toggle{{ $fieldset->getUniqueId() }}"
						  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
						  uk-icon="chevron-up"></span>
					<span class="toggle{{ $fieldset->getUniqueId() }} uk-hidden"
						  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
						  uk-icon="chevron-down"></span>
				</legend>

				<div class="toggle toggle{{ $fieldset->getUniqueId() }}">
					@endif

					@if($description ?? ($description = $fieldset->getDescription()))
						<div class="uk-margin-medium-bottom">
							{!! $description !!}
						</div>
					@endif

					@if($fieldset->getView())
						<div {{ $fieldset->getHtmlAttributesString() }}>
							{!! $fieldset->renderView() !!}
						</div>
					@endif

					@foreach($fieldset->getFetchers() as $fetcher)
						{!! $fetcher->render() !!}
					@endforeach

					@foreach($fieldset->getButtons() as $button)
						{!! $button->render() !!}
					@endforeach

					<div>
						<div {{ $fieldset->getHtmlAttributesString() }} uk-grid
							 class="{{ $fieldset->getGridSizeHtmlClass() }} {{ $fieldset->getColumnsClass() }} @if($fieldset->hasCollapse()) uk-grid-collapse @if($fieldset->hasDivider()) uk-grid-divider @endif @endif">
							@include('form::uikit.fields.show', ['fields' => $fieldset->fields])
						</div>
					</div>

					@if(count($fieldset->fieldsets))
						<div uk-grid uk-height-match class="uk-grid-divider {{ $fieldset->getGridSizeHtmlClass() }}">
							@foreach($fieldset->fieldsets as $fieldset)
								@include('form::uikit.fieldsets.show')
							@endforeach
						</div>
					@endif

					@if($fieldset->showLegend())
				</div>
			@endif

		</fieldset>

	</div>

</div>

