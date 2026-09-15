<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="shortcut icon" href="<?= base_url(setting('cafe_favicon', 'assets/images/favicon.ico')) ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #0d1117;
            color: #ffffff;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-queue {
            background: #161b22;
            border-bottom: 3px solid #21262d;
            padding: 1rem 3rem;
        }
        .queue-box {
            height: calc(100vh - 110px);
            padding: 2rem;
        }
        .section-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #30363d;
            margin-bottom: 1.5rem;
        }
        .queue-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1.25rem;
        }
        .queue-card {
            background: #21262d;
            border: 2px solid #30363d;
            border-radius: 16px;
            padding: 1.25rem 0.5rem;
            text-align: center;
            font-size: 2.75rem;
            font-weight: 900;
            letter-spacing: 2px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .queue-card.preparing {
            color: #ffc107;
            border-color: #ffc107;
            background: rgba(255, 193, 7, 0.05);
        }
        .queue-card.ready {
            color: #2ecc71;
            border-color: #2ecc71;
            background: rgba(46, 204, 113, 0.1);
            animation: pulse-ready 1.5s infinite;
        }
        @keyframes pulse-ready {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
            70% { transform: scale(1.03); box-shadow: 0 0 0 15px rgba(46, 204, 113, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
    </style>
</head>
<body>

<!-- HEADER QUEUE -->
<header class="header-queue d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-cup-hot-fill text-warning display-5"></i>
        <div>
            <h3 class="fw-bold mb-0 text-white"><?= esc(setting('cafe_name', 'Kopi Senja Utama')) ?></h3>
            <span class="text-secondary small fw-semibold">MONITOR ANTRIAN PESANAN</span>
        </div>
    </div>
    <div class="text-end">
        <div class="fs-4 fw-bold text-warning" id="live-clock">00:00:00</div>
        <div class="text-secondary small"><?= date('d F Y') ?></div>
    </div>
</header>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <!-- KOLOM KIRI: SEDANG DISIAPKAN -->
        <div class="col-6 queue-box border-end border-secondary">
            <div class="section-title text-warning d-flex align-items-center justify-content-between">
                <span><i class="bi bi-hourglass-split me-2"></i>Sedang Disiapkan</span>
                <span class="badge bg-warning text-dark fs-6" id="count-preparing">0</span>
            </div>
            <div class="queue-grid" id="grid-preparing"></div>
        </div>

        <!-- KOLOM KANAN: PESANAN SUDAH SIAP -->
        <div class="col-6 queue-box bg-dark bg-opacity-25">
            <div class="section-title text-success d-flex align-items-center justify-content-between">
                <span><i class="bi bi-bell-fill me-2"></i>Pesanan Sudah Siap</span>
                <span class="badge bg-success fs-6" id="count-ready">0</span>
            </div>
            <div class="queue-grid" id="grid-ready"></div>
        </div>

    </div>
</div>

<script src="<?= base_url('assets/js/realtime-engine.js') ?>"></script>
<script>
    let previousReadyCount = 0;

    setInterval(() => {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID');
    }, 1000);

    function fetchQueueData() {
        fetch('<?= base_url('/api/queue-display/data') ?>')
            .then(res => res.json())
            .then(res => {
                if (!res.status) return;

                // Render Preparing
                const prepGrid = document.getElementById('grid-preparing');
                prepGrid.innerHTML = '';
                document.getElementById('count-preparing').innerText = res.preparing.length;

                if (res.preparing.length === 0) {
                    prepGrid.innerHTML = '<div class="text-secondary small fst-italic py-3">Tidak ada antrian disiapkan.</div>';
                } else {
                    res.preparing.forEach(num => {
                        prepGrid.innerHTML += `<div class="queue-card preparing">${num}</div>`;
                    });
                }

                // Render Ready
                const readyGrid = document.getElementById('grid-ready');
                readyGrid.innerHTML = '';
                document.getElementById('count-ready').innerText = res.ready.length;

                if (res.ready.length === 0) {
                    readyGrid.innerHTML = '<div class="text-secondary small fst-italic py-3">Belum ada pesanan siap.</div>';
                } else {
                    res.ready.forEach(num => {
                        readyGrid.innerHTML += `<div class="queue-card ready">${num}</div>`;
                    });
                }

                previousReadyCount = res.ready.length;
            })
            .catch(err => console.error(err));
    }

    // Realtime Engine Listener & Auto Polling 2s
    const engine = new RealtimeEngine({
        endpoint: '<?= base_url('/api/customer-display/active-order') ?>',
        interval: 2000,
        onEvent: function(event, data) {
            fetchQueueData();
        }
    });

    fetchQueueData();
    setInterval(fetchQueueData, 2000);
</script>
</body>
</html>