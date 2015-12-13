<?php
/**
* Homepage presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;

final class IdentitiesPresenter extends BasePresenter{
	public $editId;
	private $logosList;
	private $folder = "images/identities/";
	public $refname = "identities";
	
	public function actionDefault(){
		$this->logosList = $this->context->createLogos()->order('order DESC');
	}
	
	public function renderDefault(){
		$this->template->logosList = $this->logosList;
		//$this->template->reference = substr($this->getName(),strrpos($this->getName(),':')+1);
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
	}
	
	public function actionEdit($editid){
		$this->editId = $editid;
		$this->template->editItem = $this->context->createLogos()->get($editid);
	}
	
	public function renderEdit(){
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
		$this->template->path = "/images/".$this->refname."/";
	}

	public function handleChangeDisplay($logoid, $checked){
		if($this->presenter->isAjax()){
			$this->context->createLogos()->where(array('id' => $logoid))->update(array('display' => $checked));
			$this->invalidateControl("identities");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleChangeMouseView($logoid, $mouseview){
		if($this->presenter->isAjax()){
			$this->context->createLogos()->where(array('id' => $logoid))->update(array('mouseview' => $mouseview));
			$this->invalidateControl("identities");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveUp($logoid, $order){
		$prev_order = $this->context->createLogos()->where('order < ?', intval($order))->max('order');
		$prev_row = $this->context->createLogos()->where('order', $prev_order)->fetch();
		$prev_id = $prev_row["id"];
		$this->context->createLogos()->get($logoid)->update(array('order' => $prev_order));
		$this->context->createLogos()->get($prev_id)->update(array('order' => intval($order)));

		$this->template->logosList = $this->context->createLogos()->order('order DESC');
		$this->invalidateControl("identities");
		$this->invalidateControl("logosList");
		$this->invalidateControl('flashMessages');
	}
	
	public function handleMoveDown($logoid, $order){
		$next_order = $this->context->createLogos()->where('order > ?', intval($order))->min('order');
		$next_row = $this->context->createLogos()->where('order',$next_order)->fetch();
		$next_id = $next_row["id"];
		$this->context->createLogos()->get($logoid)->update(array('order' => $next_order));
		$this->context->createLogos()->get($next_id)->update(array('order' => intval($order)));

		$this->template->logosList = $this->context->createLogos()->order('order DESC');
		$this->invalidateControl("identities");
		$this->invalidateControl("logosList");
		$this->invalidateControl('flashMessages');
	}
	
	public function handleDelete($logoid){
		$logo = $this->context->createLogos()->find($logoid)->fetch();
		if($logo){
			unlink($this->context->params['wwwDir'].'/'.$this->folder.$logo->file);
			if($logo->detail != NULL){
				unlink($this->context->params['wwwDir'].'/'.$this->folder.$logo->detail);
			}
			$logo->delete();
			$this->flashMessage('Logo bylo smazÃ??Ã‚Â¡no.');
			$this->template->logosList = $this->context->createLogos()->order('order DESC');
			$this->invalidateControl("identities");
			$this->invalidateControl("logosList");
		}else{
			$this->flashMessage('Tuto položku nelze smazat. Pravděpodobně již neexistuje.', 'error');
		}
		$this->invalidateControl('flashMessages');
	}
	

	
	protected function createComponentIdentities(){
		return new \Identities($this->context->createLogos()->where('display = 1')->order('order DESC'));
	}
	
	protected function createComponentUploadForm(){
		$form = new Form();
		$form->addUpload('image', 'Logo (file):')
			->addRule($form::FILLED, '...to logo si mám nakreslit? Nevybrali jste žádný soubor.')
			->addRule($form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
			->addRule($form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');
		
		$form->addUpload('detail', 'Detail (file):')
			->addCondition($form::FILLED)
			->addRule($form::IMAGE, 'Soubor(detail) musí být JPEG, PNG nebo GIF.')
			->addRule($form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');

		$form->addText('title', 'Název: ', 35)
			->addRule($form::FILLED, 'Ještě nějaký ten title, please...');


		$form->addSubmit('create', 'Nahrát');
		$form->onSuccess[] = callback($this, 'uploadFormSubmitted');
        return $form;
    }

	public function uploadFormSubmitted(Form $form){
		$values = $form->getValues();
		if(!$values['image']->isOk()){
			$this->flashMessage('Obrázek se nepodařilo načíst. Prosím, opakujte.', 'warning');
		}else{
			$insert = array();
			$insert['title'] = $values['title'];
			$insert['order'] = $this->context->createLogos()->max('order') + 1;
			$inserted = $this->context->createLogos()->insert($insert);
			
			$update = array();
			//image
			$filename = $inserted['id'].'_'.$this->refname . substr($values['image']->getSanitizedName(),strrpos($values['image']->getSanitizedName(),'.'));
			$file = $form['image']->getValue();
            $path = $this->folder . $filename;
            $file->move($path);
			$update['file'] = "$filename";
			//detail
			if($values['detail']->isOk()){
				$filename_detail = $inserted['id'].'_D_'.$this->refname . substr($values['detail']->getSanitizedName(),strrpos($values['detail']->getSanitizedName(),'.'));
				$file = $form['detail']->getValue();
    	        $path = $this->folder . $filename_detail;
        	    $file->move($path);
				$update['detail'] = "$filename_detail";				
			}
			$inserted->update($update);
			
			$this->flashMessage('Logo přidáno.', 'success');
			$this->redirect('this');
		}
    }
	
	protected function createComponentEditForm($name){
	    $form = new \EditForm($this);
    	$form->folder = $this->folder;
		$form->editId = $this->editId;
		$form->table = $this->context->createLogos();
	    return $form;
	}
	
	
}

?>