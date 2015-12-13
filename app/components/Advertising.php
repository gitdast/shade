<?php

use Nette\Application\UI,
	Nette\Database\Table\Selection;

class Advertising extends UI\Control{
	/** @var \Nette\Database\Table\Selection */
	private $adds;

	public function __construct(Selection $adds){
		parent::__construct();
		$this->adds = $adds;
		foreach($this->adds as $add){
			$add->puretitle = $add->title;
			if(strpos($add->title, "http") !== false){
				$add->title = "<a href='".$add->title."'>".$add->title."</a>";
			}
		}
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/Advertising.latte');
		$this->template->adds = $this->adds;
		$this->template->render();
	}
}