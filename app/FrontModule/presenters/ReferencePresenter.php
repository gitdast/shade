<?php
/**
* Homepage presenter.
*
* @author     Dast
*/

namespace FrontModule;

final class ReferencePresenter extends BasePresenter{
	public $refname;
	private $identities;
	private $printPoster;
	private $websites;
	private $packaging;
	private $advertising;
	
	public function actionIdentities(){
		$this->template->refname = $this->refname = "identities";
		//$this->template->identities = $this->identities;
	}
	public function actionPackaging(){
		$this->template->refname = $this->refname = "packaging";
		//$this->template->packaging = $this->packaging;
	}
	public function actionWebsites(){
		$this->template->refname = $this->refname = "websites";
		//$this->template->websites = $this->websites;
	}
	public function actionPrintPoster(){
		$this->template->refname = $this->refname = "printposter";
		//$this->template->printPoster = $this->printPoster;
	}
	public function actionAdvertising(){
		$this->template->refname = $this->refname = "advertising";
		//$this->template->advertising = $this->advertising;
	}
	
	protected function createComponentIdentities(){
		return new \Identities($this->context->createLogos()->where('display = 1')->order('order DESC'));
	}
	
	protected function createComponentWebsites(){
		$res = $this->context->createWebs()->where('display = 1')->order('order DESC');
		return new \Websites($res);
	}
	
	protected function createComponentPrintPoster(){
		return new \PrintPoster($this->context->createPrints()->where('display = 1')->order('order DESC'));
	}
	
	protected function createComponentPackaging(){
		return new \Packaging($this->context->createPacks()->where('display = 1')->order('order DESC'));
	}
	
	protected function createComponentAdvertising(){
		return new \Advertising($this->context->createAdds()->where('display = 1')->order('order DESC'));
	}

	/* not used anymore, snippet deleted from layout */
	public function handleShowDetail($showid){
		if($this->presenter->isAjax()){
			switch($this->refname){
				case "identities": $item = $this->context->createLogos()->get($showid); break;
				case "packaging": $item = $this->context->createPacks()->get($showid); break;
				case "websites": $item = $this->context->createWebs()->get($showid); break;
				case "printposter": $item = $this->context->createPrints()->get($showid); break;
				case "advertising": $item = $this->context->createAdds()->get($showid); break;
			}
			
			$this->template->detailBox = $item;
			if($item->detail == NULL) $item->detail = 'detail_missing.png';
			$this->template->references = $this->context->createReferences()->select('name')->where('refname', $this->refname)->fetch()->name;
			$this->template->detailBox->path = "/images/".$this->refname."/";
			$this->invalidateControl("detailBox");
		}
	}
	
	public function handleHideDetail($showid){
		if($this->presenter->isAjax()){
		}
	}
	
	public function beforeRender(){
		parent::beforeRender();
		$seo = $this->context->createReferences()->where(array('refname' => $this->refname))->fetch();
		$this->template->title = $seo->title;
		$this->template->description = $seo->description;
	}

}

?>