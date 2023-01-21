<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Constant; // TODO REMOVE
use Application\Services\Formula\Node\Formula;
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
        // TODO parse input formula string
    }
    
    public function evaluate_formulas($students_data, $virtual_types): array {
        $results = [];
        
        foreach($students_data as $id=>$student_data){
            $results[$id] = $this->evaluate_formulas_for_student($student_data, $virtual_types);
        }
        
        return $results;
    }
    
    public function evaluate_formulas_for_student($student_data, $virtual_types): array {
        $virtual_types_results = [];
        $to_process = $virtual_types;
        while(true){
            $not_processed = [];
            foreach($to_process as $virtual_type) {
                /** @var Formula $formula*/
                $formula = unserialize($virtual_type->join_formula_object);
                $result = $formula->evaluate($student_data);
                
                if ($result !== null){
                    $student_data[$virtual_type->id] = $result;
                    $virtual_types_results[$virtual_type->id] = $result;
                }
                else {
                    $not_processed[] = $virtual_type;
                }
            }
            
            if (count($not_processed) == 0){
                break;
            }
            
            if (count($not_processed) == count($to_process)) {
                foreach($not_processed as $virtual_type){
                    $virtual_types_results[$virtual_type->id] = -1;
                }
                break;
            }
            $to_process = $not_processed;
        }
        
        return $virtual_types_results;
    }
}
