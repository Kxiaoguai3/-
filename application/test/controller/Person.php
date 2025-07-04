<?php

namespace app\test\controller;

use traits\controller;

class Person
{
    public function eat()
    {
        return 'eat~';
    }

    public function sayCiallo($name = '赵浩然')
    {

        return $name.'Ciallo～(∠・ω< )⌒★';
    }
}