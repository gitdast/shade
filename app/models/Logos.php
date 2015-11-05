<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Logos extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('logos', $connection);
    }
}
