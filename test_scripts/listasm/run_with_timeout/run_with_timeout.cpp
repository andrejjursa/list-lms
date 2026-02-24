#include <stdio.h>
#include <string.h>
#include <stdint.h>
#include <Windows.h>

int main(int argc, const char** argv)
{
	int timeout;

	if (argc < 2)
	{
		printf("usage: run_with_timeout program_to_run [arguments...]\n (will use value from timeout.txt file)\n");
		return 0;
	}

	FILE* f;
	errno_t er = fopen_s(&f, "timeout.txt", "r");
	if (er != 0)
	{
		fprintf(stderr, "could not find timeout specification file\n");
		return 0;
	}
	if (1 != fscanf_s(f, "%d", &timeout))
	{
		fprintf(stderr, "timeout specification format malformed\n");
		fclose(f);
		return 0;
	}
	fclose(f);	

	char command_line[MAX_PATH];
	int l = (int)strlen(argv[1]);
	memcpy(command_line, argv[1], l);
	char* next = command_line + l;
	*(next++) = ' ';

	for (int i = 2; i < argc; i++)
	{
		l = (int)strlen(argv[i]);
		memcpy(next, argv[i], l);
		next += l;
		if (i < argc - 1) *(next++) = ' ';
	}

	*next = 0;
	//printf("program: %s\ncommand line: %s\n", argv[1], command_line);

	STARTUPINFOA si;
	PROCESS_INFORMATION pi;
	ZeroMemory(&si, sizeof(si));
	si.cb = sizeof(si);
	ZeroMemory(&pi, sizeof(pi));
	if (!CreateProcessA(argv[1], command_line, 0, 0, 0, 0, 0, 0, &si, &pi))
	{
		fprintf(stderr, "CreateProcess failed (%d)\n", GetLastError());
		return 0;
	}
	uint64_t program_started = GetTickCount64();
	int timeouted = 0;

	//wait for exit
	do {
		if (GetTickCount64() - program_started > timeout)
		{
			timeouted = 1;
			break;
		}
		if (WAIT_TIMEOUT == WaitForSingleObject(pi.hProcess, 100)) continue;
		break;
	} while (1);

	if (timeouted)
	{
		snprintf(command_line, MAX_PATH, "taskkill.exe /F /T /PID %lu > nul", pi.dwProcessId);
		system(command_line);

		fprintf(stderr, "\ntime is up (%d ms), process terminated.\n", timeout);
	}

	CloseHandle(pi.hProcess);
	CloseHandle(pi.hThread);
}