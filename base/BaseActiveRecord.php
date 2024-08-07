<?php

namespace app\base;

use DateTime;
use Exception;
use Yii;
use yii\db\ActiveRecord;

/**
 * 模型基类
 * 重写了删除方法，删除数据将更改为将is_deleted设置为0（false）
 * 重写保存方法，更新时自动增加更新人和更新时间，新增数据自动增加创建人和创建时间
 */
class BaseActiveRecord extends ActiveRecord
{
    public function delete(): bool
    {
        $this->setAttribute('is_deleted', true);
        $this->setAttribute('deleted_at', (new DateTime())->format('y-m-d H:i:s'));
        $this->setAttribute('deleted_by', Yii::$app->user->identity->id);
        try{
            $this->save();
        } catch (Exception) {
            return false;
        }
        return true;
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->setAttribute('created_at', (new DateTime())->format('y-m-d H:i:s'));
            $this->setAttribute('created_by', Yii::$app->user->identity->id);
        } else if (!$this->getAttribute('is_deleted')) {
            $this->setAttribute('updated_at', (new DateTime())->format('y-m-d H:i:s'));
            $this->setAttribute('updated_by', Yii::$app->user->identity->id);
        }
        return parent::beforeSave($insert);
    }

}