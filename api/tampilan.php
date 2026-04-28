<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Project Cloud</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: white; padding: 50px; }
        h2 { text-align: center; color: #00d4ff; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background-color: #1e1e1e; }
        th, td { border: 1px solid #333; padding: 12px; text-align: left; }
        th { background-color: #00d4ff; color: black; }
        tr:nth-child(even) { background-color: #2a2a2a; }
        tr:hover { background-color: #333; }
    </style>
</head>
<body>

<h2>Daftar Mahasiswa (TiDB Cloud + Vercel)</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Jurusan</th>
        </tr>
    </thead>
    <tbody id="data-tabel">
        </tbody>
</table>

<script>
    // Mengambil data dari api_mahasiswa.php
    fetch('api/api_mahasiswa.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('data-tabel');
            data.forEach(mhs => {
                const row = `<tr>
                    <td>${mhs.id}</td>
                    <td>${mhs.nama}</td>
                    <td>${mhs.nim}</td>
                    <td>${mhs.jurusan}</td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => console.error('Error:', error));
</script>

</body>
</html>
