@php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; @endphp
@php echo '<?mso-application progid="Excel.Sheet"?>' . "\n"; @endphp
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1"/>
            <Font ss:FontName="Arial" ss:Size="10"/>
        </Style>
        <Style ss:ID="Header">
            <Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1"/>
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
            <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
        <Style ss:ID="Cell">
            <Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
        <Style ss:ID="Marker">
            <Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1"/>
            <Interior ss:Color="#F4B6AD" ss:Pattern="Solid"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
    </Styles>

    @php
        $headers = [
            'Ruangan Ujian',
            'JAM',
            'Nim',
            'Nama Mahasiswa',
            'Pembimbing Utama',
            'Pembimbing Pendamping',
            'Penguji I',
            'Penguji II',
            'Penguji III',
            'Ketua Sidang',
            'Jenis Ujian',
        ];

        $writeHeader = function () use ($headers) {
            echo '<Row>';
            foreach ($headers as $header) {
                echo '<Cell ss:StyleID="Header"><Data ss:Type="String">' . e($header) . '</Data></Cell>';
            }
            echo '</Row>';
        };

        $writeRow = function ($row, $highlights = []) {
            $cells = [
                ['value' => $row->nama_ruangan ?: '-', 'role' => null],
                ['value' => $row->jam_ujian_rekap, 'role' => null],
                ['value' => $row->C_NPM, 'role' => null],
                ['value' => $row->NAMA_MAHASISWA, 'role' => null],
                ['value' => $row->pembimbing_utama, 'role' => 'pembimbing_I_id'],
                ['value' => $row->pembimbing_pendamping, 'role' => 'pembimbing_II_id'],
                ['value' => $row->penguji_1, 'role' => 'penguji_I_id'],
                ['value' => $row->penguji_2, 'role' => 'penguji_II_id'],
                ['value' => $row->penguji_3, 'role' => 'penguji_III_id'],
                ['value' => $row->ketua_sidang, 'role' => 'ketua_sidang_id'],
                ['value' => $row->kode_jenis_tugas_akhir, 'role' => null],
            ];

            echo '<Row>';
            foreach ($cells as $cell) {
                $style = $cell['role'] && isset($highlights[$cell['role']]) ? 'Marker' : 'Cell';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e($cell['value']) . '</Data></Cell>';
            }
            echo '</Row>';
        };
    @endphp

    <Worksheet ss:Name="Rekap Jadwal">
        <Table>
            <Column ss:AutoFitWidth="0" ss:Width="95"/>
            <Column ss:AutoFitWidth="0" ss:Width="95"/>
            <Column ss:AutoFitWidth="0" ss:Width="95"/>
            <Column ss:AutoFitWidth="0" ss:Width="130"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="135"/>
            <Column ss:AutoFitWidth="0" ss:Width="80"/>
            @php($writeHeader())
            @forelse($rows as $row)
                @php($writeRow($row))
            @empty
                <Row>
                    <Cell ss:MergeAcross="10" ss:StyleID="Cell"><Data ss:Type="String">Belum ada peserta pada jadwal yang dipilih.</Data></Cell>
                </Row>
            @endforelse
        </Table>
    </Worksheet>

    @foreach($lecturerSheets as $sheet)
        <Worksheet ss:Name="{{ $sheet['sheet_name'] }}">
            <Table>
                <Column ss:AutoFitWidth="0" ss:Width="95"/>
                <Column ss:AutoFitWidth="0" ss:Width="95"/>
                <Column ss:AutoFitWidth="0" ss:Width="95"/>
                <Column ss:AutoFitWidth="0" ss:Width="130"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="135"/>
                <Column ss:AutoFitWidth="0" ss:Width="80"/>
                @php($writeHeader())
                @foreach($sheet['rows'] as $sheetRow)
                    @php($writeRow($sheetRow['data'], $sheetRow['highlight_roles']))
                @endforeach
            </Table>
        </Worksheet>
    @endforeach
</Workbook>
