<?php
/**
* Admin default presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Application\UI\Form;
use Nette\Security as NS;

final class DefaultPresenter extends BasePresenter{

	private $users;
	private $authenticator;

	protected function startup(){
		parent::startup();

		$this->users = $this->context->createUsers();
		$this->authenticator = $this->context->authenticator;
	}

	protected function createComponentPasswordForm(){
		$form = new Form();
		$form->addPassword('oldPassword', 'Staré heslo:', 30)
			->addRule($form::FILLED, 'Je nutné zadat staré heslo.');
		$form->addPassword('newPassword', 'Nové heslo:', 30)
			->addRule($form::MIN_LENGTH, 'Nové heslo musí mít alespoň %d znaků.', 6);
		$form->addPassword('confirmPassword', 'Potvrzení hesla:', 30)
			->addRule($form::FILLED, 'Nové heslo je nutné zadat ještě jednou pro potvrzení.')
			->addRule($form::EQUAL, 'Zadná hesla se musejí shodovat.', $form['newPassword']);
		$form->addSubmit('submit', 'Změnit heslo');
		$form->onSuccess[] = callback($this, 'passwordFormSubmitted');
		return $form;
	}

	public function passwordFormSubmitted(Form $form){
		$values = $form->getValues();
		$user = $this->getUser();
		try{
			$this->authenticator->authenticate(array($user->getIdentity()->username, $values->oldPassword));
            $this->users->setPassword($user->getId(), $values->newPassword);
            $this->flashMessage('Heslo bylo změněno.', 'success');
            $this->redirect('Default:');
        } catch (NS\AuthenticationException $e) {
            $form->addError('Zadané heslo není správné.');
        }
    }
	
	public function renderDefault(){
	}

}

?>