<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tiket - {{ $startDate }} s/d {{ $endDate }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        .periode {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            width: 30%;
        }
        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat-box .label {
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Cetak / Save as PDF</button>

    <div class="container">
        <div class="header">
            <h1>LAPORAN MONITORING DAN EVALUASI</h1>
            <h2>Sistem Ticketing Pengelolaan Layanan TI - BBPPT</h2>
        </div>

        <div class="periode">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            <br>
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="number">{{ $stats['total'] }}</div>
                <div class="label">Total Tiket</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $stats['open'] }}</div>
                <div class="label">Tiket Open</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $stats['closed'] }}</div>
                <div class="label">Tiket Closed</div>
            </div>
        </div>

        <!-- Breakdown by Status -->
        <div class="section-title">Rekapitulasi per Status</div>
        <table>
            <tr>
                <th>Status</th>
                <th>Jumlah</th>
            </tr>
            @foreach($stats['by_status'] as $name => $count)
            <tr>
                <td>{{ $name }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Breakdown by Category -->
        <div class="section-title">Rekapitulasi per Kategori</div>
        <table>
            <tr>
                <th>Kategori</th>
                <th>Jumlah</th>
            </tr>
            @foreach($stats['by_category'] as $name => $count)
            <tr>
                <td>{{ $name }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Detail Tickets -->
        <div class="section-title">Daftar Tiket</div>
        <table>
            <tr>
                <th>No.</th>
                <th>No. Tiket</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Pemohon</th>
                <th>Tanggal</th>
            </tr>
            @foreach($tickets as $index => $ticket)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ Str::limit($ticket->title, 30) }}</td>
                <td>{{ $ticket->category->name ?? '-' }}</td>
                <td>{{ $ticket->status->name ?? '-' }}</td>
                <td>{{ $ticket->requester->name ?? '-' }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="footer">
            Dokumen ini digenerate secara otomatis oleh Sistem Ticketing BBPPT
        </div>
    </div>
</body>
</html>
