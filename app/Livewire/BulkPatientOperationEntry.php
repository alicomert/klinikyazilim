<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Operation;
use App\Models\OperationType;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BulkPatientOperationEntry extends Component
{
    public $showModal = false;
    public $rows = []; // Kept minimal to avoid large snapshots; main grid state is client-side
    public $operationTypesMap = [
        'surgery' => [],
        'mesotherapy' => [],
        'botox' => [],
        'filler' => [],
    ];
    protected $listeners = ['open-bulk-entry' => 'openModal'];

    public function mount()
    {
        // Pre-populate 10 empty rows so inputs render reliably
        $this->rows = [];
        for ($i = 0; $i < 10; $i++) {
            $this->rows[] = $this->emptyRow();
        }
        $this->loadOperationTypesMap();
    }

    public function render()
    {
        return view('livewire.bulk-patient-operation-entry');
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function addRow()
    {
        $this->rows[] = $this->emptyRow();
    }

    public function removeRow($index)
    {
        if (isset($this->rows[$index])) {
            unset($this->rows[$index]);
            $this->rows = array_values($this->rows);
        }
    }

    public function addOperation($rowIndex)
    {
        if (!isset($this->rows[$rowIndex]['operations'])) {
            $this->rows[$rowIndex]['operations'] = [];
        }
        $this->rows[$rowIndex]['operations'][] = [
            'process' => null,
            'type_name' => '',
            'process_detail' => '',
            'registration_period' => Carbon::now()->format('Y-m'),
            'process_date' => Carbon::today()->toDateString(),
        ];
    }

    public function removeOperation($rowIndex, $opIndex)
    {
        if (isset($this->rows[$rowIndex]['operations'][$opIndex])) {
            unset($this->rows[$rowIndex]['operations'][$opIndex]);
            $this->rows[$rowIndex]['operations'] = array_values($this->rows[$rowIndex]['operations']);
        }
    }

    private function emptyRow(): array
    {
        return [
            'first_name' => '',
            'last_name' => '',
            'tc_identity' => '',
            'phone' => '',
            'birth_date' => '',
            'registration_date' => Carbon::now()->toDateString(),
            'address' => '',
            'add_operation' => true,
            'operations' => [
                [
                    'process' => null,
                    'type_name' => '',
                    'process_detail' => '',
                    'registration_period' => Carbon::now()->format('Y-m'),
                    'process_date' => Carbon::today()->toDateString(),
                ]
            ]
        ];
    }

    private function loadOperationTypesMap()
    {
        $user = Auth::user();
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
        $types = OperationType::active()
            ->when($doctorId, fn($q) => $q->forDoctor($doctorId))
            ->ordered()
            ->get();
        foreach ($types as $t) {
            $proc = $t->process;
            if (!isset($this->operationTypesMap[$proc])) {
                $this->operationTypesMap[$proc] = [];
            }
            $this->operationTypesMap[$proc][] = $t->name;
        }
    }

    private function getDoctorIdForSaving($user, $patient = null)
    {
        if ($patient && $patient->doctor_id) {
            return $patient->doctor_id;
        }
        if ($user->role === 'doctor') {
            return $user->id;
        } elseif (in_array($user->role, ['nurse','secretary'])) {
            return $user->doctor_id;
        } elseif ($user->role === 'admin') {
            return $patient ? $patient->doctor_id : null;
        }
        return null;
    }

    private function normalizeProcess($value)
    {
        if (!$value) return null;
        $v = strtolower(trim($value));
        $map = [
            'surgery' => ['surgery','ameliyat'],
            'mesotherapy' => ['mesotherapy','mezoterapi'],
            'botox' => ['botox','botoks'],
            'filler' => ['filler','dolgu']
        ];
        foreach ($map as $key => $aliases) {
            if (in_array($v, $aliases, true)) return $key;
        }
        return null;
    }

    private function convertToTurkishMonth($yearMonth)
    {
        $months = [
            '01' => 'ocak', '02' => 'şubat', '03' => 'mart', '04' => 'nisan',
            '05' => 'mayıs', '06' => 'haziran', '07' => 'temmuz', '08' => 'ağustos',
            '09' => 'eylül', '10' => 'ekim', '11' => 'kasım', '12' => 'aralık'
        ];
        $parts = explode('-', $yearMonth);
        if (count($parts) === 2) {
            $year = $parts[0];
            $month = $parts[1];
            return ($months[$month] ?? $month) . ' ' . $year;
        }
        return $yearMonth;
    }

    private function parseRegistrationPeriod($value)
    {
        if (!$value) {
            return $this->convertToTurkishMonth(Carbon::now()->format('Y-m'));
        }
        $val = trim((string)$value);
        if (preg_match('/^(\d{4})[-\/.](\d{1,2})$/', $val, $m)) {
            $yearMonth = sprintf('%04d-%02d', (int)$m[1], (int)$m[2]);
            return $this->convertToTurkishMonth($yearMonth);
        }
        if (preg_match('/^(\d{1,2})[-\/.](\d{4})$/', $val, $m)) {
            $yearMonth = sprintf('%04d-%02d', (int)$m[2], (int)$m[1]);
            return $this->convertToTurkishMonth($yearMonth);
        }
        if (preg_match('/(ocak|şubat|subat|mart|nisan|mayıs|mayis|haziran|temmuz|ağustos|agustos|eylül|eylul|ekim|kasım|kasim|aralık|aralik)/iu', $val, $mm) && preg_match('/(\d{4})/', $val, $yy)) {
            $months = [
                'ocak' => '01','şubat' => '02','subat' => '02','mart' => '03','nisan' => '04',
                'mayıs' => '05','mayis' => '05','haziran' => '06','temmuz' => '07','ağustos' => '08','agustos' => '08',
                'eylül' => '09','eylul' => '09','ekim' => '10','kasım' => '11','kasim' => '11','aralık' => '12','aralik' => '12'
            ];
            $monthName = strtolower($mm[1]);
            $year = (int)$yy[1];
            $month = $months[$monthName] ?? null;
            if ($month) {
                return $this->convertToTurkishMonth(sprintf('%04d-%02d', $year, (int)$month));
            }
        }
        return $this->convertToTurkishMonth(Carbon::now()->format('Y-m'));
    }

    private function resolveOrCreateOperationType(?string $process, ?string $typeName, ?int $doctorId)
    {
        if (!$process || !$typeName) return null;
        $trimmed = trim((string)$typeName);
        if ($trimmed === '') return null;

        $query = OperationType::active()->ordered();
        if ($doctorId) { $query->forDoctor($doctorId); }
        $query->where(function($q) use ($process) {
            $q->where('process', $process);
            if (Schema::hasColumn('operation_types', 'value')) {
                $q->orWhere('value', $process);
            }
        });

        $lowerTrim = mb_strtolower($trimmed);
        $direct = (clone $query)->whereRaw('LOWER(TRIM(name)) = ?', [$lowerTrim])->first();
        if ($direct) return $direct;
        $like = (clone $query)->where('name', 'like', '%'.$trimmed.'%')->first();
        if ($like) return $like;

        // Create new type if not exists
        $orderQuery = OperationType::query()->where('process', $process);
        if ($doctorId) { $orderQuery->where('created_by', $doctorId); }
        $nextOrder = ((int) $orderQuery->max('sort_order')) + 1;
        $newTypeData = [
            'name' => $trimmed,
            'process' => $process,
            'is_active' => true,
            'sort_order' => $nextOrder,
            'created_by' => $doctorId
        ];
        if (Schema::hasColumn('operation_types', 'value')) {
            $slug = Str::slug($trimmed, '_');
            $baseValue = $process . '_' . $slug;
            $uniqueValue = $baseValue;
            $i = 1;
            while (OperationType::where('value', $uniqueValue)->exists()) {
                $uniqueValue = $baseValue . '_' . $i;
                $i++;
            }
            $newTypeData['value'] = $uniqueValue;
        }
        return OperationType::create($newTypeData);
    }

    public function saveAll($rows = null)
    {
        $user = Auth::user();
        $savedCount = 0;
        $errors = [];

        // Prefer client-provided rows to avoid syncing large arrays
        $rowsToProcess = is_array($rows) ? $rows : $this->rows;
        // Limit to 20 rows per batch
        if (is_array($rowsToProcess)) {
            $rowsToProcess = array_slice($rowsToProcess, 0, 20);
        }

        // Determine template operations from the first row that has operations
        $templateOperations = null;
        foreach ($rowsToProcess as $r) {
            if (!empty($r['add_operation']) && !empty($r['operations']) && is_array($r['operations'])) {
                $templateOperations = $r['operations'];
                break;
            }
        }

        foreach ($rowsToProcess as $idx => $row) {
            $first = trim((string)($row['first_name'] ?? ''));
            $last = trim((string)($row['last_name'] ?? ''));
            $tc = preg_replace('/[^0-9]/', '', (string)($row['tc_identity'] ?? ''));
            $phone = preg_replace('/[^0-9]/', '', (string)($row['phone'] ?? ''));
            $birth = $row['birth_date'] ?? null;
            $regDate = $row['registration_date'] ?? Carbon::now()->toDateString();
            $address = $row['address'] ?? '';

            // Skip completely empty rows
            if ($first === '' && $last === '' && $tc === '' && $phone === '') {
                continue;
            }

            try {
                // Validate like normal single-add flow
                if ($first === '') { throw new \Exception('Ad zorunlu'); }
                if ($last === '') { throw new \Exception('Soyad zorunlu'); }
                if ($tc === '' || strlen($tc) !== 11) { throw new \Exception('TC Kimlik 11 hane olmalı'); }
                if (\App\Models\Patient::where('tc_identity', $tc)->exists()) { throw new \Exception('TC Kimlik zaten kayıtlı'); }
                if ($phone === '' || strlen($phone) < 10) { throw new \Exception('Telefon geçersiz'); }
                // Parse dates
                try { $birth = Carbon::parse($birth)->toDateString(); } catch (\Throwable $e) { throw new \Exception('Doğum tarihi geçersiz'); }
                try { $regDate = Carbon::parse($regDate)->toDateString(); } catch (\Throwable $e) { $regDate = Carbon::now()->toDateString(); }

                // Create patient
                $patientData = [
                    'first_name' => $first,
                    'last_name' => $last,
                    'tc_identity' => $tc,
                    'phone' => $phone,
                    'birth_date' => $birth,
                    'address' => $address ?: null,
                    'registration_date' => $regDate,
                    'is_active' => true,
                    'last_visit' => Carbon::now(),
                ];
                $patientData['doctor_id'] = $this->getDoctorIdForSaving($user);
                $patient = Patient::create($patientData);

                // Optional operations
                if (!empty($row['add_operation'])) {
                    $ops = (!empty($row['operations']) && is_array($row['operations'])) ? $row['operations'] : ($templateOperations ?? []);
                    foreach ($ops as $op) {
                        $proc = $this->normalizeProcess($op['process'] ?? null);
                        if (!$proc) { continue; }
                        $detail = $op['process_detail'] ?? '';
                        $period = $this->parseRegistrationPeriod($op['registration_period'] ?? null);
                        $date = null;
                        try { $date = Carbon::parse($op['process_date']); } catch (\Exception $e) { $date = Carbon::today(); }

                        $doctorId = $this->getDoctorIdForSaving($user, $patient);
                        $operationType = null;
                        if (Schema::hasColumn('operations', 'process_type')) {
                            $operationType = $this->resolveOrCreateOperationType($proc, $op['type_name'] ?? null, $doctorId);
                        }

                        $operationData = [
                            'patient_id' => $patient->id,
                            'process' => $proc,
                            'process_detail' => $detail,
                            'registration_period' => $period,
                            'process_date' => $date,
                            'doctor_id' => $doctorId,
                            'created_by' => $user->id,
                        ];
                        if ($operationType) {
                            $operationData['process_type'] = $operationType->id;
                        }

                        if (Schema::hasColumn('operations', 'patient_name')) {
                            $operationData['patient_name'] = $patient->first_name . ' ' . $patient->last_name;
                        }

                        Operation::create($operationData);
                        Activity::create([
                            'type' => 'operation_added',
                            'description' => 'Toplu kayıt ile işlem eklendi: ' . $proc,
                            'patient_id' => $patient->id,
                            'doctor_id' => $doctorId,
                        ]);
                    }
                }

                // Aktivite kaydı - yeni hasta
                Activity::create([
                    'type' => 'new_patient_registration',
                    'description' => 'Toplu kayıt ile yeni hasta: ' . $patient->full_name,
                    'patient_id' => $patient->id,
                    'doctor_id' => $patient->doctor_id,
                ]);

                $savedCount++;
            } catch (\Throwable $e) {
                $errors[] = 'Satır ' . ($idx + 1) . ': ' . $e->getMessage();
            }
        }

        if (empty($errors)) {
            session()->flash('message', 'Toplu kayıt tamamlandı. Başarılı hasta sayısı: ' . $savedCount);
            $this->dispatch('bulk-entry-saved');
            $this->showModal = false;
        } else {
            session()->flash('error', 'Bazı satırlar kaydedilirken hata oluştu.');
            $this->dispatch('bulk-entry-errors', errors: $errors);
        }
    }
}