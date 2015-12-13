<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Sections extends Selection
{
    public function __construct(Connection $connection){
        parent::__construct('sections', $connection);
		//$this->where[] = "visible = 1";
    }
}
