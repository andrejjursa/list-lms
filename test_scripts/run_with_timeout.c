// run_with_timeout.c - runs args[2...] with the timeout args[1]

#include <stdio.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>
#include <string.h>
#include <signal.h>
#include <stdlib.h>
#include <sys/times.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <errno.h>

static int rv;
unsigned int ticks_per_second;

int test_timeout(int timeout, pid_t pid) {
	FILE *process_file;
	char file_path[80];
	unsigned long int utime = 0;
	unsigned long int cutime = 0;
	char stmp;
	unsigned long int itmp = 0;
	int ntmp = 0;
	unsigned int utmp = 0;
	char ctmp;
	sprintf(file_path, "/proc/%u/stat", pid);
	process_file = fopen(file_path, "r");
	if (process_file == NULL) {
		printf("FATAL ERROR: process statistics file not found, program probably crashed, terminating test run ...\n");
		return 2;
	}
	//                    1  2  3  4  5  6  7  8  9  10  11  12  13  14  15  16  | 1      2      3      4      5      6      7      8      9      10     11     12     13     14      15     16
	fscanf(process_file, "%d %s %c %d %d %d %d %d %u %lu %lu %lu %lu %lu %lu %lu", &ntmp, &stmp, &ctmp, &ntmp, &ntmp, &ntmp, &ntmp, &ntmp, &utmp, &itmp, &itmp, &itmp, &itmp, &utime, &itmp, &cutime);
	fclose(process_file);
	unsigned long int total = utime + cutime;
	double total_seconds = (double)total / (double)ticks_per_second;
	double timeout_seconds = (double)timeout / 1000.0;
	if (total_seconds > timeout_seconds) { return 1; }
	return 0;
}

// waits until the specified process terminates, returns 1 if timeout occurs
int wait_timeout(int timeout, pid_t pid)
{
  int status;
  long t = 0;
  do {
    usleep(100000);
    t+=100;
    int tmout = test_timeout(timeout, pid);
    if (tmout) { kill(pid, 9); return tmout; }
    if (t > 4L * timeout) { kill(pid, 9); return 1; }
    if (pid != waitpid(pid, &status, WNOHANG)) continue;
    if (WIFEXITED(status)) break;
  } while (1);
  rv = WEXITSTATUS(status);
  return 0;
}

int main(int argc, char **args)
{
  ticks_per_second = sysconf(_SC_CLK_TCK);
  pid_t pid;
  int timeout, i;

  if (argc < 3)
  {
    printf("usage: run_with_timeout timeout_ms prog [args...]\n");
    return 0;
  }

  sscanf(args[1], "%d", &timeout);

  for (i = 1; i < argc - 1; i++)
    args[i] = args[i + 1];
  args[argc - 1] = 0;

  if (!(pid = fork())) 
  {
    struct rlimit memlimit;
    memlimit.rlim_cur = 4294967296L;
    memlimit.rlim_max = 4294967296L;
    if (setrlimit(RLIMIT_AS, &memlimit) < 0)
    {
        printf("could not set memory limit, errno=%d\n", errno);
	return 0;
    }
    execvp(args[1], args+1);
  }
  else 
  {
    rv = 50;
    if (wait_timeout(timeout, pid) == 1)
      printf("\n**** TIMEOUT %d ms PASSED, PROCESS TERMINATED ****\n", timeout);
  }

  exit(rv);
}
