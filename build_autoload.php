<?php
    function searchInDirectory($dir)
    {
        $classes = array();
        $handle = opendir($dir);
        while ($f = readdir($handle))
        {
            if ($f != '.' && $f != '..')
            {
                if (is_dir($dir.$f))
                {
                    $classes = array_merge($classes, searchInDirectory($dir.$f.'/'));
                }
                else
                {
                    if (substr($f, -10) == '.class.php')
                    {
                        $classes[strtolower(substr($f, 0, -10))] = substr($dir.$f, 2);
                    }
                }
            }
        }
        closedir($handle);
        return $classes;
    }
    $classes = var_export(searchInDirectory('./lib/'), true);
    file_put_contents('lib/autoload.php', preg_replace('`array \(.+\)`sU', $classes, file_get_contents('lib/autoload.php'))); 
