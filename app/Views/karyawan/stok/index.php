<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">

    <?php if (!empty($tree)): ?>

        <?php
            $total_merek = count($tree);
            $total_motif = 0;
            $total_sku   = 0;
            $total_stok  = 0;
            foreach ($tree as $supplier) {
                $total_motif += count($supplier['motif']);
                foreach ($supplier['motif'] as $motif) {
                    $total_sku  += count($motif['produk']);
                    $total_stok += $motif['total_stok'];
                }
            }
        ?>
        <div class="summary-bar mb-3">
            <div class="summary-chip">
                <strong><?= $total_merek ?></strong> Merek
            </div>
            <div class="summary-chip">
                <strong><?= $total_motif ?></strong> Motif
            </div>
            <div class="summary-chip">
                <strong><?= $total_sku ?></strong> Produk
            </div>
            <div class="summary-chip summary-chip--stok">
                <i class="bi bi-box-seam"></i>
                <strong><?= number_format($total_stok) ?></strong> Total Stok
            </div>
        </div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari merek, motif, warna, atau SKU...">
            </div>
            <select id="statusFilter" class="form-select form-select-sm" style="width:auto; min-width:155px;">
                <option value="">-- Semua Status --</option>
                <option value="aman">✅ Stok Aman</option>
                <option value="menipis">⚠️ Stok Menipis</option>
                <option value="habis">❌ Stok Habis</option>
            </select>
        </div>

        <div id="product-tree">
            <?php foreach ($tree as $supplier): ?>

                <div class="brand-card" data-brand-id="<?= $supplier['id'] ?>">

                    <div class="brand-header" onclick="toggleBrand(<?= $supplier['id'] ?>)">
                        <i class="bi bi-chevron-right toggle-icon" id="brand-toggle-<?= $supplier['id'] ?>"></i>
                        <span class="brand-name"><?= esc($supplier['nama']) ?></span>
                        <span class="badge-pill badge-motif-count">
                            <i class="bi bi-grid"></i> <?= count($supplier['motif']) ?> motif
                        </span>
                        <span class="badge-pill badge-stok">
                            <i class="bi bi-box-seam"></i>
                            <?= number_format($supplier['total_stok']) ?> stok
                        </span>
                    </div>

                    <div class="brand-body" id="brand-body-<?= $supplier['id'] ?>">
                        <?php foreach ($supplier['motif'] as $motif): ?>

                            <div class="motif-card" data-motif-id="<?= $motif['id'] ?>">

                                <div class="motif-header" onclick="toggleMotif(<?= $motif['id'] ?>)">
                                    <i class="bi bi-chevron-right motif-icon" id="motif-toggle-<?= $motif['id'] ?>"></i>
                                    <span class="motif-name"><?= esc($motif['nama']) ?></span>
                                    <span class="badge-pill badge-stok" style="font-size:11px;">
                                        <i class="bi bi-box-seam"></i>
                                        <?= number_format($motif['total_stok']) ?> stok
                                    </span>
                                    <span class="badge-pill badge-motif-count" style="font-size:11px;">
                                        <?= count($motif['produk']) ?> warna
                                    </span>
                                </div>

                                <div class="motif-body" id="motif-body-<?= $motif['id'] ?>">
                                    <?php foreach ($motif['produk'] as $produk):
                                        $min  = (int) $produk['min_stok'];
                                        $stok = (int) $produk['stok'];

                                        if ($stok == 0) {
                                            $stok_class = 'stok-danger';
                                            $stok_icon  = '❌ Habis';
                                            $status_key = 'habis';
                                            $bar_color  = '#dc2626';
                                        } elseif ($stok <= $min) {
                                            $stok_class = 'stok-warning';
                                            $stok_icon  = '⚠️ Menipis';
                                            $status_key = 'menipis';
                                            $bar_color  = '#d97706';
                                        } else {
                                            $stok_class = 'stok-aman';
                                            $stok_icon  = '';
                                            $status_key = 'aman';
                                            $bar_color  = '#059669';
                                        }

                                        $bar_max = max($stok, $min * 2, 1);
                                        $bar_pct = min(100, round($stok / $bar_max * 100));
                                    ?>

                                        <div class="produk-item"
                                             data-status="<?= $status_key ?>"
                                             onclick="toggleDetail('p<?= $produk['id'] ?>', event)">

                                            <div class="produk-row">

                                                <?php if ($produk['foto']): ?>
                                                    <img src="<?= base_url('uploads/produk/' . $produk['foto']) ?>"
                                                         class="color-chip"
                                                         style="object-fit:cover;"
                                                         alt="<?= esc($produk['nama_warna']) ?>">
                                                <?php else: ?>
                                                    <div class="color-chip color-chip--fallback">
                                                        <?= mb_strtoupper(mb_substr($produk['nama_warna'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="produk-info">
                                                    <div class="warna"><?= esc($produk['nama_warna']) ?></div>
                                                    <div class="sku"><?= esc($produk['sku']) ?></div>
                                                </div>

                                                <div class="stok-indicator">
                                                    <span class="stok-val <?= $stok_class ?>"><?= $stok ?></span>
                                                    <span class="stok-label">potong</span>
                                                </div>

                                                <?php if ($stok_icon): ?>
                                                    <span class="status-mini status-mini--<?= $status_key ?>">
                                                        <?= $stok_icon ?>
                                                    </span>
                                                <?php endif; ?>

                                                <div class="produk-actions" onclick="event.stopPropagation()">
                                                    <a href="<?= base_url('karyawan/stok/detail/' . $produk['id']) ?>"
                                                       class="btn-icon btn-detail" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('karyawan/stok/opname/' . $produk['id']) ?>"
                                                       class="btn-icon btn-opname" title="Opname">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="produk-detail" id="detail-p<?= $produk['id'] ?>">

                                                <div class="detail-foto">
                                                    <?php if ($produk['foto']): ?>
                                                        <img src="<?= base_url('uploads/produk/' . $produk['foto']) ?>"
                                                             class="detail-foto__img"
                                                             onclick="openLightbox(this.src); event.stopPropagation();"
                                                             alt="Foto <?= esc($produk['nama_warna']) ?>">
                                                        <span class="detail-foto__hint">
                                                            <i class="bi bi-zoom-in"></i> Klik untuk perbesar
                                                        </span>
                                                    <?php else: ?>
                                                        <div class="foto-placeholder">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                        <span class="detail-foto__hint">No foto</span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="detail-info">
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">SKU</span>
                                                        <span class="detail-val"><code><?= esc($produk['sku']) ?></code></span>
                                                    </div>
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">Merek</span>
                                                        <span class="detail-val"><?= esc($produk['nama_supplier']) ?></span>
                                                    </div>
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">Motif</span>
                                                        <span class="detail-val"><?= esc($produk['nama_motif']) ?></span>
                                                    </div>
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">Warna</span>
                                                        <span class="detail-val"><?= esc($produk['nama_warna']) ?></span>
                                                    </div>
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">Stok</span>
                                                        <span class="detail-val <?= $stok_class ?>">
                                                            <strong><?= $stok ?></strong> potong
                                                            <?php if ($stok_icon): ?>
                                                                &nbsp;<?= $stok_icon ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <div class="detail-row-item">
                                                        <span class="detail-key">Min. Stok</span>
                                                        <span class="detail-val"><?= $min ?> potong</span>
                                                    </div>
                                                    <div class="stok-bar-wrap">
                                                        <div class="stok-bar-bg">
                                                            <div class="stok-bar-fill"
                                                                 style="width:<?= $bar_pct ?>%;
                                                                        background:<?= $bar_color ?>;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="detail-row-item" style="margin-top:4px;">
                                                        <span class="detail-key">Keterangan</span>
                                                        <span class="detail-val"><?= esc($produk['keterangan'] ?? '—') ?></span>
                                                    </div>

                                                    <div class="detail-quick-links">
                                                        <a href="<?= base_url('karyawan/stok/detail/' . $produk['id']) ?>"
                                                           class="btn-quick btn-quick--detail">
                                                            <i class="bi bi-eye"></i> Detail &amp; Riwayat
                                                        </a>
                                                        <a href="<?= base_url('karyawan/stok/opname/' . $produk['id']) ?>"
                                                           class="btn-quick btn-quick--opname">
                                                            <i class="bi bi-pencil-square"></i> Opname Stok
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    <?php endforeach; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data produk.</p>
        </div>
    <?php endif; ?>

</div>

<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img" src="" alt="Foto Produk">
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
function toggleBrand(id) {
    const body   = document.getElementById('brand-body-' + id);
    const icon   = document.getElementById('brand-toggle-' + id);
    const isOpen = body.classList.toggle('open');
    icon.classList.toggle('open', isOpen);

    if (!isOpen) {
        body.querySelectorAll('.motif-body.open').forEach(mb => {
            mb.classList.remove('open');
            const mid = mb.id.replace('motif-body-', '');
            document.getElementById('motif-toggle-' + mid)?.classList.remove('open');
        });
        body.querySelectorAll('.produk-detail.open').forEach(d => {
            d.classList.remove('open');
            d.closest('.produk-item')?.classList.remove('active');
        });
    }
}

function toggleMotif(id) {
    const body   = document.getElementById('motif-body-' + id);
    const icon   = document.getElementById('motif-toggle-' + id);
    if (!body) return;
    const isOpen = body.classList.toggle('open');
    icon.classList.toggle('open', isOpen);

    if (!isOpen) {
        body.querySelectorAll('.produk-detail.open').forEach(d => {
            d.classList.remove('open');
            d.closest('.produk-item')?.classList.remove('active');
        });
    }
}

function toggleDetail(id, e) {
    if (e.target.closest('a') || e.target.closest('button')) return;
    const detail = document.getElementById('detail-' + id);
    if (!detail) return;
    const isOpen = detail.classList.toggle('open');
    detail.closest('.produk-item')?.classList.toggle('active', isOpen);
}

function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

function applyFilter() {
    const q      = document.getElementById('searchInput').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;
    const active = q || status;

    document.querySelectorAll('.brand-card').forEach(brand => {
        const brandMatch = brand.querySelector('.brand-name').textContent.toLowerCase().includes(q);
        let anyMotifVisible = false;

        brand.querySelectorAll('.motif-card').forEach(motif => {
            const motifMatch = motif.querySelector('.motif-name').textContent.toLowerCase().includes(q);
            let anyProdukVisible = false;

            motif.querySelectorAll('.produk-item').forEach(item => {
                const warna    = item.querySelector('.warna').textContent.toLowerCase();
                const sku      = item.querySelector('.sku').textContent.toLowerCase();
                const itemStat = item.dataset.status;

                const textOk   = !q || brandMatch || motifMatch || warna.includes(q) || sku.includes(q);
                const statusOk = !status || itemStat === status;
                const show     = textOk && statusOk;

                item.style.display = show ? '' : 'none';
                if (show) anyProdukVisible = true;
            });

            const showMotif = anyProdukVisible || (!status && q && (brandMatch || motifMatch));
            motif.style.display = showMotif ? '' : 'none';
            if (showMotif) anyMotifVisible = true;

            const mb = motif.querySelector('.motif-body');
            const mi = motif.querySelector('.motif-icon');
            if (active && showMotif) {
                mb?.classList.add('open');
                mi?.classList.add('open');
            } else if (!active) {
                mb?.classList.remove('open');
                mi?.classList.remove('open');
            }
        });

        brand.style.display = (!active || anyMotifVisible) ? '' : 'none';

        const bb = brand.querySelector('.brand-body');
        const bi = brand.querySelector('.toggle-icon');
        if (active && anyMotifVisible) {
            bb?.classList.add('open');
            bi?.classList.add('open');
        } else if (!active) {
            bb?.classList.remove('open');
            bi?.classList.remove('open');
        }
    });
}

document.getElementById('searchInput').addEventListener('input', applyFilter);
document.getElementById('statusFilter').addEventListener('change', applyFilter);
</script>

<style>
:root {
    --primary:        #4f46e5;
    --primary-light:  #6366f1;
    --primary-dark:   #4338ca;
    --success:        #10b981;
    --success-light:  #34d399;
    --success-dark:   #059669;
    --danger:         #ef4444;
    --danger-light:   #f87171;
    --danger-dark:    #dc2626;
    --warning:        #f59e0b;
    --warning-light:  #fbbf24;
    --warning-dark:   #d97706;
    --info:           #06b6d4;
    --gray-50:        #f8fafc;
    --gray-100:       #f1f5f9;
    --gray-200:       #e2e8f0;
    --gray-300:       #cbd5e1;
    --gray-400:       #94a3b8;
    --gray-500:       #64748b;
    --gray-600:       #475569;
    --gray-700:       #334155;
    --gray-800:       #1e293b;
    --card-bg:        #ffffff;
    --card-border:    #e2e8f0;
}

/* ── SUMMARY BAR ── */
.summary-bar { display:flex; gap:8px; flex-wrap:wrap; }
.summary-chip {
    background: var(--card-bg);
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--gray-700);
}
.summary-chip strong { font-size:16px; font-weight:600; color: var(--primary); }
.summary-chip--stok { border-color:#6ee7b7; background:#ecfdf5; }
.summary-chip--stok strong { color: var(--success-dark); }
.summary-chip--stok i      { color: var(--success-dark); }

/* ── SEARCH BAR ── */
.search-bar { position:relative; }
.search-bar i {
    position:absolute; left:12px; top:50%;
    transform:translateY(-50%);
    color: var(--gray-500); font-size:15px; pointer-events:none;
}
.search-bar input {
    width:100%;
    padding:9px 12px 9px 36px;
    border:1px solid var(--gray-200);
    border-radius: 10px;
    background: var(--card-bg);
    font-size:13px;
    color: var(--gray-800);
    outline:none;
    transition:border-color 0.15s;
}
.search-bar input:focus { border-color: var(--primary-light); }
.search-bar input::placeholder { color: var(--gray-400); }

/* ── BRAND CARD ── */
.brand-card {
    background: var(--card-bg);
    border: 1px solid var(--gray-200);
    border-left: 4px solid var(--primary);
    border-radius: 12px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: box-shadow 0.15s;
}
.brand-card:hover { box-shadow: 0 4px 12px rgba(79,70,229,0.08); }

.brand-header {
    display:flex; align-items:center;
    padding:12px 16px;
    cursor:pointer; user-select:none;
    gap:10px; flex-wrap:wrap;
}
.brand-header:hover { background: #eef2ff; }

.toggle-icon {
    font-size:13px; color: var(--primary);
    transition:transform 0.2s; flex-shrink:0;
}
.toggle-icon.open { transform:rotate(90deg); }
.motif-icon {
    font-size:13px; color: var(--warning-dark);
    transition:transform 0.2s; flex-shrink:0;
}
.motif-icon.open { transform:rotate(90deg); }

.brand-name { font-size:15px; font-weight:600; color: var(--primary-dark); flex:1; min-width:120px; }

.badge-pill {
    display:inline-flex; align-items:center; gap:4px;
    border-radius:20px; padding:3px 10px;
    font-size:12px; font-weight:500; white-space:nowrap;
}
.badge-stok        { background:#ecfdf5; color: var(--success-dark); border:1px solid #6ee7b7; }
.badge-motif-count { background: var(--gray-100); color: var(--gray-700); border:1px solid var(--gray-200); }

.brand-body { display:none; padding:0 12px 12px; }
.brand-body.open { display:block; }

/* ── MOTIF CARD ── */
.motif-card {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-left: 3px solid var(--warning);
    border-radius: 10px;
    margin-bottom: 8px;
    overflow: hidden;
}
.motif-header {
    display:flex; align-items:center;
    padding:9px 12px;
    cursor:pointer; user-select:none;
    gap:8px; flex-wrap:wrap;
}
.motif-header:hover { background:#fffbeb; }
.motif-name { font-size:13px; font-weight:600; color: var(--warning-dark); flex:1; min-width:100px; }
.motif-body { display:none; padding:0 8px 8px; }
.motif-body.open { display:block; }

/* ── PRODUK ITEM ── */
.produk-item {
    background: var(--card-bg);
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: border-color 0.15s;
    overflow: hidden;
}
.produk-item:hover  { border-color: var(--gray-500); }
.produk-item.active { border-color: var(--primary-light); }

.produk-row {
    display:flex; align-items:center;
    padding:8px 12px; gap:10px;
}

.color-chip {
    width:32px; height:32px;
    border-radius: 8px;
    border:1px solid rgba(0,0,0,0.08);
    flex-shrink:0;
}
.color-chip--fallback {
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:700;
    color:rgba(255,255,255,0.9);
    background: var(--gray-500);
}

.produk-info      { flex:1; min-width:0; }
.produk-info .warna {
    font-size:13px; font-weight:600; color: var(--gray-800);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.produk-info .sku {
    font-size:11px; color: var(--gray-500);
    font-family:'Courier New', monospace; margin-top:1px;
}

.stok-indicator { display:flex; flex-direction:column; align-items:flex-end; flex-shrink:0; }
.stok-val   { font-size:14px; font-weight:600; }
.stok-label { font-size:10px; color: var(--gray-400); margin-top:1px; }
.stok-aman    { color: var(--success-dark); }
.stok-warning { color: var(--warning-dark); }
.stok-danger  { color: var(--danger-dark); }

/* Status mini badge */
.status-mini {
    font-size:10px; font-weight:600;
    padding:2px 7px; border-radius:20px;
    white-space:nowrap; flex-shrink:0;
}
.status-mini--menipis { background:#fffbeb; color: var(--warning-dark); border:1px solid var(--warning-light); }
.status-mini--habis   { background:#fef2f2; color: var(--danger-dark);  border:1px solid var(--danger-light); }

/* ── ACTION BUTTONS ── */
.produk-actions { display:flex; gap:4px; flex-shrink:0; }
.btn-icon {
    width:30px; height:30px;
    border-radius: 8px;
    border:1px solid var(--gray-200);
    background: var(--card-bg);
    display:flex; align-items:center; justify-content:center;
    font-size:13px; cursor:pointer;
    transition:background 0.12s, border-color 0.12s;
    text-decoration:none;
}
.btn-detail { color: var(--info); }
.btn-detail:hover { background:#ecfeff; border-color: var(--info); }
.btn-opname { color: var(--warning-dark); }
.btn-opname:hover { background:#fffbeb; border-color: var(--warning-light); }

/* ── DETAIL PANEL ── */
.produk-detail {
    display:none;
    padding:12px 14px 14px;
    border-top:1px solid var(--gray-200);
    background: var(--gray-100);
    gap:14px; flex-wrap:wrap;
}
.produk-detail.open { display:flex; }

.detail-foto {
    flex-shrink:0; display:flex;
    flex-direction:column; align-items:center; gap:4px;
}
.detail-foto__img {
    width:88px; height:88px; object-fit:cover;
    border-radius: 10px; border:1px solid var(--gray-200);
    cursor:zoom-in; transition:border-color 0.15s;
}
.detail-foto__img:hover { border-color: var(--primary-light); }
.detail-foto__hint { font-size:10px; color: var(--gray-400); }
.foto-placeholder {
    width:88px; height:88px;
    background: var(--gray-200); border-radius: 10px;
    display:flex; align-items:center; justify-content:center;
    color: var(--gray-500); font-size:28px;
}

.detail-info  { flex:1; min-width:200px; }
.detail-row-item {
    display:flex; font-size:12px;
    padding:3px 0; border-bottom:1px solid rgba(0,0,0,0.04); gap:8px;
}
.detail-row-item:last-child { border-bottom:none; }
.detail-key { color: var(--gray-500); width:80px; flex-shrink:0; font-weight:500; }
.detail-val { color: var(--gray-800); }

/* Stok progress bar */
.stok-bar-wrap { padding:4px 0 2px; }
.stok-bar-bg   { height:5px; background: var(--gray-200); border-radius:10px; overflow:hidden; }
.stok-bar-fill { height:100%; border-radius:10px; transition:width 0.3s; }

/* Quick action links */
.detail-quick-links {
    display:flex; gap:8px; flex-wrap:wrap;
    margin-top:10px; padding-top:10px;
    border-top:1px solid var(--gray-200);
}
.btn-quick {
    display:inline-flex; align-items:center; gap:5px;
    padding:6px 14px; border-radius: 8px;
    font-size:12px; font-weight:500;
    text-decoration:none; transition:background 0.12s;
    border:1px solid transparent;
}
.btn-quick--detail  { background:#ecfeff; color: var(--info); border-color: var(--info); }
.btn-quick--detail:hover  { background:#cffafe; }
.btn-quick--opname  { background:#fffbeb; color: var(--warning-dark); border-color: var(--warning-light); }
.btn-quick--opname:hover  { background:#fef3c7; }

/* ── EMPTY STATE ── */
.empty-state { text-align:center; padding:3rem 1rem; color: var(--gray-400); }
.empty-state i { font-size:40px; margin-bottom:12px; display:block; }

/* ── LIGHTBOX ── */
.lightbox-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.85);
    z-index:9999; align-items:center; justify-content:center;
}
.lightbox-overlay.show { display:flex; }
.lightbox-overlay img {
    max-width:90%; max-height:80vh;
    border-radius:6px; border:2px solid #fff; object-fit:contain;
}
.lightbox-close {
    position:absolute; top:20px; right:30px;
    color:#fff; font-size:36px;
    cursor:pointer; line-height:1; user-select:none;
}

/* ── RESPONSIVE ── */
@media (max-width: 480px) {
    .stok-indicator    { display:none; }
    .status-mini       { display:none; }
    .badge-motif-count { display:none; }
    .brand-header .badge-stok { display:inline-flex; }
}
</style>
<?= $this->endSection() ?>