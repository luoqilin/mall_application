<?php

namespace app\base;

use yii\base\Model;
use yii\data\Pagination;

/**
 * 表单模型基类，增加了一些快捷返回数据的模板
 */
class BaseForm extends Model
{
    /**
     * @var string 表单查询模型
     */
    protected string $formModel = 'app\base\BaseActiveRecord';
    /**
     * @var int 当前请求分页
     */
    protected int $page = 0;
    /**
     * @var int 分页大小，默认为20
     */
    protected int $pageSize = 20;
    /**
     * @var array list的附加查询条件
     */
    protected array $conditions = [];

    /**
     * 通过condition的查询条件，查询所有未删除数据，page为当前分页，pageSize为分页大小
     * @return array 查询结果
     */
    protected function listQuery(): array
    {
        if (class_exists($this->formModel) and method_exists($this->formModel, 'find')) {
            $model = new $this->formModel();
            $query = $model::find()->where(['is_deleted' => false])->and($this->conditions);
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

    /**
     * 加载参数，通过data的数组将表单参数加载到表单中
     * @param array $data 需要加载的数据
     * @param string|null $formName 表单名称，如果post的表单根节点是表单名称则添加此参数
     * @return bool 是否加载成功
     */
    public function load($data, string|null $formName = ''): bool
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