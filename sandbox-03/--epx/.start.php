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
    1 AND \set_include_path(
        \_\START_DIR.PATH_SEPARATOR
        //.\_\START_DIR.'/sandbox'.PATH_SEPARATOR
        .\_\START_DIR.'/.local-plugins'.PATH_SEPARATOR
        .\get_include_path()
    );
    1 AND \spl_autoload_extensions("-#.php,/-#.php");
    1 AND \spl_autoload_register();
    global $START;
    $START ??= "epx__250706_01_start__pax__klude_org__github";
    \class_exists($START) OR (function($START){
        $file = "{$START}/-#.php";
        $localfile = \_\START_DIR."/{$file}";
        $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/".\urlencode($file);
        if(!($contents = \file_get_contents($url))){
            echo "Failed: Unable to locate: {$START}".PHP_EOL;
            exit();
        }
        \is_dir($d = \dirname($localfile)) OR \mkdir($d, 0777, true);
        \file_put_contents($localfile, $contents);
        if(!\class_exists($START)){
            echo "Failed: Unable to locate: {$START}".PHP_EOL;
            exit();
        } 
    })($START);
    return $START::_();
}
