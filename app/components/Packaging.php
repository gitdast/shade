<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Packaging extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $packs;

	public function __construct(Selection $packs){
		parent::__construct();
		$this->packs = $packs;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Packaging.latte');
		$this->template->packs = $this->packs;
		$this->template->render();
	}
}