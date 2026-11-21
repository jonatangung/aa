@extends('template')

@section('back_url', '/supplier')
@section('back_text', 'Kembali')

@section('content')
{{-- Halaman form tambah supplier baru. Data dikirim ke SupplierController method simpanSupplier() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Supplier</h5>
        </div>

        <div class="card-body">
            <form action="/tambah_supplier_aksi" method="POST" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="nama_supplier" class="form-control" value="{{ old('nama_supplier') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Kontak Person</label>
                    <input type="text" name="kontak_person" class="form-control" value="{{ old('kontak_person') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="{{ old('stok', 0) }}" min="0" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga', 0) }}" min="0" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning text-dark fw-semibold rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/supplier" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
