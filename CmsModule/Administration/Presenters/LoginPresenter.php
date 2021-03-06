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
use CmsModule\Forms\LoginFormFactory;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class LoginPresenter extends BasePresenter
{


	/** @persistent */
	public $backlink;

	/** @var LoginFormFactory */
	protected $form;


	/**
	 * @param LoginFormFactory $form
	 */
	public function injectForm(LoginFormFactory $form)
	{
		$this->form = $form;
	}


	public function startup()
	{
		parent::startup();

		if (!$this->context->createCheckConnection()) {
			$this->flashMessage("Only administrator can be logged", "warning");
		}
	}


	/**
	 * Sign in form component factory.
	 *
	 * @return \Venne\Application\UI\Form
	 */
	protected function createComponentSignInForm($name)
	{
		$form = $this->form->invoke();
		return $form;
	}
}
