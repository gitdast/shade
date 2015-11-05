<?php

use Nette\Security as NS;

class Authenticator extends Nette\Object implements NS\IAuthenticator{
	/** @var Nette\Database\Table\Selection */
	private $users;

	public function __construct(Nette\Database\Table\Selection $users){
		$this->users = $users;
	}

	/**
	 * Performs an authentication
	 * @param  array
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$row = $this->users->where('username', $username)->fetch();

		if (!$row) {
			throw new NS\AuthenticationException("User '$username' nenalezen.", self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== self::calculateHash($password, $row->password)) {
			throw new NS\AuthenticationException("Nesprávné heslo.", self::INVALID_CREDENTIAL);
		}

		unset($row->password);
		return new NS\Identity($row->id, NULL, $row->toArray());
	}



	public static function calculateHash($password, $salt = null){
		if($salt === null){
			$salt = '$2a$07$' . Nette\Utils\Strings::random(32) . '$';
		}
		return crypt($password, $salt);
	}
}
