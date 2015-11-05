<?php
/**
* Base class for all application presenters.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Application\UI\Presenter;
 
abstract class BasePresenter extends Presenter{
	
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
}
