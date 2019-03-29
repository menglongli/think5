<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//组合变量规则
//http://think5.com/public/index.php/item/long-45
//Route::get('item/:name-:id', 'index/index/read')->https(false)->domain('think5.com')
//    ->pattern(['name' => '\w+', 'id' => '\d+']);

//http://think5.com/public/index.php/item-long-45
//Route::get('item-<name>-<id>', 'index/index/read')
//    ->pattern(['name' => '\w+', 'id' => '\d+']);

//// 定义GET请求路由规则 并设置name变量规则
//Route::get('new/:name', 'index/index/read')
//    ->pattern(['name' => '\d+']); //name值的数据类型；


//Route::name('new_read')->rule('new/:id','index/index/read');
//Route::rule('new/:id','index/index/read')->name('new_read');

//闭包  | 依赖注入
//Route::get('hello/:name', function (Response $response, $name) {
//    return $response
//        ->data('Hello,' . $name)
//        ->code(200)
//        ->contentType('text/plain');
//});

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('auth','Multi/authorize');
Route::get('auth2','Multi/authorize2');
Route::get('multis','Multi/callback');

//Route::get('hello/:name', 'index/hello');
//变量用[ ]包含起来后就表示该变量是路由匹配的可选变量
//可选参数只能放到路由规则的最后，如果在中间使用了可选参数的话，后面的变量都会变成可选参数。
Route::get('blog/:year/[:month]','index/index/index');
Route::rule('hello/:name', 'index/index/hello');

return [

];
