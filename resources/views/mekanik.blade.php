@extends('template')

@section('content')
{{-- Halaman daftar mekanik. Data $karyawan dikirim dari MekanikController method karyawan() --}}

    <h1 class="mb-3">Data Mekanik</h1>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <a href="/tkry" class="btn btn-secondary">Tambah Mekanik</a>
        <div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Print Window</button>
            <a href="/mekanik/pdf" target="_blank" class="btn btn-danger btn-sm">Download PDF</a>
            <a href="/mekanik/excel" class="btn btn-success btn-sm">Export Excel</a>
        </div>
    </div>

    {{-- Tabel daftar mekanik --}}
    <div class="card">
        <div class="card-body table-responsive">
            <h5 class="card-title">Daftar Mekanik</h5>

            <table class="table table-bordered table-striped align-middle mt-3">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Spesialisasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- $no = variabel nomor urut, dimulai dari 1 --}}
                    <?php $no = 1; ?>
                    {{-- $karyawan = kumpulan data mekanik dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($karyawan as $value)
                        <tr>
                            {{-- Tampilkan nomor urut --}}
                            <td>{{ $no }}</td>
                            {{-- Tambah nomor urut --}}
                            <?php $no++; ?>
                            <td>{{ $value->nama }}</td>
                            {{-- Tampilkan no HP, jika kosong tampilkan tanda - --}}
                            <td>
                                <?php if ($value->no_hp == '') { ?>
                                    -
                                <?php } else { ?>
                                    {{ $value->no_hp }}
                                <?php } ?>
                            </td>
                            {{-- Tampilkan spesialisasi, jika kosong tampilkan tanda - --}}
                            <td>
                                <?php if ($value->spesialisasi == '') { ?>
                                    -
                                <?php } else { ?>
                                    {{ $value->spesialisasi }}
                                <?php } ?>
                            </td>
                            <td>{{ $value->status }}</td>
                            {{-- Tombol edit dan hapus --}}
                            <td>
                                <a href="/edit_mekanik/{{ $value->id }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/hapus_mekanik/{{ $value->id }}" method="POST" class="d-inline">
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
