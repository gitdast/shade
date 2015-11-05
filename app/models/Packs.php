<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Packs extends Selection
{
    public function __construct(Connection $connection)
    {
        parent::__construct('packaging', $connection);
    }
}
