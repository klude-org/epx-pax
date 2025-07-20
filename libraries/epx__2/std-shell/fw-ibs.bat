@echo off
if "%FW__DEBUG%"=="batch-trace" echo on

:: call %~dp0cmd-opt\--reload.bat

if "%FX__DEBUG%" EQU "" (
    if "%~1"=="start" (
        index /--php-ibs/std_server/start
    ) else if "%~1"=="stop" (
        index /--php-ibs/std_server/stop
    ) else if "%~1"=="" (
        index /--php-ibs/std_server/launch
    )
) else (
    if "%~1"=="start" (
        index /--php-ibs/xdbg_server/start
    ) else if "%~1"=="stop" (
        index /--php-ibs/xdbg_server/stop
    ) else if "%~1"=="" (
        index /--php-ibs/xdbg_server/launch
    )
)
