<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class PrintPoster extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $items;

	public function __construct(Selection $items){
		parent::__construct();
		$this->items = $items;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/PrintPoster.latte');
		$this->template->items = $this->items;
		$this->template->render();
	}
}