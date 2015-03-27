import java.io.*;

public class LISTTests 
{
	private static final String EvaluationFileName="evaluation.percent.txt";

	private static int scorePercent = 0;

	static synchronized void setTaskEvaluation(int taskScorePercent) 
	{
		try {
		File f = new File(EvaluationFileName);
		if (f.exists())
			f.delete();
		PrintStream handle = new PrintStream(new FileOutputStream(EvaluationFileName));
		handle.print(Integer.toString(taskScorePercent));
		handle.close();
		} catch (Exception e) { e.printStackTrace(); } 
	}

	static synchronized void addTaskEvaluation(int partialTaskScorePercent) 
	{
		try {
		scorePercent += partialTaskScorePercent;
		setTaskEvaluation(scorePercent);
		} catch (Exception e) { e.printStackTrace(); } 
	}
			
}