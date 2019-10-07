<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group float-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>订单金额：</td>
                <td>￥{{ $order->total_amount }}</td>
                <td>支付时间：</td>
                <td>{{ $order->paid_at ? $order->paid_at : '0000-00-00' }}</td>
                <td>订单类型：</td>
                <td>{{ \App\Models\Order::$orderTypeMap[$order->type] }}</td>
                <td>支付状态：</td>
                <td>{{ \App\Models\Order::$payStatusMap[$order->pay_status] }}</td>
            </tr>
            <tr>
                <td colspan="1">订单利润：</td>
                <td colspan="1">￥{{ $order->total_profit }}</td>
                <td colspan="1">买家电话号：</td>
                <td colspan="5">{{ $order->user->phone }}</td>
            </tr>

            @if($order->type === \App\Models\Order::ORDER_TYPE_TOURISM)
                <tr>
                    <td rowspan="{{ $order->items->count() + 1 }}"  colspan="1">商品列表</td>
                    <td colspan="2">商品名称</td>
                    <td colspan="1">单价</td>
                    <td colspan="1">利润</td>
                    <td colspan="1">数量</td>
                    <td colspan="1">单价 ✖ 数量</td>
                    <td colspan="1">利润 ✖ 数量</td>
                </tr>
                @foreach($order->items as $item)
                    <tr>
                        <td colspan="2">{{ $item->product->title }} {{ $item->productSku->title }}</td>
                        <td colspan="1">￥{{ $item->price }}</td>
                        <td colspan="1">￥{{ $item->profit }}</td>
                        <td colspan="1">{{ $item->amount }}</td>
                        <td colspan="1">￥{{ $item->price * $item->amount }}</td>
                        <td colspan="1">￥{{ $item->profit * $item->amount }}</td>
                    </tr>
                @endforeach
            @endif
            <!-- 未支付状态 -->
            @if($order->pay_status === \App\Models\Order::PAY_STATUS_UNPAID)
                <tr>
                    <td colspan="8">
                        <form action="{{ route('admin.orders.review',['id' => $order->id]) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-inline col-md-12 payment_method {{ $errors->has('remark') ? 'has-error' : '' }}"
                                            data-placeholder="选择支付方式"
                                            id=""
                                    >
                                        <option value="WeChatPay">微信支付</option>
                                        <option value="AliPay">支付宝支付</option>
                                        <option value="BankCaryPay">银行卡支付</option>
                                        <option value="CashPay">现金支付</option>
                                        <option value="OrderPay">其他支付</option>
                                    </select>
                                    <input type="hidden" name="payment_method" value="WeChatPay" placeholder="选择支付方式">
                                    @if($errors->has('payment_method'))
                                        @foreach($errors->get('payment_method') as $msg)
                                            <span class="help-block">{{ $msg }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-md-2">
                                    <input class="form-inline col-md-12 form-inline {{ $errors->has('remark') ? 'has-error' : '' }}"
                                           type="text"
                                           name="remark"
                                           placeholder="订单备注"
                                           style="line-height: 28px;"
                                    >
                                    @if($errors->has('remark'))
                                        @foreach($errors->get('remark') as $msg)
                                            <span class="help-block">{{ $msg }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn" style="background-color: #00bcd4; color: #f0f0f0;">审核</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="1">订单备注：</td>
                    <td colspan="7">{{ $order->remark }}</td>
                </tr>
                <tr>
                    <td colspan="1">支付方式：</td>
                    <td colspan="7">{{ $order->payment_method }}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>


<script>
    $(function () {
        const payment_method = $('.payment_method');
        payment_method.select2();
        payment_method.on('change',function () {
            console.log(111);
            $('input[name="payment_method"]').val(payment_method.val());
        });
    });
</script>
