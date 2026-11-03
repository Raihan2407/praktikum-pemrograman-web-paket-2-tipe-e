<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Fungsi bantuan
|--------------------------------------------------------------------------
*/

if (!function_exists('bersihkanTeks')) {
    function bersihkanTeks($nilai)
    {
        if (!is_string($nilai)) {
            return $nilai;
        }

        return trim(strip_tags($nilai));
    }
}

if (!function_exists('statusHarga')) {
    function statusHarga($harga)
    {
        return $harga >= 20000 ? 'Premium' : 'Reguler';
    }
}

/*
|--------------------------------------------------------------------------
| Validasi data menu
|--------------------------------------------------------------------------
*/

function validasiMenu(Request $request, $id = null)
{
    $data = [
        'kode_menu' => bersihkanTeks(
            $request->input('kode_menu')
        ),

        'nama_menu' => bersihkanTeks(
            $request->input('nama_menu')
        ),

        'barcode' => bersihkanTeks(
            $request->input('barcode')
        ),

        'harga' => $request->input('harga'),

        'kategori_menu_id' => $request->input(
            'kategori_menu_id'
        ),
    ];

    $validator = Validator::make($data, [
        'kode_menu' => [
            'required',
            'string',
            'size:10',
            'regex:/^[A-Za-z0-9]{10}$/',
        ],

        'nama_menu' => [
            'required',
            'string',
            'min:3',
            'max:100',
        ],

        'barcode' => [
            'required',
            'string',
            'min:5',
            'max:30',
        ],

        'harga' => [
            'required',
            'integer',
            'between:1000,100000',
        ],

        'kategori_menu_id' => [
            'required',
            'integer',
        ],
    ], [
        'kode_menu.required' => 'Kode menu wajib diisi.',
        'kode_menu.size' => 'Kode menu harus tepat 10 karakter.',
        'kode_menu.regex' => 'Kode menu hanya boleh berisi huruf dan angka.',

        'nama_menu.required' => 'Nama menu wajib diisi.',
        'nama_menu.min' => 'Nama menu minimal 3 karakter.',
        'nama_menu.max' => 'Nama menu maksimal 100 karakter.',

        'barcode.required' => 'Barcode wajib diisi.',
        'barcode.min' => 'Barcode minimal 5 karakter.',
        'barcode.max' => 'Barcode maksimal 30 karakter.',

        'harga.required' => 'Harga wajib diisi.',
        'harga.integer' => 'Harga harus berupa bilangan bulat.',
        'harga.between' => 'Harga harus antara 1000 sampai 100000.',

        'kategori_menu_id.required' => 'Kategori wajib dipilih.',
        'kategori_menu_id.integer' => 'Kategori tidak valid.',
    ]);

    if ($validator->fails()) {
        return [
            'valid' => false,
            'errors' => $validator,
            'data' => $data,
        ];
    }

    $pdo = DB::connection()->getPdo();

    /*
    | Memastikan kategori tersedia.
    */

    $stmt = $pdo->prepare(
        'SELECT id FROM kategori_menu WHERE id = :id'
    );

    $stmt->execute([
        'id' => $data['kategori_menu_id'],
    ]);

    if (!$stmt->fetch(\PDO::FETCH_ASSOC)) {
        $validator->errors()->add(
            'kategori_menu_id',
            'Kategori yang dipilih tidak tersedia.'
        );
    }

    /*
    | Memeriksa kode menu duplikat.
    */

    $sqlKode = '
        SELECT id FROM menu
        WHERE kode_menu = :kode_menu
    ';

    if ($id !== null) {
        $sqlKode .= ' AND id <> :id';
    }

    $stmt = $pdo->prepare($sqlKode);

    $paramsKode = [
        'kode_menu' => $data['kode_menu'],
    ];

    if ($id !== null) {
        $paramsKode['id'] = $id;
    }

    $stmt->execute($paramsKode);

    if ($stmt->fetch(\PDO::FETCH_ASSOC)) {
        $validator->errors()->add(
            'kode_menu',
            'Kode menu sudah digunakan.'
        );
    }

    /*
    | Memeriksa barcode duplikat.
    */

    $sqlBarcode = '
        SELECT id FROM menu
        WHERE barcode = :barcode
    ';

    if ($id !== null) {
        $sqlBarcode .= ' AND id <> :id';
    }

    $stmt = $pdo->prepare($sqlBarcode);

    $paramsBarcode = [
        'barcode' => $data['barcode'],
    ];

    if ($id !== null) {
        $paramsBarcode['id'] = $id;
    }

    $stmt->execute($paramsBarcode);

    if ($stmt->fetch(\PDO::FETCH_ASSOC)) {
        $validator->errors()->add(
            'barcode',
            'Barcode sudah digunakan.'
        );
    }

    return [
        'valid' => !$validator->errors()->any(),
        'errors' => $validator,
        'data' => $data,
    ];
}

/*
|--------------------------------------------------------------------------
| GET /menu
| Menampilkan data, pencarian, dan filter
|--------------------------------------------------------------------------
*/

