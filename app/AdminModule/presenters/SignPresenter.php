<?php
/**
* Sign in/out presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;

class SignPresenter extends Presenter{

	protected function createComponentSignInForm(){
    	$form = new Form();
	    $form->addText('username', 'Uživatelské jméno:', 30, 20)
			->addRule($form::FILLED,'Prosím, vyplňte uživatelské jméno.');

	    $form->addPassword('password', 'Heslo:', 30)
			->addRule($form::FILLED,'Prosím, vyplňte heslo.');
		
	    $form->addCheckbox('persistent', 'Pamatovat si mě na tomto počítači');
	
    	$form->addSubmit('login', 'Přihlásit se');
	
	    $form->onSuccess[] = callback($this, 'signInFormSubmitted');
    	return $form;
	}

	public function signInFormSubmitted(Form $form){
		try{
        	$user = $this->getUser();
    	    $values = $form->getValues();
	        if ($values->persistent) {
        	    $user->setExpiration('+30 days', FALSE);
    	    }
	        $user->login($values->username, $values->password);
        	$this->flashMessage('Přihlášení bylo úspěšné.', 'success');
    	    $this->redirect('Default:');
	    } catch (NS\AuthenticationException $e) {
        	$form->addError('Neplatné uživatelské jméno nebo heslo.');
    	}
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('in');
	}

}
