<?php

// Handle CSV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload']) && isset($_POST['filename'])) {
    $target = __DIR__.'/.local/'.basename($_POST['filename']);
    \is_dir($d = \dirname($target)) OR \mkdir($d,0777,true);
    move_uploaded_file($_FILES['upload']['tmp_name'], $target);
    http_response_code(200);
    exit;
}

$gen_file_list = \iterator_to_array((function(){
    foreach(glob(__DIR__."/gen*.php") as $f){
        yield \basename($f) => $f;
    }
})());
$gen_file_selected = $_GET['gen-file'] ?? null;
if($gen_file_selected){
    include $gen_file_selected;
}


$csv_file_list = \iterator_to_array((function(){
    foreach(glob(__DIR__."/.local/*.csv") as $f){
        yield \basename($f) => $f;
    }
})());
$csv_file_selected = $_GET['file'] ?? null;
$data = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (\is_file($target = $csv_file_list[basename($_POST['delete'])])) {
        unlink($target);
        http_response_code(200);
    } else {
        http_response_code(404);
    }
    exit;
}

// Handle export
if (isset($_GET['export']) && $file = $csv_file_list[$_GET['export']]) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    readfile($file);
    exit;
}




if(isset($_GET['render'])) {
    if (($render_file = $csv_file_list[basename($_GET['file'])] ?? null) && ($handle = fopen($render_file, "r")) !== false) {
        $render_data = [];
        while (($row = fgetcsv($handle)) !== false) {
            $render_data[] = $row;
        }
        fclose($handle);
        if ($render_data) {
            switch(\basename($render_file)){
                case 'consolidated.csv': {
                    (function ($data) {
                        $input_headers = $data[0];
                        $ndata = [];
                        $row_count = count($data);
                        for ($i = 1; $i < $row_count; $i++) {
                            $row = \array_combine($input_headers, $data[$i]);
                            $ndata["{$row['product_name']} <span class=\"fs-5 fw-lighter\">[ {$row['market_qty_type']}  ]</span>"][] = $row;
                        }
                        $output_headers = [
                            'product_name',
                            'o-order',
                            'o-type',
                            'i-order',
                            'i-type',
                            'o-mod',
                            'o-pkg',
                            'o-count',
                            'o-wt',
                            'o-wtu',
                            'i-unit',
                            'i-qty',
                            'product_code',
                            'product_group',
                        ];
                        ?>
                        <style>
                        .x-dull{
                            opacity: 0.4;
                        }
                        </style>
                        <?php 
                        echo '<table class="table table-bordered table-hover table-sm">';
                        echo '<thead class="table-light"><tr>';
                        foreach ($output_headers as $col) {
                            echo '<th>' . htmlspecialchars($col) . '</th>';
                        }
                        echo '</tr></thead><tbody>';
                        foreach($ndata as $product_name => $subrows) {
                            $subrows_count = count($subrows);
                            $i = 0;
                            $group_idk = \uniqid('group-');
                            foreach($subrows as $subrow){
                                if(!$i++){
                                    echo "<tr style=\"border-top:2px solid grey\" x-group=\"{$group_idk}\">";
                                    echo "<td rowspan=\"{$subrows_count}\">" . $product_name . '</td>';
                                    echo "<td rowspan=\"{$subrows_count}\" style=\"text-align:right\">" .'<span class="x-output-order"></span></td>';
                                } else {
                                    echo "<tr x-group=\"{$group_idk}\">";
                                }
                                $idk = \uniqid('ff-');
                                echo '<td>' . htmlspecialchars($subrow['market_qty_type']) . '</td>';
                                echo "<td style=\"max-width:70px\"><div><input id=\"{$idk}\" type=\"number\" class=\"x-input-order\" style=\"width:100%;text-align:right\"></div></td>";
                                echo '<td>' . htmlspecialchars($subrow['quantity_type']) . '</td>';
                                echo '<td class="x-dull x-mod-value">' . htmlspecialchars($subrow['o-mod']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['o-pkg']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['o-count']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['o-wt']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['o-wtu']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['unit']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['quantity']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['product_code']) . '</td>';
                                echo '<td class="x-dull">' . htmlspecialchars($subrow['product_group']) . '</td>';
                                echo '</tr>';
                            }
                        }
                        echo '</tbody></table>';
                    })($render_data);
                } break;
                default: {
                    (function ($data) {
                        echo '<table class="table table-bordered table-striped table-hover table-sm">';
                        echo '<thead class="table-light"><tr>';
                        foreach ($data[0] as $col) {
                            echo '<th>' . htmlspecialchars($col) . '</th>';
                        }
                        echo '</tr></thead><tbody>';
                        for ($i = 1; $i < count($data); $i++) {
                            echo '<tr>';
                            foreach ($data[$i] as $cell) {
                                echo '<td>' . htmlspecialchars($cell) . '</td>';
                            }
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    })($render_data);
                } break;
            }

            
            
            
        } else {
            echo '<div class="text-muted">CSV is empty.</div>';
        }
    } else {
        echo '<div class="text-danger">Invalid file.</div>';
    }
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSV Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .header {
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .table-container {
            flex: 1;
            overflow: auto;
            padding: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <strong class="me-3">CSV Viewer: <?= htmlspecialchars($csv_file_selected ?? 'No file') ?></strong>
            </div>
            <div class="col-auto">
                <div class="row">
                    <div class="col-auto">
                        <button id="delete-btn" class="btn btn-sm btn-outline-danger ms-2" type="button" <?=$csv_file_selected ?'':'disabled'?>>Delete</button>
                    </div>
                    <div class="col-auto">
                        <button id="export-btn" class="btn btn-sm btn-outline-secondary" type="button" <?=$csv_file_selected ?'':'disabled'?>>Export</button>
                    </div>
                    <div class="col">
                        <form id="file-select-form" onsubmit="return false;">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="input-group input-group-sm me-2" style="min-width: 250px;">
                                        <select name="file" class="form-select form-select-sm x-submit-on-change">
                                            <option value="" <?=!$csv_file_selected ? 'selected' : ''?>>--Select CSV--</option>
                                            <?php foreach ($csv_file_list as $file => $fpath): ?>
                                                <option value="<?= htmlspecialchars($file) ?>" <?= $file === $csv_file_selected ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($file) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-secondary x-submit-on-click" title="Refresh CSV">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <select name="gen-file" class="form-select form-select-sm me-2 x-submit-on-change">
                                        <option value="" <?=!$gen_file_selected ? 'selected' : ''?>>--Select Gen--</option>
                                        <?php foreach ($gen_file_list as $file => $fpath): ?>
                                            <option value="<?= htmlspecialchars($file) ?>" <?= $file === $gen_file_selected ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($file) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="table-container" id="drop-zone">
    <div id="csv-output">
        <div class="text-muted">No CSV file selected or file is empty.</div>
    </div>
</div>

<div id="spinner-overlay" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.7);
    z-index: 2000;
    align-items: center;
    justify-content: center;
">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>


<script>
function showSpinner() {
    document.getElementById('spinner-overlay').style.display = 'flex';
}

function hideSpinner() {
    document.getElementById('spinner-overlay').style.display = 'none';
}    
$(function () {
    const dropZone = $('#drop-zone');

    dropZone.on('dragover', function (e) {
        e.preventDefault();
        dropZone.addClass('bg-light');
    });

    dropZone.on('dragleave drop', function () {
        dropZone.removeClass('bg-light');
    });

    dropZone.on('drop', function (e) {
        e.preventDefault();
        const files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            const file = files[0];
            const now = new Date();
            const pad = (n) => n.toString().padStart(2, '0');
            const timestamp = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}-${pad(now.getSeconds())}`;
            const filename = file.name.replace(/\.csv$/i, '') + '-' + timestamp + '.csv';

            const formData = new FormData();
            formData.append('upload', file);
            formData.append('filename', filename);

            showSpinner();
            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: () => location.href = '?file=' + encodeURIComponent(filename),
                 error: () => { hideSpinner(); alert('Upload failed.'); }
            });
        }
    });
});
</script>
<script>
    document.getElementById('export-btn')?.addEventListener('click', function () {
        const file = new URLSearchParams(window.location.search).get('file');
        if (!file) return alert("No file selected.");

        showSpinner();
        fetch(`?export=${encodeURIComponent(file)}`)
            .then(res => {
                if (!res.ok) throw new Error("Failed to export file.");
                return res.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = file;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(err => alert(err.message))
            .finally(() => {
                hideSpinner();
            });
    });
</script>
<script>
    document.getElementById('delete-btn')?.addEventListener('click', function () {
        const file = new URLSearchParams(window.location.search).get('file');
        if (!file) return alert("No file selected.");

        if (!confirm(`Are you sure you want to delete "${file}"?`)) return;

        showSpinner();
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                delete: file
            })
        })
        .then(res => res.ok ? location.href = location.pathname : Promise.reject("Delete failed."))
        .catch(err => alert(err))
        .finally(() => {
             hideSpinner();
        });
    });
</script>

<script>
    document.querySelectorAll('.x-submit-on-click').forEach(el => {
        el.addEventListener('click', function () {
            this.closest('form')?.requestSubmit();
        });
    });
    document.querySelectorAll('.x-submit-on-change').forEach(el => {
        el.addEventListener('change', function () {
            this.form?.requestSubmit();
        });
    });
    $(document).on('change', '.x-input-order',function(){
        var sum = 0;
        var group_idk = $(this).closest('tr').attr('x-group');
        $(`tr[x-group=${group_idk}]`).find('.x-input-order').each(function(){
            var my_idk = $(this).attr('id');
            var mod = Number($(this).closest('tr').find('.x-mod-value').html()) || 0;
            var num = Number($(this).val()) || 0;
            console.log({my_idk, group_idk, mod, num});
            if(mod){
                sum += num / mod;
            }
        });
        console.log({sum});
        $(`tr[x-group=${group_idk}]`).find('.x-output-order').html(Math.ceil(sum));
    });
    document.getElementById('file-select-form')?.addEventListener('submit', function (e) {
        0 && console.log('submit requested');
        e.preventDefault(); // Prevent actual form submit
        const form = e.target;
        const formData = new FormData(form);
        const query = new URLSearchParams(formData).toString();

        showSpinner();
        fetch('?render=1&' + query)
            .then(res => res.text())
            .then(html => {
                document.getElementById('csv-output').innerHTML = html;
                //* Update URL without reloading page
                // const newUrl = new URL(window.location);
                // newUrl.searchParams.set('file', formData.get('file'));
                // history.replaceState(null, '', newUrl);
            })
            .catch(err => alert("Failed to load CSV."))
        .finally(() => {
             hideSpinner();
        });
    });
     
</script>

</body>
</html>

