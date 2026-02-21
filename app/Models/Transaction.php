<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\ActivityLoggable;

class Transaction extends Model
{
    use ActivityLoggable;
    //Models Information Data
    /********
     *
     *
     * Column:
     *
     * id               =bigint(20):None,
     * user_id          =bigint(20):null,
     * provider_name    =varchar(255):null,
     * provider_id      =varchar(255):null,
     * provider_token   =varchar(255):null,
     * provider_img_url =varchar(255):null,
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     *
     *
     *
     ****/
    protected $guarded = [];
    public function imageFile(){
    	return $this->hasOne(Media::class,'src_id')->where('src_type',9)->where('use_Of_file',1);
    }

    public function purchase(){
    	return $this->belongsTo(PurchaseOrder::class,'src_id');
    }

    public function expense(){
    	return $this->belongsTo(Expense::class,'src_id');
    }

    public function expenseIou(){
    	return $this->belongsTo(ExpenseIou::class,'src_id');
    }

    public function method(){
    	return $this->belongsTo(Attribute::class,'src_id');
    }

    public function account(){
    	return $this->belongsTo(Attribute::class,'account_id');
    }

    public function paymentMethod(){
    	return $this->belongsTo(Attribute::class,'payment_method_id');
    }

    public function sale(){
    	return $this->belongsTo(Order::class,'src_id');
    }
    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }

    public function company(){
    	return $this->belongsTo(Company::class,'user_id');
    }


    public function assinee(){
    	return $this->belongsTo(User::class,'addedby_id');
    }

    public function purchaseOrder(){
    	return $this->belongsTo(PurchaseOrder::class, 'src_id');
    }

    public static function accountAffectingQuery(int $accountId)
    {
        return static::query()->where(function ($q) use ($accountId) {
            $q->where('account_id', $accountId)
                ->orWhere(function ($q) use ($accountId) {
                    $q->where('type', 4)
                      ->where(function ($q) use ($accountId) {
                          $q->where('src_id', $accountId)
                            ->orWhere('payment_method_id', $accountId);
                      });
                });
        });
    }

    public static function accountBalance(
        int $accountId,
        $from = null,
        $to = null,
        ?string $currency = null,
        bool $before = false
    ): float {
        $query = static::accountAffectingQuery($accountId)
            ->where('status', 'success');

        if ($currency === 'USD') {
            $query->where('currency', 'USD');
        } elseif ($currency === 'BDT') {
            $query->where(function ($q) {
                $q->whereNull('currency')->orWhere('currency', '<>', 'USD');
            });
        }

        if ($before && $from) {
            $query->where('created_at', '<', $from);
        } else {
            if ($from) {
                $query->where('created_at', '>=', $from);
            }
            if ($to) {
                $query->where('created_at', '<=', $to);
            }
        }

        return (float) ($query->selectRaw("
            SUM(
                CASE
                    WHEN type IN (0,1) AND account_id = ? THEN amount
                    WHEN type IN (3,5,6,7) AND account_id = ? THEN -amount
                    WHEN type = 4 AND src_id = ? THEN -amount
                    WHEN type = 4 AND payment_method_id = ? THEN amount
                    ELSE 0
                END
            ) as balance
        ", [$accountId, $accountId, $accountId, $accountId])->value('balance') ?? 0);
    }

    public function signedAmountForAccount(int $accountId): float
    {
        if ((int) $this->type === 4) {
            if ((int) $this->src_id === $accountId) {
                return -1 * (float) $this->amount;
            }
            if ((int) $this->payment_method_id === $accountId) {
                return (float) $this->amount;
            }
            return 0;
        }

        if ((int) $this->account_id !== $accountId) {
            return 0;
        }

        if (in_array($this->type, [0, 1], true)) {
            return (float) $this->amount;
        }

        if (in_array($this->type, [3, 5, 6, 7], true)) {
            return -1 * (float) $this->amount;
        }

        return 0;
    }


}
