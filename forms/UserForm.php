<?php

namespace app\forms;

use app\base\BaseForm;
use app\models\User;


class UserForm extends BaseForm
{
    public int $id;

    protected string $formModel = 'app\models\User';

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
        ];
    }

    public function list($pagination): array
    {
        $this->page = $pagination['page'] - 1 ?? $this->page;
        $this->pageSize = $pagination['page_size'] ?? $this->pageSize;
        return $this->listQuery();
    }

    public function deleteUser(): array
    {
        $user = User::findOne($this->id);
        if($user->delete()){
            return $this->success([
                'id' => $this->id,
            ]);
        } else {
            return $this->failure('500', '操作失败');
        }
    }

}