<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background-color: #121212; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .container { width: 90%; max-width: 800px; margin: auto; }
        h1 { margin-bottom: 30px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background-color: #1e1e1e; 
            margin-top: 20px;
        }
        th, td { padding: 12px; border: 1px solid #333; text-align: left; }
        th { background-color: #333; color: #00d4ff; }
        tr:nth-child(even) { background-color: #252525; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Daftar Mahasiswa</h1>
        
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
    </div>

    <script>
        async function muatData() {
            try {
                const respon = await fetch('https://willdone.wuaze.com/api_mahasiswa.php');
                const data = await respon.json();
                const tabel = document.getElementById('data-mahasiswa');
                tabel.innerHTML = ''; 

                if (data.length === 0) {
                    tabel.innerHTML = '<tr><td colspan="4" style="text-align:center;">Data kosong.</td></tr>';
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
                console.error("Gagal:", error);
                document.getElementById('data-mahasiswa').innerHTML = 
                    '<tr><td colspan="4" style="text-align:center; color:red;">Gagal memuat data.</td></tr>';
            }
        }

        muatData();
    </script>
</body>
</html>
