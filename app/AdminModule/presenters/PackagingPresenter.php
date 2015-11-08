<?php
/**
* Packaging presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;

final class PackagingPresenter extends BasePresenter{
	public $editId;
	private $packList;
	private $folder = "images/packaging/";
	public $refname = "packaging";
	
	public function actionDefault(){
		$this->packList = $this->context->createPacks()->order('order DESC');
	}
	
	public function renderDefault(){
		$this->template->packList = $this->packList;
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
	}
	
	public function actionEdit($editid){
		$this->editId = $editid;
		$this->template->editItem = $this->context->createPacks()->get($editid);
	}
	
	public function renderEdit(){
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
		$this->template->path = "/images/".$this->refname."/";
	}
	
	public function handleChangeDisplay($packid, $checked){
		if($this->presenter->isAjax()){
			$this->context->createPacks()->where(array('id' => $packid))->update(array('display' => $checked));
			$this->invalidateControl("packaging");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveUp($packid, $order){
		if($this->presenter->isAjax()){
			$prev_order = $this->context->createPacks()->where('order < ?', intval($order))->max('order');
			$prev_row = $this->context->createPacks()->where('order',$prev_order)->fetch();
			$prev_id = $prev_row["id"];
			$this->context->createPacks()->get($packid)->update(array('order' => $prev_order));
			$this->context->createPacks()->get($prev_id)->update(array('order' => intval($order)));

			$this->template->packList = $this->context->createPacks()->order('order DESC');
			$this->invalidateControl("packaging");
			$this->invalidateControl("packList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveDown($packid, $order){
		if($this->presenter->isAjax()){
			$next_order = $this->context->createPacks()->where('order > ?', intval($order))->min('order');
			$next_row = $this->context->createPacks()->where('order',$next_order)->fetch();
			$next_id = $next_row["id"];
			$this->context->createPacks()->get($packid)->update(array('order' => $next_order));
			$this->context->createPacks()->get($next_id)->update(array('order' => intval($order)));

			$this->template->packList = $this->context->createPacks()->order('order DESC');
			$this->invalidateControl("packaging");
			$this->invalidateControl("packList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleEdit($packid){}
	
	public function handleDelete($packid){
		$pp = $this->context->createPacks()->find($packid)->fetch();
		if($pp){
			unlink($this->context->params['wwwDir'].'/'.$this->folder.$pp->file);
			if($pp->detail != NULL){
				unlink($this->context->params['wwwDir'].'/'.$this->folder.$pp->detail);
			}
			$pp->delete();
			$this->flashMessage('Packaging byl smazán.');
			$this->template->packList = $this->context->createPacks()->order('order DESC');
			$this->invalidateControl("packaging");
			$this->invalidateControl("packList");
		}else{
			$this->flashMessage('Tuto položku nelze smazat. Pravděpodobně již neexistuje.', 'error');
		}
		$this->invalidateControl('flashMessages');
	}
	
	public function handleShowDetail($showid){}
	
	protected function createComponentPackaging(){
		return new \Packaging($this->context->createPacks()->where('display = 1')->order('order DESC'));
	}
	
	protected function createComponentUploadForm(){
		$form = new Form();
		$form->addUpload('image', 'Náhled (file):')
			->addRule($form::FILLED, 'Nevybrali jste žádný soubor.')
			->addRule($form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
			->addRule($form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '5000000');
		
		$form->addUpload('detail', 'Detail (file):')
			->addCondition($form::FILLED)
			->addRule($form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
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
			$insert['order'] = $this->context->createPacks()->max('order') + 1;
			$inserted = $this->context->createPacks()->insert($insert);
			
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
			
			$this->flashMessage('Packaging přidán.', 'success');
			$this->redirect('this');
		}
    }
	
	protected function createComponentEditForm($name){
	    $form = new \EditForm($this);
    	$form->folder = $this->folder;
		$form->editId = $this->editId;
		$form->table = $this->context->createPacks();
	    return $form;
	}

}

?>