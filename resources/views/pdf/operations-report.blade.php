<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşlem Detay Raporu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
        }
        
        th {
            background-color: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .process-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .process-preoperative {
            background-color: #3b82f6;
        }
        
        .process-operative {
            background-color: #10b981;
        }
        
        .process-postoperative {
            background-color: #f59e0b;
        }
        
        .process-discharge {
            background-color: #8b5cf6;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #6b7280;
        }
        
        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>İşlem Detay Raporu</h1>
        <p class="subtitle">{{ $clinic_name ?? 'Klinik Yönetim Sistemi' }}</p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Rapor Tarihi:</span>
            <span class="info-value">{{ now()->format('d.m.Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dönem:</span>
            <span class="info-value">
                @if($period === 'monthly')
                    Aylık ({{ now()->format('F Y') }})
                @elseif($period === 'yearly')
                    Yıllık ({{ now()->format('Y') }})
                @else
                    Tüm Zamanlar
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Toplam İşlem:</span>
            <span class="info-value">{{ count($operations) }} adet</span>
        </div>
        <div class="info-row">
            <span class="info-label">Raporu Hazırlayan:</span>
            <span class="info-value">{{ auth()->user()->name }}</span>
        </div>
    </div>
    
    @if(count($operations) > 0)
        <div class="summary-stats">
            @php
                $processStats = [];
                foreach($operations as $operation) {
                    $process = $operation->process ?? 'unknown';
                    $processStats[$process] = ($processStats[$process] ?? 0) + 1;
                }
            @endphp
            
            @foreach($processStats as $process => $count)
                <div class="stat-item">
                    <div class="stat-number">{{ $count }}</div>
                    <div class="stat-label">
                        @switch($process)
                            @case('preoperative')
                                Ameliyat Öncesi
                                @break
                            @case('operative')
                                Ameliyat
                                @break
                            @case('postoperative')
                                Ameliyat Sonrası
                                @break
                            @case('discharge')
                                Taburcu
                                @break
                            @default
                                {{ ucfirst($process) }}
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>İşlem Türü</th>
                        <th>Operasyon Türü</th>
                        <th>Tarih</th>
                        <th>Hasta</th>
                        <th>Doktor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operations as $operation)
                        <tr>
                            <td>
                                <span class="process-badge process-{{ $operation->process ?? 'unknown' }}">
                                    @switch($operation->process)
                                        @case('preoperative')
                                            Ameliyat Öncesi
                                            @break
                                        @case('operative')
                                            Ameliyat
                                            @break
                                        @case('postoperative')
                                            Ameliyat Sonrası
                                            @break
                                        @case('discharge')
                                            Taburcu
                                            @break
                                        @default
                                            {{ ucfirst($operation->process ?? 'Bilinmiyor') }}
                                    @endswitch
                                </span>
                            </td>
                            <td>{{ $operation->operationType ? $operation->operationType->name : 'Belirtilmemiş' }}</td>
                            <td>{{ $operation->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $operation->patient ? $operation->patient->name : 'Bilinmiyor' }}</td>
                            <td>{{ $operation->doctor ? $operation->doctor->name : 'Bilinmiyor' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <p>Seçilen dönemde işlem kaydı bulunmamaktadır.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Bu rapor {{ now()->format('d.m.Y H:i') }} tarihinde otomatik olarak oluşturulmuştur.</p>
        <p>{{ $clinic_name ?? 'Klinik Yönetim Sistemi' }} - İşlem Detay Raporu</p>
    </div>
</body>
</html>