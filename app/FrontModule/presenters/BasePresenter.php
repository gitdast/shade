<?php
/**
* Base class for all application presenters.
*
* @author     Dast
*/

namespace FrontModule;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter{
//public $oldModuleMode = true;
//public $oldLayoutMode = true;
public function beforeRender()
{
    $this->template->sections = $this->context->createSections();
}
}
