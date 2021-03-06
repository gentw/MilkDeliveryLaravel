<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\Order;
use DateTime;
use DateTimeZone;

class MilkManDeliveryPlan extends Model
{
    use SoftDeletes;

    protected $table = "milkmandeliveryplan";

    protected $fillable =[
        'order_id',
        'station_id',
        'customer_id',
        'order_product_id',
        'time',
        'status',
        'product_price',
        'produce_at',
        'deliver_at',
        'plan_count',
        'changed_plan_count',
        'delivery_count',
        'delivered_count',
        'comment',
        'report',
        'type',
        'flag',
        'milkman_id',
        'cancel_reason',
    ];

    protected $appends = [
    ];

    const MILKMAN_DELIVERY_PLAN_TYPE_USER = 1;
    const MILKMAN_DELIVERY_PLAN_TYPE_GROUP = 2;
    const MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL = 3;
    const MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK = 4;
    const MILKMAN_DELIVERY_PLAN_TYPE_RETAIL = 6;
    const MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL = 5;

    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER = 1;
    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE = 2;

    const MILKMAN_DELIVERY_PLAN_STATUS_CANCEL = 0;
    const MILKMAN_DELIVERY_PLAN_STATUS_WAITING = 1;
    const MILKMAN_DELIVERY_PLAN_STATUS_PASSED = 2;
    const MILKMAN_DELIVERY_PLAN_STATUS_SENT = 3;
    const MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED = 4;

    const DP_CANCEL_PRODUCE         = 1;
    const DP_CANCEL_POSTPONE        = 2;
    const DP_CANCEL_CHANGEORDER     = 3;

    const NOTICE_NONE = 0;
    const NOTICE_FIRST_DEVLIVER = 1;
    const NOTICE_ALMOST_END = 2;
    const NOTICE_END_TODAY = 3;

    /**
     * 获取奶站
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }

    /**
     * 获取奶品图片
     * @return mixed
     */
    public function getPlanProductImage()
    {
        $order_product = OrderProduct::find($this->order_product_id);
        if($order_product)
        {
            $product = $order_product->product;
            return $product->photo_url1;
        }
    }

    /**
     * 获取状态名称
     * @return string
     */
    public function getStatusName(){
        $strStatus = "";

        if ($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED) {
            $strStatus = "已配送";
        }
        else if ($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL) {
            if ($this->cancel_reason == $this::DP_CANCEL_CHANGEORDER) {
                $strStatus = "订单修改";
            }
            else if ($this->cancel_reason == $this::DP_CANCEL_POSTPONE) {
                $strStatus = "已顺延";
            }
            else {
                $strStatus = "生产取消";
            }
        }
        else {
            $strStatus = "未配送";
        }

        return $strStatus;
    }

    /**
     * 获取奶品名称
     * @return mixed
     */
    public function getProductName()
    {
        return $this->order_product->product_name;
    }

    /**
     * 获取奶品简称
     * @return mixed
     */
    public function getProductSimpleName()
    {
        return $this->order_product->product_simple_name;
    }

    /**
     * 获取该配送明细的金额
     * @return mixed
     */
    public function getPlanPrice()
    {
        $plan_price = ($this->product_price) * ($this->changed_plan_count);
        return $plan_price;
    }

    public function order(){
        if($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            return $this->orderDelivery();
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrder', 'order_id', 'id');
    }

    /**
     * 获取配送订单
     * @return mixed
     */
    public function orderDelivery() {
        return $this->belongsTo('App\Model\OrderModel\Order', 'order_id');
    }

    public function order_product(){
        if($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            return $this->orderProduct();
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrderProduct', 'order_product_id', 'id');
    }

    /**
     * 获取订单奶品
     * @return mixed
     */
    public function orderProduct() {
        return $this->belongsTo('App\Model\OrderModel\OrderProduct', 'order_product_id')->withTrashed();
    }

    /**
     * 获取配送员
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function milkman(){
        return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

    public function getTypeDesc() {
        $strRes = '';

        if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER) {
            $strRes = '计划订单';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP) {
            $strRes = '团购业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK) {
            $strRes = '试饮赠品';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL) {
            $strRes = '渠道业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL) {
            $strRes = '店内零售';
        }

        return $strRes;
    }

    /**
     * 是否奶箱安装
     * @return bool
     */
    public function isBoxInstall() {
        return ($this->flag && $this->order->milk_box_install);
    }

    /**
     * 设置配送数量
     * @param $value
     */
    public function setCount($value) {

        $this->changed_plan_count = $value;

        // 已提交生产计划才算是修改
        if ($this->status > MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING &&
            $this->status < MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT) {
            $this->plan_count = $value;
            $this->delivery_count = $value;
        }

        $this->save();
    }

    /**
     * 获取奶品id
     * @return int
     */
    public function getProductId() {
        $nProductId = 0;

        // 这个明细已生成，直接用奶品id
        if ($this->order_product) {
            $nProductId = $this->order_product->product->id;
        }
        else {
            // 这个明细未生成，查询奶品id
            $product = OrderProduct::find($this->order_product_id);
            if ($product) {
                $nProductId = $product->product->id;
            }
        }

        return $nProductId;
    }

    /**
     * 根据生产日期，决定配送明细的状态
     */
    public function determineStatus() {
        // 待审核状态就直接退出
        if ($this->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING) {
            return;
        }

        $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $nProductId = $this->getProductId();

        $factory = $this->station->factory;

        // 计算提交日期
        $dateSubmit = getDateWithOffsetString(-1 - $factory->prod_offset, $this->produce_at);
        $datetimeSubmit = DateTime::createFromFormat('Y-m-j', $dateSubmit);

        // 默认是通过状态
        $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;

        // 提交日期已过
        if ($dateCurrent > $datetimeSubmit) {
            $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
        }
        else {
            // 如果今天是提交日期，是查看是否已提交
            $count = DSProductionPlan::where('station_id', $this->station_id)
                ->where('produce_start_at', $this->produce_at)
                ->where('product_id', $nProductId)
                ->count();

            // 已提交，状态就设成已提交
            if ($count) {
                $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
            }
        }
    }

    /**
     * 审核通过处理
     * @param $passed - true: 通过, false: 不通过
     */
    public function passCheck($passed) {
        if ($passed) {
            // 把待审核状态设成通过
            if ($this->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING) {
                $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;
                $this->determineStatus();
                $this->setCount($this->changed_plan_count);
            }
        }
        else {
            // 不通过
            $this->delete();
        }
    }

    /**
     * 查询能否修改数量
     * @return bool
     */
    public function isEditAvailable() {
        $editAvailable = true;

        // 已完成或已取消的不能修改
        if ($this->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED ||
            $this->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL) {
            $editAvailable = false;
        }
        else {
            // 配送时间已过的不能修改
            $dateCurrent = date(getCurDateString());
            $dateDeliver = date($this->deliver_at);

            if ($dateCurrent > $dateDeliver) {
                $editAvailable = false;
            }
            else if ($dateCurrent == $dateDeliver) {
                // 已配送、配送取消，当天配送列表生成的情况下不能修改
                if (DSDeliveryPlan::getDeliveryPlanGenerated($this->station_id, $this->order_product->product->id)) {
                    $editAvailable = false;
                }
            }
        }

        return $editAvailable;
    }
}
