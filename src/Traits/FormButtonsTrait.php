<?php

namespace IlBronza\Form\Traits;

use IlBronza\Buttons\Button;

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

		$button = Button::create([
			'text' => 'form::form.cancel',
			'href' => $this->getCancelUrl()
		]);

		$button->addClass('uk-button-default');

		// $button->setText(
		// 	trans('form::form.cancel')
		// );

		// $button->setHref(
		// 	$this->getCancelHref()
		// );

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
		$button = Button::create([
			'name' => 'save_and_new',
			'text' => 'form::form.saveAndNew'
		]);

		$button->setPrimary();

		$button->setSubmit();

		$this->addClosureButton($button);
	}

	public function addSaveAndRefreshButton()
	{
		$button = Button::create([
			'name' => 'save_and_refresh',
			'text' => 'form::form.saveAndRefresh'
		]);

		$button->setPrimary();

		$button->setSubmit();

		$this->addClosureButton($button);
	}
}