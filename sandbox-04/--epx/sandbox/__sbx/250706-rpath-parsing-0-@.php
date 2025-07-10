<?php 

$expression_a = "#^(?<p>(?<a>[^@]+)(?<b>@(?<c>[^/]*))(?<d>.*))#";
$expression_b = "#^(?<p>(?<a>[^@]*)(?:(?<b>@(?<c>[^/]*))(?<d>.*))?)#";

foreach([
    '@',
    '@/editor/tab_1',
    '@25/editor/tab_1',
    'customer',
    'customer/@',
    'customer/@/editor/tab_1',
    'customer/@25/editor/tab_1',
    'customer/orders',
    'customer/orders/@',
    'customer/orders/@/editor/tab_1',
    'customer/orders/@25/editor/tab_1',
] as $rpath) {
    \preg_match($expression_b, $rpath, $m);
    echo '<hr>';
    echo $rpath.'<br>';
    \_\pre(\_\i\assoc\filter_by_keys::non_numeric($m));
    //echo '<pre>'.\json_encode($m, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre>';
}
