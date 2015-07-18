<?php
/**
 * Created by PhpStorm.
 * User: andrej
 * Date: 3.5.2015
 * Time: 16:02
 */

class cli_progress_bar {

    protected $state = 0;
    protected $states = array('|', '/', '-', '\\');
    protected $bar_length = 80;
    protected $done = 0;
    protected $from = 100;

    public function init($maximum, $bar_size = 50) {
        $this->from = $maximum;
        $this->bar_length = $bar_size;
        $this->state = 0;
        $this->done = 0;
    }

    public function tick() {
        $this->state = ($this->state + 1) % count($this->states);
        $this->print_bar();
    }

    public function finish() {
        $this->print_bar();
        echo "\n";
    }

    public function increment($by = 1) {
        $this->done += $by;
        $this->print_bar();
    }

    protected function  print_bar() {
        $to_print = '';
        $to_print .= $this->states[$this->state] . ' ';
        $total_done = $this->done >= 0 ? ($this->done <= $this->from ? $this->done : $this->from) : 0;
        $done_length = round($total_done / $this->from * ($this->bar_length - 2));
        $rest_length = $this->bar_length - 2 - $done_length;
        $to_print .= '[';
        $to_print .= str_repeat('|', $done_length);
        $to_print .= str_repeat('.', $rest_length);
        $to_print .= '] ';
        $percent = (double)$total_done / (double)$this->from * 100.0;
        $percent_number = number_format($percent, 3, '.', '') . '%';
        $to_print .= str_pad($percent_number, 8);
        $this->back_to_first_column();
        echo $to_print;
    }

    protected function back_to_first_column() {
        echo chr(27) . '[0G'; // Set cursor to first column
    }

    protected function up_n_lines($n) {
        echo chr(27) . '[' . $n . 'A';
    }

    public function print_text($text, $tick = FALSE) {
        $this->back_to_first_column();
        echo str_repeat(' ', $this->bar_length + 11);
        $this->back_to_first_column();
        echo $text . "\n";
        if ($tick) {
            $this->tick();
        } else {
            $this->print_bar();
        }
    }

}