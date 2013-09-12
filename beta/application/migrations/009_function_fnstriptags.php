<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_function_fnstriptags extends CI_Migration {

    public function up() {
        $this->db->query('DROP FUNCTION IF EXISTS fnStripTags');
        $this->db->query('CREATE FUNCTION fnStripTags( Dirty varchar(65535) )  
 RETURNS varchar(65535) CHARSET utf8  
 DETERMINISTIC   
 BEGIN  
   DECLARE iStart, iEnd, iLength int;  
   WHILE Locate( \'<\', Dirty ) > 0 And Locate( \'>\', Dirty, Locate( \'<\', Dirty )) > 0 DO  
     BEGIN  
       SET iStart = Locate( \'<\', Dirty ), iEnd = Locate( \'>\', Dirty, Locate(\'<\', Dirty ));  
       SET iLength = ( iEnd - iStart) + 1;  
       IF iLength > 0 THEN  
         BEGIN  
           SET Dirty = Insert( Dirty, iStart, iLength, \'\');  
         END;  
       END IF;  
     END;  
   END WHILE;  
   RETURN Dirty;  
 END;');
    }
    
    public function down() {
        $this->db->query('DROP FUNCTION IF EXISTS fnStripTags');
    }
    
}