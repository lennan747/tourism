<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\BankCardRequest;
use App\Models\BankCard;
use App\Transformers\BankCardsTransformer;
use Illuminate\Http\Request;

class BankCardsController extends Controller
{
    //

    public function index(){
        $bank_list = $this->user()->bank_card()->get();
        return $this->response->collection($bank_list,new BankCardsTransformer())->setStatusCode(200);
    }

    public function store(BankCardRequest $request)
    {
        $user = $this->user();
        $type = ['wechat', 'alipay', 'bank'];

        if(!in_array($request->type,$type)){
            return $this->response->error('提现类型错误', 422);
        }

        $bank_card = new BankCard([
            'type'       => $request->type,
            'name'       => $request->name,
            'card_name'  => $request->card_name,
            'account'    => $request->account
        ]);

        $bank_card->user()->associate($user);
        $bank_card->save();

        return $this->response->item($bank_card, new BankCardsTransformer())->setStatusCode(201);
    }

    public function update(BankCardRequest $request,BankCard $card)
    {
        $this->authorize('update', $card);
        $card->update($request->all());
        return $this->response->item($card, new BankCardsTransformer());
    }

    public function destroy(BankCard $card)
    {
        $this->authorize('destroy', $card);
        $card->delete();
        return $this->response->noContent();
    }
}