Route::get('/menu', function (Request $request) {
    try {
        $pdo = DB::connection()->getPdo();

        $search = bersihkanTeks(
            $request->input('search')
        );

        $kategori = $request->input('kategori_menu_id');

        $sql = '
            SELECT
                menu.*,
                kategori_menu.nama_kategori
            FROM menu
            INNER JOIN kategori_menu
                ON menu.kategori_menu_id = kategori_menu.id
            WHERE 1 = 1
        ';

        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= '
                AND (
                    kode_menu LIKE :kode
                    OR nama_menu LIKE :nama
                    OR barcode LIKE :barcode
                )
            ';

            $keyword = '%' . $search . '%';

            $params['kode'] = $keyword;
            $params['nama'] = $keyword;
            $params['barcode'] = $keyword;
        }

        if (
            $kategori !== null &&
            $kategori !== '' &&
            filter_var($kategori, FILTER_VALIDATE_INT) !== false
        ) {
            $sql .= '
                AND kategori_menu_id = :kategori
            ';

            $params['kategori'] = $kategori;
        }

        $sql .= ' ORDER BY menu.id ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $menus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtKategori = $pdo->prepare(
            'SELECT * FROM kategori_menu ORDER BY nama_kategori ASC'
        );

        $stmtKategori->execute();

        $kategoris = $stmtKategori->fetchAll(
            \PDO::FETCH_ASSOC
        );

        return view('menu', compact(
            'menus',
            'kategoris',
            'search',
            'kategori'
        ));

    } catch (\Throwable $e) {
        report($e);

        return response(
            'Terjadi kesalahan saat mengambil data menu.',
            500
        );
    }
})->name('menu.index');

/*
|--------------------------------------------------------------------------
| POST /menu
| Menambahkan menu
|--------------------------------------------------------------------------
*/

Route::post('/menu', function (Request $request) {
    try {
        $hasil = validasiMenu($request);

        if (!$hasil['valid']) {
            return back()
                ->withErrors($hasil['errors'])
                ->withInput();
        }

        $data = $hasil['data'];

        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare('
            INSERT INTO menu (
                kode_menu,
                nama_menu,
                barcode,
                harga,
                kategori_menu_id,
                created_at,
                updated_at
            )
            VALUES (
                :kode_menu,
                :nama_menu,
                :barcode,
                :harga,
                :kategori_menu_id,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            )
        ');

        $stmt->execute([
            'kode_menu' => $data['kode_menu'],
            'nama_menu' => $data['nama_menu'],
            'barcode' => $data['barcode'],
            'harga' => $data['harga'],
            'kategori_menu_id' => $data['kategori_menu_id'],
        ]);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');

    } catch (\Throwable $e) {
        report($e);

        return back()
            ->withInput()
            ->with('error', 'Menu gagal ditambahkan. Periksa kembali data.');
    }
})->name('menu.store');

/*
|--------------------------------------------------------------------------
| GET /menu/{id}/edit
| Menampilkan form edit
|--------------------------------------------------------------------------
*/

Route::get('/menu/{id}/edit', function ($id) {
    try {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare(
            'SELECT * FROM menu WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $menu = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$menu) {
            abort(404, 'Menu tidak ditemukan.');
        }

        $stmtKategori = $pdo->prepare(
            'SELECT * FROM kategori_menu ORDER BY nama_kategori ASC'
        );

        $stmtKategori->execute();

        $kategoris = $stmtKategori->fetchAll(
            \PDO::FETCH_ASSOC
        );

        return view('menu-edit', compact(
            'menu',
            'kategoris'
        ));

    } catch (\Throwable $e) {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            throw $e;
        }

        report($e);

        return response(
            'Terjadi kesalahan saat membuka data menu.',
            500
        );
    }
})->whereNumber('id')->name('menu.edit');

/*
|--------------------------------------------------------------------------
| PUT /menu/{id}
| Memperbarui menu
|--------------------------------------------------------------------------
*/

Route::put('/menu/{id}', function (Request $request, $id) {
    try {
        $hasil = validasiMenu($request, $id);

        if (!$hasil['valid']) {
            return back()
                ->withErrors($hasil['errors'])
                ->withInput();
        }

        $data = $hasil['data'];

        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare('
            UPDATE menu
            SET
                kode_menu = :kode_menu,
                nama_menu = :nama_menu,
                barcode = :barcode,
                harga = :harga,
                kategori_menu_id = :kategori_menu_id,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');

        $stmt->execute([
            'kode_menu' => $data['kode_menu'],
            'nama_menu' => $data['nama_menu'],
            'barcode' => $data['barcode'],
            'harga' => $data['harga'],
            'kategori_menu_id' => $data['kategori_menu_id'],
            'id' => $id,
        ]);

        if ($stmt->rowCount() === 0) {
            $cek = $pdo->prepare(
                'SELECT id FROM menu WHERE id = :id'
            );

            $cek->execute(['id' => $id]);

            if (!$cek->fetch(\PDO::FETCH_ASSOC)) {
                abort(404, 'Menu tidak ditemukan.');
            }
        }

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui.');

    } catch (\Throwable $e) {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            throw $e;
        }

        report($e);

        return back()
            ->withInput()
            ->with('error', 'Menu gagal diperbarui. Periksa kembali data.');
    }
})->whereNumber('id')->name('menu.update');

/*
|--------------------------------------------------------------------------
| DELETE /menu/{id}
| Menghapus menu
|--------------------------------------------------------------------------
*/

Route::delete('/menu/{id}', function ($id) {
    try {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare(
            'DELETE FROM menu WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        if ($stmt->rowCount() === 0) {
            return redirect()
                ->route('menu.index')
                ->with('error', 'Menu tidak ditemukan.');
        }

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil dihapus.');

    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('menu.index')
            ->with('error', 'Menu gagal dihapus.');
    }
})->whereNumber('id')->name('menu.destroy');