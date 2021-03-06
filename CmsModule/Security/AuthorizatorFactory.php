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
use Nette\Object;
use Nette\Reflection\ClassType;
use Nette\Security\Permission;
use Nette\Security\User;
use Nette\Callback;
use Venne\Application\PresenterFactory;
use Nette\Http\SessionSection;
use Nette\Http\Session;
use DoctrineModule\Repositories\BaseRepository;
use Venne\Security\IControlVerifierReader;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class AuthorizatorFactory extends Object
{


	const SESSION_SECTION = 'Venne.Security.Authorizator';

	/** @var array */
	protected $defaultRoles = array('admin', 'authenticated', 'guest');

	/** @var PresenterFactory */
	protected $presenterFactory;

	/** @var BaseRepository */
	protected $roleRepository;

	/** @var SessionSection */
	protected $session;

	/** @var Callback */
	protected $checkConnection;

	/** @var IControlVerifierReader */
	protected $reader;


	/**
	 * @param PresenterFactory $presenterFactory
	 * @param BaseRepository $roleRepository
	 * @param Session $session
	 */
	public function __construct(PresenterFactory $presenterFactory, BaseRepository $roleRepository, \Nette\Http\Session $session, Callback $checkConnection)
	{
		$this->presenterFactory = $presenterFactory;
		$this->roleRepository = $roleRepository;
		$this->session = $session->getSection(self::SESSION_SECTION);
		$this->checkConnection = $checkConnection;
	}


	/**
	 * @param IControlVerifierReader $reader
	 */
	public function setReader(IControlVerifierReader $reader)
	{
		$this->reader = $reader;
	}


	public function clearPermissionSession()
	{
		if (isset($this->session['permission'])) {
			unset($this->session['permission']);
		}
	}


	/**
	 * Get permission for current user.
	 *
	 * @param User $user
	 * @return Permission
	 */
	public function getPermissionsByUser(User $user, $fromSession = FALSE)
	{
		if ($fromSession) {
			if ($this->session['permission']) {
				return $this->session['permission'];
			}

			return $this->session['permission'] = $this->getPermissionsByUser($user, FALSE);
		}

		return $this->getPermissionsByRoles(array_merge($user->roles, array("guest", "authenticated")));
	}


	/**
	 * Get permission for roles.
	 *
	 * @param array $roles
	 * @return Permission
	 */
	public function getPermissionsByRoles(array $roles)
	{
		$permission = $this->getRawPermissions();

		foreach ($roles as $role) {
			$this->setPermissionsByRole($permission, $role);
		}

		return $permission;
	}


	/**
	 * Get raw permissions without privileges.
	 *
	 * @return Permission
	 */
	public function getRawPermissions()
	{
		$permission = new Permission;

		foreach ($this->scanResources() as $resource => $privileges) {
			$permission->addResource($resource);
		}

		foreach ($this->defaultRoles as $role) {
			if (!$permission->hasRole($role)) {
				$permission->addRole($role);
			}
		}

		return $permission;
	}


	/* ************************************ PROTECTED **************************************** */


	/**
	 * Setup permission by role
	 *
	 * @param Permission $permission
	 * @param string $role
	 * @return Permission
	 */
	protected function setPermissionsByRole(Permission $permission, $role)
	{
		if ($role == "admin") {
			$permission->allow("admin", \Nette\Security\Permission::ALL);
			return $permission;
		}

		if ($this->checkConnection->invoke()) {
			$roleEntity = $this->roleRepository->findOneByName($role);
			if ($roleEntity) {
				if ($roleEntity->parent) {
					$this->setPermissionsByRole($permission, $roleEntity->parent->name);
				}

				if ($roleEntity && !$permission->hasRole($role)) {
					$permission->addRole($role, $roleEntity->parent ? $roleEntity->parent->name : NULL);
				}

				// allow/deny
				foreach ($roleEntity->permissions as $perm) {
					if ($permission->hasResource($perm->resource)) {
						if ($perm->allow) {
							$permission->allow($role, $perm->resource, $perm->privilege ? $perm->privilege : NULL);
						} else {
							$permission->deny($role, $perm->resource, $perm->privilege ? $perm->privilege : NULL);
						}
					}
				}
			}
		}

		return $permission;
	}


	/**
	 * Array of all resources.
	 *
	 * @return array
	 */
	protected function scanResources()
	{
		$ret = array();

		foreach ($this->presenterFactory->getPresenters() as $class => $name) {
			$schema = $this->reader->getSchema($class);

			foreach ($schema as $item) {
				if (!array_key_exists($item['resource'], $ret)) {
					$ret[$item['resource']] = array();
				}

				$ret[$item['resource']] = array_unique(array_merge($ret[$item['resource']], $item['privilege'] ? (array)$item['privilege'] : array()));
			}
		}

		return $ret;
	}
}
