<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Pelajar</title>
    <style>
        /* F4 Paper: 215mm x 330mm */
        @page {
            size: 215mm 330mm;
            margin: 8mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1a1a1a;
        }

        .page {
            page-break-after: always;
            width: 100%;
            height: 314mm; /* F4 height - margins */
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .cards-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .cards-grid td {
            width: 50%;
            padding: 2mm;
            vertical-align: top;
        }

        /* ============== CARD DESIGN ============== */
        .card {
            width: 100%;
            height: 58mm; /* ~58mm per card for 2x5 on F4 */
            border: 1px solid #ccc;
            border-radius: 4mm;
            overflow: hidden;
            position: relative;
            background: #ffffff;
        }

        .card-2x4 {
            height: 72mm;
        }

        .card-2x3 {
            height: 98mm;
        }

        /* Header Bar */
        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 2mm 3mm;
            display: flex;
            align-items: center;
            height: 14mm;
        }

        .card-header-logo {
            width: 10mm;
            height: 10mm;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            margin-right: 2mm;
            overflow: hidden;
        }

        .card-header-logo img {
            width: 10mm;
            height: 10mm;
            object-fit: contain;
        }

        .card-header-text {
            flex: 1;
        }

        .card-header-text .school-name {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.3pt;
            line-height: 1.2;
        }

        .card-header-text .card-title {
            font-size: 6pt;
            opacity: 0.9;
            letter-spacing: 0.5pt;
            margin-top: 0.5mm;
        }

        /* Card Body */
        .card-body {
            padding: 2mm 3mm;
            display: flex;
            height: calc(100% - 14mm);
        }

        /* Photo Area */
        .card-photo {
            width: 18mm;
            height: 24mm;
            border: 1px solid #ddd;
            border-radius: 2mm;
            overflow: hidden;
            margin-right: 2.5mm;
            flex-shrink: 0;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-photo .initials {
            font-size: 14pt;
            font-weight: bold;
            color: #6b7280;
        }

        /* Info Area */
        .card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-info-rows {
            width: 100%;
        }

        .info-row {
            display: flex;
            margin-bottom: 1mm;
            font-size: 7pt;
            line-height: 1.3;
        }

        .info-label {
            width: 14mm;
            color: #6b7280;
            font-weight: 600;
            flex-shrink: 0;
        }

        .info-sep {
            width: 3mm;
            text-align: center;
            color: #6b7280;
        }

        .info-value {
            flex: 1;
            font-weight: 700;
            color: #111827;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* QR Code Area */
        .card-qr {
            width: 22mm;
            height: 22mm;
            flex-shrink: 0;
            margin-left: 2mm;
            border: 1px solid #e5e7eb;
            border-radius: 2mm;
            padding: 1mm;
            background: white;
        }

        .card-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-qr-label {
            text-align: center;
            font-size: 5pt;
            color: #9ca3af;
            margin-top: 0.5mm;
        }

        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1mm 3mm;
            background: #f8fafc;
            border-top: 0.5px solid #e5e7eb;
            font-size: 5pt;
            color: #9ca3af;
            text-align: center;
        }

        /* Empty card placeholder */
        .card-empty {
            border: 1px dashed #e5e7eb;
            border-radius: 4mm;
            height: 58mm;
        }

        .card-empty-2x4 {
            height: 72mm;
        }

        .card-empty-2x3 {
            height: 98mm;
        }

        /* ============== TABLE LAYOUT ============== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 0.5mm 0;
            font-size: 7pt;
            vertical-align: top;
        }

        .info-table .lbl {
            width: 14mm;
            color: #6b7280;
            font-weight: 600;
        }

        .info-table .sep {
            width: 3mm;
            text-align: center;
            color: #6b7280;
        }

        .info-table .val {
            font-weight: 700;
            color: #111827;
        }
    </style>
</head>
<body>
    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="page">
        <table class="cards-grid">
            <?php for($row = 0; $row < $config['rows']; $row++): ?>
            <tr>
                <?php for($col = 0; $col < $config['cols']; $col++): ?>
                    <?php
                        $idx = ($row * $config['cols']) + $col;
                        $item = $page[$idx] ?? null;
                        $cardClass = 'card';
                        $emptyClass = 'card-empty';
                        if ($layout === '2x4') {
                            $cardClass .= ' card-2x4';
                            $emptyClass .= ' card-empty-2x4';
                        } elseif ($layout === '2x3') {
                            $cardClass .= ' card-2x3';
                            $emptyClass .= ' card-empty-2x3';
                        }
                    ?>
                    <td>
                        <?php if($item): ?>
                            <?php $s = $item['student']; ?>
                            <div class="<?php echo e($cardClass); ?>">
                                
                                <div class="card-header">
                                    <div class="card-header-logo">
                                        <?php if($logoBase64): ?>
                                            <img src="<?php echo e($logoBase64); ?>" alt="Logo">
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-header-text">
                                        <div class="school-name"><?php echo e(strtoupper($schoolName)); ?></div>
                                        <div class="card-title">KARTU PELAJAR <?php echo e($tahunAjaran); ?></div>
                                    </div>
                                </div>

                                
                                <div class="card-body">
                                    
                                    <div class="card-photo">
                                        <?php if($item['foto_base64']): ?>
                                            <img src="<?php echo e($item['foto_base64']); ?>" alt="Foto">
                                        <?php else: ?>
                                            <span class="initials"><?php echo e(strtoupper(substr($s->nama, 0, 1))); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="card-info">
                                        <table class="info-table">
                                            <tr>
                                                <td class="lbl">Nama</td>
                                                <td class="sep">:</td>
                                                <td class="val"><?php echo e($s->nama); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">NIS</td>
                                                <td class="sep">:</td>
                                                <td class="val"><?php echo e($s->nis); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Kelas</td>
                                                <td class="sep">:</td>
                                                <td class="val"><?php echo e($s->kelas->nama_kelas ?? '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">T.A.</td>
                                                <td class="sep">:</td>
                                                <td class="val"><?php echo e($tahunAjaran); ?></td>
                                            </tr>
                                        </table>
                                    </div>

                                    
                                    <div style="display: flex; flex-direction: column; align-items: center; margin-left: 1mm;">
                                        <div class="card-qr">
                                            <?php if($item['qr_base64']): ?>
                                                <img src="<?php echo e($item['qr_base64']); ?>" alt="QR">
                                            <?php else: ?>
                                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:5pt;color:#999;">No QR</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-qr-label">Scan Absensi</div>
                                    </div>
                                </div>

                                
                                <?php if($schoolAddress): ?>
                                <div class="card-footer">
                                    <?php echo e($schoolAddress); ?>

                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="<?php echo e($emptyClass); ?>"></div>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </table>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/students/card-print.blade.php ENDPATH**/ ?>