<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Content;

use Venne;
use Nette\Application\UI\IRenderable;
use CmsModule\Content\Entities\LayoutconfigEntity;
use CmsModule\Content\Entities\RouteEntity;
use CmsModule\Content\Entities\PageEntity;
use Doctrine\ORM\EntityManager;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
interface IElement extends IRenderable
{


	/**
	 * @abstract
	 * @param RouteEntity $routeEntity
	 */
	public function setRoute(RouteEntity $routeEntity);


	/**
	 * @abstract
	 * @param $name
	 */
	public function setName($name);


	/**
	 * @abstract
	 */
	public function render();


	/**
	 * @abstract
	 */
	public function getViews();
}
