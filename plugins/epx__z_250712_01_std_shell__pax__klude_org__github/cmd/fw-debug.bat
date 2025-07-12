@echo off
rem echo [32m*********************************************************************[0m

:: Check for argument
if "%~1"=="" (
    if "%FX__DEBUG%" NEQ "" (
        <nul set /p=[92mDEBUG: %FX__DEBUG%[0m
    ) else (
        <nul set /p=[92mDEBUG is off[0m
    )
    exit /b 1
)


if "%~1"=="/?" (
    echo Usage: fw-debug [on|off|level]
    exit /b 1
)

:: Set FX__DEBUG based on the argument
if "%~1"=="on" (
    set "FX__DEBUG=on"
) else if "%~1"=="off" (
    set FX__DEBUG=
) else (
    set "FX__DEBUG=%~1"
)

:: call %~dp0cmd-opt\--reload.bat

if "%FX__DEBUG%" NEQ "" (
    <nul set /p=[92mDEBUG: %FX__DEBUG%[0m
) else (
    <nul set /p=[92mDEBUG is off[0m
)
