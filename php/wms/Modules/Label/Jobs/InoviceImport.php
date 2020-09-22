<?php

namespace Modules\Label\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Modules\Label\Utils\Import;
use Modules\Label\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Http\Controllers\BaseController;

class InoviceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        Redis::setex('invoice_import_count_' . $this->user->id, 24 * 3600, 0);
        Redis::del('invoice_import_errors_' . $this->user->id);
        $path = Redis::get('invoice_import_path_' . $this->user->id);
        if (!$path) {
            $this->finish();
            return true;
        }
        try {
            log::info("开始校验:" . date('Y-m-d H:i:s') . "\n");
            $import = new Import($path, 'invoices');
            $excelData = $import->getImportData();
            if ($excelData['errors']) {
                $this->finish();
                Log::info($path . '基础数据验证错误');
                Redis::setex('invoice_import_errors_' . $this->user->id, 24 * 3600, json_encode($excelData['errors']));
                return true;
            }
            $count = 0;
            log::info("开始导入:" . date('Y-m-d H:i:s') . "\n");
            DB::connection('labelDB')->beginTransaction();
            // 合并同样的
            $tmp = [];
            foreach ($excelData['data'] as $item) {
                $key = $item['invoice_no'] . '_' . $item['item_no'];
                if (isset($tmp[$key])) {
                    $tmp[$key]['num'] += $item['num'];
                } else {
                    $tmp[$key] = $item;
                }
            }
            foreach ($tmp as $item) {
                $search = [
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no']
                ];
                $item['arrivetime'] = date('Y-m-d', strtotime($item['arrivetime']));
                $invoice = Invoice::where($search)->first();
                if (!$invoice) {
                    Invoice::create($item);
                } else {
                    $invoice->num = $item['num'];
                    $invoice->save();
                }
                $count++;
                Redis::setex('invoice_import_count_' . $this->user->id, 24 * 3600, $count);
            }
            DB::connection('labelDB')->commit();
            log::info("导入完成:" . date('Y-m-d H:i:s') . "\n");
            $this->finish();
        } catch (\Exception $e) {
            log::info("导入报错:" . date('Y-m-d H:i:s') . "\n");
            $this->finish();
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            $errors = [['line' => 0, 'message' => $e->getMessage()]];
            Redis::setex('invoice_import_errors_' . $this->user->id, 24 * 3600, json_encode($errors));
        }
        if ($count > 0) {
            $bc = new BaseController;
            $bc->log($bc::LOG_IMPORT, '导入' . $count . '条发票清单数据');
        }

        return true;
    }

    private function finish()
    {
        Redis::del('invoice_import_path_' . $this->user->id);
        Redis::del('invoice_import_dealing_' . $this->user->id);
    }
}
