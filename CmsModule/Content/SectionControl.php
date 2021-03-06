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
use CmsModule\Content\Entities\PageEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class SectionControl extends \Venne\Application\UI\Control implements ISectionControl
{

	/** @var PageEntity */
	protected $entity;


	/**
	 * @param \CmsModule\Content\Entities\PageEntity $entity
	 */
	public function setEntity(PageEntity $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * @return \CmsModule\Content\Entities\PageEntity
	 */
	public function getEntity()
	{
		return $this->entity;
	}


}
