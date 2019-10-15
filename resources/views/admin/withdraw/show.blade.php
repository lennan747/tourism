<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">提现内容</h3>
        <div class="box-tools">
            <div class="btn-group float-right" style="margin-right: 10px">
                <a href="" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td colspan="6" class="text-center">提现信息</td>
            </tr>
            <tr>
                <td>申请用户：</td>
                <td class="text-red">{{ $withdraw->user->name }}</td>
                <td>申请时间：</td>
                <td class="text-red">{{ $withdraw->created_at }}</td>
                <td>申请金额：</td>
                <td class="text-red">{{ $withdraw->application_amount }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-center">提现银行卡信息</td>
            </tr>
            <tr>
                <td>类型：</td>
                <td class="text-red">{{ $type[$withdraw->bank_card['type']] }}</td>
                <td>账户名称：</td>
                <td class="text-red">{{ $withdraw->bank_card['card_name'] }}</td>
                <td>账户号：</td>
                <td class="text-red">{{ $withdraw->bank_card['account'] }}</td>
            </tr>

            <!-- 拒绝 -->
            @if($withdraw->status === \App\Models\Withdraw::REVIEW_STATUS_REFUSE)
                <tr>
                    <td colspan="6" class="text-center">拒绝理由</td>
                </tr>
                <tr>
                    <td colspan="6">{{ $withdraw->reason }}</td>
                </tr>
            @endif
            <!-- 通过 -->
            @if($withdraw->status === \App\Models\Withdraw::REVIEW_STATUS_BY)
                <tr>
                    <td colspan="6" class="text-center">提现成功信息</td>
                </tr>
                <tr>
                    <td>实际转账金额：</td>
                    <td class="text-red">{{ $withdraw->transfer_amount }}</td>
                    <td>转账时间：</td>
                    <td class="text-red">{{ $withdraw->transfer_date }}</td>
                    <td>转账费率：</td>
                    <td class="text-red">{{ $withdraw->handling_fee }}%</td>
                </tr>
            @endif

            </tbody>
        </table>
        <div class="box" style="margin-top: 50px;">
            <!-- 申请 -->
            @if($withdraw->status === \App\Models\Withdraw::REVIEW_STATUS_APPLICATION)
                <div class="box-header">
                    <div class="box-title">提现审核</div>
                </div>
                <div class="box-body">
                    <div class="col-md-12 text-center text-red">
                        系统设置提现费率为 {{ site_config()['bank_rate']['value'] }}%
                    </div>
                    <form action="{{ route('admin.withdraws.update',['id' => $withdraw->id]) }}" method="post">
                        {{ method_field('PUT') }}
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="transfer_amount">实际转账金额：</label>
                                <input type="number" name="transfer_amount" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="transfer_date">转账时间：</label>
                                <input id="transfer_date" type="text" name="transfer_date" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="reason">拒绝理由：</label>
                                <input type="text" name="reason" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="transfer_date">审核：</label>
                                <select class="form-control"
                                        data-placeholder="选择支付方式"
                                        id="status"
                                >
                                    <option value="by">通过</option>
                                    <option value="Refuse">拒绝</option>
                                </select>
                                <input type="hidden" name="status" value="by" placeholder="通过">
                            </div>
                            <div class="form-group col-md-2">
                                <input type="hidden" name="user_id" value="{{ $withdraw->user_id }}">
                                <button class="btn btn-app btn-danger">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>


<script>
    $(function () {
        let transfer_date = $('#transfer_date');
        transfer_date.datepicker({autoclose: true, format: 'yyyy-mm-dd', language: 'zh-CN',});

        const status = $('#status');
        status.select2();
        status.on('change', function () {
            $('input[name="status"]').val(status.val());
        });
    });
</script>
