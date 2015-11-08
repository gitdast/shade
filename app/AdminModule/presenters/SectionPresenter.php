<?php
/**
* Homepage presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;
 
final class SectionPresenter extends BasePresenter{
	
	public $section;
	
	public function startup(){
		parent::startup();
		$this->section = ucfirst($this->getAction());
	}
	
	public function actionDefault(){
	}
	
	public function actionAbout(){
	}
	
	public function actionServices(){
	}
	
	public function actionNews(){
	}
	
	public function actionClients(){
	}
	
	public function actionContacts(){
	}
	
	protected function createComponentContentForm(){
		$form = new Form();
		$form->addTextArea('cont')
			->setAttribute('cols',103)
			->setAttribute('rows',25)
			->setValue($this->getContent())
			->getControlPrototype()->class('mceEditor');
		
		$form->addUpload('image', 'Nahrát na server obrázek:')
			->setAttribute('size',30)
			->addCondition($form::FILLED)
			->addRule($form::IMAGE, 'Obrázek musí být JPEG, PNG nebo GIF.')
			->addRule($form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');
			
		$form->addSubmit('save', 'Uložit');
		$form->onSuccess[] = callback($this, 'contentFormSubmitted');
        return $form;
    }


	public function contentFormSubmitted(Form $form){
		$values = $form->getValues();

		$this->context->createSections()->where(array('webname' => $this->section))->update(array('sectiontext' => $values['cont']));
		
		if($values['image']->isOk()){
			$filename = $values['image']->getSanitizedName();
			$file = $form['image']->getValue();
            $path = 'images/' . $filename;
   	        $file->move($path);
		}
			
		$this->redirect('this');
	}
	
	public function getContent(){
		$row = $this->context->createSections()->where(array('webname' => $this->section));
		return $row->fetch()->sectiontext;
	}
	
	private function getFileList(){
		$folder_name = "images";
		$dir = dir($folder_name);
		while ($file = $dir->read()) {
			if($file == "." || $file == "..") continue;
			if(is_file ($folder_name."/".$file)) $imgList[] = $file;
		}
		return $imgList;	
	}

	public function beforeRender(){
		$this->setLayout('layout.sections');
		$this->setView('default');
		$this->template->section = $this->section = ucfirst($this->getAction());
		$this->template->title = $this->section;
		$this->template->content = $this->getContent();
		$this->template->imgList = $this->getFileList();		
	}
	
	public function renderDefault(){
	}

}

?>