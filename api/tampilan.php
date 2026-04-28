<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #121212; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        table { 
            width: 80%; 
            margin: 20px auto; 
            border-collapse: collapse; 
            background-color: #1e1e1e; 
        }
        th, td { 
            padding: 12px; 
            border: 1px solid #333; 
        }
        th { 
            background-color: #333; 
            color: #00d4ff; 
        }
        tr:nth-child(even) { 
            background-color: #252525; 
        }
        .btn-tambah { 
            display: inline-block; 
            margin-bottom: 20px; 
            padding: 10px 20px; 
            background-color: #00d4ff; 
            color: #121212; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
        }
        .btn-tambah:hover { 
            background-color: #0099cc; 
        }
    </style>
</head>
<body>

    <h1>Daftar Mahasiswa</h1>

    <a href="/tambah" class="btn-tambah">[+ Tambah Data]</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Jurusan</th>
            </tr>
        </thead>
        <tbody id="data-mahasiswa">
            <tr>
                <td colspan="4" style="text-align:center;">Memuat data...</td>
            </tr>
        </tbody>
    </table>

    <script>
        async function muatData() {
            try {
                // Mengambil data dari rute API yang baru
                const respon = await fetch('/api_mahasiswa.php');
                
                if (!respon.ok) throw new Error('API tidak ditemukan');
                
                const data = await respon.json();
                
                // Menghubungkan ke ID tbody "data-mahasiswa"
                const tabel = document.getElementById('data-mahasiswa');
                tabel.innerHTML = ''; 

                if (data.length === 0) {
                    tabel.innerHTML = '<tr><td colspan="4">Belum ada data di database.</td></tr>';
                    return;
                }

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
                console.error("Gagal memuat data:", error);
                document.getElementById('data-mahasiswa').innerHTML = 
                    '<tr><td colspan="4" style="color:red;">Gagal mengambil data dari database. Cek koneksi atau API.</td></tr>';
            }
        }

        // Jalankan fungsi saat halaman dibuka
        muatData();
    </script>
</body>
</html>
