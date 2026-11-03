<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Menu Kantin</title>
</head>
<body>

    <h1>Sistem Menu Kantin</h1>

    <p>
        Nama: Raihan Darma Putra<br>
        NIM: 2401020138
    </p>

    <hr>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <h3>Terjadi kesalahan:</h3>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Tambah Menu</h2>

    <form action="{{ route('menu.store') }}" method="POST">
        @csrf

        <p>
            <label>Kode Menu</label><br>
            <input
                type="text"
                name="kode_menu"
                maxlength="10"
                value="{{ old('kode_menu') }}"
                required
            >
        </p>

        <p>
            <label>Nama Menu</label><br>
            <input
                type="text"
                name="nama_menu"
                maxlength="100"
                value="{{ old('nama_menu') }}"
                required
            >
        </p>

        <p>
            <label>Barcode</label><br>
            <input
                type="text"
                name="barcode"
                maxlength="30"
                value="{{ old('barcode') }}"
                required
            >
        </p>

        <p>
            <label>Harga</label><br>
            <input
                type="number"
                name="harga"
                min="1000"
                max="100000"
                value="{{ old('harga') }}"
                required
            >
        </p>

        <p>
            <label>Kategori</label><br>

            <select name="kategori_menu_id" required>
                <option value="">Pilih Kategori</option>

                @foreach ($kategoris as $kategoriItem)
                    <option
                        value="{{ $kategoriItem['id'] }}"
                        @selected(old('kategori_menu_id') == $kategoriItem['id'])
                    >
                        {{ $kategoriItem['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Menu</button>
    </form>

    <hr>

    <h2>Pencarian dan Filter Menu</h2>

    <form action="{{ route('menu.index') }}" method="GET">
        <p>
            <label>Cari Menu</label><br>

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Kode, nama, atau barcode"
            >
        </p>

        <p>
            <label>Filter Kategori</label><br>

            <select name="kategori_menu_id">
                <option value="">Semua Kategori</option>

                @foreach ($kategoris as $kategoriItem)
                    <option
                        value="{{ $kategoriItem['id'] }}"
                        @selected($kategori == $kategoriItem['id'])
                    >
                        {{ $kategoriItem['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Cari</button>

        <a href="{{ route('menu.index') }}">Reset</a>
    </form>

    <hr>

    <h2>Daftar Menu Kantin</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Menu</th>
                <th>Nama Menu</th>
                <th>Barcode</th>
                <th>Harga</th>
                <th>Status Harga</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($menus as $item)
                <tr>
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['kode_menu'] }}</td>
                    <td>{{ $item['nama_menu'] }}</td>
                    <td>{{ $item['barcode'] }}</td>
                    <td>
                        Rp{{ number_format($item['harga'], 0, ',', '.') }}
                    </td>
                    <td>{{ statusHarga($item['harga']) }}</td>
                    <td>{{ $item['nama_kategori'] }}</td>

                    <td>
                        <a href="{{ route('menu.edit', $item['id']) }}">
                            Edit
                        </a>

                        <form
                            action="{{ route('menu.destroy', $item['id']) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Belum ada data menu.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>Jumlah data: {{ count($menus) }}</p>

</body>
</html>