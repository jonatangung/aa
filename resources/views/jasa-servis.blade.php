@extends('template')

@section('content')
{{-- Halaman daftar jasa servis. Data $rows dikirim dari JasaServisController method jasaServis() --}}

    <h1 class="mb-3">Data Jasa Servis</h1>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <a href="/tambah_jasa_servis" class="btn btn-secondary">Tambah Jasa Servis</a>
        <div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Print Window</button>
            <a href="/jasa-servis/pdf" target="_blank" class="btn btn-danger btn-sm">Download PDF</a>
            <a href="/jasa-servis/excel" class="btn btn-success btn-sm">Export Excel</a>
        </div>
    </div>

    {{-- Tabel daftar jasa servis --}}
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <h5 class="card-title">Data Jasa Servis</h5>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode Jasa</th>
                        <th>Nama Jasa</th>
                        <th>Harga</th>
                        <th>Estimasi Menit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- $rows = kumpulan data jasa servis dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($rows as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>{{ $value->kode_jasa }}</td>
                            <td>{{ $value->nama_jasa }}</td>
                            <td>{{ $value->harga }}</td>
                            <td>{{ $value->estimasi_menit }}</td>
                            <?php if ($value->is_active == 1) { ?>
                                <td>Aktif</td>
                            <?php } else { ?>
                                <td>Tidak Aktif</td>
                            <?php } ?>
                            <td>
                                <a href="/edit_jasa_servis/{{ $value->id }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/hapus_jasa_servis/{{ $value->id }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
