<div {{ $fieldset->getContainerHtmlAttributesString() }} class="{{ $fieldset->getContainerHtmlClassesString() }}">

	<div>

		<fieldset class="{{ $fieldset->getUniqueId() }} {{ $fieldset->getHtmlClassesString() }}"
				  data-name="{{ $fieldset->getUniqueId() }}" id="fieldset{{ $fieldset->getUniqueId() }}">

			@if($fieldset->showLegend())
				<legend class="uk-legend @if(! $description = $fieldset->getDescription()) uk-margin-medium-bottom @endif">
					<span>{!! $fieldset->getLegend() !!}</span>
					@if($fieldset->canBeHidden())
					<span class="toggler toggle{{ $fieldset->getUniqueId() }} @if($fieldset->isCollapsedInitially()) uk-hidden @endif uk-icon-button"
						  data-fieldset-collapse-toggle
						  data-fieldset-collapse-target-id="fieldset{{ $fieldset->getUniqueId() }}-collapse-target"
						  data-collapse-state-key="{{ $fieldset->getCollapseStateKey() }}"
						  data-fieldset-collapse-expanded-icon
						  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
						  uk-icon="chevron-up"></span>
					<span class="toggle{{ $fieldset->getUniqueId() }} @unless($fieldset->isCollapsedInitially()) uk-hidden @endunless uk-icon-button"
						  data-fieldset-collapse-toggle
						  data-fieldset-collapse-target-id="fieldset{{ $fieldset->getUniqueId() }}-collapse-target"
						  data-collapse-state-key="{{ $fieldset->getCollapseStateKey() }}"
						  data-fieldset-collapse-collapsed-icon
						  uk-toggle="target: .toggle{{ $fieldset->getUniqueId() }}; cls: uk-hidden"
						  uk-icon="chevron-down"></span>
					@endif
				</legend>

				<div id="fieldset{{ $fieldset->getUniqueId() }}-collapse-target" class="toggle toggle{{ $fieldset->getUniqueId() }} @if($fieldset->isCollapsedInitially()) uk-hidden @endif">
					@if($fieldset->canBeHidden() && $fieldset->showLegend())
						@include('form::uikit.fieldsets._collapse-state')
					@endif
					@endif

					@if($description ?? ($description = $fieldset->getDescription()))
						<div class="uk-margin-medium-bottom">
							{!! $description !!}
						</div>
					@endif

					@if($fieldset->getView())
						<div {{ $fieldset->getHtmlAttributesString() }} class="view view{{ Str::slug($fieldset->getView()) }}">
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
							 class="{{ $fieldset->getGridSizeHtmlClass() }} {{ $fieldset->getColumnsClass() }} {{ $fieldset->getCollapseDividerString() }}">
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
