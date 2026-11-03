<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Kantin</title>
</head>
<body>

    <h1>Edit Menu Kantin</h1>

    <p>
        Nama: Raihan Darma Putra<br>
        NIM: 2401020138
    </p>

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

    <form
        action="{{ route('menu.update', $menu['id']) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <p>
            <label>Kode Menu</label><br>

            <input
                type="text"
                name="kode_menu"
                maxlength="10"
                value="{{ old('kode_menu', $menu['kode_menu']) }}"
                required
            >
        </p>

        <p>
            <label>Nama Menu</label><br>

            <input
                type="text"
                name="nama_menu"
                maxlength="100"
                value="{{ old('nama_menu', $menu['nama_menu']) }}"
                required
            >
        </p>

        <p>
            <label>Barcode</label><br>

            <input
                type="text"
                name="barcode"
                maxlength="30"
                value="{{ old('barcode', $menu['barcode']) }}"
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
                value="{{ old('harga', $menu['harga']) }}"
                required
            >
        </p>

        <p>
            <label>Kategori</label><br>

            <select name="kategori_menu_id" required>
                @foreach ($kategoris as $kategoriItem)
                    <option
                        value="{{ $kategoriItem['id'] }}"
                        @selected(
                            old('kategori_menu_id', $menu['kategori_menu_id'])
                            == $kategoriItem['id']
                        )
                    >
                        {{ $kategoriItem['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Perubahan</button>

        <a href="{{ route('menu.index') }}">Kembali</a>
    </form>

</body>
</html>