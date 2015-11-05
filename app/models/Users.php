<?php

use Nette\Database\Connection,
    Nette\Database\Table\Selection;
	
class Users extends Selection{
	protected $connection;
	
    public function __construct(Connection $connection){
        parent::__construct('user', $connection);
		$this->connection = $connection;
    }
	
	public function setPassword($id, $password){
		$this->connection->table('user')->where(array('id' => $id))->update(array('password' => Authenticator::calculateHash($password)));
	}
}
