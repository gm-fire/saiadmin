<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saiadmin\app\logic\system;

use plugin\saiadmin\app\model\system\SystemLoginLog;
use plugin\saiadmin\basic\BaseLogic;

/**
 * 登录日志逻辑层
 */
class SystemLoginLogLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new SystemLoginLog();
    }

    /**
     * 登录统计图表
     * @return array
     */
    public function loginChart(): array
    {
        $dates = [];
        for ($i = 9; $i >= 0; $i--) {
            $dates[] = date('Y-m-d', strtotime("-$i days"));
        }

        $placeholders = implode(',', array_fill(0, count($dates), '?'));
        $data = $this->model->whereRaw("DATE(login_time) IN ($placeholders)", $dates)
            ->field('DATE(login_time) as login_date, COUNT(*) as login_count')
            ->group('DATE(login_time)')
            ->order('login_date', 'ASC')
            ->select()
            ->toArray();

        $dataMap = array_column($data, 'login_count', 'login_date');
        
        return [
            'login_count' => array_map(function($date) use ($dataMap) {
                return $dataMap[$date] ?? 0;
            }, $dates),
            'login_date'  => $dates,
        ];
    }

}
