<?php

/**
 * Phanalyzer
 * 
 * @author Alexander Batukhtin [mr.olorin@gmail.com]
 */

namespace Phanalyzer;

/**
 * Phanalyzer class
 */
class Phanalyzer
{

    /**
     * Project instance
     * 
     * @var \Phanalyzer\File 
     */
    private $project;

    /**
     * Constructor
     * 
     * @param \Phanalyzer\File $project Top-level directory
     */
    public function __construct(\Phanalyzer\File $project)
    {
        $this->set($project);
        return $this;
    }

    /**
     * Set project
     * 
     * @param \Phanalyzer\File $project Top-level directory
     * @return \Phanalyzer\Phanalyzer
     */
    public function set(\Phanalyzer\File $project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * Analyze current project
     * 
     * @api
     * 
     * @return \Phanalyzer\Phanalyzer
     */
    public function analyze()
    {
        foreach ($this->project->getFiles(true) as $file) {
            $this->analyzeFile($file);
        }
        return $this;
    }

    /**
     * Analyze file
     * 
     * @param \Phanalyzer\File $file File
     * @return \Phanalyzer\Phanalyzer
     */
    private function analyzeFile(\Phanalyzer\File $file)
    {
        $file->setLinesNumber($this->countLines($file));
        return $this;
    }

    /**
     * Count lines in a file
     * 
     * @param \Phanalyzer\File $file File
     * @return int
     */
    private function countLines(\Phanalyzer\File $file)
    {
        return count($file->getContentsAsArray()) + 1;
    }

}
