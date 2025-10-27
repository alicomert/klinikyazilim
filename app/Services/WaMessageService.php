<?php

namespace App\Services;

use App\Models\MessageAutomationConfig;
use App\Models\MessageAutomationLog;
use App\Models\Appointment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WaMessageService
{
    private $baseUrl = 'https://api.toplusms.app';

    /**
     * Yaklaşan randevular için mesaj gönder
     */
    public function sendUpcomingAppointmentMessages()
    {
        $configs = MessageAutomationConfig::getActiveConfigs();
        
        foreach ($configs as $config) {
            $this->processConfigMessages($config);
        }
    }

    /**
     * Belirli bir konfigürasyon için mesajları işle
     */
    private function processConfigMessages(MessageAutomationConfig $config)
    {
        try {
            // Türkiye saat diliminde hesaplama yap
            $targetTime = now('Europe/Istanbul')->addHours($config->hours_before_appointment);
            
            // Bu doktora ait yaklaşan randevuları bul
            $appointments = Appointment::where('doctor_id', $config->doctor_id)
                ->whereBetween('appointment_date', [
                    $targetTime->copy()->startOfHour(),
                    $targetTime->copy()->endOfHour()
                ])
                ->whereHas('patient', function($query) {
                    $query->whereNotNull('phone');
                })
                ->with(['patient', 'doctor'])
                ->get();

            foreach ($appointments as $appointment) {
                $this->sendAppointmentMessage($config, $appointment);
                
                // Hız kontrolü - saniyede kaç mesaj gönderileceği
                if ($config->send_speed > 0) {
                    sleep(60 / $config->send_speed); // send_speed kadar mesaj/dakika
                }
            }

        } catch (\Exception $e) {
            Log::error('WaMessageService processConfigMessages error: ' . $e->getMessage(), [
                'config_id' => $config->id,
                'doctor_id' => $config->doctor_id
            ]);
        }
    }

    /**
     * Tek bir randevu için mesaj gönder
     */
    private function sendAppointmentMessage(MessageAutomationConfig $config, Appointment $appointment)
    {
        try {
            // Bu randevu için daha önce mesaj gönderilmiş mi kontrol et
            $existingLog = MessageAutomationLog::where('config_id', $config->id)
                ->where('appointment_id', $appointment->id)
                ->first();

            if ($existingLog) {
                return; // Zaten gönderilmiş
            }

            // Mesaj içeriğini hazırla
            $messageContent = $config->processMessageTemplate($appointment);
            
            // Telefon numarasını temizle
            $phoneNumber = $this->cleanPhoneNumber($appointment->patient->phone);
            
            if (!$phoneNumber) {
                $this->logMessage($config, $appointment, null, 'failed', 'Geçersiz telefon numarası');
                return;
            }

            // Log kaydı oluştur
            $log = $this->logMessage($config, $appointment, $phoneNumber, 'pending', null, $messageContent);

            // API'ye mesaj gönder
            $response = $this->sendMessage($config, $phoneNumber, $messageContent);

            if ($response['success']) {
                // Başarılı gönderim
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'response_data' => $response['data'],
                    'wamessage_report_id' => $response['data']['report_id'] ?? null
                ]);
            } else {
                // Başarısız gönderim
                $log->update([
                    'status' => 'failed',
                    'error_message' => $response['error']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WaMessageService sendAppointmentMessage error: ' . $e->getMessage(), [
                'config_id' => $config->id,
                'appointment_id' => $appointment->id
            ]);

            if (isset($log)) {
                $log->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * WhatsApp üzerinden mesaj gönder (Preview + Finalize)
     */
    private function sendMessage(MessageAutomationConfig $config, $phoneNumber, $message)
    {
        try {
            $numbers = $this->cleanPhoneNumber($phoneNumber);
            if (!$numbers) {
                return [
                    'success' => false,
                    'error' => 'Geçersiz telefon numarası'
                ];
            }

            $sendSpeed = max(1, min(4, (int)($config->send_speed ?? 1)));
            $campaignName = $config->campaign_name ?: 'Randevu Hatırlatma';

            // Preview isteği (form-data)
            $formData = [
                'numbers' => $numbers,
                'message' => $message,
                'campaign_name' => $campaignName,
                'now' => 'true',
                'send_speed' => (string)$sendSpeed,
                'send_date' => '',
                'add_cancel_link' => 'false',
            ];

            $previewResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config->api_token,
                ])
                ->asForm()
                ->post($this->baseUrl . '/bulk/preview/wp', $formData);

            if (!$previewResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'HTTP ' . $previewResponse->status() . ': ' . $previewResponse->body(),
                ];
            }

            $previewData = $previewResponse->json();
            $previewId = $previewData['id'] ?? ($previewData['data']['id'] ?? null);

            if (!$previewId) {
                return [
                    'success' => false,
                    'error' => 'Önizleme ID alınamadı',
                ];
            }

            // Gönderimi finalize et (JSON body)
            $sendResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config->api_token,
                ])
                ->post($this->baseUrl . '/bulk/wp', [
                    'id' => $previewId,
                ]);

            if ($sendResponse->successful()) {
                $sendData = $sendResponse->json();
                return [
                    'success' => true,
                    'data' => [
                        'preview' => $previewData,
                        'send' => $sendData,
                        'preview_id' => $previewId,
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP ' . $sendResponse->status() . ': ' . $sendResponse->body(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mesaj log kaydı oluştur
     */
    private function logMessage(MessageAutomationConfig $config, Appointment $appointment, $phoneNumber, $status, $errorMessage = null, $messageContent = null)
    {
        return MessageAutomationLog::create([
            'config_id' => $config->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $config->doctor_id,
            'user_id' => $config->user_id,
            'phone_number' => $phoneNumber,
            'message_content' => $messageContent,
            'status' => $status,
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Telefon numarasını temizle ve formatla (+90XXXXXXXXXX)
     */
    private function cleanPhoneNumber($phone)
    {
        if (!$phone) {
            return null;
        }

        // Sadece rakamları al
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Türkiye mobil numaraları için +90 formatına normalize et
        if (strlen($digits) === 10 && substr($digits, 0, 1) === '5') {
            return '+90' . $digits; // 5XXXXXXXXX -> +905XXXXXXXXX
        }

        if (strlen($digits) === 11 && substr($digits, 0, 1) === '0' && substr($digits, 1, 1) === '5') {
            return '+90' . substr($digits, 1); // 05XXXXXXXXX -> +905XXXXXXXXX
        }

        if (strlen($digits) === 12 && substr($digits, 0, 2) === '90' && substr($digits, 2, 1) === '5') {
            return '+' . $digits; // 90 5XXXXXXXXX -> +905XXXXXXXXX
        }

        if (strlen($digits) === 12 && substr($digits, 0, 3) === '905') {
            return '+' . $digits; // 905XXXXXXXXX -> +905XXXXXXXXX
        }

        return null; // Geçersiz format veya sabit hat
    }

    /**
     * API bağlantısı testi kaldırıldı (Reg ID artık kullanılmıyor)
     */
    // public function testConnection(MessageAutomationConfig $config)
    // {
    //     // Kaldırıldı
    // }

    /**
     * Mesaj raporunu kontrol et
     */
    public function checkMessageReport($reportId, $apiToken)
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/message-report', [
                'token' => $apiToken,
                'report_id' => $reportId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('WaMessageService checkMessageReport error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Günlük mesaj istatistiklerini al
     */
    public function getDailyStats($doctorId = null)
    {
        $today = MessageAutomationLog::getTodayLogs($doctorId);
        $thisMonth = MessageAutomationLog::getThisMonthLogs($doctorId);
        $successRate = MessageAutomationLog::getSuccessRate($doctorId);

        return [
            'today' => $today,
            'this_month' => $thisMonth,
            'success_rate' => $successRate
        ];
    }

    /**
     * GET-REPORTS: WhatsApp mesaj raporlarını getir
     * Kaynak: api.toplusms.app/reports/multi
     */
    public function getReports(array $filters, $apiToken)
    {
        try {
            $query = [
                'source' => $filters['source'] ?? 1, // WhatsApp
                'state' => $filters['state'] ?? 0,   // 0: tümü
                'type' => $filters['type'] ?? 0,
                'report_id' => $filters['report_id'] ?? null,
                'page' => $filters['page'] ?? 1,
                'count' => $filters['count'] ?? 10,
            ];

            if (!empty($filters['start_date'])) {
                $query['start_date'] = $filters['start_date'];
            }
            if (!empty($filters['end_date'])) {
                $query['end_date'] = $filters['end_date'];
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                ])
                ->get($this->baseUrl . '/reports/multi', $query);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'HTTP ' . $response->status() . ': ' . $response->body(),
            ];

        } catch (\Exception $e) {
            return [
                'error' => 'Bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }
}