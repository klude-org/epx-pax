@echo off
setlocal enabledelayedexpansion

:: Loop through all directories in current folder
for /d %%F in (*) do (
    if not "%%F"=="." (
        echo Compressing folder: %%F
        if exist "%%F.zip" del "%%F.zip"
        "C:\Program Files\7-Zip\7z.exe" a -tzip "%%F.zip" "%%F\"
    )
)

echo Done.
endlocal
pause
