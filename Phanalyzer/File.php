<?php

/**
 * Dir
 * 
 * @author Alexander Batukhtin [mr.olorin@gmail.com]
 */

namespace Phanalyzer;

/**
 * File class
 */
class File extends \SplFileInfo
{

	const NO_EXT = '[no ext]';
	/**
	 * File name
	 * 
	 * @var string 
	 */
	private $name;
	
	/**
	 * Lines number
	 * 
	 * @var int 
	 */
	private $linesNum;

	/**
	 * Constructor
	 * 
	 * @param string $fileName File name
	 */
	public function __construct($fileName)
	{
		parent::__construct($fileName);
	}

	/**
	 * Set file name
	 * 
	 * @param string $name File name
	 * @return \Phanalyzer\File
	 */
	protected function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get file name
	 * 
	 * @api
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get file contents as string
	 * 
	 * @api
	 * 
	 * @return string
	 */
	public function getContents()
	{
		return file_get_contents($this->getRealPath());
	}
	
	/**
	 * Get file contents as array
	 * 
	 * @api
	 * 
	 * @return array
	 */
	public function getContentsAsArray()
	{
		return file($this->getRealPath());
	}

	/**
	 * Set lines number
	 * 
	 * @param int $linesNum Number of lines
	 * @return \Phanalyzer\File
	 */
	public function setLinesNumber($linesNum)
	{
		$this->linesNum = $linesNum;
		return $this;
	}

	/**
	 * Get lines number
	 * 
	 * @api
	 * 
	 * @return int
	 */
	public function getLinesNumber()
	{
		return $this->linesNum;
	}

}