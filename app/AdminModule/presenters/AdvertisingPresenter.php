<?php
/**
* Advertising presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;
 
final class AdvertisingPresenter extends BasePresenter{
	public $editId;
	private $addList;
	private $folder = "images/advertising/";
	public $refname = "advertising";
	
	public function actionDefault(){
		$this->addList = $this->context->createAdds()->order('order DESC');
	}
	
	public function renderDefault(){
		$this->template->addList = $this->addList;
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
	}
	
	public function actionEdit($editid){
		$this->editId = $editid;
		$this->template->editItem = $this->context->createAdds()->get($editid);
	}
		
	public function renderEdit(){
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
		$this->template->path = "/images/".$this->refname."/";
	}

		
	public function handleChangeDisplay($addid, $checked){
		if($this->presenter->isAjax()){
			$this->context->createAdds()->where(array('id' => $addid))->update(array('display' => $checked));
			$this->invalidateControl("advertising");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveUp($addid, $order){
		if($this->presenter->isAjax()){
			$prev_order = $this->context->createAdds()->where('order < ?', intval($order))->max('order');
			$prev_row = $this->context->createAdds()->where('order',$prev_order)->fetch();
			$prev_id = $prev_row["id"];
			$this->context->createAdds()->get($addid)->update(array('order' => $prev_order));
			$this->context->createAdds()->get($prev_id)->update(array('order' => intval($order)));

			$this->template->addList = $this->context->createAdds()->order('order DESC');
			$this->invalidateControl("advertising");
			$this->invalidateControl("addList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveDown($addid, $order){
		if($this->presenter->isAjax()){
			$next_order = $this->context->createAdds()->where('order > ?', intval($order))->min('order');
			$next_row = $this->context->createAdds()->where('order',$next_order)->fetch();
			$next_id = $next_row["id"];
			$this->context->createAdds()->get($addid)->update(array('order' => $next_order));
			$this->context->createAdds()->get($next_id)->update(array('order' => intval($order)));

			$this->template->addList = $this->context->createAdds()->order('order DESC');
			$this->invalidateControl("advertising");
			$this->invalidateControl("addList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleEdit($addid){}
	
	public function handleDelete($addid){
		$add = $this->context->createAdds()->find($addid)->fetch();
		if($add){
			unlink($this->context->params['wwwDir'].'/'.$this->folder.$add->file);
			if($add->detail != NULL){
				unlink($this->context->params['wwwDir'].'/'.$this->folder.$add->detail);
			}
			$add->delete();
			$this->flashMessage('Add byl smazán.');
			$this->template->addList = $this->context->createAdds()->order('order DESC');
			$this->invalidateControl("advertising");
			$this->invalidateControl("addList");
		}else{
			$this->flashMessage('Tuto položku nelze smazat. Pravděpodobně již neexistuje.', 'error');
		}
		$this->invalidateControl('flashMessages');
	}
	
	public function handleShowDetail($showid){}
	
	protected function createComponentAdvertising(){
		return new \Advertising($this->context->createAdds()->where('display = 1')->order('order DESC'));
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
			$insert['order'] = $this->context->createAdds()->max('order') + 1;
			$inserted = $this->context->createAdds()->insert($insert);
			
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

			$this->flashMessage('Add přidán.', 'success');
			$this->redirect('this');
		}
    }
	
	protected function createComponentEditForm($name){
	    $form = new \EditForm($this);
    	$form->folder = $this->folder;
		$form->editId = $this->editId;
		$form->table = $this->context->createAdds();
	    return $form;
	}

}

?>