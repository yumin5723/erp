<?php
return [
        'material'=>[
            'name'=>'物料',
            'route'=>'',
            'icon'=>'tasks',
            'style'=>'tasks',
            'subs' => [
                [
                    'name' => '我的物料',
                    'route' => 'material/list',
                ],
                // [
                //     'name' => '添加物料',
                //     'route' => 'material/create',
                // ],
            ],
        ],
        'order'=>[
            'name'=>'订单',
            'route'=>'',
            'icon'=>'truck',
            'subs' => [
                [
                    'name' => '未处理订单',
                    'route' => 'order/list?OrderSearch[status]=0',
                ],
                [
                    'name' => '已确认订单',
                    'route' => 'order/list?OrderSearch[status]=4',
                ],
                [
                    'name' => '已包装订单',
                    'route' => 'order/list?OrderSearch[status]=1',
                ],
                [
                    'name' => '已发货订单',
                    'route' => 'order/list?OrderSearch[status]=2',
                ],
                [
                    'name' => '已签收订单',
                    'route' => 'order/list?OrderSearch[status]=3',
                ],
                [
                    'name' => '已拒绝订单',
                    'route' => 'order/list?OrderSearch[status]=5',
                ],
                [
                    'name' => '下订单',
                    'route' => 'order/create',
                ],
            ],
        ],
];