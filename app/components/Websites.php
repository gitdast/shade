<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Websites extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $items;

	public function __construct(Selection $sel){
		parent::__construct();
		$this->items = $sel;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Websites.latte');
		$this->template->items = $this->items;
		$this->template->render();
	}
}