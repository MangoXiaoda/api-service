<?php

namespace App\Admin\Metrics\Examples;

use App\Models\TopicsCategory;
use Dcat\Admin\Widgets\Metrics\Round;
use Illuminate\Http\Request;

class ProductOrders extends Round
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $topics_data = $this->getChartData();
        $name_arr = array_column($topics_data, 'name');

        $this->title('话题');
        $this->chartLabels($name_arr);
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
//                $this->withContent(23043, 14658, 4758);
//
//                // 图表数据
//                $this->withChart([70, 52, 26]);
//
//                // 总数
//                $this->chartTotal('Total', 344);
//        }

        $topics_data = $this->getChartData();
        $data_arr = array_column($topics_data, 'works_count');

        // 卡片内容
        $this->withContent($topics_data);

        // 图表数据
        $this->withChart($data_arr);

        // 总数
        $this->chartTotal('总数', array_sum($data_arr));
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(array $data)
    {
        return $this->chart([
            'series' => $data,
        ]);
    }

    /**
     * 获取图表数据
     * @return array
     */
    private function getChartData()
    {
        $topics_data = TopicsCategory::query()
            ->select('id','name')
            ->withCount(['works'])
            ->orderByDesc('works_count')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->toArray();

        return $topics_data;
    }

    /**
     * 卡片内容.
     *
     * @param int $finished
     * @param int $pending
     * @param int $rejected
     *
     * @return $this
     */
    public function withContent($topics_data)
    {
        $html_str = '';

        foreach ($topics_data as $value) {

            $name = $value['name'];
            $works_count = $value['works_count'];

            $html_str .= "<div class='chart-info d-flex justify-content-between mb-1 mt-2' >
          <div class='series-info d-flex align-items-center'>
              <i class='fa fa-circle-o text-bold-700 text-primary'></i>
              <span class='text-bold-600 ml-50'>{$name}</span>
          </div>
          <div class='product-result'>
              <span>{$works_count}</span>
          </div>
    </div>";

        }

        return $this->content(
            <<<HTML
<div class="col-12 d-flex flex-column flex-wrap text-center" style="max-width: 220px">
    $html_str
</div>
HTML
        );
    }
}
