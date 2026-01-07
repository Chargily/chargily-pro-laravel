<?php

namespace Chargily\ChargilyProLaravel\Services;

use Chargily\ChargilyPro\Auth\Credentials;
use Chargily\ChargilyPro\ChargilyPro;
use Chargily\ChargilyPro\Core\Helpers\Collection;
use Chargily\ChargilyPro\Elements\VoucherElement;
use Chargily\ChargilyProLaravel\Enums\ChargilyProVoucherStatusEnum;
use Chargily\ChargilyProLaravel\Exceptions\InsufficientBalanceException;
use Chargily\ChargilyProLaravel\Exceptions\VoucherNotFoundException;
use Chargily\ChargilyProLaravel\Exceptions\VoucherOutOfStockException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ChargilyProVoucherService
{
    /**
     * Chargily pro instance.
     *
     * @var ChargilyPro
     */
    protected ChargilyPro $chargily;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->chargily = new ChargilyPro(new Credentials(config("chargily-pro.credentials")));
    }

    /**
     * Get specific voucher id
     *
     * @param string $id
     * @return \Chargily\ChargilyPro\Elements\VoucherElement|null
     */
    public function get(string $id)
    {
        return $this->chargily->voucher()->vouchers()->get($id);
    }
    /**
     * Get Sold items
     *
     * @return Collection|null
     */
    public function sold()
    {
        return $this->chargily->voucher()->vouchers()->sold();
    }
    /**
     * List all vouchers
     *
     * @return Collection|null
     */
    public function all()
    {
        return $this->chargily->voucher()->vouchers()->list();
    }
    /**
     * Make new voucher request
     *
     * @param string $name
     * @param string $value
     * @return Model|null
     */
    public function request(string $name, string $value): ?Model
    {
        $user = $this->chargily->user();
        $vouchers = $this->chargily->voucher()->vouchers();
        // ===================
        // Retrieve Balance ==
        // ===================
        $balance = $user->balance()->get();
        //
        if ($balance or $balance->getBalance() > 0) {
            // ============================
            // Create new voucher record ==
            // ============================
            $model = config("chargily-pro.models.vouchers");
            $item = $model::create([
                "name" => $name,
                "value" => $value,
            ]);
            // =============
            // Lets Start ==
            // =============
            if ($item->id) {
                /// =============================
                /// Change status to processing =
                /// =============================
                $item->status = ChargilyProVoucherStatusEnum::PROCESSING;
                $item->update();
                // ==================
                // Start processing =
                // ==================
                try {
                    $element = $vouchers->make([
                        "request_number" => $item->id,
                        "customer_name" => config("app.name"),
                        "voucher_name" => $item->name,
                        "value" => $item->value,
                    ]);

                    if ($element) {
                        /// =============================
                        /// Change status to completed =
                        /// =============================
                        $item->status = ChargilyProVoucherStatusEnum::COMPLETED;
                        $item->serial = $element->getSerial() ?? null;
                        $item->key = $element->getKey() ?? null;
                        $item->update();
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
                    $item->status = ChargilyProVoucherStatusEnum::FAILED;
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
     * Make new voucher request according voucher id
     *
     * @param string|VoucherElement $id
     * @return Model|null
     */
    public function requestById(string|VoucherElement $id): ?Model
    {
        $user = $this->chargily->user();
        $vouchers = $this->chargily->voucher()->vouchers();
        // ===================
        // Retrieve Balance ==
        // ===================
        $balance = $user->balance()->get();
        //
        if ($balance or $balance->getBalance() > 0) {
            /// =====================
            /// Get voucher details =
            /// =====================
            $details = (is_string($id)) ? $vouchers->get($id) : $id;
            //
            if ($details) {
                //
                if (!$details->getIsOutOfStock()) {
                    //
                    if ($balance->getBalance() >= $details->getAmount()) {
                        // ============================
                        // Create new voucher record ==
                        // ============================
                        $model = config("chargily-pro.models.vouchers");

                        $item = $model::create([
                            "name" => $details->getName(),
                            "value" => $details->getValue(),
                        ]);
                        // =============
                        // Lets Start ==
                        // =============
                        if ($item->id) {
                            /// =============================
                            /// Change status to processing =
                            /// =============================
                            $item->status = ChargilyProVoucherStatusEnum::PROCESSING;
                            $item->update();
                            // ==================
                            // Start processing =
                            // ==================
                            try {
                                $element = $vouchers->make([
                                    "request_number" => $item->id,
                                    "customer_name" => config("app.name"),
                                    "voucher_name" => $item->name,
                                    "value" => $item->value,
                                ]);

                                if ($element) {
                                    /// =============================
                                    /// Change status to completed =
                                    /// =============================
                                    $item->status = ChargilyProVoucherStatusEnum::COMPLETED;
                                    $item->serial = $element->getSerial() ?? null;
                                    $item->key = $element->getKey() ?? null;
                                    $item->update();
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
                                $item->status = ChargilyProVoucherStatusEnum::FAILED;
                                $item->update();
                            }

                            return $item;
                        }
                    } else {
                        // ========================
                        // Insufficient Balance. ==
                        // ========================
                        throw new InsufficientBalanceException();
                    }
                } else {
                    // ==========================
                    // Product is out of stock ==
                    // ==========================
                    throw new VoucherOutOfStockException($id);
                }
            } else {
                // ========================
                // Product not found ==
                // ========================
                throw new VoucherNotFoundException($id);
            }
        } else {
            // ========================
            // Balance not available ==
            // ========================
            throw new InsufficientBalanceException();
        }
        return null;
    }
}
