@extends('template')

@section('content')
{{-- Halaman kelola stok barang (masuk & keluar). Data $supplierData dan $selectedBarang dikirim dari StokBarangController method inputStokBarang() --}}

    <h1 class="mb-4 fw-bold">Kelola Stok Barang</h1>

    <div class="card">
        <div class="card-body">
            {{-- Form pilih barang: GET ke halaman yang sama dengan supplier_id --}}
            <form action="/input_stok_barang" method="GET" class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="fw-semibold">Pilih Barang</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        {{-- $supplierData = daftar barang dari supplier, $item = satu baris data di setiap putaran --}}
                        @foreach ($supplierData as $item)
                            <?php
                                //----- Nama barang kosong -> tampilkan teks pengganti
                                $namaBarang = 'Tanpa Nama Barang';
                                if ($item->nama_barang != null && $item->nama_barang != '') {
                                    $namaBarang = $item->nama_barang;
                                }
                            ?>
                            <option value="{{ $item->id }}" @selected(request('supplier_id') == $item->id)>
                                {{ $item->nama_supplier }} - {{ $namaBarang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Pilih</button>
                </div>
            </form>

            <?php if ($selectedBarang != null) { ?>
                {{-- $selectedBarang = data barang yang dipilih, ditampilkan dua form di bawah --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">Stok Masuk</h5>
                            <form action="/stok-barang_tambah" method="POST">
                                @csrf
                                <input type="hidden" name="supplier_id" value="{{ $selectedBarang->id }}">

                                <div class="mb-3">
                                    <label class="fw-semibold">Jumlah Masuk</label>
                                    <input type="number" name="jumlah" min="1" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Tanggal Masuk</label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan Masuk</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">Stok Keluar</h5>
                            <form action="/stok-barang_keluar/{{ $selectedBarang->id }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="fw-semibold">Jumlah Keluar</label>
                                    <input type="number" name="jumlah" min="1" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Tanggal Keluar</label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Harga Jual</label>
                                    <input type="number" name="harga_jual" min="0" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-danger">Simpan Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>

                <a href="/stok-barang" class="btn btn-secondary mt-3">Kembali</a>
            <?php } ?>
        </div>
    </div>

@endsection
