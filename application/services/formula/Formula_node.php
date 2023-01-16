<?php

interface Formula_node
{
    public function compute();
    public function evaluate();
    public function toString();
}