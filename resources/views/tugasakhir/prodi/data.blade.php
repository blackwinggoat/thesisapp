<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report - SIMProdi</title>
</head>
<body>
    <form method="POST" action="generate">
        @csrf
        <table>
            <tr>
                <td><label>Dosen Pembimbing Utama</label></td>
                <td>
                    <select name="dosen_1">
                        @foreach($dosen as $d)
                            <option value="{{$d->id}}">{{$d->nama}} - {{$d->pangkat}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Dosen Pembimbing Pendamping</label></td>
                <td>
                    <select name="dosen_2">
                        @foreach ($dosen as $d)
                            <option value="{{$d->id}}">{{$d->nama}} - {{$d->pangkat}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Mahasiswa</label></td>
                <td>
                    <select name="mahasiswa">
                        @foreach ($mahasiswa as $m)
                            <option value="{{$m->id}}">{{$m->nama}} - {{$m->topik}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Generate Report</button></td>
            </tr>
        </table>
    </form>
</body>
</html>
