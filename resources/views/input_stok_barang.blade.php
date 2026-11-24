@extends('template')

@section('content')
{{-- Halaman kelola stok barang (masuk & keluar). Data $supplierData dan $selectedBarang dikirim dari StokBarangController method inputStokBarang() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Kelola Stok Barang</h5>
        </div>

        <div class="card-body">
            {{-- Form pilih barang: GET ke halaman yang sama dengan supplier_id --}}
            <form action="/input_stok_barang" method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-12">
                    <label class="form-label">Pilih Barang</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="" disabled @selected(request('supplier_id') == null)>-- Pilih Barang --</option>
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

                <div class="col-12">
                    <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill w-100"><i class="bi bi-search"></i> Pilih</button>
                </div>
            </form>

            <?php if ($selectedBarang != null) { ?>
                {{-- $selectedBarang = data barang yang dipilih, ditampilkan form stok keluar --}}
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 border-top border-warning border-3 h-100"><div class="card-body">
                            <h6 class="d-flex align-items-center gap-2 mb-3"><i class="bi bi-box-arrow-up text-danger"></i> Stok Keluar</h6>
                            <form action="/stok-barang_keluar/{{ $selectedBarang->id }}" method="POST" class="row g-3">
                                @csrf

                                <div class="col-12">
                                    <label class="form-label">Jumlah Keluar</label>
                                    <input type="number" name="jumlah" min="1" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Tanggal Keluar</label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Harga Jual</label>
                                    <input type="number" name="harga_jual" min="0" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger rounded-pill w-100"><i class="bi bi-check-lg-circle"></i> Simpan Keluar</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>

                <a href="/stok-barang" class="btn btn-outline-secondary rounded-pill mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            <?php } ?>
        </div>
    </div>

@endsection
