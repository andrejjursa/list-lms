# Define the remote server details and file paths
$remoteUser = "elieste"
$remoteHost = "list.fmph.uniba.sk"
$remotePath = "/var/www/list/private/asm_test_for_remote_eval"
$resultPath = "/var/www/list/private/asm_test_for_remote_result"
$localDestination = "$($PWD.Path)\..\incoming"
$privateKey = "$($PWD.Path)\..\pscp-key\privatekey.ppk"
$digestingFolder = "$($PWD.Path)\..\digesting"
$evalScript = "evaluator.ps1"
$max_number_of_jobs = 10
$running_jobs_count = 0

while ($true) {
	Write-Output "Starting file transfer from $remoteHost..."
    pscp.exe -i $privateKey -r "${remoteUser}@${remoteHost}:${remotePath}/*" $localDestination
	Write-Output "File transfer complete."

	Write-Output "Processing each folder..."
	$folders = Get-ChildItem -Path $localDestination -Directory
	foreach ($folder in $folders) {
		Write-Output "Processing folder: $($folder.Name)"    
		Move-Item -Path $folder.FullName -Destination "$digestingFolder\$($folder.Name)"
	    
		pscp.exe -i $privateKey -r "accepted.txt" "${remoteUser}@${remoteHost}:${remotePath}/$($folder.Name)"        
		
		while ($running_jobs_count -ge $max_number_of_jobs) {
			$completed_jobs = Get-Job | Where-Object { $_.State -eq 'Completed' }
			
			foreach ($job in $completed_jobs) {
				$running_jobs_count--
				Remove-Job -Job $job
			}			
			Start-Sleep -Seconds 0.5  # Small delay to prevent tight loop
		}
		$digiFolderName="$digestingFolder\$($folder.Name)"
		
		Write-Output "digi: $digiFolderName"
		$job = Start-Job -ScriptBlock {
			param ($digiFolderName, $evalScript, $digestingFolder, $remoteUser, $remoteHost, $resultPath, $privateKey, $folder)

            $outfile="__list_output.txt"
            $resultfile="__list_score.txt"

			# do the evaluation
        	cd $digiFolderName
			Write-Output "Compiling...<pre>" | Out-File -Encoding default $outfile
			#Start-Sleep -Seconds 1000
			# if we have starter/ subfolder, we have to do it a little bit differently...
			if (Test-Path starter -PathType Container)
			{
				cd starter
				cl *.cpp /Fetest_run_file.exe /nologo 2>&1 | Out-File -Encoding default -Append "$(outfile).tmp"
				cd ..

				if (Test-Path "*.cpp") {
				  cl *.cpp /Fetest_one_case.exe /nologo 2>&1 | Out-File -Encoding default -Append "$(outfile).tmp" 
				}
				elseif (Test-Path "*.c") {
				  cl *.c /Fetest_one_case.exe /nologo 2>&1 | Out-File -Encoding default -Append "$(outfile).tmp"
				}
				
				if (Test-Path ./test_one_case.exe)
				{
				  Move-Item starter/test_run_file.exe .
				}
			}
			else  # normal situation - only one exe file
			{
				if (Test-Path "*.cpp") {
				  cl *.cpp /Fetest_run_file.exe /nologo 2>&1 | Out-File -Encoding default -Append "$(outfile).tmp" 
				}
				elseif (Test-Path "*.c") {
				  cl *.c /Fetest_run_file.exe /nologo 2>&1 | Out-File -Encoding default -Append "$(outfile).tmp"
				}
			}
            
			Get-Content "$(outfile).tmp" | Where-Object { $_ -ne "" } | Select-String -NotMatch "^(.*\.exe|list_simple_cpp_test.*|test_.*\.c.*|.*'main2': must return a value.*)" | Out-File -Encoding default -Append $outfile
			Remove-Item "$(outfile).tmp"
			
			
			if (Test-Path ./test_run_file.exe) {
 			  Write-Output "</pre>Running...<pre>" | Out-File -Encoding default -Append $outfile
			  ../../scripts/run_with_timeout.exe ./test_run_file.exe $resultfile 2>&1 | Out-File -Encoding default -Append $outfile 
			  $resultContent = Get-Content -Path $resultfile -Raw
 			  Write-Output "</pre><br>completed with $resultContent percent.<br>" | Out-File -Encoding default -Append $outfile
			}
			else { 
			  Write-Output "</pre>Compilation failed<br>" | Out-File -Encoding default -Append $outfile
			  Write-Output "0" | Out-File -Encoding default $resultfile
			}

            Write-Output "Job finished"
			Write-Output "Starting file transfer to $remoteHost..."
 		    pscp.exe -i $privateKey -r "$digiFolderName\$resultfile" "${remoteUser}@${remoteHost}:${resultPath}/${folder}"
 		    pscp.exe -i $privateKey -r "$digiFolderName\$outfile" "${remoteUser}@${remoteHost}:${resultPath}/${folder}"
	        cd ..
			Remove-Item -Path $digiFolderName -Recurse -Force
			Write-Output "File transfer complete."
        } -ArgumentList $digiFolderName, $evalScript, $digestingFolder, $remoteUser, $remoteHost, $resultPath, $privateKey, $($folder.Name)

        $running_jobs_count++
        Write-Output "Started Job $running_jobs_count for $($folder.Name)"
	}
	Write-Output "All folders processed."
	Start-Sleep -Seconds 1
}
