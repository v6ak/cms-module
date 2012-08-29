<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Forms;

use Venne;
use Venne\Forms\FormFactory;
use Venne\Forms\Form;
use DoctrineModule\Forms\Mappers\EntityMapper;
use DoctrineModule\ORM\BaseRepository;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class LanguageFormFactory extends FormFactory
{

	/** @var EntityMapper */
	protected $mapper;

	/** @var BaseRepository */
	protected $repository;


	/**
	 * @param EntityMapper $mapper
	 * @param BaseRepository $repository
	 */
	public function __construct(EntityMapper $mapper, BaseRepository $repository)
	{
		$this->mapper = $mapper;
		$this->repository = $repository;
	}


	protected function getMapper()
	{
		return $this->mapper;
	}


	/**
	 * @param Form $form
	 */
	public function configure(Form $form)
	{
		$form->addGroup("Language");
		$form->addText /*WithSelect*/
		("name", "Name"); //->setItems(array("English", "Deutsch", "Čeština"), false)->setOption("description", "(enhlish, deutsch,...)")->addRule(self::FILLED, "Please set name");
		$form->addText /*WithSelect*/
		("short", "Short"); //->setItems(array("en", "de", "cs"), false)->setOption("description", "(en, de,...)")->addRule(self::FILLED, "Please set short");
		$form->addText /*WithSelect*/
		("alias", "Alias"); //->setItems(array("en", "de", "cs", "www"), false)->setOption("description", "(www, en, de,...)")->addRule(self::FILLED, "Please set alias");

		$form->addSubmit('_submit', 'Save');
	}


	public function handleSave(Form $form)
	{
		try {
			$this->repository->save($form->data);
		} catch (\DoctrineModule\ORM\SqlException $e) {
			if ($e->getCode() == 23000) {
				$form->addError("Language is not unique", "warning");
				return;
			} else {
				throw $e;
			}
		}
	}
}