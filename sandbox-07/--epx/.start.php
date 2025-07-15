<?php 
########################################################################################################################
#region
    /* 
                                               EPX-PAX-START
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-PAX
    AUTHOR   : BRIAN PINTO
    RELEASED : 2025-07-04
    
    Copyright (c) 2017-2023 Klude Pty Ltd. https://klude.com.au

    The MIT License

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    */
#endregion
# ######################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
# ######################################################################################################################
#region START
namespace _ { 
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    \define('_\START_FILE', \str_replace('\\','/', __FILE__));
    \define('_\START_DIR', \dirname(\_\START_FILE));
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    0 AND \error_reporting(E_ALL);
    try {
        $_SERVER['FW__SHELL_PLUGIN'] ??= "epx__250712_01_std_boot_i__pax__klude_org__github";
        if(!($boot_plugin_name = $_SERVER['FW__SHELL_PLUGIN' ] ?? null)){
            throw new \Exception("Shell plugin name is not specified");
        }
        if(
            !\is_file($f = __DIR__."/{$boot_plugin_name}/.boot.php")
            && !\is_file($f = __DIR__."/.local-plugins/{$boot_plugin_name}/.boot.php")
        ){
            $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/{$boot_plugin_name}/.boot.php";
            if(!($contents = \file_get_contents($url))){
                throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
            }
            \is_dir($d = \dirname($f = __DIR__."/{$boot_plugin_name}/.boot.php")) OR \mkdir($d, 0777, true);
            \file_put_contents($f, $contents);
        }
        if(!\is_file($f)){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to locate start file");
        }
        return include $f;
    } catch (\Throwable $ex){
        echo "[91m!!! {$ex->getMessage()} [0m\n";
        exit(1);
    }
}
