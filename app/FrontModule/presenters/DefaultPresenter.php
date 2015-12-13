<?php
/*
* Homepage presenter.
*
* @author     Dast
*/

namespace FrontModule;
 
final class DefaultPresenter extends BasePresenter{
	
	public $section = "About";
	public $content;
	
	public function actionDefault($section = "About"){
		$this->template->sectionOld = "";
		$this->template->section = $this->section = $section;
		$this->getContent();
		$this->template->title = $this->content->title;
		$this->template->description = $this->content->description;
	}
	
	public function handleSectionClick($section = "About", $secOld){
		$sec = $this->context->createSections()->where(array('webname' => $section));
		$newId = $sec->fetch()->id;
		if($secOld != ''){
			$sec = $this->context->createSections()->where(array('webname' => $secOld));
			$oldId = $sec->fetch()->id;
			$this->payload->operation = 'sectionChange';
		}else{
			$oldId = 0;
			$this->payload->operation = 'sectionInit';
		}

		$this->payload->direction = ($newId > $oldId) ? -1 : 1;
		$this->invalidateControl("topmenu");
		$this->invalidateControl("bgd");
		$this->invalidateControl("content");
		$this->invalidateControl('title');
		$this->template->sectionOld = $secOld;
		$this->template->section = $section;
		$this->template->content = $this->getContent();
		$this->template->title = !empty($this->content->title) ? $this->content->title : $section;
	}
	
	public function renderDefault($section){
	}
	
	public function getContent(){
		if(!$this->content){
			$this->content = $this->context->createSections()->where(array('webname' => $this->section))->fetch();
		}
		return $this->content->sectiontext;
	}
	
	

}

?>