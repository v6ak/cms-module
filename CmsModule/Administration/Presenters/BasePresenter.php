<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Administration\Presenters;

use Venne;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 *
 * @secured
 * @persistent(panel)
 */
class BasePresenter extends \CmsModule\Presenters\AdminPresenter
{

	public function beforeRender()
	{
		parent::beforeRender();

		if (!$this->getSignal()) {
			$this->invalidateControl('content');
			$this->invalidateControl('header');
			$this->invalidateControl('toolbar');

			if (!isset($this->template->hideMenuItems)) {
				$this['panel']->invalidateControl('tabs');
			}
		}
	}


	public function handleLogout()
	{
		$this->user->logout(TRUE);
		$this->flashMessage('Logout success');
		$this->redirect('this');
	}
}
