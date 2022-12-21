<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Addition;
use Application\Services\Formula\Node\Conjunction;
use Application\Services\Formula\Node\Constant;
use Application\Services\Formula\Node\Disjunction;
use Application\Services\Formula\Node\Division;
use Application\Services\Formula\Node\Equal;
use Application\Services\Formula\Node\Formula_node;
use Application\Services\Formula\Node\Greater;
use Application\Services\Formula\Node\Greater_or_equal;
use Application\Services\Formula\Node\Modulo;
use Application\Services\Formula\Node\Multiplication;
use Application\Services\Formula\Node\Negation;
use Application\Services\Formula\Node\Not_equal;
use Application\Services\Formula\Node\Smaller;
use Application\Services\Formula\Node\Smaller_or_equal;
use Application\Services\Formula\Node\Subtraction;
use Application\Services\Formula\Node\Ternary_operator;
use Application\Services\Formula\Node\Variable;

class NodeFactory
{
    public function getAddition(Formula_node $left, Formula_node $right): Addition
    {
        return new Addition($left, $right);
    }
    
    public function getConjunction(Formula_node $left, Formula_node $right): Conjunction
    {
        return new Conjunction($left, $right);
    }
    
    public function getConstant(float $value): Constant
    {
        return new Constant($value);
    }
    
    public function getDisjunction(Formula_node $left, Formula_node $right): Disjunction
    {
        return new Disjunction($left, $right);
    }
    
    public function getDivision(Formula_node $left, Formula_node $right): Division
    {
        return new Division($left, $right);
    }
    
    public function getEqual(Formula_node $left, Formula_node $right): Equal
    {
        return new Equal($left, $right);
    }
    
    public function getGreater(Formula_node $left, Formula_node $right): Greater
    {
        return new Greater($left, $right);
    }
    
    public function getGreaterEqual(Formula_node $left, Formula_node $right): Greater_or_equal
    {
        return new Greater_or_equal($left, $right);
    }
    
    public function getModulo(Formula_node $left, Formula_node $right): Modulo
    {
        return new Modulo($left, $right);
    }
    
    public function getMultiplication(Formula_node $left, Formula_node $right): Multiplication
    {
        return new Multiplication($left, $right);
    }
    
    public function getNegation(Formula_node $formula): Negation
    {
        return new Negation($formula);
    }
    
    public function getNotEqual(Formula_node $left, Formula_node $right): Not_equal
    {
        return new Not_equal($left, $right);
    }
    
    public function getSmaller(Formula_node $left, Formula_node $right): Smaller
    {
        return new Smaller($left, $right);
    }
    
    public function getSmallerEqual(Formula_node $left, Formula_node $right): Smaller_or_equal
    {
        return new Smaller_or_equal($left, $right);
    }
    
    public function getSubtraction(Formula_node $left, Formula_node $right): Subtraction
    {
        return new Subtraction($left, $right);
    }
    
    public function getTernary(Formula_node $left, Formula_node $right, Formula_node $condition): Ternary_operator
    {
        return new Ternary_operator($left, $right, $condition);
    }
    
    public function getVariable(string $name, int $type_id): Variable
    {
        return new Variable($name, $type_id);
    }
}