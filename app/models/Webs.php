<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Webs extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('websites', $connection);
    }
}
