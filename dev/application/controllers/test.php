<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->load->database();
    }
    
    public function index() {
        $post = new Post();
        $post->get();
        foreach ($post as $p) {
            echo $p->title;
            echo '<br />';
            echo $p->text;
            echo '<br />';
            echo $p->id . ' / ' . $p->created . ' / ' . $p->updated;
            $p->comment->get();
            $p->tag->get();
            echo '<br />Tags: ';
            foreach ($p->tag as $tag) {
                echo ' ' . $tag->tag . ' ';
            }
            foreach ($p->comment as $comment) {
                echo '<br />';
                echo $comment->text;
            }
            //echo '<br /><pre>' . print_r($p, true) . '</pre>';
            echo '<hr />';
        }
        /*$new_post = new Post();
        $new_post->from_array(array(
            'title' => 'Pokus 3',
            'text' => 'Lorem ipsum a tak dalej ...',
        ));
        $new_post->save();*/
    }
    
}

?>