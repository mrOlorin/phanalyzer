<?php

/**
 * Dir
 * 
 * @author Alexander Batukhtin [mr.olorin@gmail.com]
 */

namespace Phanalyzer;

use Phanalyzer\File;

/**
 * Directory class
 */
class Dir extends File
{

	/**
	 * Directories list
	 * 
	 * @var array
	 */
	private $dirs = [];

	/**
	 * Subdirectories list
	 * 
	 * @var array
	 */
	private $subDirs = [];

	/**
	 * Directories number
	 * 
	 * @var int
	 */
	private $dirsNum;
	
	/**
	 * Subdirectories number
	 * 
	 * @var int
	 */
	private $subDirsNum;
	
	/**
	 * Files list
	 * 
	 * @var array
	 */
	private $files = [];
	
	/**
	 * Files number
	 * 
	 * Keys are extensions
	 * 
	 * @var array 
	 */
	private $filesNum = [];

	/**
	 * Files number
	 * 
	 * Keys are extensions
	 * 
	 * @var array 
	 */
	private $subFilesNum = [];
	
	/**
	 * Filenames that should be skipped
	 * 
	 * @var array
	 */
	private $skip = [];
	
	/**
	 * Regexes in accordance to which files should be skipped
	 * 
	 * @var array 
	 */
	private $skipRegex = [];
	
	/**
	 * Lines number
	 * 
	 * Keys are extensions
	 * 
	 * @var array 
	 */
	private $linesNum = [];

	/**
	 * Constructor
	 * 
	 * @param string $dirName Directory name
	 * @throws \Exception
	 */
	public function __construct($dirName)
	{
		if (!realpath($dirName)) {
			throw new \FileNotFoundException('Not real path');
		}
		parent::__construct($dirName);
	}

	/**
	 * Add directory
	 * 
	 * @param \Phanalyzer\Dir $dir Directory
	 * @return \Phanalyzer\Dir
	 */
	public function addDir(\Phanalyzer\Dir $dir)
	{
		$this->dirs[] = $dir;
		return $this;
	}

	/**
	 * Get directories
	 * 
	 * @return array
	 */
	public function getDirs()
	{
		return $this->dirs;
	}

	/**
	 * Get subdirectories
	 * 
	 * @return array
	 */
	public function getSubDirs()
	{
		if (empty($this->subDirs)) {
			$this->subDirs = $this->dirs;
			foreach ($this->dirs as $dir) {
				$this->subDirs = array_merge($this->subDirs, $dir->getSubDirs());
			}
		}
		return $this->subDirs;
	}

	/**
	 * Count directories
	 * 
	 * @return \Phanalyzer\Dir
	 */
	public function countDirs()
	{
		foreach ($this->dirs as $subDir) {
			$this->dirsNum++;
			$this->subDirsNum++;
			if (empty($subDir->subDirsNum)) {
				$this->subDirsNum += $subDir->getDirsNumber(true);
			}
		}
		return $this;
	}

	/**
	 * Get directories number
	 * 
	 * @param bool $withSubDirs With subdirectories
	 * @return int
	 */
	public function getDirsNumber($withSubDirs = true)
	{
		if (empty($this->subDirsNum)) {
			$this->countDirs();
		}
		return (int) ($withSubDirs ? $this->subDirsNum : $this->dirsNum);
	}

	/**
	 * Add file 
	 * 
	 * @param \Phanalyzer\File $file File
	 * @return \Phanalyzer\Dir
	 */
	public function addFile(\Phanalyzer\File $file)
	{
		$this->files[] = $file;
		return $this;
	}

	/**
	 * Get files
	 * 
	 * @param bool $withSubDirs With subdirectories
	 * @return array
	 */
	public function getFiles($withSubDirs = true)
	{
		if ($withSubDirs) {
			$files = $this->files;
			foreach ($this->dirs as $dir) {
				$files = array_merge($files, $dir->getFiles(true));
			}
			return $files;
		} else {
			return $this->files;
		}
	}

	/**
	 * Count files
	 * 
	 * @return \Phanalyzer\Dir
	 */
	public function countFiles()
	{
		foreach ($this->files as $file) {
			$this->filesNum[$file->getExtension() ?: File::NO_EXT] ++;
		}
		$this->subFilesNum = $this->filesNum;
		foreach ($this->dirs as $subDir) {
			foreach ($subDir->getFilesNumber(true) as $ext => $count) {
				$this->subFilesNum[$ext] += $count;
			}
		}
		return $this;
	}

	/**
	 * Get files number
	 * 
	 * @param bool $withSubDirs With subdirectories
	 * @return array
	 */
	public function getFilesNumber($withSubDirs = true)
	{
		if (empty($this->subFilesNum)) {
			$this->countFiles();
		}
		return $withSubDirs ? $this->subFilesNum : $this->filesNum;
	}

	/**
	 * Get lines number
	 * 
	 * @return array
	 */
	public function getLinesNumber()
	{
		if (empty($this->linesNum)) {
			foreach ($this->getFiles() as $file) {
				$this->linesNum[$file->getExtension() ?: File::NO_EXT] += $file->getLinesNumber();
			}
		}
		return $this->linesNum;
	}

	/**
	 * Skip files that are corresponds to the $pattern
	 * 
	 * @api
	 * 
	 * @param string $pattern Pattern
	 * @param bool $isRegex Is regex
	 * @return \Phanalyzer\Dir
	 */
	public function skip($pattern, $isRegex = false)
	{
		if (!is_array($pattern)) {
			$pattern = array($pattern);
		}
		if ($isRegex) {
			$this->skipRegex = array_merge($this->skipRegex, $pattern);
		} else {
			$this->skip = array_merge($this->skip, $pattern);
		}
		return $this;
	}

	/**
	 * Read
	 * 
	 * @api
	 * 
	 * @return \Phanalyzer\Dir
	 */
	public function read()
	{
		$this->add($this);
		return $this;
	}

	/**
	 * Check whether file has to be skipped or not
	 * 
	 * @param \SplFileInfo $fileInfo File
	 * @return boolean
	 */
	private function checkSkip(\SplFileInfo $fileInfo)
	{
		if (in_array($fileInfo->getBasename(), $this->skip)) {
			return true;
		}
		foreach ($this->skipRegex as $pattern) {
			if (preg_match($pattern, $fileInfo->getBasename())) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Iterate over directory and subdirectories
	 * 
	 * @param \SplFileInfo $dir Directory
	 * @return \Phanalyzer\Dir
	 */
	private function add(\SplFileInfo $dir)
	{
		foreach (new \DirectoryIterator($dir->getRealPath()) as $fileInfo) {
			if ($fileInfo->isDot() || $this->checkSkip($fileInfo)) {
				continue;
			} elseif ($fileInfo->isDir()) {
				$subDir = new Dir($fileInfo->getRealPath());
				$dir->addDir($subDir);
				$this->add($subDir);
			} else {
				$file = new File($fileInfo->getRealPath());
				$dir->addFile($file);
			}
		}
		return $this;
	}

}
