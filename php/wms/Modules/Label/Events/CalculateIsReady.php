<?php

namespace Modules\Label\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\LabelArrival;

class CalculateIsReady
{
    use SerializesModels;

    protected $invoice_no;
    protected $item_no;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($invoice_no, $item_no)
    {
        $this->invoice_no = $invoice_no;
        $this->item_no = $item_no;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    // 计算到位率和到位日期
    public function calculate()
    {
        $arrival = LabelArrival::where(['invoice_no' => $this->invoice_no, 'item_no' => $this->item_no])->first();
        $invoice = Invoice::where(['invoice_no' => $this->invoice_no, 'item_no' => $this->item_no])->first();
        if (!$arrival || !$invoice) return true;
        if ($invoice->confirm_num == 0) return true;
        $this->updateInvoice($arrival, $invoice);
    }

    // 更新到位率和时间
    private function updateInvoice($arrival, $invoice)
    {
        $rate = round($arrival->confirm_num * 100/$invoice->confirm_num, 2);
        if ($invoice->ready_rate != $rate) {
            $invoice->ready_rate = $rate;
            if ($rate < 100) {
                $invoice->ready_time = null;
            } else {
                if (!$invoice->ready_time) {
                    $invoice->ready_time = date('Y-m-d H:i:s');
                }
            }
            $invoice->save();
        }
        
        return true;
    }
}
