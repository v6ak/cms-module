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

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class Control extends \Venne\Application\UI\Control
{

	/**
	 * Formats component template files
	 *
	 * @param string
	 * @return array
	 */
	protected function formatTemplateFiles()
	{
		$list = parent::formatTemplateFiles();
		$refl = $this->getReflection();
		$path = dirname($this->getPresenter()->getLayoutPath());

		return array_merge(array(
			dirname($path) . '/' . $refl->getShortName() . '.latte',
			$path . '/' . $refl->getShortName() . '.latte',
		), $list);
	}
}
