<?php

namespace Application\ModelTraits;

trait JSONFieldTrait
{
    public function _jsonDecode($field)
    {
        if (is_string($this->{$field})) {
            $data = @json_decode($this->{$field}, true);
            if (is_array($data) && json_last_error() === JSON_ERROR_NONE) {
                $this->{$field} = $data;
            }
        }
    }
    
    public function _jsonEncode($field)
    {
        if (is_array($this->{$field})) {
            $data = @json_encode($this->{$field});
            if (is_string($data) && json_last_error() === JSON_ERROR_NONE) {
                $this->{$field} = $data;
            }
        }
    }
}