<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Addition;
use Application\Services\Formula\Node\Conjunction;
use Application\Services\Formula\Node\Constant;
use Application\Services\Formula\Node\Disjunction;
use Application\Services\Formula\Node\Division;
use Application\Services\Formula\Node\Equal;
use Application\Services\Formula\Node\Formula;
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
use Cassandra\Set;
use Course;
use Task_set_type;

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
    
    public function evaluateWithDependencies($course_id,$formula_task_set_type_id,Formula_node $formula, &$variables,$task_set_types = null): float {
        if (in_array($formula_task_set_type_id,$variables)) return $variables[$formula_task_set_type_id];
        if ($task_set_types == null) $task_set_types = $this->get_virtual_task_set_types($course_id);
        $dependsOn = $this->getFormulaDependencies($formula);
        foreach ($dependsOn as $depends_id) {
            if (array_key_exists($depends_id,$variables)) continue;
            $child_formula = null;
            foreach ($task_set_types as $type) {
                if ($type->id == $depends_id) {
                    $child_formula =unserialize($type->join_formula_object);
                }
            }
            //should not ever happen
            //if ($child_formula == null) continue;
            $result = $this->evaluateWithDependencies($course_id,$depends_id,$child_formula,$variables,$task_set_types);
            $variables[$depends_id] = $result;
        }
        return $formula->evaluate($variables);
    }
    
    public function hasDependencyLoops($course_id,$formula_task_set_type_id,$formula,$task_set_types = null): bool {
        if ($task_set_types == null) $task_set_types = $this->get_virtual_task_set_types($course_id);
        return $this->hasDependencyLoopsInternal($task_set_types,$formula_task_set_type_id,$formula);
    }
    
    private function hasDependencyLoopsInternal($task_set_types,$formula_task_set_type_id,Formula_node $formula,$task_set_type_id_used = []): bool
    {
        //echo "LOOPCHECK begin: " . $formula->toString() . "<br>";
        $task_set_type_id_used[] = $formula_task_set_type_id;
        $dependsOn = $this->getFormulaDependencies($formula);
        //echo "LOOPCHECK depends: ";print_r($dependsOn);echo "<br>";
        foreach ($dependsOn as $depends_id) {
            if (in_array($depends_id,$task_set_type_id_used)) {
                //echo "LOOPCHECK foundLoop: ";print_r($task_set_type_id_used);echo " has " . $depends_id . "<br>";
                return true;
            }
            $child_formula = null;
            foreach ($task_set_types as $type) {
                if ($type->id == $depends_id) {
                    $child_formula = unserialize($type->join_formula_object);
                }
            }
            if ($child_formula == null) continue;
            if ($this->hasDependencyLoopsInternal($task_set_types,$depends_id,$child_formula,$task_set_type_id_used)) {
                //echo "LOOPCHECK childLoop in: " . $depends_id . "<br>";
                return true;
            }
        }
        //echo "LOOPCHECK ok end: <br>";
        return false;
    }
    
    public function getFormulaDependencies($formula): array {
        $toProcess = [];
        $toProcess[] = $formula;
        $dependsOn = [];
        while (count($toProcess) > 0) {
            $node = array_pop($toProcess);
            if ($node instanceof Variable) {
                if (!in_array($node->type_id,$dependsOn)) {
                    $dependsOn[] = $node->type_id;
                }
            } elseif ($node instanceof Ternary_operator) {
                $toProcess[] = $node->left;
                $toProcess[] = $node->right;
                $toProcess[] = $node->condition;
            } elseif ($node instanceof Formula) {
                $toProcess[] = $node->left;
                $toProcess[] = $node->right;
            } elseif ($node instanceof Negation) {
                $toProcess[] = $node->original_formula;
            }
        }
        return $dependsOn;
    }
    
    private function get_virtual_task_set_types($course_id) : Task_set_type
    {
        $course = new Course();
        $course->get_by_id($course_id);
        $course->task_set_type
            ->include_join_fields()
            ->where('virtual', 1)
            ->get();
        
        return $course->task_set_type;
    }
}