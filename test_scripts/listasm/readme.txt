This folder is required to be installed on some Windows machine
with MSVS installed. It is here only for archiving.

Place file run_with_timeout.exe into scripts/ folder.

Some ASM tests require CONEMU application to be installed in C:\a\util\conemu
It needs to be configured to output console log to %CONEMULOGS%

Create ssh key in pscp-key/privatekey.ppk using puttygen and add the public
key it to the authorized_keys for the user that can access the asm_test_for_remote* 
folders.

Start importer.ps1 in MSVS Powershell window from scripts/ folder.

