<?php

use Nette\Application\UI,
	Nette\ComponentModel\IContainer;

class EditForm extends UI\Form{
	public $folder;
	private $presenter;
	public $editId;
	public $table;
	
	private $page;

	public function __construct(IContainer $parent = NULL, $page = NULL){
		parent::__construct();
		$this->presenter = $parent;
		$this->page = $page;
		
		$this->addUpload('image', 'Obrázek (vybrat nový soubor)')
			->setAttribute('size',30)
			->addCondition($this::FILLED)
			->addRule($this::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
			->addRule($this::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');
		
		$this->addUpload('detail', 'Detail (vybrat nový soubor)')
			->setAttribute('size',30)
			->addCondition($this::FILLED)
			->addRule($this::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
			->addRule($this::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');

		$this->addText('title', 'Název', 40);
		
		if($this->page == "printPoster"){
			$this->addText('link', 'Odkaz(youtube): ', 40);
		}

		$this->addSubmit('create', 'Uložit');
		$this->onSuccess[] = callback($this, 'editFormSubmitted');
	}
	
	public function editFormSubmitted(){
		$values = $this->getValues();
		$update = array();
		if($values['image']->isOk()){
			$filename = $this->editId.'_'.$this->presenter->refname . substr($values['image']->getSanitizedName(),strrpos($values['image']->getSanitizedName(),'.'));
			$update['file'] = "$filename";
		}
		if($values['detail']->isOk()){
			$filename_detail = $this->editId.'_D_'.$this->presenter->refname . substr($values['detail']->getSanitizedName(),strrpos($values['detail']->getSanitizedName(),'.'));
			$update['detail'] = "$filename_detail";
		}
		if($values['title'] != ''){
			$update['title'] = $values['title'];
		}
		if($this->page == "printPoster"){
			$update['link'] = $values['link'];
		}
		
		if(count($update) > 0){
			$this->table->get($this->editId)->update($update);
			if($values['image']->isOk()){
				$file = $this['image']->getValue();
	            $path = $this->folder . $filename;
    	        $file->move($path);
			}
			if($values['detail']->isOk()){
				$file = $this['detail']->getValue();
	            $path = $this->folder . $filename_detail;
    	        $file->move($path);
			}

			$this->presenter->flashMessage('Upraveno.', 'success');
			$this->presenter->redirect('this');
		}
	}

}