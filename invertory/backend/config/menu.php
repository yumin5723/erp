<?php
return [
        'managers' => [
            'name' => '管理员',
            'route' => 'manager/admin',
            'icon'=>'user',
            'style'=>'',
            'subs' => [
                [
                    'name' => '管理员列表',
                    'route' => 'manager/admin',
                ],
                [
                    'name' => '添加管理员',
                    'route' => 'manager/create',
                ],
                [
                    'name' => '角色列表',
                    'route' => 'auth/rolelist',
                ],
                [
                    'name' => '分配权限',
                    'route' => 'auth/assign',
                ],
                [
                    'name' => '权限添加',
                    'route' => 'auth/flushperms',
                ],
            ],
        ],
        'owner' => [
            'name' => '物主',
            'route' => 'owner/list',
            'icon'=>'globe',
            'style'=>'',
            'subs' => [
                [
                    'name' => '物主列表',
                    'route' => 'owner/list',
                ],
                [
                    'name' => '添加物主',
                    'route' => 'owner/create',
                ],
            ],
        ],
        'storeroom'=>[
            'name'=>'仓库',
            'route'=>'',
            'icon'=>'key',
            'style'=>'',
            'subs' => [
                [
                    'name' => '仓库列表',
                    'route' => 'storeroom/list',
                ],
                [
                    'name' => '添加仓库',
                    'route' => 'storeroom/create',
                ],
            ],
        ],
        'project'=>[
            'name'=>'项目',
            'route'=>'',
            'icon'=>'tags',
            'style'=>'tags',
            'subs' => [
                [
                    'name' => '项目列表',
                    'route' => 'project/list',
                ],
                [
                    'name' => '添加项目',
                    'route' => 'project/create',
                ],
            ],
        ],
        'material'=>[
            'name'=>'物料',
            'route'=>'',
            'icon'=>'tasks',
            'style'=>'tasks',
            'subs' => [
                [
                    'name' => '物料列表',
                    'route' => 'material/list',
                ],
                [
                    'name' => '添加物料',
                    'route' => 'material/create',
                ],
            ],
        ],
        'stock'=>[
            'name'=>'库存',
            'route'=>'',
            'icon'=>'cloud-upload',
            'subs' => [
                [
                    'name' => '库存明细',
                    'route' => 'stock/list',
                ],
                [
                    'name' => '入库',
                    'route' => 'stock/create',
                ],
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
                    'name' => '下订单',
                    'route' => 'order/create',
                ],
            ],
        ],
        'package'=>[
            'name'=>'包装及发货',
            'route'=>'',
            'icon'=>'gift',
            'subs' => [
                [
                    'name' => '包装明细',
                    'route' => 'package/list',
                ],
            ],
        ],
        'channel'=>[
            'name'=>'订单与渠道',
            'route'=>'',
            'icon'=>'plane',
            'subs' => [
                [
                    'name' => '渠道订单',
                    'route' => 'channel/list',
                ],
                [
                    'name' => '新建渠道订单',
                    'route' => 'channel/create',
                ],
            ],
        ],
];