<?php

namespace Qihucms\UCenter\Controllers\Admin;

use Illuminate\Support\Str;
use Qihucms\UCenter\Models\UcenterSite;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SiteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '关联站点';

    const ENCRYPT_TYPE = [
        'ip' => 'IP验证',
        'string' => '字符验证',
        'sign' => '签名验证',
    ];

    const STATUS = ['关闭', '开启'];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UcenterSite());

        $grid->model()->latest();

        $grid->column('id', 'ID');
        $grid->column('name', '站点名称');
        $grid->column('ip', 'IP地址');
        $grid->column('token', 'token')->copyable();
        $grid->column('encrypt_type', '加密方式')->using(self::ENCRYPT_TYPE);
        $grid->column('status', '状态')->using(self::STATUS);
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UcenterSite::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', '站点名称');
        $show->field('user_api', '会员接口路径');
        $show->field('account_api', '账户接口路径');
        $show->field('ip', 'IP地址');
        $show->field('token', 'token');
        $show->field('encrypt_type', '加密方式')->using(self::ENCRYPT_TYPE);
        $show->field('desc', '备注');
        $show->field('status', '状态')->using(self::STATUS);
        $show->field('created_at', __('admin.created_at'));
        $show->field('updated_at', __('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UcenterSite());

        $form->text('name', '站点名称');
        $form->url('user_api', '会员接口路径');
        $form->url('account_api', '账户接口路径');
        $form->ip('ip', 'IP地址');
        $form->text('token', 'token')->default(Str::random(26));
        $form->select('encrypt_type', '加密方式')->default('string')->options(self::ENCRYPT_TYPE);
        $form->textarea('desc', '备注');
        $form->select('status', '状态')->default(1)->options(self::STATUS);

        return $form;
    }
}
