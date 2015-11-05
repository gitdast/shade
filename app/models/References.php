<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class References extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('references', $connection);
    }
}
