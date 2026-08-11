<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR Code Siswa - Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f0f0f0;
            padding: 20px;
        }

        .preview-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preview-header h1 {
            color: #333;
            margin-bottom: 5px;
        }

        .preview-header p {
            color: #666;
            font-size: 14px;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            background: white;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .cards-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .cards-table tr {
            page-break-inside: avoid;
        }

        .cards-table td {
            width: 50mm;
            height: 50mm;
            border: 1px solid #000;
            padding: 0.5mm;
            text-align: center;
            vertical-align: top;
            background: white;
        }

        .card-inner {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 0.3mm;
        }

        .card-qr {
            width: 40mm;
            height: 40mm;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.2mm;
            background: white;
        }

        .card-qr img {
            width: 40mm;
            height: 40mm;
            object-fit: contain;
        }

        .card-text {
            font-size: 8px;
            font-weight: bold;
            font-family: monospace;
            margin-bottom: 0.1mm;
            line-height: 1;
        }

        .card-nama {
            font-size: 7px;
            margin-bottom: 0.1mm;
            line-height: 1;
            text-transform: uppercase;
        }

        .card-kelas {
            font-size: 7px;
            line-height: 1;
            text-transform: uppercase;
        }

        .page-number {
            text-align: center;
            margin-top: 10px;
            color: #999;
            font-size: 12px;
            margin-bottom: 30px;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .preview-header {
                display: none;
            }
            .page {
                margin: 0;
                padding: 5mm;
                box-shadow: none;
                page-break-after: always;
            }
            .page-number {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="preview-header">
    <h1>Kartu QR Code Siswa</h1>
    <p>Layout 3x3 - 9 kartu per halaman A4 - Preview</p>
</div>

<?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIdx => $cards): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="page">
    <table class="cards-table" cellpadding="0" cellspacing="0" border="0">
        <?php for($i = 0; $i < 3; $i++): ?>
        <tr>
            <?php for($j = 0; $j < 3; $j++): ?>
                <?php $idx = ($i * 3) + $j; $student = $cards[$idx] ?? null; ?>
                <td>
                    <?php if($student): ?>
                    <div class="card-inner">
                        <div class="card-qr">
                            <?php if(!empty($student['qr_code_base64'])): ?>
                            <img src="data:image/png;base64,<?php echo e($student['qr_code_base64']); ?>" alt="QR" />
                            <?php else: ?>
                            <span style="color: #ccc; font-size: 8px;">-</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-text"><?php echo e($student['nis']); ?></div>
                        <div class="card-nama"><?php echo e($student['nama']); ?></div>
                        <?php if($includeClass && $student['kelas']): ?>
                        <div class="card-kelas"><?php echo e($student['kelas']['nama_kelas']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
        <?php endfor; ?>
    </table>
</div>
<div class="page-number">Halaman <?php echo e($pageIdx + 1); ?></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/pdfs/qr-cards-unified.blade.php ENDPATH**/ ?>