<?php 
echo "<?php\n";
?>
if(false){
<?php
foreach($accessDefinitions['roles'] as $roleName => $actions){
?>
    echo <?= $generator->generateString($roleName) ?>;
<?php    
}
?>
}
