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
use CmsModule\Managers\DependencyNotExistsException;
use CmsModule\Managers\DependencyException;
use Venne\Module\ModuleManager;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 *
 * @secured
 */
class ModulePresenter extends BasePresenter
{


	/** @persistent */
	public $hash;

	/** @var ModuleManager */
	protected $moduleManager;


	function __construct(ModuleManager $moduleManager)
	{
		$this->moduleManager = $moduleManager;
	}


	public function startup()
	{
		parent::startup();

		// defined as of PHP 5.4
		if (!defined('JSON_PRETTY_PRINT')) {
			define('JSON_PRETTY_PRINT', 128);
		}

		if (!$this->hash) {
			$this->hash = \Nette\Utils\Strings::random();
		}

		if(!is_writable($this->context->parameters['libsDir'])) {
			$this->flashMessage('libsDir is not writable!', 'warning');
		}

		if(!is_writable($this->getOrigComposer()) || !is_writable($this->getOrigLock())) {
			$this->flashMessage('composer.json and composer.lock are not writable!');
		}
	}


	public function actionDefault()
	{
		$this->template->modules = $this->moduleManager->findRepositoryModules();
	}


	public function handleSync()
	{
		$this->moduleManager->scanRepositoryModules();
		$this->flashMessage('Database has been synced.', 'success');
		$this->redirect('this');
	}


	protected function createComponentDownloadForm()
	{
		$require = $this->context->parameters['modules'];

		$form = new \Venne\Application\UI\Form();
		$form->getElementPrototype()->attrs['class'] = 'noAjax';
		$form->addSubmit('_submit', 'Sync');
		$form->onSuccess[] = $this->save;

		foreach ($this->moduleManager->findRepositoryModules() as $name => $items) {
			$versions = explode(', ', $items->versions);

			if (($pos = array_search('dev-master', $versions)) !== false) {
				unset($versions[$pos]);
			}

			$form->addCheckbox('check_' . $name);
			$form->addSelect('version_' . $name)->setItems($versions, false);

			if (isset($require[$name])) {
				$form['check_' . $name]->setDefaultValue(true);
				$form['version_' . $name]->setDefaultValue($require[$name]['version']);
			}
		}

		return $form;
	}


	public function save(\Venne\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$data = json_decode(file_get_contents($this->getOrigComposer()), true);

		foreach ($this->moduleManager->findRepositoryModules() as $name => $items) {
			if ($values["check_$name"]) {
				$data['require']["venne/{$name}-module"] = $values["version_$name"];
			} else {
				if (isset($data['require']["venne/{$name}-module"])) {
					unset($data['require']["venne/{$name}-module"]);
				}
			}
		}

		file_put_contents($this->getComposer(), str_replace('\/', '/', json_encode($data, JSON_PRETTY_PRINT)));
		copy($this->getOrigLock(), $this->getLock());

		$ret = $this->moduleManager->updateByComposer($this->getComposer());

		unlink($this->getOrigComposer());
		unlink($this->getOrigLock());
		copy($this->getComposer(), $this->getOrigComposer());
		copy($this->getLock(), $this->getOrigLock());

		$el = \Nette\Utils\Html::el('pre');
		$el->setHtml($ret);
		$this->flashMessage($el, strpos($ret, 'Generating autoload files') !== false ? 'success' : 'warning');

		$this->redirect('this');
	}


	protected function getOrigComposer()
	{
		return $this->context->parameters['appDir'] . '/../composer.json';
	}


	protected function getOrigLock()
	{
		return $this->context->parameters['appDir'] . '/../composer.lock';
	}


	protected function getComposer()
	{
		return $this->context->parameters['tempDir'] . "/composer-{$this->hash}.json";
	}


	protected function getLock()
	{
		return $this->context->parameters['tempDir'] . "/composer-{$this->hash}.lock";
	}
}