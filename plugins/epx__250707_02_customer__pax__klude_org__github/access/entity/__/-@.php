<?php 

echo __FILE__.\_\BR;
\_\pre($this); echo \_\BR;
\_\pre(o('customer')->model[3]); echo \_\BR;



// //o('product')->model[25]
// //o('product/type')->model[22]
// //o('customer/category')->model[22]
// o('product/customer')->model->item[22] = $item;
// o('product/customer')->model->pool[] = $item;
// o('product/customer')->model->item (22);
// o('product/customer')->model->query();
// o('product/customer')->model->struct();
// o('product/customer')->model->stash();

// $item = o()->product->item($product_id);
// $_ENV->com->product[$product_id] = [];
// $_ENV->com('product')->model;
// o('product')->model['product_id'];

// $_ENV('product')->model;
// o('product')->model->item(22);

// $_ENV->hold();
// $_ENV['product'][22]['name'] = 'test';
// $_ENV('product')[22]['name'] = 'test';
// $_ENV->save();