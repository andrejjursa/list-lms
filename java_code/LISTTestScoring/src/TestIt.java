
import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.Random;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author andrej
 */
public class TestIt {

    public static void main(String[] args) {
        String encryptPhrase = randomString();
        System.out.println(encryptPhrase);
        try {
            BufferedWriter bw = new BufferedWriter(new FileWriter("__list_encrypt_phrase.txt"));
            bw.write(encryptPhrase);
            bw.close();
        } catch (IOException ex) {
            Logger.getLogger(TestIt.class.getName()).log(Level.SEVERE, null, ex);
        }
        LISTTestScoring lts = new LISTTestScoring();
        lts.setScore("Test", 10.0, 35.0);
        lts.updateScore("Test 2 \\ test 1", 12.0, 25.0);
        lts.setScore("\"test\"", 0.5, 40.0);
        lts.updateScore("\"test\"", 25.8, 40.0);
    }
    
    private static String randomString() {
        StringBuilder sb = new StringBuilder();
        
        Random r = new Random();
        String aplhabet = "ABCDEFGHIJKLMNOPRSQTUVWXYZ0123456789";
        
        for (int i = 0; i < 8192; i++) {
            sb.append(aplhabet.charAt(r.nextInt(aplhabet.length())));
        }
        
        return sb.toString();
    }
}
