<?php

return [
    'name' => 'Label',
    // 导入字段
    'import' => [
        // 标签到货明细
        'label_arrivals' => [
            'pro_mag_no' => [
                'field' => 'pro_mag_no',
                'name' => '生产管理编号',
                'type' => 'string',
                'required' => true
            ],
            'item_no' => [
                'field' => 'item_no',
                'name' => '进口代码',
                'type' => 'string',
                'required' => true
            ],
            'label_name' => [
                'field' => 'label_name',
                'name' => '标签名称',
                'type' => 'string',
                'required' => true
            ],
            'customer_order_no' => [
                'field' => 'customer_order_no',
                'name' => '客户订货单号码',
                'type' => 'string',
                'required' => true
            ],
            'num' => [
                'field' => 'num',
                'name' => '标签数量pcs',
                'type' => 'number',
                'required' => true
            ],
            'expired_at' => [
                'field' => 'expired_at',
                'name' => '部品代码',
                'type' => 'string',
                'required' => false
            ],
            'invoice_no' => [
                'field' => 'invoice_no',
                'name' => '发票号',
                'type' => 'string',
                'required' => true
            ],
        ],
        'invoices' => [
            'invoice_no' => [
                'field' => 'invoice_no',
                'name' => '发票号',
                'type' => 'string',
                'required' => true
            ],
            'sap_no' => [
                'field' => 'sap_no',
                'name' => '内向交货单',
                'type' => 'string',
                'required' => true
            ],
            'arrivetime' => [
                'field' => 'arrivetime',
                'name' => '预计到港日',
                'type' => 'date',
                'required' => true
            ],
            'material_code' => [
                'field' => 'material_code',
                'name' => '物料号',
                'type' => 'string',
                'required' => true
            ],
            'item_no' => [
                'field' => 'item_no',
                'name' => '其他代码',
                'type' => 'string',
                'required' => true
            ],
            'material_desc' => [
                'field' => 'material_desc',
                'name' => '物料描述',
                'type' => 'string',
                'required' => false
            ],
            'factory_code' => [
                'field' => 'factory_code',
                'name' => '工厂',
                'type' => 'string',
                'required' => false
            ],
            'num' => [
                'field' => 'num',
                'name' => '总数量',
                'type' => 'number',
                'required' => true
            ],
            'intransit_num' => [
                'field' => 'intransit_num',
                'name' => '在途数量',
                'type' => 'number',
                'required' => false
            ],
            'prepare_num' => [
                'field' => 'prepare_num',
                'name' => '预备数量',
                'type' => 'number',
                'required' => false
            ],
            'stock_num' => [
                'field' => 'stock_num',
                'name' => '主仓数量',
                'type' => 'number',
                'required' => false
            ],
            'diff_num' => [
                'field' => 'diff_num',
                'name' => '差异数量',
                'type' => 'number',
                'required' => false
            ],
            'other_num' => [
                'field' => 'other_num',
                'name' => '其他数量',
                'type' => 'number',
                'required' => false
            ],
            'provider_code' => [
                'field' => 'provider_code',
                'name' => '供应商编码',
                'type' => 'string',
                'required' => false
            ],
            'purchase_no' => [
                'field' => 'purchase_no',
                'name' => '采购订单号',
                'type' => 'string',
                'required' => false
            ],
            'purchase_num' => [
                'field' => 'purchase_num',
                'name' => '采购订单数量',
                'type' => 'number',
                'required' => false
            ],
            'brand' => [
                'field' => 'brand',
                'name' => '品牌产品层次前两个字符',
                'type' => 'string',
                'required' => false
            ],
            'brand_desc' => [
                'field' => 'brand_desc',
                'name' => '品牌描述',
                'type' => 'string',
                'required' => false
            ],
            'produce_line' => [
                'field' => 'produce_line',
                'name' => '产品线',
                'type' => 'string',
                'required' => false
            ],
            'produce_line_desc' => [
                'field' => 'produce_line_desc',
                'name' => '产品线描述',
                'type' => 'string',
                'required' => false
            ],
            'purchase_group' => [
                'field' => 'purchase_group',
                'name' => '采购组',
                'type' => 'string',
                'required' => false
            ],
            'purchase_group_desc' => [
                'field' => 'purchase_group_desc',
                'name' => '采购组描述',
                'type' => 'string',
                'required' => false
            ],
        ],
        'location' => [
            'type' => [
                'field' => 'type',
                'name' => '库位类型',
                'type' => 'string',
                'required' => true
            ],
            'location_no' => [
                'field' => 'location_no',
                'name' => '库位标号',
                'type' => 'string',
                'required' => true
            ],
            'label_type' => [
                'field' => 'label_type',
                'name' => '标签库位类型',
                'type' => 'string',
                'required' => false
            ]
        ],
        'items' => [
            'item_no' => [
                'field' => 'item_no',
                'name' => '产品代码',
                'type' => 'string',
                'required' => true
            ],
            'material_code' => [
                'field' => 'material_code',
                'name' => '新产品代码',
                'type' => 'string',
                'required' => true
            ],
            'name' => [
                'field' => 'name',
                'name' => '中文名',
                'type' => 'string',
                'required' => true
            ],
            'brand' => [
                'field' => 'brand',
                'name' => '品牌',
                'type' => 'string',
                'required' => true
            ],
            'flg' => [
                'field' => 'flg',
                'name' => '产品类型',
                'type' => 'string',
                'required' => true
            ],
            'label_usage' => [
                'field' => 'label_usage',
                'name' => '标签使用数量',
                'type' => 'number',
                'required' => true
            ],
            'instruction' => [
                'field' => 'instruction',
                'name' => '指示书',
                'type' => 'string',
                'required' => true
            ],
            'case_num' => [
                'field' => 'case_num',
                'name' => '盒规',
                'type' => 'number',
                'required' => true
            ],
            'box_num' => [
                'field' => 'box_num',
                'name' => '箱规',
                'type' => 'number',
                'required' => true
            ],
            'support_num' => [
                'field' => 'support_num',
                'name' => '托规',
                'type' => 'number',
                'required' => true
            ],
            'is_mark_valid' => [
                'field' => 'is_mark_valid',
                'name' => '是否查询制造记号',
                'type' => 'string',
                'required' => true
            ],
            'valid_month' => [
                'field' => 'valid_month',
                'name' => '有效期',
                'type' => 'number',
                'required' => true
            ],
            'is_stick_valid' => [
                'field' => 'is_stick_valid',
                'name' => '是否贴标',
                'type' => 'string',
                'required' => true
            ],
            'note' => [
                'field' => 'note',
                'name' => '操作要求',
                'type' => 'string',
                'required' => false
            ],
        ],
        'invoices_in'=>[
            'invoice_no' => [
                'field' => 'invoice_no',
                'name' => '发票号',
                'type' => 'string',
                'required' => false
            ],
            'sap_no' => [
                'field' => 'sap_no',
                'name' => '内向交货单',
                'type' => 'string',
                'required' => false
            ],
            'arrivetime' => [
                'field' => 'arrivetime',
                'name' => '预计到港日',
                'type' => 'date',
                'required' => false
            ],
            'material_code' => [
                'field' => 'material_code',
                'name' => '物料号',
                'type' => 'string',
                'required' => false
            ],
            'item_no' => [
                'field' => 'item_no',
                'name' => '其他代码',
                'type' => 'string',
                'required' => false
            ],
            'material_desc' => [
                'field' => 'material_desc',
                'name' => '物料描述',
                'type' => 'string',
                'required' => false
            ],
            'factory_code' => [
                'field' => 'factory_code',
                'name' => '工厂',
                'type' => 'string',
                'required' => false
            ],
            'num' => [
                'field' => 'num',
                'name' => '总数量',
                'type' => 'number',
                'required' => false
            ],
            'intransit_num' => [
                'field' => 'intransit_num',
                'name' => '在途数量',
                'type' => 'number',
                'required' => false
            ],
            'prepare_num' => [
                'field' => 'prepare_num',
                'name' => '预备数量',
                'type' => 'number',
                'required' => false
            ],
            'stock_num' => [
                'field' => 'stock_num',
                'name' => '主仓数量',
                'type' => 'number',
                'required' => false
            ],
            'diff_num' => [
                'field' => 'diff_num',
                'name' => '差异数量',
                'type' => 'number',
                'required' => false
            ],
            'other_num' => [
                'field' => 'other_num',
                'name' => '其他数量',
                'type' => 'number',
                'required' => false
            ],
            'provider_code' => [
                'field' => 'provider_code',
                'name' => '供应商编码',
                'type' => 'string',
                'required' => false
            ],
            'purchase_no' => [
                'field' => 'purchase_no',
                'name' => '采购订单号',
                'type' => 'string',
                'required' => false
            ],
            'purchase_num' => [
                'field' => 'purchase_num',
                'name' => '采购订单数量',
                'type' => 'number',
                'required' => false
            ],
            'brand' => [
                'field' => 'brand',
                'name' => '品牌（产品层次前两个字符）',
                'type' => 'string',
                'required' => false
            ],
            'brand_desc' => [
                'field' => 'brand_desc',
                'name' => '品牌描述',
                'type' => 'string',
                'required' => false
            ],
            'produce_line' => [
                'field' => 'produce_line',
                'name' => '产品线',
                'type' => 'string',
                'required' => false
            ],
            'produce_line_desc' => [
                'field' => 'produce_line_desc',
                'name' => '产品线描述',
                'type' => 'string',
                'required' => false
            ],
            'purchase_group' => [
                'field' => 'purchase_group',
                'name' => '采购组',
                'type' => 'string',
                'required' => false
            ],
            'purchase_group_desc' => [
                'field' => 'purchase_group_desc',
                'name' => '采购组描述',
                'type' => 'string',
                'required' => false
            ],
        ]
    ]
];
