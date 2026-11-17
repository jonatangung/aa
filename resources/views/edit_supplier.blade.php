@extends('template')

@section('content')
{{-- Halaman form edit supplier. Data $row dikirim dari SupplierController method editSupplier(). Data dikirim ke method updateSupplier() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Supplier</h5>
        </div>

        <div class="card-body">
            <form action="/edit_supplier_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="nama_supplier" value="{{ $row->nama_supplier }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kontak Person</label>
                    <input type="text" name="kontak_person" value="{{ $row->kontak_person }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" value="{{ $row->no_hp }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $row->email }}" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ $row->alamat }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ $row->nama_barang }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" value="{{ $row->stok }}" min="0" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" value="{{ $row->harga }}" min="0" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="{{ $row->tanggal_masuk }}" class="form-control">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/supplier" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
