<?php

namespace App\Observers;

use App\Models\OrderDetail;
use App\Models\ProformaInvoiceItem;
use App\Models\ProductionPlanning;
use App\Models\OrderDetailItem;

class OrderDetailObserver
{
    /**
     * Handle the OrderDetail "updated" event.
     */
    public function updated(OrderDetail $order)
    {
        // Get OLD values (important)
        $oldOrderNo = $order->getOriginal('order_no');
        $oldStyle   = $order->getOriginal('style_no');


        /*
        |--------------------------------------------------------------------------
        | Fabrication Sync
        |--------------------------------------------------------------------------
        */
        if ($order->wasChanged('fabrication')) {

            $this->updatePiItems($order, $oldOrderNo, $oldStyle, [
                'fabrication' => $order->fabrication,
            ]);

            OrderDetailItem::where('order_detail_id', $order->id)
                ->update([
                    'fabrication' => $order->fabrication,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Shipment Date Sync
        |--------------------------------------------------------------------------
        */
        if ($order->wasChanged('shipment_date')) {

            $this->updatePiItems($order, $oldOrderNo, $oldStyle, [
                'shipment_date' => $order->shipment_date,
            ]);

            OrderDetailItem::where('order_detail_id', $order->id)
                ->update([
                    'shipment_date' => $order->shipment_date,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Style No Sync
        |--------------------------------------------------------------------------
        */
        if ($order->wasChanged('style_no')) {

            // PI Items
            ProformaInvoiceItem::where('order_no', $oldOrderNo)
                ->where('style_no', $oldStyle)
                ->update([
                    'style_no' => $order->style_no,
                ]);

            // Production Planning
            ProductionPlanning::where('order_no', $oldOrderNo)
                ->where('style_no', $oldStyle)
                ->update([
                    'style_no' => $order->style_no,
                ]);

            // Order Items
            OrderDetailItem::where('order_detail_id', $order->id)
                ->update([
                    'style_no' => $order->style_no,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Order No Sync
        |--------------------------------------------------------------------------
        */
        if ($order->wasChanged('order_no')) {

            // PI Items
            ProformaInvoiceItem::where('order_no', $oldOrderNo)
                ->update([
                    'order_no' => $order->order_no,
                ]);

            // Production Planning
            ProductionPlanning::where('order_no', $oldOrderNo)
                ->update([
                    'order_no' => $order->order_no,
                ]);

            // Order Items
            OrderDetailItem::where('order_detail_id', $order->id)
                ->update([
                    'order_no' => $order->order_no,
                ]);
        }
    }



    /**
     * Update PI Items safely using old + new values
     */
    protected function updatePiItems($order, $oldOrderNo, $oldStyle, array $data)
    {
        ProformaInvoiceItem::where(function ($q) use ($order, $oldOrderNo, $oldStyle) {

            // Match new values
            $q->where(function ($q1) use ($order) {
                $q1->where('order_no', $order->order_no)
                   ->where('style_no', $order->style_no);
            });

            // OR match old values
            $q->orWhere(function ($q2) use ($oldOrderNo, $oldStyle) {
                $q2->where('order_no', $oldOrderNo)
                   ->where('style_no', $oldStyle);
            });

        })->update($data);
    }
}
