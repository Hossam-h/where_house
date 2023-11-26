<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Refund extends Model
{
    use HasFactory;

    protected $guarded = [];


     public function delivery()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }

    public function packingUser()
    {
        return $this->belongsTo(PackingUser::class,'packed_user_id');
    }

    public function refunProducts(){
        return $this->belongsToMany(Product::class,'refund_products')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }

    public function products(){
        return $this->belongsToMany(Product::class,'refund_products')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }


    public function scopeDailyFilter($query)
    {
        return $query->when(request('from_date') && request('to_date') && (!request('period')), function ($filter) {
            return $filter->whereDate('created_at', '>=', request('from_date'))->whereDate('created_at', '<=', request('to_date'));
            })->when(request('from_date_time') && request('to_date_time') && (!request('period')), function ($filter) {
                return $filter->where('created_at', '>=', request('from_date_time'))->where('created_at', '<=', request('to_date_time'));
            })->when(request('mandoobs'), function ($filter) {
                $mandoobs = explode(',', request('mandoobs'));
                return $filter->whereIn('mandoob_id', $mandoobs);
            })->when(request('period'), function ($filter) {
                $filter->when(request('period') == 'today', function ($subFilter) {
                    $subFilter->whereDate('created_at', Carbon::today()->format('Y-m-d'));

                })->when(request('period') == 'yesterday', function ($subFilter) {
                        $subFilter->whereDate('created_at', Carbon::yesterday()->format('Y-m-d'));

                    })->when(request('supervisor_id'), function ($filter) {
                        return $filter->whereHas('mandoob', function ($query) {
                            $query->where('supervisor_id', request('supervisor_id'));
                        });
                    })->when(request('mandoob_type'), function ($filter) {
                        return $filter->whereHas('mandoob', function ($query) {
                            $query->where('type', request('mandoob_type'));
                        });
                    })->when(request('area_id'), function ($filter) {
                        return $filter->whereHas('mandoob', function ($query) {
                            $query->whereHas('areas', function ($query) {
                                return $query->where('area_id', request('area_id'));
                            });
                        });
                    })->when(request('period') == 'month', function ($subFilter) {
                        $subFilter->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                    })->when(request('period') == 'last_month', function ($subFilter) {
                        $date = date("Y-m", strtotime("-1 months"));
                        $subFilter->whereMonth('created_at', explode('-', $date)[1])->whereYear('created_at', explode('-', $date)[0]);

                    })->when(request('period') == 'year', function ($subFilter) {
                        $subFilter->whereYear('created_at', Carbon::now()->year);

                    })->when(request('period') == 'last_year', function ($subFilter) {
                        $subFilter->whereYear('created_at', Carbon::now()->year - 1);

                    });
            });
    }

}
