<?php

namespace schmunk42\giiant\crud\callbacks\base;

class Callback
{
    static public function false()
    {
        return function () {
            return false;
        };
    }
}
