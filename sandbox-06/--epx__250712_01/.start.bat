::<?php echo "\r   \r"; if(0): ?>
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
if "%FW__BOOT_PLUGIN%"=="" SET FW__BOOT_PLUGIN=epx__250712_01_std_boot_i__pax__klude_org__github
if exist %~dp0%FW__BOOT_PLUGIN%\.boot.bat goto :launch
if exist %~dp0.local-plugins\%FW__BOOT_PLUGIN%\.boot.bat goto :launch_2

C:/xampp/current/php__xdbg/php.exe "%~f0" %*;
if not exist %~dp0%FW__BOOT_PLUGIN%\.boot.bat goto :abort    
:launch
call %~dp0%FW__BOOT_PLUGIN%\.boot.bat %*
if %errorlevel%==0 goto :exit_ok
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_ok
:launch_2
call %~dp0.local-plugins\%FW__BOOT_PLUGIN%\.boot.bat %*
if %errorlevel%==0 goto :exit_ok
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_ok
:abort
echo [91m!!! INVALID SHELL[0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1
:exit_ok
exit /b 0
<?php endif;
try {
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    $plugin_dirpath= \str_replace('\\','/',__DIR__);
    $url__fn = function($w_owner, $w_repo, $w_domain, $u_path){
        return match($w_domain){
            'github' => "https://raw.githubusercontent.com/{$w_owner}/{$w_repo}/main/plugins/{$u_path}",
            'epx' => "https://epx-modules.neocloud.com.au/{$w_owner}/{$w_repo}/live/{$u_path}", 
            default => null,
        };
    };
    if(!($boot_plugin_name = $_SERVER['FW__BOOT_PLUGIN' ] ?? null)){
        throw new \Exception("Shell plugin name is not specified");
    }
    if(!\preg_match(
        "#^(?<w_plugin>epx__(?<w_partno>.+)__(?<w_repo>.+)__(?<w_owner>.+)__(?<w_domain>[^/]+))(?<w_sub>/[^/]+)?#",
        $p = \str_replace('\\','/', $boot_plugin_name),
        $m
    )){
        throw new \Exception("Shell plugin name format is invalid");
    }
    \extract($mx = \array_filter($m, fn($k) => !is_numeric($k), \ARRAY_FILTER_USE_KEY)); 
    $w_owner = \str_replace('_','-',$w_owner);
    $w_repo = "epx-".\str_replace('_','-',$w_repo);
    if(\str_ends_with($w_partno, '_i')){
        $start_filename = $w_sub ?? null ?: "/.boot.bat";
        $start_plugin_stub = "{$boot_plugin_name}{$start_filename}";
        $localfile = "{$plugin_dirpath}/{$start_plugin_stub}";
        $u_path = \urlencode("{$start_plugin_stub}");
        if(!($url = $url__fn($w_owner, $w_repo, $w_domain, $u_path))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Invalid domain");
        }
        if(!($contents = \file_get_contents($url))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
        }
        \is_dir($d = \dirname($localfile)) OR \mkdir($d, 0777, true);
        \file_put_contents($localfile, $contents);
    } else {
        if(\is_dir($boot_dirpath = "{$plugin_dirpath}/{$boot_plugin_name}")){
            throw new \Exception("Shell plugin folder '{$boot_plugin_name}' already exist");
        }
        $u_path = \urlencode("{$boot_plugin_name}.zip");
        if(!($url = $url__fn($w_owner, $w_repo, $w_domain, $u_path))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Invalid domain");
        }
        $l_zip= "{$plugin_dirpath}/.local-".uniqid().".zip";
        if(!($contents = \file_get_contents($url))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
        }
        \file_put_contents($l_zip, $contents);
        if (!(($zip = new \ZipArchive)->open($l_zip) === true)) {
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
        }
        $zip->extractTo($plugin_dirpath);
        unlink($l_zip);    
    }
} catch (\Throwable $ex){
    echo "[91m!!! {$ex->getMessage()} [0m\n";
    exit(1);
}
