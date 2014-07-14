<?php
return [
	[0]=>['序号','收件人','收件地址','收件人电话','收件城市','活动','发货仓库','物料编码','物料属主','数量','备注'],
	[1]=>['1','wanglei','beijing office','13800138000','beijing','2014 word cup','center','SKJF-DL-01-LWL','susie','40','must be'],
	[2]=>['1','','','','','','center','SKJF-DL-022-LWL','susie','20','must be'],
	[3]=>['2','lisi','beijing office','13800138000','beijing','2014 word cup','center','SKJF-DL-01-LWL','susie','40','must be'],
	[4]=>['3','wangwu','beijing office','13800138000','beijing','2014 word cup','center','SKJF-DL-01-LWL','susie','40','must be'],
];

else{
                            $db = static::getDb();
                            $transaction = $db->beginTransaction();
                            try {
                                //create order
                                $model = new Order;
                                $model->goods_active = $v['goods_active'];
                                $model->storeroom_id = $storeroom->id;
                                $model->owner_id = $owner->id;
                                $model->to_city = $v['to_city'];
                                $model->recipients = $v['recipients'];
                                $model->recipients_address = $v['recipients_address'];
                                $model->recipients_contact = $v['recipients_contact'];
                                $model->info = $v['info'];
                                $model->limitday = $v['limitday'];
                                $model->created = date('Y-m-d H:i:s');
                                $model->created_uid = Yii::$app->user->id;
                                $model->source = Order::ORDER_SOURCE_CUSTOMER;
                                $model->save(false);
                                $model->viewid = date('Ymd')."-".$model->id;
                                $model->update();


                                foreach($postData as $key=>$value){
                                    $model = new OrderDetail;
                                    $model->order_id = $this->id;
                                    $model->goods_code = $value['code'];
                                    $model->goods_quantity = $value['count'];
                                    $model->save();
                                }
                                //Subtract stock
                                foreach($postData as $key=>$value){
                                    $material = Material::find()->where(['code'=>$value['code']])->one();
                                    $model = new Stock;
                                    $model->material_id = $material->id;
                                    $model->storeroom_id = $this->storeroom_id;
                                    $model->owner_id = $this->owner_id;
                                    $model->project_id = $material->project_id;
                                    $model->actual_quantity = 0 - $value['count'];
                                    $model->stock_time = date('Y-m-d H:i:s');
                                    $model->created = date('Y-m-d H:i:s');
                                    $model->increase = Stock::IS_NOT_INCREASE;
                                    $model->order_id = $this->id;
                                    $model->active = $this->goods_active;
                                    $model->save(false);

                                    //subtract stock total
                                    StockTotal::updateTotal($model->storeroom_id,$material->id,(0 - $value['count']));
                                }
                                return true;
                                //create order detail
                                $transaction->commit();
                            } catch (Exception $e) {
                                $transaction->rollBack();
                                return false;
                            }
                        }