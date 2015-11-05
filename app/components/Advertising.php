<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Advertising extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $adds;

	public function __construct(Selection $adds){
		parent::__construct();
		$this->adds = $adds;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Advertising.latte');
		$this->template->adds = $this->adds;
		$this->template->render();
	}
}