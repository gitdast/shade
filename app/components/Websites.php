<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Websites extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $panel1;
	private $panel2;

	public function __construct(Selection $panel1, $panel2){
		parent::__construct();
		$this->panel1 = $panel1;
		$this->panel2 = $panel2;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Websites.latte');
		$this->template->panel1 = $this->panel1;
		$this->template->panel2 = $this->panel2;
		$this->template->render();
	}
}