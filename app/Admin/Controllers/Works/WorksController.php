<?php

namespace App\Admin\Controllers\Works;

use App\Models\Works;
use App\Models\WorksResource;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use App\Admin\Actions\Grid\ReviewWorks;

class WorksController extends AdminController
{
    // 页面标题
    protected $title = '作品';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Works::with(['user', 'topics_categories', 'works_resource']), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            $grid->column('topics_categories.name', '话题');

            $grid->column('u_type', '发布作品者类型')->display(function ($u_type) {
                switch ($u_type) {
                    case 0:
                        $str = '平台';
                        break;
                    case 1:
                        $str = '用户';
                        break;
                    default:
                        $str = '未知';
                        break;
                }
                return $str;
            });

            $grid->column('user.nickname', '发布人')->display(function () {

                if ($this->user_id)
                    return $this->user->nickname;

                return '平台';

            });

            // 目前支持图片资源预览
            $grid->column('works_resource', '作品资源')->display(function ($model) {

                $w_re = $model->toArray();
                return [$w_re[0]['r_url'] ?? ''];

            })->image('http://xxx.com', 50, 50)->help('目前支持图片资源列表内预览');

            $grid->column('content', '作品内容');

            $grid->column('data_count', '数据')->display(function () {
                $str = '浏览人次：'. $this->view_count .'<br>';
                $str .= '点赞数：'. $this->likes_count .'<br>';
                $str .= '评论数：'. $this->comment_count .'<br>';
                $str .= '收藏数：'. $this->collection_count .'<br>';
                $str .= '分享数：'. $this->share_count .'<br>';

                return $str;
            });

            $grid->column('review_status', '审核状态')->using([0 => '未审核', 1 => '审核通过', 2 => '审核未通过'])->label([
                'default' => 'primary', // 设置默认颜色，不设置则默认为 default

                1 => 'primary',
                2 => 'danger',
                3 => 'success',
                4 => Admin::color()->info(),
            ]);

            $grid->column('permission_status', '权限状态')->switch()->help('文章的权限：设置开放或私有');

            $grid->column('updated_at');

            $grid->actions([new ReviewWorks()]);

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 作品内容
                $filter->like('content', '作品内容');
            });
        });
    }

    /**
     * 表单页
     * @return Form
     */
    public function form()
    {
        return Form::make(new Works(), function (Form $form) {

            $form->display('id', 'ID');

            $options_url = config('app.url') . '/api/topics/dcat_category_list';
            $form->select('topics_category_id', '话题分类')->options($options_url);

            $select_options = [
                0 => '平台',
            ];
            $form->select('u_type', '发布作品者类型')
                ->options($select_options)
                ->default(0);

            $form->textarea('content', '作品内容');

            $form->multipleImage('images', '上传图片')
                ->limit(3)
                ->accept('jpg,png,gif,jpeg,mp4,3gp,m3u8')
                ->autoUpload()
                ->uniqueName();

            $radio_options = [
                0 => '开放',
                1 => '私有',
            ];
            $form->radio('permission_status', '作品权限')->options($radio_options)->default(0);

            $form->footer(function ($footer) {

                // 去掉`重置`按钮
                $footer->disableReset();

                // 去掉`查看`checkbox
                $footer->disableViewCheck();

                // 去掉`继续编辑`checkbox
                $footer->disableEditingCheck();

                // 去掉`继续创建`checkbox
                $footer->disableCreatingCheck();

            });

            // 保存后的回调（处理作品资源数据）
            $form->saved(function (Form $form) {
                $images = \request('images');
                $works_id  = $form->getKey();

                // 先删除旧数据
                WorksResource::query()->where('works_id', $works_id)->delete();

                $r_urls = explode(',', $images);
                foreach ($r_urls as $r_url) {
                    // 保存新数据
                    $data = [
                        'works_id' => $works_id,
                        'r_url'    => $r_url,
                    ];
                    WorksResource::query()->create($data);
                }
            });

            // 忽略掉不需要保存的字段
            $form->ignore(['images']);

        });
    }

    /**
     * 显示页
     * @param $id
     * @return Show
     */
    protected function detail($id)
    {
        $model = Works::query()->with('works_resource')->findOrFail($id);

        return Show::make($id, $model, function (Show $show) {
            $show->field('id');
            $show->field('content', '作品内容');
            $show->field('r_url', '作品资源')->as(function () {
                $r_list = $this->getRelation('works_resource')->toArray();
                return array_column($r_list, 'r_url');
            })->image('http://xxx.com', 100, 100);
            $show->field('view_count', '浏览人次');
            $show->field('likes_count', '点赞数');
            $show->field('comment_count', '评论数');
            $show->field('collection_count', '收藏数');
            $show->field('share_count', '分享数');
            $show->field('review_status', '审核状态')->as(function () {
                $review_status = $this->review_status;

                switch ($review_status) {
                    case 0:
                        $str = '未审核';
                        break;
                    case 1:
                        $str = '审核通过';
                        break;
                    case 2:
                        $str = '审核未通过';
                        break;
                    default:
                        $str = '未知';
                        break;
                }

                return $str;

            });
            $show->field('review_remark', '审核说明');
            $show->field('created_at');
            $show->field('updated_at');
        });


    }

}
