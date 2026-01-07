<?php

namespace Chargily\ChargilyProLaravel\Http\Controllers\Api\V1;

use Chargily\ChargilyPro\Auth\Credentials;
use Chargily\ChargilyPro\ChargilyPro;
use Chargily\ChargilyPro\Elements\WebhookElement;
use Chargily\ChargilyProLaravel\Enums\ChargilyProTopupStatusEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle webhook request
     *
     * @return void
     */
    public function handle()
    {
        $message = null;

        DB::beginTransaction();
        try {
            $chargily = new ChargilyPro(new Credentials(config("chargily-pro.credentials")));

            $webhook = $chargily->topup()->webhook();

            $element = $webhook->capture();

            if ($element and $element instanceof WebhookElement) {
                $model = config("chargily-pro.models.topups");
                //// ========================
                //// Check if exists in DB ==
                //// ========================
                $item_id = $element->getRequestNumber();
                $item = $model::find($item_id);
                //
                if ($item) {
                    //
                    if ($element->getStatus() === "sent") {
                        // =============================
                        // Change status to completed ==
                        // =============================
                        $item->status = ChargilyProTopupStatusEnum::COMPLETED;
                    } elseif (!in_array($element->getStatus(), ["pending", "sent"])) {
                        // ==========================
                        // Change status to failed ==
                        // ==========================
                        $item->status = ChargilyProTopupStatusEnum::FAILED;
                    }

                    $item->update();
                    //confirm changes
                    DB::commit();
                    ///
                    return new JsonResponse([
                        "status" => true,
                        "message" => "OK",
                    ], 200);
                }
            }
        } catch (Exception $e) {
            //rollback
            DB::rollBack();
            // laravel.log
            Log::error($e);
        }

        return new JsonResponse([
            "status" => false,
            "message" => "FORBIDDEN",
        ], 403);
    }
}
