<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #121212; 
            color: white; 
            text-align: center; 
            padding: 20px; 
        }
        .container { width: 90%; max-width: 800px; margin: auto; }
        h1 { margin-bottom: 20px; }
        .btn-tambah { 
            display: inline-block;
            padding: 10px 20px; 
            background: #00d4ff; 
            color: #121212; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            margin-bottom: 25px;
        }
        .btn-tambah:hover { background: #008fb3; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background-color: #1e1e1e; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }
        th, td { padding: 15px; border: 1px solid #333; text-align: left; }
        th { background-color: #333; color: #00d4ff; }
        tr:nth-child(even) { background-color: #252525; }
        tr:hover { background-color: #2c2c2c; }
    </style>
</head>
<body>

    <div class="container">
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
            <tbody id="isi-tabel">
                <tr>
                    <td colspan="4" style="text-align:center;">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        async function ambilData() {
            try {
                // Memanggil rute baru sesuai vercel.json kamu
                const respon = await fetch('/api_mahasiswa.php');
                
                if (!respon.ok) throw new Error('Gagal memuat API');
                
                const data = await respon.json();
                const tabel = document.getElementById('isi-tabel');
                tabel.innerHTML = ''; 

                if (data.length === 0) {
                    tabel.innerHTML = '<tr><td colspan="4" style="text-align:center;">Belum ada data mahasiswa.</td></tr>';
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
                console.error("Error:", error);
                document.getElementById('isi-tabel').innerHTML = 
                    '<tr><td colspan="4" style="text-align:center; color:red;">Gagal mengambil data dari database.</td></tr>';
            }
        }

        // Jalankan fungsi otomatis saat halaman dibuka
        ambilData();
    </script>
</body>
</html>
