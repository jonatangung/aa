@extends('template')

@section('content')
{{-- Halaman daftar pelanggan. Data $pelanggan dikirim dari PelangganController method pelanggan() --}}

    <h1 class="mb-3">Data Pelanggan</h1>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <a href="/tplg" class="btn btn-secondary">Tambah Pelanggan</a>
        <div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Print Window</button>
            <?php
            $queryString = request()->getQueryString();
            if ($queryString != '') {
                $pdfUrl = '/pelanggan/pdf?' . $queryString;
                $excelUrl = '/pelanggan/excel?' . $queryString;
            } else {
                $pdfUrl = '/pelanggan/pdf';
                $excelUrl = '/pelanggan/excel';
            }
            ?>
            <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-danger btn-sm">Download PDF</a>
            <a href="{{ $excelUrl }}" class="btn btn-success btn-sm">Export Excel</a>
        </div>
    </div>

    

    {{-- Tabel daftar pelanggan --}}
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <h5 class="card-title">Daftar Pelanggan</h5>

            <table id="users-table" class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- $pelanggan = kumpulan data pelanggan dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($pelanggan as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>{{ $value->nama }}</td>
                            <td>{{ $value->alamat }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->no_hp }}</td>
                            <td>
                                <a href="/edit_pelanggan/{{ $value->id }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/hapus_pelanggan/{{ $value->id }}" method="POST" class="d-inline">
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
