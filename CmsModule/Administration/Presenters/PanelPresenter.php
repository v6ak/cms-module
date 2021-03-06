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
use DoctrineModule\Repositories\BaseRepository;
use CmsModule\Content\Entities\RouteEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 *
 * @secured
 */
class PanelPresenter extends BasePresenter
{


	/** @persistent */
	public $elementName;

	/** @persistent */
	public $elementId;

	/** @persistent */
	public $elementRouteId;

	/** @persistent */
	public $elementView;

	/** @var RouteEntity */
	public $route;

	/** @var string */
	protected $_layoutPath;


	public function beforeRender()
	{
		parent::beforeRender();

		$this->validateControl('content');
		$this->validateControl('panel');
		$this->validateControl('header');
		$this->validateControl('toolbar');
		$this->validateControl('navigation');
		$this['panel']->validateControl('tabs');

		$this->template->elementName = $this->elementName;
		$this->template->elementId = $this->elementId;
		$this->template->elementView = $this->elementView;
		$this->template->elementRouteId = $this->elementRouteId;
		if ($this->elementRouteId) {
			$this->route = $this->getContext()->cms->routeRepository->find($this->elementRouteId);
		}

		$this->invalidateControl('elementView');
		$this->invalidateControl('element');
	}


	/**
	 * @secured(privilege="show")
	 */
	public function actionDefault()
	{
	}


	public function handleElement()
	{
		$this->template->showElementView = true;
	}


	public function handleRefreshElement()
	{
		$this->template->showElement = true;
		$this->elementView = NULL;
	}


	public function createComponent($name)
	{
		if (strpos($name, \CmsModule\Content\ElementManager::ELEMENT_PREFIX) === 0) {
			$name = substr($name, strlen(\CmsModule\Content\ElementManager::ELEMENT_PREFIX));
			$name = explode('_', $name);
			$id = end($name);
			unset($name[count($name) - 1]);
			$name = implode('_', $name);

			$route = $this->getContext()->cms->routeRepository->find($this->elementRouteId);

			/** @var $component \CmsModule\Content\IElement */
			$component = $this->context->cms->elementManager->createInstance($id);
			$component->setRoute($route);
			$component->setName($name);
			return $component;
		}

		return parent::createComponent($name);
	}


	/**
	 * Get layout path.
	 *
	 * @return null|string
	 */
	public function getLayoutPath()
	{
		if ($this->_layoutPath === NULL) {
			if (!$this->route->layout) {
				$this->_layoutPath = false;
			}

			if ($this->route->layout == 'default') {
				$layout = $this->context->parameters['website']['layout'];
			} else {
				$layout = $this->route->layout;
			}

			$pos = strpos($layout, "/");
			$module = lcfirst(substr($layout, 1, $pos - 1));

			if ($module === 'app') {
				$this->_layoutPath = $this->context->parameters['appDir'] . '/layouts/' . substr($layout, $pos + 1);
			} else if (!isset($this->context->parameters['modules'][$module]['path'])) {
				$this->_layoutPath = false;
			} else {
				$this->_layoutPath = $this->context->parameters['modules'][$module]['path'] . "/layouts/" . substr($layout, $pos + 1);
			}
		}

		return $this->_layoutPath === false ? NULL : $this->_layoutPath;
	}
}
