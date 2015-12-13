<?php
/**
* Base class for all application presenters.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Application\UI\Presenter,
	Nette\Application\UI\Form;
 
abstract class BasePresenter extends Presenter{
	public $seo;
	public $refname;
	
	protected function startup(){
		parent::startup();
		
		if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }else{
			$this->setLayout('layout.admin');
		}
	}
	
	public function handleSignOut(){
    	$this->getUser()->logout();
	    $this->redirect('Sign:in');
	}


	public function beforeRender(){
    	$this->template->sections = $this->context->createSections();
		$this->template->username = $this->getUser()->getIdentity()->username;
	}
	
	protected function createComponentSeoForm(){
		$form = new Form();
		$this->seo = $this->context->createReferences()->where(array('refname' => $this->refname))->fetch();
			
		$form->addText('title', 'Title')->setDefaultValue($this->seo->title);
		$form->addTextArea('description', 'Description')
			->setAttribute('cols',30)
			->setAttribute('rows',5)
			->setDefaultValue($this->seo->description);
			
		$form->addSubmit('save', 'Uložit');
		$form->onSuccess[] = callback($this, 'seoFormSubmitted');
        return $form;
    }
	public function seoFormSubmitted(Form $form){
		$values = $form->getValues();
		
		try{
			$this->context->createReferences()
				->where(array('refname' => $this->refname))
				->update(array('title' => $values['title'], 'description' => $values['description']));
		
			$this->flashMessage('Seo uloženo.', 'success');
		}
		catch(\Exception $e){
			$this->flashMessage('Chyba při ukládání.', 'error');
		}
		$this->redirect('this');
	}
}
