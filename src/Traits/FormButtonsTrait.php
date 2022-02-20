<?php

namespace IlBronza\Form\Traits;

use IlBronza\Button\Button;

trait FormButtonsTrait
{
	public function hasCancelButton()
	{
		return $this->cancelButton;
	}

	public function getCancelButton() : ? Button
	{
		if(! $this->hasCancelButton())
			return null;

		$button = new Button();

		$button->addClass('uk-button-default');

		$button->setText(
			trans('form::form.cancel')
		);

		$button->setHref(
			$this->getCancelHref()
		);

		return $button;
	}

	public function setSubmitButtonText(string $submitButtonText)
	{
		$this->submitButtonText = $submitButtonText;
	}

	public function getSubmitButtonText()
	{
		return $this->submitButtonText ?? __('forms.save');
	}

	public function getClosureButtons()
	{
		return $this->closureButtons;
	}

	public function addClosureButton(Button $button)
	{
		$this->closureButtons->push($button);
	}

	public function addSaveAndNewButton()
	{
		$button = new Button();

		$button->setPrimary();
		$button->setName('save_and_new');

		$button->setText(
			trans('form::form.saveAndNew')
		);

		$button->setSubmit();

		$this->addClosureButton($button);
	}

	public function addSaveAndRefreshButton()
	{
		$button = new Button();

		$button->setPrimary();
		$button->setName('save_and_refresh');

		$button->setText(
			trans('form::form.saveAndRefresh')
		);

		$button->setSubmit();

		$this->addClosureButton($button);
	}
}