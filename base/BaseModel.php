<?php

namespace app\base;

use yii\base\Model;
use yii\data\Pagination;

class BaseModel extends Model
{
    protected string $formModel = 'app\base\BaseActiveRecord';
    protected int $page = 0;
    protected int $pageSize = 20;
    protected array $conditions = [];

    protected function listQuery(): array
    {
        if (class_exists($this->formModel) and method_exists($this->formModel, 'find')) {
            $model = new $this->formModel();
            $query = $model->find()->where(['is_deleted' => false])->where($this->conditions);
            $pagination = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => $this->pageSize,
                'page' => $this->page
            ]);
            return $this->success([
                'list' => $query->offset($pagination->getOffset())->limit($pagination->getPageSize())->all(),
                'pagination' => [
                    'total' => $pagination->totalCount,
                    'page_size' => $pagination->getPageSize(),
                    'current_page' => $pagination->getPage() + 1,
                    'page_count' => $pagination->getPageCount()
                ]
            ]);
        } else {
            return $this->failure(500, '模型不存在或模型非实体');
        }
    }

    public function load($data, $formName = ''): bool
    {
        if ($formName === '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$formName])) {
            $this->setAttributes($data[$formName]);

            return true;
        }

        return false;
    }

    public function success($data): array
    {
        return [
            'success' => true,
            'code' => 200,
            'message' => '操作成功',
            'data' => $data
        ];
    }

    public function failure($code, $message): array
    {
        return [
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => []
        ];
    }

}