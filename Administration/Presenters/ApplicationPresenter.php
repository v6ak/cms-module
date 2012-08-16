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
use Nette\Callback;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 *
 * @secured
 */
class ApplicationPresenter extends BasePresenter
{


	/** @persistent */
	public $key;

	/** @var Callback */
	protected $systemForm;

	/** @var Callback */
	protected $applicationForm;

	/** @var Callback */
	protected $databaseForm;

	/** @var Callback */
	protected $accountForm;



	function __construct($systemForm, $applicationForm, $databaseForm, $accountForm)
	{
		$this->systemForm = $systemForm;
		$this->applicationForm = $applicationForm;
		$this->databaseForm = $databaseForm;
		$this->accountForm = $accountForm;
	}



	public function createComponentSystemForm()
	{
		$form = $this->systemForm->invoke();
		$form->onSuccess[] = function($form) {
					$form->getPresenter()->flashMessage("Global settings has been updated", "success");
					$form->getPresenter()->redirect("this");
				};
		return $form;
	}



	public function createComponentApplicationForm()
	{
		$form = $this->applicationForm->invoke();
		$form->onSuccess[] = function($form) {
					$form->getPresenter()->flashMessage("Application settings has been updated", "success");
					$form->getPresenter()->redirect("this");
				};
		return $form;
	}



	public function createComponentDatabaseForm()
	{
		$form = $this->databaseForm->invoke();
		$form->onSuccess[] = function($form) {
					$form->getPresenter()->flashMessage("Database settings has been updated", "success");
					$form->getPresenter()->redirect("this");
				};
		return $form;
	}



	public function createComponentAccountForm()
	{
		$form = $this->accountForm->invoke();
		$form->onSuccess[] = function($form) {
					$form->getPresenter()->flashMessage("Account settings has been updated", "success");
					$form->getPresenter()->redirect("this");
				};
		return $form;
	}

}