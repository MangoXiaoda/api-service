<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Works;
use Dcat\Admin\Widgets\Metrics\RadialBar;
use Illuminate\Http\Request;

class Tickets extends RadialBar
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title('作品');
        $this->height(400);
        $this->chartHeight(300);
        $this->chartLabels('比上周增长');
        $this->dropdown([
            '0' => '全部',
//            '28' => 'Last 28 Days',
//            '30' => 'Last Month',
//            '365' => 'Last Year',
        ]);
    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
//        switch ($request->get('option')) {
//            case '365':
//            case '30':
//            case '28':
//            case '7':
//            default:
//                // 卡片内容
//                $this->withContent(162);
//                // 卡片底部
//                $this->withFooter(29, 63, '1d');
//                // 图表数据
//                $this->withChart(83);
//
        // 获取上周日前的作品数（用于计算新作品数比上周新增数据）
        $old_updated_at = date('Y-m-d H:i:s', GetWeekStart());
        $old_works_num  = Works::query()->where('updated_at', '<', $old_updated_at)->count();
        // 作品数
        $works_num = Works::query()->count();
        // 作品增长率（注意除数分母不能为零）
        $works_add_rate = ($works_num - $old_works_num) / ($old_works_num?:1) * 100;
        // 未审核作品数
        $un_review_works_num = Works::query()->where('review_status', 0)->count();
        // 已审核作品数
        $pass_works_num = Works::query()->where('review_status', 1)->count();
        // 审核未通过作品数
        $review_unpass_works_num = Works::query()->where('review_status', 2)->count();

        // 卡片内容
        $this->withContent($works_num);
        // 卡片底部
        $this->withFooter($un_review_works_num, $pass_works_num, $review_unpass_works_num);
        // 图表数据
        $this->withChart($works_add_rate);
    }

    /**
     * 设置图表数据.
     *
     * @param int $data
     *
     * @return $this
     */
    public function withChart(int $data)
    {
        return $this->chart([
            'series' => [$data],
        ]);
    }

    /**
     * 卡片内容
     *
     * @param string $content
     *
     * @return $this
     */
    public function withContent($content)
    {
        return $this->content(
            <<<HTML
<div class="d-flex flex-column flex-wrap text-center">
    <h1 class="font-lg-2 mt-2 mb-0">{$content}</h1>
    <small>条</small>
</div>
HTML
        );
    }

    /**
     * 卡片底部内容.
     *
     * @param string $new
     * @param string $open
     * @param string $response
     *
     * @return $this
     */
    public function withFooter($new, $open, $response)
    {
        return $this->footer(
            <<<HTML
<div class="d-flex justify-content-between p-1" style="padding-top: 0!important;">
    <div class="text-center">
        <p>未审核</p>
        <span class="font-lg-1">{$new}</span>
    </div>
    <div class="text-center">
        <p>审核通过</p>
        <span class="font-lg-1">{$open}</span>
    </div>
    <div class="text-center">
        <p>审核未通过</p>
        <span class="font-lg-1">{$response}</span>
    </div>
</div>
HTML
        );
    }
}
