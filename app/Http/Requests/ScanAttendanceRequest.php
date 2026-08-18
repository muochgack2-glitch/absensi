<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\QRCodeService;

class ScanAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No authentication required for scanner
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The scanner sends the raw QR content as `qr_token`.
     * We validate its format here, then verify the HMAC signature
     * in prepareForValidation() and inject the decoded `nis` into
     * the request so the rest of the stack (controller, service)
     * can use $validated['nis'] exactly as before.
     */
    public function rules(): array
    {
        return [
            // qr_token format: "<NIS>:<64-char hex HMAC>"
            'qr_token'     => 'required|string|max:150',
            'photo_base64' => 'nullable|string',
            'action'       => 'required|in:check_in,check_out',
            // nis di-inject oleh prepareForValidation() setelah verifikasi HMAC
            'nis'          => 'nullable|string',
        ];
    }

    /**
     * Verify the HMAC signature before validation runs.
     * If valid, inject the decoded NIS into the request data
     * so downstream code can use $validated['nis'] normally.
     */
    protected function prepareForValidation(): void
    {
        $token = $this->input('qr_token', '');

        /** @var QRCodeService $qrService */
        $qrService = app(QRCodeService::class);
        $nis       = $qrService->verifyQRToken($token);

        // Merge decoded NIS (or empty string on failure — caught by afterValidation)
        $this->merge(['nis' => $nis ?? '']);
    }

    /**
     * Add post-validation rules that depend on the decoded NIS.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->input('nis'))) {
                $validator->errors()->add(
                    'qr_token',
                    'QR Code tidak valid atau sudah kadaluarsa. Gunakan kartu QR resmi.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'qr_token.required' => 'QR Code tidak terdeteksi.',
            'qr_token.string'   => 'Format QR Code tidak valid.',
            'photo_base64.required' => 'Foto wajib diambil saat scan.',
            'action.required'   => 'Tipe absensi (check in/check out) wajib dipilih.',
            'action.in'         => 'Tipe absensi tidak valid. Harus check_in atau check_out.',
        ];
    }
}
