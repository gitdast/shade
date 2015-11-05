<?php
/**
* websites presenter.
*
* @author     Dast
*/

namespace AdminModule;

use Nette\Database\Connection,
	Nette\Application\UI\Form;
 
final class WebsitesPresenter extends BasePresenter{
	public $editId;
	private $itemsList;
	private $folder = "images/websites/";
	public $refname = "websites";
	
	public function actionDefault(){
		$this->itemsList = $this->context->createWebs()->order('panel')->order('order');
	}
	
	public function actionEdit($editid){
		$this->editId = $editid;
		$this->template->editItem = $this->context->createWebs()->get($editid);
	}
	
	public function handleChangeDisplay($itemid, $checked){
		if($this->presenter->isAjax()){
			$this->context->createWebs()->where(array('id' => $itemid))->update(array('display' => $checked));
			$this->invalidateControl("websites");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveUp($itemid, $order, $panel){
		if($this->presenter->isAjax()){
			$prev_order = $this->context->createWebs()->where('panel', $panel)->where('order < ?', intval($order))->max('order');
			if(count($prev_order)){
				$prev_row = $this->context->createWebs()->where('panel', $panel)->where('order',$prev_order)->fetch();
				$prev_id = $prev_row["id"];
				$this->context->createWebs()->get($itemid)->update(array('order' => $prev_order));
				$this->context->createWebs()->get($prev_id)->update(array('order' => intval($order)));
			}else{
				$max_order = $this->context->createWebs()->where('panel != ?', $panel)->max('order');
				$this->context->createWebs()->get($itemid)->update(array('order' => $max_order+1, 'panel' => $panel-1));
			}

			$this->template->itemsList = $this->context->createWebs()->order('panel')->order('order');
			$this->invalidateControl("websites");
			$this->invalidateControl("itemsList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleMoveDown($itemid, $order, $panel){
		if($this->presenter->isAjax()){
			$next_order = $this->context->createWebs()->where('panel', $panel)->where('order > ?', intval($order))->min('order');
			if(count($next_order)){
				$next_row = $this->context->createWebs()->where('panel', $panel)->where('order',$next_order)->fetch();
				$next_id = $next_row["id"];
				$this->context->createWebs()->get($itemid)->update(array('order' => $next_order));
				$this->context->createWebs()->get($next_id)->update(array('order' => intval($order)));
			}else{
				$min_order = $this->context->createWebs()->where('panel != ?', $panel)->min('order');
				$this->context->createWebs()->get($itemid)->update(array('order' => $min_order-1, 'panel' => $panel+1));
			}

			$this->template->itemsList = $this->context->createWebs()->order('panel')->order('order');
			$this->invalidateControl("websites");
			$this->invalidateControl("itemsList");
			$this->invalidateControl('flashMessages');
		}
	}
	
	public function handleEdit($itemid){}
	
	public function handleDelete($itemid){
		$pp = $this->context->createWebs()->find($itemid)->fetch();
		if($pp){
			unlink($this->context->params['wwwDir'].'/'.$this->folder.$pp->file);
			if($pp->detail != NULL){
				unlink($this->context->params['wwwDir'].'/'.$this->folder.$pp->detail);
			}
			$pp->delete();
			$this->flashMessage('Web byl smazán.');
			$this->template->itemsList = $this->context->createWebs()->order('panel')->order('order');
			$this->invalidateControl("websites");
			$this->invalidateControl("itemsList");
		}else{
			$this->flashMessage('Tuto položku nelze smazat. Pravděpodobně již neexistuje.', 'error');
		}
		$this->invalidateControl('flashMessages');
	}
	
	public function handleShowDetail($showid){}
	
	protected function createComponentWebsites(){
		$p1 = $this->context->createWebs()->where('display = 1')->where('panel', 1)->order('order');
		$p2 = $this->context->createWebs()->where('display = 1')->where('panel', 2)->order('order');
		return new \Websites($p1, $p2);
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
			$insert['order'] = $this->context->createWebs()->max('order') + 1;
			$inserted = $this->context->createWebs()->insert($insert);
			
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
			
			$this->flashMessage('Web přidán.', 'success');
			$this->redirect('this');
		}
    }
	
	protected function createComponentEditForm($name){
	    $form = new \EditForm($this);
    	$form->folder = $this->folder;
		$form->editId = $this->editId;
		$form->table = $this->context->createWebs();
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