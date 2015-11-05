<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Adds extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('advertising', $connection);
    }
}
