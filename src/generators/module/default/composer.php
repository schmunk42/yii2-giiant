<?php 
$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$name = str_replace('\\','/',$ns);
?>
{
    "name": "<?= $name ?>",
    "description": "Just generated"
}
