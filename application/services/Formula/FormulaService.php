<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Constant;
use Application\Services\Formula\Node\Formula_node;

class FormulaService
{
    /** @var NodeFactory */
    private $nodeFactory;
    
    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }
    
    public function build($input, $types): ?Formula_node
    {
        return $this->nodeFactory->getAddition(new Constant(5), new Constant(10));
        // TODO parse input formula string here
    }
    
    // DO NOT REMOVE!!!
//    public function parseFormula($formula, $types) {
//        $operatory = ['+', '-', '×', '/', '%', '&lt;=', '&gt;=', '&lt;', '&gt;', '==', '!=', '∧', '∨', '?', ':', '¬'];
//
//        // Zabezpecenie, ze vsetky prvky formuly su oddelene
//        $formula = str_replace(["<p>", "</p>", "<span>", "</span>"], "", $formula);
//        $formula = str_replace("(", " ( ", $formula);
//        $formula = str_replace(")", " ) ", $formula);
//        foreach($operatory as $operator) {
//            $formula = str_replace($operator, " ".$operator." ", $formula);
//        }
//        $formula = preg_replace("(&lt; =)", " &lt;= ", $formula);
//        $formula = preg_replace("(&gt; =)", " &gt;= ", $formula);
//
//        $ary = array_filter(explode(" ", $formula));
//        // stacks
//        $zatvorky = new Stack();
//        $vals = new Stack();
//        $znamienka = new Stack();
//        foreach($ary as $item) {
//
//            if ($item == "("){
//                $zatvorky->push($item);
//            } elseif (in_array($item, $operatory)) {
//                $znamienka->push($item);
//            } elseif ($item == ")") {
//                if ($zatvorky->isEmpty() || $vals->isEmpty() || $znamienka->isEmpty()) {
//                    echo "1";
//                    return NULL;
//                }
//                $right = $vals->pop();
//                if (is_string($right)) {
//                    if (is_numeric($right) != 1){
//                        $right = str_replace("_", " ", $right);
//                        $right = new Variable($right, $types[$right]);
//                    } else {
//                        $right = new Constant((float) $right);
//                    }
//                }
//                $operator = $znamienka->pop();
//                $zatvorky->pop();
//
//                if ($operator == "¬") {
//                    $vals->push(new Negation($right));
//                    continue;
//                }
//
//                if ($operator == ":") {
//                    if ($znamienka->isEmpty() || $znamienka->topElement() != "?" || $vals->size() < 2){
//                        echo "2";
//                        return NULL;
//                    }
//                    $left = $vals->pop();
//                    if (is_string($left)){
//                        if (is_numeric($left) != 1){
//                            $left = str_replace("_", " ", $left);
//                            $left = new Variable($left, $types[$left]);
//                        } else {
//                            $left = new Constant((float) $left);
//                        }
//                    }
//                    $condition = $vals->pop();
//                    if (is_string($condition)) {
//                        return null;
//                    }
//                    $znamienka->pop();
//                    $vals->push(new Ternary_operator($left, $right, $condition));
//                    continue;
//                }
//
//                if ($vals->isEmpty()) {
//                    echo "3";
//                    return NULL;
//                }
//                $left = $vals->pop();
//
//                if (is_string($left)){
//                    if (is_numeric($left) != 1){
//                        $left = str_replace("_", " ", $left);
//                        $left = new Variable($left, $types[$left]);
//                    } else {
//                        $left = new Constant((float) $left);
//                    }
//                }
//
//                switch ($operator) {
//                    case "+":
//                        $vals->push(new Addition($left, $right));
//                        break;
//                    case "-":
//                        $vals->push(new Subtraction($left, $right));
//                        break;
//                    case "×":
//                        $vals->push(new Multiplication($left, $right));
//                        break;
//                    case "/":
//                        // TODO if $right === 0 throw exception
//                        $vals->push(new Division($left, $right));
//                        break;
//                    case "%":
//                        $vals->push(new Modulo($left, $right));
//                        break;
//                    case "&lt;":
//                        $vals->push(new Smaller($left, $right));
//                        break;
//                    case "&gt;":
//                        $vals->push(new Greater($left, $right));
//                        break;
//                    case "&lt;=":
//                        $vals->push(new Smaller_or_equal($left, $right));
//                        break;
//                    case "&gt;=":
//                        $vals->push(new Greater_or_equal($left, $right));
//                        break;
//                    case "==":
//                        $vals->push(new Equal($left, $right));
//                        break;
//                    case "!=":
//                        $vals->push(new Not_equal($left, $right));
//                        break;
//                    case "∧":
//                        $vals->push(new Conjunction($left, $right));
//                        break;
//                    case "∨":
//                        $vals->push(new Disjunction($left, $right));
//                        break;
//                }
//
//            } else {
//                if ($item == "_"){
////                    echo "4";
//                    return NULL;
//                }
//                $vals->push($item); // constanta alebo premenna
//            }
//
//        }
//        $result = $vals->pop();
//
//        if (!$zatvorky->isEmpty() || !$vals->isEmpty() || !$znamienka->isEmpty()) {
////            echo "5";
//            return NULL;
//        }
//
//        return $result;
//    }
}