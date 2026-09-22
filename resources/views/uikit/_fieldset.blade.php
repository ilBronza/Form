@if($fieldset->isVisible())
	<div {{ $fieldset->getContainerHtmlAttributesString() }} class="{{ $fieldset->getContainerHtmlClassesString() }}">

		<div>

			<fieldset class="{{ $fieldset->getUniqueId() }} {{ $fieldset->getHtmlClassesString() }}"
					  data-name="{{ $fieldset->getUniqueId() }}" id="fieldset{{ $fieldset->getUniqueId() }}">

				@if($fieldset->showLegend())
					<legend class="uk-legend {{ $fieldset->getLegendHtmlClassesString() }} @if(! $description = $fieldset->getDescription()) uk-margin-{{ $fieldset->getMarginSize() }}-bottom @endif">

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

						<span>{!! $fieldset->getLegend() !!}</span>

					</legend>
				@endif

				<div id="fieldset{{ $fieldset->getUniqueId() }}-collapse-target" class="toggle toggle{{ $fieldset->getUniqueId() }} {{ $fieldset->getBodyHtmlClassesString() }} @if($fieldset->isCollapsedInitially()) uk-hidden @endif">
					@if($fieldset->canBeHidden() && $fieldset->showLegend())
						@include('form::uikit.fieldsets._collapse-state')
					@endif

					@if($description ?? false)
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

					<div class="fieldset-buttons">
					@if($buttons = $fieldset->getButtons())
						@foreach($buttons as $button)
							{!! $button->render() !!}
						@endforeach
					@endif
					</div>

					<div>
						<div {{ $fieldset->getHtmlAttributesString() }} uk-grid
							 class="{{ $fieldset->getGridSizeHtmlClass() }} fields {{ $fieldset->getColumnsClass() }} {{ $fieldset->getCollapseDividerString() }}">
							@include('form::uikit._fields', ['fields' => $fieldset->fields])
						</div>
					</div>

					@if($fieldset->showFieldsetsAsSwitcher ?? false)
						@if(count($fieldset->fieldsets))
							<div>
								<ul class="uk-subnav uk-subnav-pill" uk-switcher>
								@foreach($fieldset->fieldsets as $innerFieldset)
									<li><a href="#">{!! $innerFieldset->getLegend() !!}</a></li>
								@endforeach
								</ul>

								<div class="uk-switcher uk-margin">
								@foreach($fieldset->fieldsets as $innerFieldset)
									<div>
										@include('form::uikit._fieldset', ['fieldset' => $innerFieldset])
									</div>
								@endforeach
								</div>
							</div>

						@endif
					@else

						@if(count($fieldset->fieldsets))
							<div uk-grid
								 class="inner-fieldsets uk-padding-small {{ $fieldset->getGridSizeHtmlClass() }} @if($fieldset->hasDivider()) uk-grid-divider @endif">
								@foreach($fieldset->fieldsets as $fieldset)
									@include('form::uikit._fieldset')
								@endforeach
							</div>
						@endif

					@endif

				</div>

			</fieldset>

		</div>

	</div>

@endif
