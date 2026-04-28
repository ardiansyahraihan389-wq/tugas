<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: white; text-align: center; padding: 20px; }
        .container { width: 80%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #1e1e1e; }
        th, td { padding: 12px; border: 1px solid #333; text-align: left; }
        th { background-color: #00d4ff; color: #121212; }
        tr:nth-child(even) { background-color: #252525; }
        .btn-tambah { padding: 10px 20px; background: #00d4ff; color: #121212; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Daftar Mahasiswa</h1>
    <a href="/tambah" class="btn-tambah">[+ Tambah Data]</a>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Jurusan</th>
                </tr>
            </thead>
            <tbody id="isi-tabel">
                </tbody>
        </table>
    </div>

    <script>
        async function ambilData() {
            try {
                // Memanggil rute yang sudah kita buat di vercel.json
                const respon = await fetch('/api_mahasiswa.php');
                const data = await respon.json();
                
                const tabel = document.getElementById('isi-tabel');
                tabel.innerHTML = ''; // Kosongkan tabel dulu

                data.forEach((mhs, index) => {
                    tabel.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${mhs.nama}</td>
                            <td>${mhs.nim}</td>
                            <td>${mhs.jurusan}</td>
                        </tr>
                    `;
                });
            } catch (error) {
                console.error("Gagal mengambil data:", error);
            }
        }

        // Jalankan fungsi saat halaman dibuka
        ambilData();
    </script>
</body>
</html>
