<?php
/**
* Websites presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;
 
final class PrintPosterPresenter extends BasePresenter{
	public $editId;
	private $itemsList;
	private $folder = "images/printposter/";
	public $refname = "printposter";
	
	public function actionDefault(){
		$this->itemsList = $this->context->createPrints()->order('order');
	}
	
	public function actionEdit($editid){
		$this->editId = $editid;
		$this->template->editItem = $this->context->createPrints()->get($editid);
	}
	
	public function handleChangeDisplay($itemid, $checked){
		if($this->presenter->isAjax()){
			$this->context->createPrints()->where(array('id' => $itemid))->update(array('display' => $checked));
			$this->invalidateControl("printPoster");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveUp($itemid, $order){
		if($this->presenter->isAjax()){
			$prev_order = $this->context->createPrints()->where('order < ?', intval($order))->max('order');
			$prev_row = $this->context->createPrints()->where('order',$prev_order)->fetch();
			$prev_id = $prev_row["id"];
			$this->context->createPrints()->get($itemid)->update(array('order' => $prev_order));
			$this->context->createPrints()->get($prev_id)->update(array('order' => intval($order)));

			$this->template->itemsList = $this->context->createPrints()->order('order');
			$this->invalidateControl("printPoster");
			$this->invalidateControl("itemsList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveDown($itemid, $order){
		if($this->presenter->isAjax()){
			$next_order = $this->context->createPrints()->where('order > ?', intval($order))->min('order');
			$next_row = $this->context->createPrints()->where('order',$next_order)->fetch();
			$next_id = $next_row["id"];
			$this->context->createPrints()->get($itemid)->update(array('order' => $next_order));
			$this->context->createPrints()->get($next_id)->update(array('order' => intval($order)));

			$this->template->itemsList = $this->context->createPrints()->order('order');
			$this->invalidateControl("printPoster");
			$this->invalidateControl("itemsList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleEdit($itemid){}
	
	public function handleDelete($itemid){
		$web = $this->context->createPrints()->get($itemid);
		if($web){
			unlink($this->context->params['wwwDir'].'/'.$this->folder.$web->file);
			if($web->detail != NULL){
				unlink($this->context->params['wwwDir'].'/'.$this->folder.$web->detail);
			}
			$web->delete();
			$this->flashMessage('P&p byl smazán.');
			$this->template->itemsList = $this->context->createPrints()->order('panel')->order('order');
			$this->invalidateControl("printPoster");
			$this->invalidateControl("itemsList");
		}else{
			$this->flashMessage('Tuto položku nelze smazat. Pravděpodobně již neexistuje.', 'error');
		}
		$this->invalidateControl('flashMessages');
	}
	
	public function handleShowDetail($showid){}
	
	protected function createComponentPrintPoster(){
		return new \PrintPoster($this->context->createPrints()->where('display = 1')->order('order'));
	}
	
	protected function createComponentUploadForm(){
		$form = new Form();
		$form->addUpload('image', 'Náhled (file):')
			->addRule($form::FILLED, '...a nějakej ten screenshot? Nevybrali jste žádný soubor.')
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
			$insert['order'] = $this->context->createPrints()->max('order') + 1;
			$inserted = $this->context->createPrints()->insert($insert);
			
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
			
			$this->flashMessage('P&p přidán.', 'success');
			$this->redirect('this');
		}
    }
	
	protected function createComponentEditForm($name){
	    $form = new \EditForm($this);
    	$form->folder = $this->folder;
		$form->editId = $this->editId;
		$form->table = $this->context->createPrints();
	    return $form;
	}
	
	public function renderEdit(){
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
		$this->template->path = "/images/".$this->refname."/";
	}

	public function renderDefault(){
		$this->template->itemsList = $this->itemsList;
		$this->template->reference = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
	}

}

?>