<?php

namespace ilBronza\Form\Traits;

trait FormButtonsTrait
{
	public function hasSubmit()
	{
		return $this->submit;
	}

	public function getExtraSubmitButtons()
	{
		return $this->extraSubmitButtons;
	}

	public function addSubmitButton(\dgButton $button)
	{
		$this->extraSubmitButtons->push($button);
	}
}