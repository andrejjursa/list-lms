
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashMap;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.logging.Level;
import java.util.logging.Logger;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author andrej
 */
public class LISTTestScoring {
    
    private String encryptPhrase = "";
    
    private final HashMap<String, Score> scoringTable = new HashMap<String, Score>();
    
    public LISTTestScoring() {
        if (!loadEncrypthPhrase()) {
            System.err.println("Can't found pre-generated encryption phrase source file. Terminating test execution.");
            System.exit(10001);
        }
    }
    
    private boolean loadEncrypthPhrase() {
        try {
            FileReader fr = new FileReader("__list_encrypt_phrase.txt");
            BufferedReader br = new BufferedReader(fr);
            encryptPhrase = br.readLine();
            if (encryptPhrase == null || encryptPhrase.length() != 8192) {
                return false;
            }
            return true;
        } catch (FileNotFoundException ex) {
            System.err.println(ex.getMessage());
            return false;
        } catch (IOException ex) {
            System.err.println(ex.getMessage());
            return false;
        }
    }
    
    public void updateScore(String scoreName, double scoreToAdd, double scoreMaximum) {
        if (scoringTable.containsKey(scoreName)) {
            scoringTable.get(scoreName).current += scoreToAdd;
            scoringTable.get(scoreName).maximum = scoreMaximum;
            if (scoringTable.get(scoreName).current <= 0.0) {
                scoringTable.get(scoreName).current = 0.0;
            } else if (scoringTable.get(scoreName).current >= scoreMaximum) {
                scoringTable.get(scoreName).current = scoreMaximum;
            }
        } else {
            Score newScore = new Score(scoreToAdd <= scoreMaximum ? (scoreToAdd >= 0.0 ? scoreToAdd : 0.0) : scoreMaximum, scoreMaximum);
            scoringTable.put(scoreName, newScore);
        }
        
        writeScore();
    }
    
    public void setScore(String scoreName, double scoreToSet, double scoreMaximum) {
        if (scoringTable.containsKey(scoreName)) {
            scoringTable.remove(scoreName);
        }
        Score newScore = new Score(scoreToSet <= scoreMaximum ? (scoreToSet >= 0.0 ? scoreToSet : 0.0) : scoreMaximum, scoreMaximum);
        scoringTable.put(scoreName, newScore);
        
        writeScore();
    }
    
    private String getJSONscoring() {
        StringBuilder sb = new StringBuilder();
        
        sb.append('{');
        boolean first = true;
        for (String scoreName: scoringTable.keySet()) {
            if (!first) { sb.append(','); }
            sb.append('{');
            sb.append("\"name\":");
            sb.append('"');
            sb.append(fixWrongChars(scoreName));
            sb.append('"');
            sb.append(',');
            sb.append("\"current\":");
            sb.append(scoringTable.get(scoreName).current);
            sb.append(',');
            sb.append("\"maximum\":");
            sb.append(scoringTable.get(scoreName).maximum);
            sb.append('}');
            first = false;
        }
        sb.append('}');
        
        return sb.toString();
    }
    
    private String fixWrongChars(String text) {
        return text.replace("\\", "\\\\").replace("\"", "\\\"");
    }
    
    private String getMD5hash(String text) {
        try {
            MessageDigest md = MessageDigest.getInstance("MD5");
            byte[] bytes = md.digest(text.getBytes());
             StringBuilder sb = new StringBuilder();
            for (byte b : bytes) {
                sb.append(String.format("%02x", b & 0xff));
            }
            return sb.toString();
        } catch (NoSuchAlgorithmException ex) {
            System.err.println(ex.getMessage());
        }
        return "";
    }
    
    private String encode(String text) {
        String md5 = getMD5hash(text);
        StringBuilder sb = new StringBuilder();
        sb.append(Base64.encode(encodeSingleLine(md5)));
        sb.append("\n");
        sb.append(Base64.encode(encodeSingleLine(text, 32)));
        return sb.toString();
    }
    
    private String encodeSingleLine(String text) {
        return encodeSingleLine(text, 0);
    }
    
    private String encodeSingleLine(String text, int offset) {
        StringBuilder sb = new StringBuilder();
        
        for (int i = 0; i < text.length(); i++) {
            int b = (int)text.charAt(i) ^ (int)encryptPhrase.charAt(offset + i % encryptPhrase.length());
            sb.append(Character.toChars(b));
        }
        
        return sb.toString();
    }
    
    private void writeScore() {
        try {
            FileWriter fw = new FileWriter("__list_score.txt");
            BufferedWriter bw = new BufferedWriter(fw);
            
            String JSONencoded = encode(getJSONscoring());
            
            bw.write(JSONencoded);
            
            bw.close();
        } catch (IOException ex) {
            System.err.println(ex.getMessage());
        }
    }
}

class Score {
    public double current = 0.0;
    public double maximum = 0.0;

    public Score(double attrCurrent, double attrMaximum) {
        current = attrCurrent;
        maximum = attrMaximum;
    }    
}

class Base64 {
 
    private static final String base64code = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            + "abcdefghijklmnopqrstuvwxyz" + "0123456789" + "+/";
 
    private static final int splitLinesAt = 76;
 
    public static byte[] zeroPad(int length, byte[] bytes) {
        byte[] padded = new byte[length]; // initialized to zero by JVM
        System.arraycopy(bytes, 0, padded, 0, bytes.length);
        return padded;
    }
 
    public static String encode(String string) {
 
        String encoded = "";
        byte[] stringArray;
        try {
            stringArray = string.getBytes("UTF-8");  // use appropriate encoding string!
        } catch (Exception ignored) {
            stringArray = string.getBytes();  // use locale default rather than croak
        }
        // determine how many padding bytes to add to the output
        int paddingCount = (3 - (stringArray.length % 3)) % 3;
        // add any necessary padding to the input
        stringArray = zeroPad(stringArray.length + paddingCount, stringArray);
        // process 3 bytes at a time, churning out 4 output bytes
        // worry about CRLF insertions later
        for (int i = 0; i < stringArray.length; i += 3) {
            int j = ((stringArray[i] & 0xff) << 16) +
                ((stringArray[i + 1] & 0xff) << 8) + 
                (stringArray[i + 2] & 0xff);
            encoded = encoded + base64code.charAt((j >> 18) & 0x3f) +
                base64code.charAt((j >> 12) & 0x3f) +
                base64code.charAt((j >> 6) & 0x3f) +
                base64code.charAt(j & 0x3f);
        }
        // replace encoded padding nulls with "="
        return splitLines(encoded.substring(0, encoded.length() -
            paddingCount) + "==".substring(0, paddingCount));
 
    }
    public static String splitLines(String string) {
 
        String lines = "";
        for (int i = 0; i < string.length(); i += splitLinesAt) {
 
            lines += string.substring(i, Math.min(string.length(), i + splitLinesAt));
            //lines += "\r\n";
 
        }
        return lines;
 
    }
    public static void main(String[] args) {
 
        for (int i = 0; i < args.length; i++) {
 
            System.err.println("encoding \"" + args[i] + "\"");
            System.out.println(encode(args[i]));
 
        }
 
    }
 
}