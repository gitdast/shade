<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Identities extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $logos;

	public function __construct(Selection $logos){
		parent::__construct();
		$this->logos = $logos;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Identities.latte');
		$this->template->logos = $this->logos;
		$this->template->render();
	}
}