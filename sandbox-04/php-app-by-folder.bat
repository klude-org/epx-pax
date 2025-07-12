SETLOCAL
call %~dp0php-@.bat
"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --incognito --app=http://%FW__ROOT_URL%  | start /min C:\%FW__PHP_PATH%\php.exe -S %FW__ROOT_URL% -t %~dp0
exit /b