<?php

namespace IlBronza\Form\Traits;

use IlBronza\Buttons\Button;

trait FormButtonsTrait
{
	public $hasSubmitButton = true;

	public function setDefaultNavbarButtons() { }

	public function hasSubmitButton()
	{
		return $this->hasSubmitButton;
	}

	public function setHasSubmitButton(bool $hasSubmitButton = true) : self
	{
		$this->hasSubmitButton = $hasSubmitButton;

		return $this;
	}

	public function setCancelButton(bool $hasCancelButton) : self
	{
		$this->cancelButton = $hasCancelButton;

		return $this;
	}

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



	public function setSubmitButtonHtmlClasses(array $submitButtonHtmlClasses = [])
	{
		$this->submitButtonHtmlClasses = $submitButtonHtmlClasses;
	}

	public function getSubmitButtonHtmlClasses() : string
	{
		return implode(" ", $this->submitButtonHtmlClasses ?? ['uk-button uk-button-primary']);
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

		$button->setForm($this);
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

	public function addSaveAndCopyButton()
	{
		$button = Button::create([
			'name' => 'save_and_copy',
			'text' => 'form::form.saveAndCopy'
		]);

		$button->setPrimary();

		$button->setSubmit();

		$this->addClosureButton($button);
	}
}