<?php
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\'))
    {
    	$namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        
        if ($namespace == "SocialTrait")
        {
	    	$fileName = "classes/traits/";
	    }
        else
        {
            $fileName = "classes/";
        }
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className);

    if ($namespace == "SocialKit")
    {
    	$fileName .= ".class.php";
    }
    elseif ($namespace == "SocialTrait")
    {
    	$fileName .= ".trait.php";
    }

    if (file_exists($fileName))
    {
        require $fileName;
    }
}

spl_autoload_register('autoload');