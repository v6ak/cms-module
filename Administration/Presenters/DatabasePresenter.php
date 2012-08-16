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
 */
class DatabasePresenter extends BasePresenter
{

	/** @var Callback */
	protected $databaseForm;


	function __construct($databaseForm)
	{
		$this->databaseForm = $databaseForm;
	}


	public function createComponentSystemDatabaseForm()
	{
		/** @var $form \CmsModule\Forms\SystemDatabaseForm */
		$form = $this->databaseForm->invoke();
		$form->getElementPrototype()->attrs['class'][] = 'noAjax';
		$form->onSuccess[] = $this->save;
		return $form;
	}


	public function save()
	{
		$this->flashMessage("Database settings has been updated", "success");
		$this->redirect('install!');
	}


	public function handleInstall()
	{
		if ($this->context->doctrine->createCheckConnection() && count($this->context->schemaManager->listTables()) == 0) {
			/** @var $em \Doctrine\ORM\EntityManager */
			$em = $this->context->entityManager;
			$tool = new \Doctrine\ORM\Tools\SchemaTool($em);

			$robotLoader = new \Nette\Loaders\RobotLoader();
			$robotLoader->setCacheStorage(new \Nette\Caching\Storages\MemoryStorage());
			$robotLoader->addDirectory($this->context->parameters['modules']['cms']['path']);
			$robotLoader->register();

			$classes = array();
			foreach ($robotLoader->getIndexedClasses() as $item => $a) {
				$ref = \Nette\Reflection\ClassType::from($item);
				if ($ref->hasAnnotation('Entity')) {
					$classes[] = $em->getClassMetadata('\\' . $item);
				}
			}

			$tool->createSchema($classes);
		}
		$this->redirect(':Cms:Admin:Default:');
	}
}