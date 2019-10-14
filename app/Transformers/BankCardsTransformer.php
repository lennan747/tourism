<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2019/10/12
 * Time: 14:16
 */

namespace App\Transformers;

use App\Models\BankCard;
use League\Fractal\TransformerAbstract;

class BankCardsTransformer extends TransformerAbstract
{
    public function transform(BankCard $bankCard)
    {
        return [
            'id'          => $bankCard->id,
            'type'        => $bankCard->type,
            'name'        => $bankCard->name,
            'card_name'   => $bankCard->card_name,
            'account'     => $bankCard->account,
        ];
    }
}
