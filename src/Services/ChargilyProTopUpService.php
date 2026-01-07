<?php

namespace Chargily\ChargilyProLaravel\Services;

use Chargily\ChargilyPro\Api\TopUp;
use Chargily\ChargilyPro\Auth\Credentials;
use Chargily\ChargilyPro\ChargilyPro;
use Chargily\ChargilyPro\Elements\ModeElement;
use Chargily\ChargilyProLaravel\Enums\ChargilyProTopupStatusEnum;
use Chargily\ChargilyProLaravel\Exceptions\InsufficientBalanceException;
use Chargily\ChargilyProLaravel\Exceptions\ModeNotFoundException;
use Chargily\ChargilyProLaravel\Models\ChargilyProTopup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ChargilyProTopUpService
{
    /**
     * Chargily pro instance.
     *
     * @var ChargilyPro
     */
    protected ChargilyPro $chargily;
    /**
     * Chargily pro topup instance.
     *
     * @var TopUp
     */
    protected TopUp $topup;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->chargily = new ChargilyPro(new Credentials(config("chargily-pro.credentials")));
        $this->topup = $this->chargily->topup();
    }
    /**
     * Get operators list
     *
     * @return \Chargily\ChargilyPro\Core\Helpers\Collection
     */
    public function operators()
    {
        return $this->topup->operators()->all();
    }
    /**
     * Get operator details
     *
     * @param string $id
     * @return \Chargily\ChargilyPro\Elements\OperatorElement
     */
    public function getOperator(string $id)
    {
        return $this->operators()->filter(fn($value, $key) => $value->getId() == $id || $value->getName() == $id)->first();
    }
    /**
     * Get modes list
     *
     * @return \Chargily\ChargilyPro\Core\Helpers\Collection
     */
    public function modes()
    {
        return $this->topup->modes()->all();
    }
    /**
     * Get mode details
     *
     * @param string $id
     * @return \Chargily\ChargilyPro\Elements\ModeElement
     */
    public function getMode(string $id)
    {
        return $this->topup->modes()->get($id);
    }
    /**
     * Get TopUp request details
     *
     * @param string $id
     * @return \Chargily\ChargilyPro\Elements\TopUpElement
     */
    public function getRequest(string $id)
    {
        return $this->topup->request()->get($id);
    }
    /**
     * Request topup
     *
     * @param string $country_code
     * @param string $phone_number
     * @param string $operator
     * @param string $mode_name
     * @param string $value
     * @return Model|null
     */
    public function request(string $country_code, string $phone_number, string $operator, string $mode_name, string $value): ?Model
    {
        $user = $this->chargily->user();
        // ===================
        // Retrieve Balance ==
        // ===================
        $balance = $user->balance()->get();
        //
        if ($balance or $balance->getBalance() > 0) {
            // ============================
            // Create new voucher record ==
            // ============================
            $model = config("chargily-pro.models.topups");

            $item = $model::create([
                "country_code" => Str::upper($country_code),
                "phone_number" => Str::ltrim($phone_number, "0"),
                "operator" => $operator,
                "mode_name" => $mode_name,
                "value" => $value,

            ]);
            // =============
            // Lets Start ==
            // =============
            if ($item->id) {
                /// =============================
                /// Change status to processing =
                /// =============================
                $item->status = ChargilyProTopupStatusEnum::PROCESSING;
                $item->update();
                // ==================
                // Start processing =
                // ==================
                try {
                    ///
                    $status = $this->topup->request()->make([
                        'request_number' => $item->id,
                        'customer_name' => config("app.name"),

                        'operator' => $item->operator,
                        'mode' => $item->mode_name,
                        'value' => $item->value,

                        'country_code' => $item->country_code,
                        'phone_number' => $item->phone_number,

                        'webhook_url' => URL::route("chargily-pro.api.topup-webhook"),
                        'created_at' => $item->created_at,
                    ]);
                    if ($status) {
                        $element = $this->topup->request()->get($item->id);
                        //
                        if ($element) {
                            /// ============================
                            /// Check Status topup Status  =
                            /// ============================
                            if ($element->getStatus() !== "pending") {
                                if ($element->getStatus() === "sent") {
                                    /// =============================
                                    /// Change status to completed  =
                                    /// =============================
                                    $item->status = ChargilyProTopupStatusEnum::COMPLETED;
                                } else {
                                    /// ==========================
                                    /// Change status to failed  =
                                    /// ==========================
                                    $item->status = ChargilyProTopupStatusEnum::FAILED;
                                }
                            }
                            $item->update();
                        }
                    }
                } catch (\Chargily\ChargilyPro\Exceptions\InvalidHttpResponse $e) {
                    /// ==================
                    /// Save to log file =
                    /// ==================
                    Log::error($e);
                    /// =========================
                    /// Change status to failed =
                    /// =========================
                    $item->message = $e->getMessage();
                    $item->status = ChargilyProTopupStatusEnum::FAILED;
                    $item->update();
                }
                return $item;
            }
        } else {
            // ===========================
            // Insufficient balance error ==
            // ===========================
            throw new InsufficientBalanceException();
        }
        return null;
    }
    /**
     * Request topup according mode id
     *
     * @param string $country_code
     * @param string $phone_number
     * @param string|ModeElement $mode_id
     * @return Model|null
     */
    public function requestById(string $country_code, string $phone_number, string|ModeElement $mode): ?Model
    {
        $user = $this->chargily->user();
        // ===================
        // Retrieve Balance ==
        // ===================
        $balance = $user->balance()->get();
        //
        if ($balance or $balance->getBalance() > 0) {
            $modes = $this->topup->modes();
            /// =====================
            /// Get voucher details =
            /// =====================
            $details = (is_string($mode)) ? $modes->get($mode) : $mode;
            //
            if ($details) {
                //
                if ($balance->getBalance() >= $details->getAmount()) {
                    // ============================
                    // Create new voucher record ==
                    // ============================
                    $model = config("chargily-pro.models.topups");

                    $item = $model::create([
                        "country_code" => Str::upper($country_code),
                        "phone_number" => Str::ltrim($phone_number, "0"),
                        "operator" => $details->getOperator(),
                        "mode_name" => $details->getName(),
                        "value" => $details->getValue(),

                    ]);
                    // =============
                    // Lets Start ==
                    // =============
                    if ($item->id) {
                        /// =============================
                        /// Change status to processing =
                        /// =============================
                        $item->status = ChargilyProTopupStatusEnum::PROCESSING;
                        $item->update();
                        // ==================
                        // Start processing =
                        // ==================
                        try {
                            ///
                            $status = $this->topup->request()->make([
                                'request_number' => $item->id,
                                'customer_name' => config("app.name"),

                                'operator' => $item->operator,
                                'mode' => $item->mode_name,
                                'value' => $item->value,

                                'country_code' => $item->country_code,
                                'phone_number' => $item->phone_number,

                                'webhook_url' => URL::route("chargily-pro.api.topup-webhook"),
                                'created_at' => $item->created_at,
                            ]);
                            if ($status) {
                                $element = $this->topup->request()->get($item->id);
                                //
                                if ($element) {
                                    /// ============================
                                    /// Check Status topup Status  =
                                    /// ============================
                                    if ($element->getStatus() !== "pending") {
                                        if ($element->getStatus() === "sent") {
                                            /// =============================
                                            /// Change status to completed  =
                                            /// =============================
                                            $item->status = ChargilyProTopupStatusEnum::COMPLETED;
                                        } else {
                                            /// ==========================
                                            /// Change status to failed  =
                                            /// ==========================
                                            $item->status = ChargilyProTopupStatusEnum::FAILED;
                                        }
                                    }
                                    $item->update();
                                }
                            }
                        } catch (\Chargily\ChargilyPro\Exceptions\InvalidHttpResponse $e) {
                            /// ==================
                            /// Save to log file =
                            /// ==================
                            Log::error($e);
                            /// =========================
                            /// Change status to failed =
                            /// =========================
                            $item->message = $e->getMessage();
                            $item->status = ChargilyProTopupStatusEnum::FAILED;
                            $item->update();
                        }
                        return $item;
                    }
                } else {
                    // ===========================
                    // Insufficient balance error ==
                    // ===========================
                    throw new InsufficientBalanceException();
                }
            } else {
                // =================
                // Mode Not found ==
                // =================
                throw new ModeNotFoundException($mode);
            }
        } else {
            // =============================
            // Insufficient balance error ==
            // =============================
            throw new InsufficientBalanceException();
        }
        return null;
    }
}
