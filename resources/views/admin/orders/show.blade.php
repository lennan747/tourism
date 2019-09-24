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
                <td>买家电话号：</td>
                <td>{{ $order->user->phone }}</td>
                <td>支付时间：</td>
                <td></td>
            </tr>
            <tr>
                <td>订单金额：</td>
                <td colspan="3">￥{{ $order->total_amount }}</td>
            </tr>
            @if($order->pay_status === \App\Models\Order::PAY_STATUS_UNPAID)
                <tr>
                    <td colspan="2">
                        <form action="{{ route('admin.orders.review',[$order->id]) }}" method="post" class="form-inline">
                            {{ csrf_field() }}
                            <div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
                                <label for="payment_method" class="control-label">支付方式</label>
                                <select class="form-control select2-search--inline" name="payment_method" id="">
                                    <option value="WeChatPay">微信支付</option>
                                    <option value="AliPay">支付宝支付</option>
                                    <option value="BankCaryPay">银行卡支付</option>
                                    <option value="CashPay">现金支付</option>
                                    <option value="OrderPay">其他支付</option>
                                </select>
                                @if($errors->has('payment_method'))
                                    @foreach($errors->get('payment_method') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('remark') ? 'has-error' : '' }}">
                                <label for="remark" class="control-label">订单备注</label>
                                <textarea name="remark" id="remark" cols="100" placeholder="订单备注"></textarea>
                                @if($errors->has('remark'))
                                    @foreach($errors->get('remark') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn-app btn-dropbox" id="ship-btn">发货</button>
                        </form>
                    </td>
                </tr>
            @else
            @endif
            </tbody>
        </table>
    </div>
</div>