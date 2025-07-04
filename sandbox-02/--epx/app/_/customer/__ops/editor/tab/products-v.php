<?php 

o()->component->view('studio/page', function(){
    o()->component->view('__ops/xui/editor',function(){
        o()->component->view('__ops/editor/tabs');
    })->prt();
})->prt();