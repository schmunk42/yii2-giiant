<?php
namespace Codeception\Module;

// here you can define custom functions for CliGuy 

class CliHelper extends \Codeception\Module
{
    public function runShellCmd($command, $failNonZero = true)
    {
        $data = array();
        #exec($command, $data, $resultCode);
        exec("/app/yii 'migrate'", $data, $resultCode);
        $this->output = implode("\n", $data);
        if ($this->output === null) {
            \PHPUnit_Framework_Assert::fail("$command can't be executed");
        }
        if ($resultCode !== 0 && $failNonZero) {
            \PHPUnit_Framework_Assert::fail("Result code was $resultCode.\n\n" . $this->output);
        }
        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $this->output));
    }
}
