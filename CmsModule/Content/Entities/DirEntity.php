<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CmsModule\Content\Entities;

use Venne;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\Strings;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @ORM\Entity(repositoryClass="\CmsModule\Content\Repositories\DirRepository")
 * @ORM\Table(name="directory")
 * @ORM\HasLifecycleCallbacks
 */
class DirEntity extends BaseFileEntity
{

	/**
	 * @var ArrayCollection|DirEntity[]
	 * @ORM\OneToMany(targetEntity="DirEntity", mappedBy="parent")
	 */
	protected $children;

	/**
	 * @var ArrayCollection|FileEntity[]
	 * @ORM\OneToMany(targetEntity="FileEntity", mappedBy="parent")
	 */
	protected $files;


	public function __construct()
	{
		parent::__construct();

		$this->children = new ArrayCollection;
		$this->files = new ArrayCollection;
	}


	public function __toString()
	{
		$ret = array();

		$parent = $this;
		while ($parent) {
			$ret[] = $parent->name;
			$parent = $parent->parent;
		}

		return implode('/', array_reverse($ret));
	}


	/**
	 * @ORM\PreFlush()
	 */
	public function preUpdate()
	{
		$protectedPath = $this->protectedDir . '/' . $this->path;
		$publicPath = $this->publicDir . '/' . $this->path;

		if ($this->_oldPath) {
			$oldProtectedPath = $this->protectedDir . '/' . $this->_oldPath;
			$oldPublicPath = $this->publicDir . '/' . $this->_oldPath;

			if (file_exists($oldProtectedPath)) {
				rename($oldProtectedPath, $protectedPath);
			}
			if (file_exists($oldPublicPath)) {
				rename($oldPublicPath, $publicPath);
			}
			return;
		}

		umask(0000);
		if (!file_exists($protectedPath)) {
			@mkdir($protectedPath, 0777, true);
		}

		if (!file_exists($publicPath)) {
			@mkdir($publicPath, 0777, true);
		}
	}


	/**
	 * @ORM\PreRemove()
	 */
	public function preRemove()
	{
		foreach ($this->getChildren() as $dir) {
			$dir->preRemove();
		}

		foreach ($this->getFiles() as $file) {
			$file->preRemove();
		}

		$protectedPath = $this->protectedDir . '/' . $this->path;
		$publicPath = $this->publicDir . '/' . $this->path;

		@rmdir($protectedPath);
		@rmdir($publicPath);
	}


	/**
	 * @param string $children
	 */
	public function setChildren(ArrayCollection $children)
	{
		$this->children = $children;
	}


	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getChildren()
	{
		return $this->children;
	}


	public function setFiles($files)
	{
		$this->files = $files;
	}


	public function getFiles()
	{
		return $this->files;
	}


	public function generatePath()
	{
		parent::generatePath();

		foreach ($this->children as $item) {
			$item->generatePath();
		}

		foreach ($this->files as $item) {
			$item->generatePath();
		}
	}
}
