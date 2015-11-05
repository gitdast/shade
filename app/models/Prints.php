<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Prints extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('printPoster', $connection);
    }
}
