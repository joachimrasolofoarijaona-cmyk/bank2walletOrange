<?php


use App\Models\ActivityLog;
use App\Models\TransactionLogActivity;


function logActivity($user_id, $action, $description)
{
    ActivityLog::create([
        'user_id'   => session('username'),
        'action'    => $action,
        'description' => $description,
        'ip_address' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
    ]);
}


function transactionLogActivity(
    $transaction_id,
    $type,
    $libelle,
    $account_no,
    $status,
    $amount,
    $currency,
    $description,
    $request_payload,
    $response_payload
) {
    TransactionLogActivity::create([
        'transaction_id'   => $transaction_id,
        'user_id'          => 'system', // si connecté, sinon "system"
        'type'             => $type,
        'libelle'          => $libelle,
        'account_no'       => $account_no,
        'status'           => $status,
        'amount'           => $amount,
        'currency'         => $currency,
        'description'      => $description,
        'ip_address' => request()->ip() ?? '127.0.0.1',
        'user_agent' => request()->header('User-Agent') ?? 'system',
        'request_payload'  => $request_payload,
        'response_payload' => $response_payload,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);
}
