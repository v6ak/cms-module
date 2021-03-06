<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Security;

use Venne;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
interface ISocialLogin extends \Nette\Security\IAuthenticator
{

	public function getType();


	public function getData();


	public function getEmail();


	public function getKey();


	public function getLoginUrl();


	/**
	 * @param Entities\UserEntity $userEntity
	 * @return bool
	 */
	public function connectWithUser(\CmsModule\Security\Entities\UserEntity $userEntity);
}
