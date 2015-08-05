<pre>
    <?php
    spl_autoload_register(function($class) {
        $path = realpath('./' . str_replace('\\', '/', $class) . '.php');
        if ($path) {
            require_once($path);
        }
    });

    $project = new Phanalyzer\Dir('.');
    $project->skip(['.hg', '.hgtags',], false)
            ->read();

    $analyzer = new Phanalyzer\Phanalyzer($project);
    $analyzer->analyze();

    $filesCounts = $project->getFilesNumber(true);
    $linesCount = $project->getLinesNumber();

    ksort($filesCounts);
    ksort($linesCount);

    echo 'Files:<br>';
    var_export($filesCounts);
    echo '<br><br>Lines:<br>';
    var_export($linesCount);
    ?>
</pre>
