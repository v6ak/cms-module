<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Content\Forms;

use Venne;
use Venne\Forms\Form;
use DoctrineModule\Forms\FormFactory;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class FileFormFactory extends FormFactory
{


	/**
	 * @param Form $form
	 */
	protected function configure(Form $form)
	{
		$form->addUpload('file', 'File')->getControlPrototype()->attrs['onChange'] = 'var data = $(this).val().split("\\\\"); data = data[data.length - 1]; $("input:eq(" + $("input").index(this) + 1 + ")").val(data);';
		$form->addText('name', 'Name');
		$form->addManyToOne('parent', 'Parent');

		$form->addSaveButton('Save');
	}


	public function handleSave(Form $form)
	{
		$file = $form['file']->value;
		if ($file->isOk()) {
			$form->data->setFile($file);
			$form->data->setName($file->name);
		}

		parent::handleSave($form);
	}


	public function handleSuccess(Form $form)
	{
		if (isset($form->presenter['panel'])) {
			$form->presenter['panel']->invalidateControl('content');
		}
	}
}
