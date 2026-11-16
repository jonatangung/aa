<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: helvetica;
            font-size: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 2px;
        }
        p.periode {
            text-align: center;
            font-size: 9px;
            margin-top: 0;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000000;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #eeeeee;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Halaman PDF laporan servis. Data dikirim dari LaporanController method laporanServis() --}}
    <?php
        //----- Tanggal filter kosong -> tampilkan teks awal/akhir
        $tanggalDari = 'awal';
        if ($from != null && $from != '') {
            $tanggalDari = $from;
        }
        $tanggalSampai = 'akhir';
        if ($to != null && $to != '') {
            $tanggalSampai = $to;
        }
    ?>
    <h2>Laporan Servis</h2>
    <p class="periode">
        Periode: {{ $tanggalDari }} s/d {{ $tanggalSampai }}
        | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Servis</th>
                <th>Pelanggan</th>
                <th>No Polisi</th>
                <th>Mekanik</th>
                <th>Tanggal Masuk</th>
                <th>Keluhan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            {{-- $rows = daftar servis dari controller, $row = satu baris data di setiap putaran --}}
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $no }}</td>
                    <?php $no++; ?>
                    <td>{{ $row->no_servis }}</td>
                    <td>{{ $row->nama_pelanggan }}</td>
                    <td>{{ $row->no_polisi }}</td>
                    <?php if ($row->nama_mekanik == null || $row->nama_mekanik == '') { ?>
                        <td>-</td>
                    <?php } else { ?>
                        <td>{{ $row->nama_mekanik }}</td>
                    <?php } ?>
                    <td>{{ $row->tanggal_masuk }}</td>
                    <?php if ($row->keluhan == null || $row->keluhan == '') { ?>
                        <td>-</td>
                    <?php } else { ?>
                        <td>{{ $row->keluhan }}</td>
                    <?php } ?>
                    <td>{{ $row->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
