<script>
	(() => {
		const initialize = () => {
			const fieldset = document.getElementById(@json('fieldset' . $fieldset->getUniqueId()));
			const target = document.getElementById(@json('fieldset' . $fieldset->getUniqueId() . '-collapse-target'));

			if (! fieldset || ! target)
				return;

			const toggles = Array.from(fieldset.querySelectorAll('[data-fieldset-collapse-toggle]'))
				.filter((toggle) => toggle.dataset.fieldsetCollapseTargetId === target.id);
			const expandedIcon = toggles.find((toggle) => toggle.hasAttribute('data-fieldset-collapse-expanded-icon'));
			const collapsedIcon = toggles.find((toggle) => toggle.hasAttribute('data-fieldset-collapse-collapsed-icon'));
			const stateKey = @json($fieldset->getCollapseStateKey());

			const setCollapsed = (collapsed) => {
				target.classList.toggle('uk-hidden', collapsed);
				expandedIcon?.classList.toggle('uk-hidden', collapsed);
				collapsedIcon?.classList.toggle('uk-hidden', ! collapsed);
			};

			try {
				const storedState = window.sessionStorage.getItem(stateKey);

				if (storedState === 'collapsed')
					setCollapsed(true);
				else if (storedState === 'expanded')
					setCollapsed(false);
			}
			catch (error) {
				return;
			}

			toggles.forEach((toggle) => {
				toggle.addEventListener('click', () => {
					window.requestAnimationFrame(() => {
						try {
							window.sessionStorage.setItem(
								stateKey,
								target.classList.contains('uk-hidden') ? 'collapsed' : 'expanded'
							);
						}
						catch (error) {
							return;
						}
					});
				});
			});
		};

		// This partial is emitted immediately after the target's opening tag,
		// before its contents can be painted.
		initialize();
	})();
</script>
