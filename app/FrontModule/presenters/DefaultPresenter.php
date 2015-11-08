<?php
/*
* Homepage presenter.
*
* @author     Dast
*/

namespace FrontModule;
 
final class DefaultPresenter extends BasePresenter{
	
	public $section = "About";
	
	public function actionDefault($section , $secOld){
		$this->template->sectionOld = $secOld;
		$this->template->section = $this->section = $section;
	}
	
	public function handleSectionClick($section, $secOld){
		if($this->presenter->isAjax()){
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
			$this->template->title = $section;
			$this->template->content = $this->getContent();
		}
	}
	
	public function getContent(){
		$row = $this->context->createSections()->where(array('webname' => $this->section));
		return $row->fetch()->sectiontext;
	}

	public function renderDefault(){
		$this->template->title = $this->section;
	}

}

?>