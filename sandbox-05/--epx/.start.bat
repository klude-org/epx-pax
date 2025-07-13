::
:: Installed: #__FW_INSTALLED__#
:: #####################################################################################################################
:: #region LICENSE
::     /* 
::                                                EPX-WIN-SHELL
::     PROVIDER : KLUDE PTY LTD
::     PACKAGE  : EPX-PAX
::     AUTHOR   : BRIAN PINTO
::     RELEASED : 2025-03-10
::     
::     The MIT License
::     
::     Copyright (c) 2017-2025 Klude Pty Ltd. https://klude.com.au
::     
::     of this software and associated documentation files (the "Software"), to deal
::     in the Software without restriction, including without limitation the rights
::     to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
::     copies of the Software, and to permit persons to whom the Software is
::     furnished to do so, subject to the following conditions:
::     
::     The above copyright notice and this permission notice shall be included in
::     all copies or substantial portions of the Software.
::     
::     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
::     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
::     FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
::     AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
::     LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
::     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
::     THE SOFTWARE.
::         
::     */
:: #endregion
:: # ###################################################################################################################
:: # i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
@echo off
:: Set variables
if "%FW__SHELL_PLUGIN%"=="" SET FW__SHELL_PLUGIN=epx__250712_01_std_boot_i__pax__klude_org__github
if exist %~dp0%FW__SHELL_PLUGIN%\.start.bat goto :launch
if exist %~dp0.local-plugins\%FW__SHELL_PLUGIN%\.start.bat goto :launch
if not exist %~dp0%FW__SHELL_PLUGIN% mkdir %~dp0%FW__SHELL_PLUGIN%
curl --globoff -o %~dp0%FW__SHELL_PLUGIN%\.start.bat https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/%FW__SHELL_PLUGIN%/.start.bat
if not exist %~dp0%FW__SHELL_PLUGIN%\.start.bat goto :abort    
:launch
call %~dp0%FW__SHELL_PLUGIN%\.start.bat %*
if %errorlevel%==0 goto :exit_b
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_b
:abort
echo [91m!!! INVALID SHELL[0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
:exit_b
exit /b 0