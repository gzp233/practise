<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Outstorage\AntiCode;

class SendAntiCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        $anticodes = AntiCode::where('status', 0)->get();
        $shds = [];
        foreach ($anticodes as $anticode) {
            if (!in_array($anticode->SHIPMENTID, $shds)) {
                $shds[] = $anticode->SHIPMENTID;
            }
            $rsp = $this->sendQRCode($anticode);
            if (empty($rsp)){
                continue;
            }
            $anticode->status = 1;
            if ($rsp->success == false) {
                $anticode->error = $rsp->errorinfo;
            } else {
                $anticode->error = null;
            }
            $anticode->save();
        }

        // 检查是否有完成的
        foreach ($shds as $shd) {
            $allCount = AntiCode::where('SHIPMENTID',$shd)->count();
            $successCount = AntiCode::where(['SHIPMENTID' => $shd, 'status' => 1])->whereNull('error')->count();
            if ($allCount == $successCount) {
                AntiCode::where('SHIPMENTID',$shd)->update(['endtime' => date('Y-m-d H:i:s')]);
            }
        }
        return true;
    }

    private function sendQRCode($anticode)
    {
        $url = env('ANTICODE_URL');
        if (!$url) return;
        $data = [
            'AUTHCODE' => $anticode->AUTHCODE,
            'SHIPMENTID' => $anticode->SHIPMENTID,
            'CUSTOMER' => $anticode->CUSTOMER,
            'CUSTOMERNAME' => $anticode->CUSTOMERNAME,
            'PRODUCTNAME' => $anticode->PRODUCTNAME,
            'PRODUCTCODE' => $anticode->PRODUCTCODE,
            'SHIPTIME' => $anticode->SHIPTIME,
            'QRCODE' => $anticode->QRCODE,
            'UNIT' => $anticode->UNIT,
            'NUM' => $anticode->NUM,
            'FROM' => $anticode->FROM,
            'EMPLOYEE' => $anticode->EMPLOYEE,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)),
        ));
        $res = json_decode(curl_exec($curl));
        curl_close($curl);

        return $res;
    }
}
