<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Store Billing — New Order</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ── Global ─────────────────────────────────────────── */
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* ── Header ─────────────────────────────────────────── */
        .main-header {
            background: linear-gradient(135deg, #0d1b2a 0%, #1b2d45 100%);
            padding: 0;
            box-shadow: 0 3px 15px rgba(0,0,0,.35);
        }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.5rem;
        }
        .header-brand { display:flex; align-items:center; gap:.75rem; }
        .header-icon {
            width:42px; height:42px; border-radius:10px;
            background:rgba(255,255,255,.12);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:1.3rem;
        }
        .header-title { color:#fff; font-weight:700; font-size:1.25rem; margin:0; line-height:1.2; }
        .header-subtitle { color:rgba(255,255,255,.55); font-size:.75rem; margin:0; }
        .header-badge {
            background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18);
            color:#cfd8e3; font-size:.78rem; border-radius:6px; padding:.3rem .7rem;
        }

        /* ── Cards ──────────────────────────────────────────── */
        .card { border:none; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
        .card-header-dark {
            background: linear-gradient(90deg,#1b2d45,#243b55);
            color:#fff; border-radius:12px 12px 0 0 !important;
            padding:.7rem 1.1rem;
        }
        .section-icon { opacity:.75; }

        /* ── Product rows ────────────────────────────────────── */
        .product-row td { vertical-align: middle; }
        .product-row select, .product-row input { font-size:.875rem; }
        #products-table th { font-size:.8rem; text-transform:uppercase; letter-spacing:.04em;
            background:#f8f9fa; color:#6c757d; border-bottom:2px solid #dee2e6; }

        /* ── Low-stock panel ─────────────────────────────────── */
        .low-stock-item { display:flex; justify-content:space-between; align-items:center;
            padding:.35rem 0; border-bottom:1px solid rgba(0,0,0,.06); font-size:.84rem; }
        .low-stock-item:last-child { border-bottom:none; }
        .stock-badge { font-size:.72rem; min-width:38px; text-align:center; }

        /* ── Payment card ────────────────────────────────────── */
        .payment-row { display:flex; justify-content:space-between; align-items:center;
            padding:.45rem 0; border-bottom:1px solid #f0f0f0; }
        .payment-row:last-child { border-bottom:none; }
        .payment-label { font-size:.85rem; color:#495057; }
        .payment-value { font-weight:600; font-size:.95rem; }
        .grand-total-row { background:#e8f5e9; border-radius:8px; padding:.5rem .75rem !important; margin:.4rem -.15rem; }
        .grand-total-row .payment-label { color:#1b5e20; font-weight:700; font-size:.95rem; }
        .grand-total-row .payment-value { color:#1b5e20; font-size:1.1rem; }

        /* ── Bill modal ─────────────────────────────────────── */
        .bill-header { background:#1b2d45; color:#fff; border-radius:8px; padding:1rem; text-align:center; }
        .bill-divider { border-top:2px dashed #dee2e6; margin:1rem 0; }
        .bill-total-row { font-size:1rem; font-weight:700; color:#1b5e20; }
        @media print {
            .no-print { display:none !important; }
            .modal-footer { display:none !important; }
            body { background:#fff; }
            .modal-dialog { max-width:100%; margin:0; }
            .modal-content { box-shadow:none; border:none; }
        }

        /* ── Misc ───────────────────────────────────────────── */
        .btn-generate {
            background: linear-gradient(135deg,#2e7d32,#43a047);
            border:none; font-weight:700; font-size:1rem; letter-spacing:.04em;
            padding:.7rem 2rem; border-radius:10px;
            box-shadow:0 4px 12px rgba(46,125,50,.4);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-generate:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(46,125,50,.5); }
        .btn-generate:active { transform:translateY(0); }
        .btn-generate:disabled { opacity:.65; transform:none; }
        .remove-row-btn { width:30px; height:30px; padding:0; line-height:1; font-size:.9rem; }
        .stock-info { font-size:.75rem; color:#6c757d; }
        .stock-warn { color:#e53935; }
        .add-row-btn { font-size:.85rem; }
        #alert-area .alert { border-radius:10px; }
        #customer-lookup-spinner { display:none; }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════════
     HEADER
═══════════════════════════════════════════════════════════ -->
<header class="main-header">
    <div class="header-inner">
        <div class="header-brand">
            <div class="header-icon"><i class="bi bi-cart3"></i></div>
            <div>
                <p class="header-title">Store Billing</p>
                <p class="header-subtitle">New Order</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="header-badge"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
            <span class="header-badge"><i class="bi bi-clock me-1"></i><span id="clock"></span></span>
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════ -->
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Alert Area -->
    <div id="alert-area" class="mb-3"></div>

    <div class="row g-4">

        <!-- ─── LEFT COLUMN (Customer + Products + Payment) ─── -->
        <div class="col-12 col-xl-8">

            <!-- ── CUSTOMER SECTION ──────────────────────────── -->
            <div class="card mb-4">
                <div class="card-header card-header-dark d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle section-icon"></i>
                    <span class="fw-semibold">Customer Information</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_email" class="form-label fw-semibold small">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="customer_email" class="form-control"
                                    placeholder="customer@example.com" autocomplete="off">
                                <span id="customer-lookup-spinner" class="input-group-text bg-light">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                </span>
                            </div>
                            <div id="email-feedback" class="form-text"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label fw-semibold small">
                                Customer Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" id="customer_name" class="form-control"
                                    placeholder="Full name" autocomplete="off">
                            </div>
                            <div id="name-feedback" class="form-text"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── PRODUCTS SECTION ──────────────────────────── -->
            <div class="card mb-4">
                <div class="card-header card-header-dark d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam section-icon"></i>
                        <span class="fw-semibold">Order Items</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-light add-row-btn" id="add-product-btn">
                        <i class="bi bi-plus-circle me-1"></i>Add Product
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="products-table">
                            <thead>
                                <tr>
                                    <th style="width:38%" class="ps-3">Product</th>
                                    <th style="width:14%">Qty</th>
                                    <th style="width:16%">Unit Price</th>
                                    <th style="width:16%">Line Total</th>
                                    <th style="width:16%">Tax</th>
                                    <th style="width:5%"></th>
                                </tr>
                            </thead>
                            <tbody id="product-rows">
                                <!-- Rows injected by JS -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no-items-msg" class="text-center text-muted py-4 small">
                        <i class="bi bi-cart-x fs-3 d-block mb-2 text-secondary"></i>
                        No products added. Click <strong>Add Product</strong> to begin.
                    </div>
                </div>
            </div>

            <!-- ── PAYMENT SECTION ───────────────────────────── -->
            <div class="card">
                <div class="card-header card-header-dark d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin section-icon"></i>
                    <span class="fw-semibold">Payment</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="payment-row">
                                <span class="payment-label">Subtotal</span>
                                <span class="payment-value" id="display-subtotal">₹ 0.00</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Tax Amount</span>
                                <span class="payment-value text-warning" id="display-tax">₹ 0.00</span>
                            </div>
                            <div class="payment-row grand-total-row mt-2">
                                <span class="payment-label"><i class="bi bi-receipt me-1"></i>Grand Total</span>
                                <span class="payment-value" id="display-grand-total">₹ 0.00</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="amount_paid" class="form-label fw-semibold small">
                                Amount Given by Customer <span class="text-danger">*</span>
                            </label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">₹</span>
                                <input type="number" id="amount_paid" class="form-control"
                                    placeholder="0.00" min="0" step="0.01">
                            </div>
                            <div class="payment-row">
                                <span class="payment-label" id="balance-label">Balance to Return</span>
                                <span class="payment-value" id="display-balance">—</span>
                            </div>
                            <div id="amount-feedback" class="form-text text-danger mt-1" style="display:none"></div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" id="generate-bill-btn" class="btn btn-success btn-generate">
                            <i class="bi bi-receipt-cutoff me-2"></i>Generate Bill
                        </button>
                    </div>
                </div>
            </div>
        </div><!-- /left col -->

        <!-- ─── RIGHT COLUMN (Low-Stock Alert) ─── -->
        <div class="col-12 col-xl-4">
            <div class="card h-auto">
                <div class="card-header card-header-dark d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle section-icon"></i>
                    <span class="fw-semibold">Low Stock Alert</span>
                    <span class="badge bg-warning text-dark ms-auto" id="low-stock-count">{{ $lowStock->count() }}</span>
                </div>
                <div class="card-body" id="low-stock-panel">
                    @if($lowStock->isEmpty())
                        <div class="text-center text-muted small py-3" id="low-stock-empty">
                            <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                            All products are well-stocked (threshold: {{ $threshold }}).
                        </div>
                    @else
                        <div class="small text-muted mb-2">
                            Products with stock &lt; {{ $threshold }} units
                        </div>
                        @foreach($lowStock as $p)
                        <div class="low-stock-item">
                            <span>{{ $p->name }}</span>
                            <span class="badge stock-badge {{ $p->stock_quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $p->stock_quantity == 0 ? 'Out' : $p->stock_quantity }}
                            </span>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="card-footer bg-transparent text-end">
                    <button class="btn btn-sm btn-outline-secondary" id="refresh-low-stock-btn">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div><!-- /right col -->

    </div><!-- /row -->
</div><!-- /container -->

<!-- ═══════════════════════════════════════════════════════════
     BILL MODAL
═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header no-print" style="background:#1b2d45; color:#fff;">
                <h5 class="modal-title" id="billModalLabel"><i class="bi bi-receipt me-2"></i>Order Bill</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="bill-content">
                <!-- Filled dynamically -->
            </div>
            <div class="modal-footer no-print">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print Bill
                </button>
                <button class="btn btn-success" id="new-order-btn">
                    <i class="bi bi-plus-circle me-1"></i>New Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ═══════════════════════════════════════════════════════════
//  CONFIG (passed from Laravel)
// ═══════════════════════════════════════════════════════════
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const API_ORDERS = '/api/orders';
const API_LOOKUP = '/billing/customer-lookup';
const API_LOW    = '/billing/low-stock';

// Product catalog loaded from backend
let productCatalog = {};    // id -> {id, name, code, price, tax_percentage, stock_quantity}
let rowCounter = 0;
let submitInFlight = false;

// ═══════════════════════════════════════════════════════════
//  CLOCK
// ═══════════════════════════════════════════════════════════
(function clock() {
    const el = document.getElementById('clock');
    const tick = () => {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('en-IN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
    };
    tick(); setInterval(tick, 1000);
})();

// ═══════════════════════════════════════════════════════════
//  LOAD PRODUCT CATALOG
// ═══════════════════════════════════════════════════════════
async function loadProducts() {
    try {
        const res  = await fetch('/api/products');
        const data = await res.json();
        if (data.success) {
            productCatalog = {};
            data.data.forEach(p => { productCatalog[p.id] = p; });
        }
    } catch(e) {
        console.error('Failed to load products', e);
    }
}
loadProducts();

// ═══════════════════════════════════════════════════════════
//  CUSTOMER LOOKUP (debounced)
// ═══════════════════════════════════════════════════════════
let lookupTimer = null;
const emailInput  = document.getElementById('customer_email');
const nameInput   = document.getElementById('customer_name');
const emailFb     = document.getElementById('email-feedback');
const nameFb      = document.getElementById('name-feedback');
const spinner     = document.getElementById('customer-lookup-spinner');

emailInput.addEventListener('input', () => {
    clearTimeout(lookupTimer);
    emailFb.textContent = '';
    emailFb.className   = 'form-text';
    nameInput.classList.remove('border-success','border-warning');

    const email = emailInput.value.trim();
    if (!email) return;

    // Basic format check before hitting server
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailFb.textContent = 'Enter a valid email address.';
        emailFb.className   = 'form-text text-danger';
        return;
    }

    lookupTimer = setTimeout(() => doLookup(email), 450);
});

async function doLookup(email) {
    spinner.style.display = 'flex';
    try {
        const res  = await fetch(`${API_LOOKUP}?email=${encodeURIComponent(email)}`);
        const data = await res.json();
        if (data.found) {
            nameInput.value = data.name;
            nameInput.classList.add('border-success');
            emailFb.textContent = '✓ Existing customer found.';
            emailFb.className   = 'form-text text-success';
            nameFb.textContent  = 'Name auto-filled from database.';
            nameFb.className    = 'form-text text-success';
        } else {
            nameInput.value = '';
            nameInput.classList.remove('border-success');
            nameInput.classList.add('border-warning');
            emailFb.textContent = 'New customer — please enter a name.';
            emailFb.className   = 'form-text text-warning';
            nameFb.textContent  = '';
        }
    } catch(e) {
        emailFb.textContent = 'Lookup failed. Check connection.';
        emailFb.className   = 'form-text text-danger';
    } finally {
        spinner.style.display = 'none';
    }
}

// ═══════════════════════════════════════════════════════════
//  PRODUCT ROW MANAGEMENT
// ═══════════════════════════════════════════════════════════
function buildProductOptions(selectedId = '') {
    const opts = ['<option value="">— Select product —</option>'];
    Object.values(productCatalog).sort((a,b) => a.name.localeCompare(b.name)).forEach(p => {
        const disabled = p.stock_quantity === 0 ? 'disabled' : '';
        const label = `${p.name} (${p.code}) — Stock: ${p.stock_quantity}`;
        const sel   = String(selectedId) === String(p.id) ? 'selected' : '';
        opts.push(`<option value="${p.id}" ${sel} ${disabled}>${label}</option>`);
    });
    return opts.join('');
}

function addProductRow(productId = '', qty = 1) {
    rowCounter++;
    const id  = rowCounter;
    const tbody = document.getElementById('product-rows');
    const tr  = document.createElement('tr');
    tr.className = 'product-row';
    tr.dataset.rowId = id;
    tr.innerHTML = `
        <td class="ps-3">
            <select class="form-select form-select-sm product-select" id="product_select_${id}">
                ${buildProductOptions(productId)}
            </select>
            <div class="stock-info mt-1" id="stock_info_${id}"></div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" id="qty_${id}"
                value="${qty}" min="1" step="1" style="width:75px">
        </td>
        <td><span class="text-muted" id="price_${id}">—</span></td>
        <td><span class="fw-semibold" id="line_total_${id}">—</span></td>
        <td><span class="text-muted small" id="tax_${id}">—</span></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"
                onclick="removeRow(${id})" title="Remove">
                <i class="bi bi-x"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);

    const sel = document.getElementById(`product_select_${id}`);
    sel.addEventListener('change', () => onProductChange(id));
    document.getElementById(`qty_${id}`).addEventListener('input', () => updateRowCalc(id));

    if (productId) onProductChange(id);
    toggleNoItemsMsg();
    return id;
}

function removeRow(id) {
    const tr = document.querySelector(`tr[data-row-id="${id}"]`);
    if (tr) tr.remove();
    toggleNoItemsMsg();
    recalcTotals();
}

function toggleNoItemsMsg() {
    const rows = document.querySelectorAll('.product-row');
    document.getElementById('no-items-msg').style.display = rows.length ? 'none' : 'block';
}

function onProductChange(id) {
    const sel  = document.getElementById(`product_select_${id}`);
    const pid  = parseInt(sel.value);
    const info = document.getElementById(`stock_info_${id}`);
    const qtyInput = document.getElementById(`qty_${id}`);

    if (!pid || !productCatalog[pid]) {
        document.getElementById(`price_${id}`).textContent      = '—';
        document.getElementById(`line_total_${id}`).textContent  = '—';
        document.getElementById(`tax_${id}`).textContent         = '—';
        info.textContent = '';
        recalcTotals();
        return;
    }

    const p   = productCatalog[pid];
    const warn = p.stock_quantity <= 5 ? ' stock-warn' : '';
    info.innerHTML = `<span class="${warn}"><i class="bi bi-box2"></i> ${p.stock_quantity} in stock</span>`;
    qtyInput.max   = p.stock_quantity;
    updateRowCalc(id);
}

function updateRowCalc(id) {
    const sel   = document.getElementById(`product_select_${id}`);
    const pid   = parseInt(sel.value);
    const qty   = Math.max(0, parseInt(document.getElementById(`qty_${id}`).value) || 0);
    const priceEl = document.getElementById(`price_${id}`);
    const totalEl = document.getElementById(`line_total_${id}`);
    const taxEl   = document.getElementById(`tax_${id}`);

    if (!pid || !productCatalog[pid]) { recalcTotals(); return; }

    const p       = productCatalog[pid];
    const sub     = parseFloat(p.price) * qty;
    const taxAmt  = sub * (parseFloat(p.tax_percentage) / 100);
    const total   = sub + taxAmt;

    priceEl.textContent = fmt(p.price);
    totalEl.textContent = fmt(total);
    taxEl.textContent   = `${p.tax_percentage}% (${fmt(taxAmt)})`;

    // Validate qty vs stock
    const qtyInput = document.getElementById(`qty_${id}`);
    if (qty > p.stock_quantity) {
        qtyInput.classList.add('is-invalid');
    } else {
        qtyInput.classList.remove('is-invalid');
    }

    recalcTotals();
}

// ═══════════════════════════════════════════════════════════
//  TOTALS CALCULATION
// ═══════════════════════════════════════════════════════════
function fmt(n) { return '₹ ' + parseFloat(n).toFixed(2); }

function recalcTotals() {
    let subtotal = 0, tax = 0;

    document.querySelectorAll('.product-row').forEach(tr => {
        const id  = tr.dataset.rowId;
        const pid = parseInt(document.getElementById(`product_select_${id}`)?.value);
        const qty = parseInt(document.getElementById(`qty_${id}`)?.value) || 0;
        if (!pid || !productCatalog[pid] || qty <= 0) return;
        const p   = productCatalog[pid];
        const sub = parseFloat(p.price) * qty;
        const tx  = sub * (parseFloat(p.tax_percentage) / 100);
        subtotal += sub;
        tax      += tx;
    });

    const grand = subtotal + tax;
    document.getElementById('display-subtotal').textContent   = fmt(subtotal);
    document.getElementById('display-tax').textContent        = fmt(tax);
    document.getElementById('display-grand-total').textContent= fmt(grand);

    calcBalance();
}

function calcBalance() {
    const grand   = parseFloat(document.getElementById('display-grand-total').textContent.replace('₹','').trim()) || 0;
    const paid    = parseFloat(document.getElementById('amount_paid').value) || 0;
    const balEl   = document.getElementById('display-balance');
    const lblEl   = document.getElementById('balance-label');
    const fbEl    = document.getElementById('amount-feedback');

    if (!paid) { balEl.textContent = '—'; lblEl.textContent = 'Balance to Return'; fbEl.style.display='none'; return; }

    if (grand === 0) { balEl.textContent = '—'; return; }

    if (paid >= grand) {
        const change = paid - grand;
        balEl.textContent  = fmt(change);
        balEl.className    = 'payment-value text-success fw-bold';
        lblEl.textContent  = 'Change to Return';
        fbEl.style.display = 'none';
    } else {
        const due = grand - paid;
        balEl.textContent  = fmt(due) + ' due';
        balEl.className    = 'payment-value text-danger';
        lblEl.textContent  = 'Balance Due';
        fbEl.textContent   = 'Amount given is less than the grand total.';
        fbEl.style.display = 'block';
    }
}

document.getElementById('amount_paid').addEventListener('input', calcBalance);

// ═══════════════════════════════════════════════════════════
//  ADD PRODUCT BUTTON
// ═══════════════════════════════════════════════════════════
document.getElementById('add-product-btn').addEventListener('click', () => addProductRow());

// ═══════════════════════════════════════════════════════════
//  CLIENT-SIDE VALIDATION
// ═══════════════════════════════════════════════════════════
function validateForm() {
    const errors = [];
    const email  = emailInput.value.trim();
    const name   = nameInput.value.trim();

    if (!email) errors.push('Customer email is required.');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Enter a valid email address.');
    if (!name)  errors.push('Customer name is required.');

    const rows = document.querySelectorAll('.product-row');
    if (!rows.length) { errors.push('Please add at least one product.'); }

    let hasInvalidQty = false;
    rows.forEach(tr => {
        const id  = tr.dataset.rowId;
        const pid = parseInt(document.getElementById(`product_select_${id}`)?.value);
        const qty = parseInt(document.getElementById(`qty_${id}`)?.value) || 0;
        if (!pid) { errors.push('Please select a product for all rows.'); }
        if (qty < 1) { errors.push('All quantities must be at least 1.'); hasInvalidQty = true; }
        if (pid && productCatalog[pid] && qty > productCatalog[pid].stock_quantity) {
            errors.push(`Quantity for "${productCatalog[pid].name}" exceeds available stock (${productCatalog[pid].stock_quantity}).`);
        }
    });

    const grand = parseFloat(document.getElementById('display-grand-total').textContent.replace('₹','').trim()) || 0;
    const paid  = parseFloat(document.getElementById('amount_paid').value) || 0;
    if (grand > 0 && paid < grand) {
        errors.push(`Amount paid (₹${paid.toFixed(2)}) is less than the grand total (₹${grand.toFixed(2)}).`);
    }

    return errors;
}

// ═══════════════════════════════════════════════════════════
//  GENERATE BILL
// ═══════════════════════════════════════════════════════════
document.getElementById('generate-bill-btn').addEventListener('click', async () => {
    if (submitInFlight) return;
    clearAlerts();

    const errors = validateForm();
    if (errors.length) {
        showAlert('danger', '<strong>Please fix the following issues:</strong><ul class="mb-0 mt-1">' +
            errors.map(e => `<li>${e}</li>`).join('') + '</ul>');
        return;
    }

    // Build payload
    const rows  = document.querySelectorAll('.product-row');
    const items = [];
    rows.forEach(tr => {
        const id  = tr.dataset.rowId;
        const pid = parseInt(document.getElementById(`product_select_${id}`)?.value);
        const qty = parseInt(document.getElementById(`qty_${id}`)?.value) || 0;
        if (pid && qty > 0) items.push({ product_id: pid, quantity: qty });
    });

    const payload = {
        customer:     { name: nameInput.value.trim(), email: emailInput.value.trim() },
        items:        items,
        amount_paid:  parseFloat(document.getElementById('amount_paid').value) || null,
    };

    // Lock button
    const btn = document.getElementById('generate-bill-btn');
    btn.disabled    = true;
    btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing…';
    submitInFlight  = true;

    try {
        const res  = await fetch(API_ORDERS, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify(payload),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            showBill(data.data, payload.amount_paid);
            await refreshLowStock();
            // Update catalog stock quantities
            if (data.data.items) {
                data.data.items.forEach(item => {
                    if (productCatalog[item.product_id]) {
                        productCatalog[item.product_id].stock_quantity -= item.quantity;
                    }
                });
                // Refresh all row stock info
                document.querySelectorAll('.product-row').forEach(tr => onProductChange(tr.dataset.rowId));
            }
        } else {
            handleApiError(data);
        }
    } catch(e) {
        showAlert('danger', 'Network error. Please check your connection and try again.');
    } finally {
        btn.disabled   = false;
        btn.innerHTML  = '<i class="bi bi-receipt-cutoff me-2"></i>Generate Bill';
        submitInFlight = false;
    }
});

function handleApiError(data) {
    if (data.errors && data.errors.items) {
        // Stock errors from OrderService
        const msgs = data.errors.items.map(e => e.message);
        showAlert('danger', '<strong>Stock Error:</strong><ul class="mb-0 mt-1">' +
            msgs.map(m => `<li>${m}</li>`).join('') + '</ul>');
        return;
    }
    if (data.errors) {
        // Laravel validation errors
        const msgs = Object.values(data.errors).flat();
        showAlert('danger', '<strong>Validation Errors:</strong><ul class="mb-0 mt-1">' +
            msgs.map(m => `<li>${m}</li>`).join('') + '</ul>');
        return;
    }
    showAlert('danger', data.message || 'An unexpected error occurred.');
}

// ═══════════════════════════════════════════════════════════
//  BILL MODAL RENDERER
// ═══════════════════════════════════════════════════════════
function showBill(order, amountPaid) {
    const c      = order.customer || {};
    const items  = order.items  || [];
    const paid   = amountPaid   || order.amount_paid || 0;
    const change = parseFloat(order.change_amount) || Math.max(0, paid - parseFloat(order.grand_total));
    const due    = parseFloat(order.grand_total) - paid;

    let itemRows = items.map(item => `
        <tr>
            <td>${item.product_name || '—'}</td>
            <td class="text-center">${item.quantity}</td>
            <td class="text-end">₹ ${parseFloat(item.unit_price).toFixed(2)}</td>
            <td class="text-end text-muted small">${parseFloat(item.tax_percentage).toFixed(2)}%</td>
            <td class="text-end text-muted small">₹ ${parseFloat(item.tax_amount).toFixed(2)}</td>
            <td class="text-end fw-semibold">₹ ${parseFloat(item.total).toFixed(2)}</td>
        </tr>`).join('');

    const balanceRow = paid >= parseFloat(order.grand_total)
        ? `<tr class="table-success">
                <td colspan="5" class="fw-semibold">Change Returned</td>
                <td class="text-end fw-bold text-success">₹ ${change.toFixed(2)}</td>
           </tr>`
        : `<tr class="table-danger">
                <td colspan="5" class="fw-semibold">Balance Due</td>
                <td class="text-end fw-bold text-danger">₹ ${due.toFixed(2)}</td>
           </tr>`;

    const now = new Date();
    document.getElementById('bill-content').innerHTML = `
        <div class="bill-header mb-3">
            <div class="fs-5 fw-bold mb-1"><i class="bi bi-cart3 me-2"></i>Store Billing</div>
            <div class="text-white-50 small">Tax Invoice</div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div class="small text-muted">Order ID</div>
                <div class="fw-bold">#${String(order.id).padStart(5,'0')}</div>
            </div>
            <div class="col-6 text-end">
                <div class="small text-muted">Date & Time</div>
                <div class="fw-bold">${now.toLocaleDateString('en-IN')} ${now.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'})}</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="small text-muted">Customer</div>
                <div class="fw-semibold">${c.name || '—'}</div>
                <div class="text-muted small">${c.email || '—'}</div>
            </div>
        </div>

        <div class="bill-divider"></div>

        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Tax%</th>
                        <th class="text-end">Tax Amt</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>${itemRows}</tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="5" class="fw-semibold">Subtotal</td>
                        <td class="text-end fw-semibold">₹ ${parseFloat(order.subtotal).toFixed(2)}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="5" class="fw-semibold">Total Tax</td>
                        <td class="text-end fw-semibold">₹ ${parseFloat(order.tax_amount).toFixed(2)}</td>
                    </tr>
                    <tr class="bill-total-row">
                        <td colspan="5">Grand Total</td>
                        <td class="text-end">₹ ${parseFloat(order.grand_total).toFixed(2)}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="5">Amount Paid</td>
                        <td class="text-end">₹ ${parseFloat(paid).toFixed(2)}</td>
                    </tr>
                    ${balanceRow}
                </tfoot>
            </table>
        </div>

        <div class="bill-divider"></div>
        <div class="text-center text-muted small">
            <i class="bi bi-check-circle text-success me-1"></i>
            Order placed successfully. Thank you for your purchase!
        </div>`;

    const modal = new bootstrap.Modal(document.getElementById('billModal'));
    modal.show();
    showAlert('success', `<i class="bi bi-check-circle me-2"></i>Order <strong>#${String(order.id).padStart(5,'0')}</strong> created successfully!`);
}

// ═══════════════════════════════════════════════════════════
//  LOW STOCK REFRESH
// ═══════════════════════════════════════════════════════════
async function refreshLowStock() {
    try {
        const res  = await fetch(API_LOW);
        const data = await res.json();
        const panel = document.getElementById('low-stock-panel');
        const badge = document.getElementById('low-stock-count');

        badge.textContent = data.products.length;

        if (!data.products.length) {
            panel.innerHTML = `<div class="text-center text-muted small py-3">
                <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                All products are well-stocked (threshold: ${data.threshold}).
            </div>`;
        } else {
            panel.innerHTML = `<div class="small text-muted mb-2">Products with stock &lt; ${data.threshold} units</div>` +
                data.products.map(p => `
                    <div class="low-stock-item">
                        <span>${p.name}</span>
                        <span class="badge stock-badge ${p.stock_quantity === 0 ? 'bg-danger' : 'bg-warning text-dark'}">
                            ${p.stock_quantity === 0 ? 'Out' : p.stock_quantity}
                        </span>
                    </div>`).join('');
        }
    } catch(e) {
        console.error('Failed to refresh low stock', e);
    }
}

document.getElementById('refresh-low-stock-btn').addEventListener('click', refreshLowStock);

// ═══════════════════════════════════════════════════════════
//  NEW ORDER (reset form)
// ═══════════════════════════════════════════════════════════
document.getElementById('new-order-btn').addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('billModal')).hide();
    resetForm();
});

function resetForm() {
    emailInput.value = '';
    nameInput.value  = '';
    emailFb.textContent = nameFb.textContent = '';
    nameInput.classList.remove('border-success','border-warning');
    document.getElementById('product-rows').innerHTML = '';
    rowCounter = 0;
    document.getElementById('amount_paid').value = '';
    recalcTotals();
    toggleNoItemsMsg();
    clearAlerts();
    // Reload fresh product catalog (stock may have changed)
    loadProducts();
}

// ═══════════════════════════════════════════════════════════
//  ALERT HELPERS
// ═══════════════════════════════════════════════════════════
function showAlert(type, msg) {
    const area = document.getElementById('alert-area');
    const div  = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show`;
    div.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    area.prepend(div);
    window.scrollTo({top:0, behavior:'smooth'});
}
function clearAlerts() { document.getElementById('alert-area').innerHTML = ''; }

// ═══════════════════════════════════════════════════════════
//  INIT — add first row automatically
// ═══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    toggleNoItemsMsg();
});
</script>
</body>
</html>
