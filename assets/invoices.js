const API_INVOICES = 'api/invoices.php';
const API_INV_DETAILS = 'api/invoiceDetails.php';
const API_SERVICES = 'api/services.php';

// Invoice CRUD
const invoicesTbody = document.getElementById('invoicesTbody');
const invoiceForm = document.getElementById('invoiceForm');
const invoiceFormMode = document.getElementById('invoiceFormMode');
const invoiceFormTitle = document.getElementById('invoiceFormTitle');
const invoiceFormMsg = document.getElementById('invoiceFormMsg');

const MaHDEl = document.getElementById('MaHD');
const MaNVSelect = document.getElementById('MaNVSelect');
const MaKHSelect = document.getElementById('MaKHSelect');
const NgayInEl = document.getElementById('NgayIn');
const invoiceResetBtn = document.getElementById('invoiceResetBtn');

// Details form + table
const invoiceDetailsTbody = document.getElementById('invoiceDetailsTbody');
const detailForm = document.getElementById('detailForm');
const detailFormMode = document.getElementById('detailFormMode');
const detailFormTitle = document.getElementById('detailFormTitle');
const detailFormMsg = document.getElementById('detailFormMsg');
const detailMaHDSelect = document.getElementById('detailMaHDSelect');
const detailMaDVSelect = document.getElementById('detailMaDVSelect');
const detailSoLuongEl = document.getElementById('detailSoLuong');
const detailResetBtn = document.getElementById('detailResetBtn');
const detailKeyMaHD = document.getElementById('detailKeyMaHD');
const detailKeyMaDV = document.getElementById('detailKeyMaDV');

function setMsg(el, text, kind) {
  el.textContent = text || '';
  el.classList.remove('ok', 'err');
  if (kind) el.classList.add(kind);
}

// ---------------- Invoice CRUD ----------------
function setInvoiceFormMode(mode) {
  invoiceFormMode.value = mode;
  if (mode === 'create') {
    invoiceFormTitle.textContent = 'Thêm hóa đơn';
    MaHDEl.readOnly = false;
  } else {
    invoiceFormTitle.textContent = 'Cập nhật hóa đơn';
    MaHDEl.readOnly = true;
  }
  setMsg(invoiceFormMsg, '', null);
}

function resetInvoiceForm() {
  invoiceForm.reset();
  setInvoiceFormMode('create');
}

function fillInvoiceForm(row) {
  MaHDEl.value = row.MaHD ?? '';
  MaNVSelect.value = row.MaNV ?? '';
  MaKHSelect.value = row.MaKH ?? '';
  NgayInEl.value = row.NgayIn ? String(row.NgayIn) : '';
}

function renderInvoices(rows) {
  if (!rows || rows.length === 0) {
    invoicesTbody.innerHTML = '<tr><td colspan="7">Chưa có dữ liệu</td></tr>';
    return;
  }
  invoicesTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaHD)}</td>
      <td>${escHtml(r.NgayIn)}</td>
      <td>${escHtml(r.MaKH)}</td>
      <td>${escHtml(r.TenKH)}</td>
      <td>${escHtml(r.MaNV)}</td>
      <td>${escHtml(r.TenNV)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaHD)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaHD)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadInvoices() {
  const res = await fetchJson(`${API_INVOICES}?action=list`);
  renderInvoices(res.data);
}

async function loadInvoiceDropdowns() {
  const [nvRes, khRes, hdRes] = await Promise.all([
    fetchJson(`${API_INVOICES}?action=dropdownEmployees`),
    fetchJson(`${API_INVOICES}?action=dropdownCustomers`),
    fetchJson(`${API_INVOICES}?action=dropdownInvoices`),
  ]);

  const nvRows = nvRes.data || [];
  MaNVSelect.innerHTML = nvRows.map(r => `<option value="${escHtml(r.MaNV)}">${escHtml(r.MaNV)} - ${escHtml(r.TenNV)}</option>`).join('');

  const khRows = khRes.data || [];
  MaKHSelect.innerHTML = khRows.map(r => `<option value="${escHtml(r.MaKH)}">${escHtml(r.MaKH)} - ${escHtml(r.TenKH)}</option>`).join('');

  // Details MaHD dropdown
  const hdRows = hdRes.data || [];
  detailMaHDSelect.innerHTML = hdRows.map(r => `<option value="${escHtml(r.MaHD)}">${escHtml(r.MaHD)} - ${escHtml(r.NgayIn)}</option>`).join('');
}

invoiceForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = invoiceFormMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaHD: MaHDEl.value.trim(),
      MaNV: MaNVSelect.value,
      MaKH: MaKHSelect.value,
      NgayIn: NgayInEl.value,
    };

    if (!payload.MaHD || !payload.MaNV || !payload.MaKH || !payload.NgayIn) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg(invoiceFormMsg, 'Đang lưu...', null);
    await fetchJson(`${API_INVOICES}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg(invoiceFormMsg, 'Lưu thành công', 'ok');
    await loadInvoices();
    await loadInvoiceDropdowns();
    resetInvoiceForm();
    await loadInvoiceDetails();
  } catch (err) {
    setMsg(invoiceFormMsg, err.message || String(err), 'err');
  }
});

invoiceResetBtn.addEventListener('click', resetInvoiceForm);

invoicesTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_INVOICES}?action=list`);
      const row = res.data.find(x => x.MaHD === id);
      if (!row) throw new Error('Không tìm thấy hóa đơn');
      setInvoiceFormMode('update');
      fillInvoiceForm(row);
      setMsg(invoiceFormMsg, '', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa hóa đơn ${id}? (Có thể fail do ràng buộc)`)) return;
      await fetchJson(`${API_INVOICES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaHD: id }),
      });
      setMsg(invoiceFormMsg, 'Đã xóa', 'ok');
      await loadInvoices();
      await loadInvoiceDropdowns();
      resetInvoiceForm();
      await loadInvoiceDetails();
    }
  } catch (err) {
    setMsg(invoiceFormMsg, err.message || String(err), 'err');
  }
});

// ---------------- Invoice details ----------------
function setDetailFormMode(mode) {
  detailFormMode.value = mode;
  if (mode === 'create') {
    detailFormTitle.textContent = 'Thêm chi tiết';
    detailKeyMaHD.value = '';
    detailKeyMaDV.value = '';
    detailMaHDSelect.disabled = false;
    detailMaDVSelect.disabled = false;
  } else {
    detailFormTitle.textContent = 'Cập nhật chi tiết';
    detailMaHDSelect.disabled = true;
    detailMaDVSelect.disabled = true;
  }
  setMsg(detailFormMsg, '', null);
}

function resetDetailForm() {
  detailForm.reset();
  setDetailFormMode('create');
}

async function loadServicesDropdown() {
  const res = await fetchJson(`${API_SERVICES}?action=dropdown`);
  const rows = res.data || [];
  detailMaDVSelect.innerHTML = rows.map(r => `<option value="${escHtml(r.MaDV)}">${escHtml(r.MaDV)} - ${escHtml(r.TenDV)}</option>`).join('');
}

function renderDetails(rows) {
  if (!rows || rows.length === 0) {
    invoiceDetailsTbody.innerHTML = '<tr><td colspan="4">Chưa có chi tiết</td></tr>';
    return;
  }
  invoiceDetailsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaDV)}</td>
      <td>${escHtml(r.TenDV)}</td>
      <td>${escHtml(r.SoLuong)}</td>
      <td>
        <button class="btn" data-action="edit" data-mahd="${escHtml(r.MaHD)}" data-madv="${escHtml(r.MaDV)}" data-sol="${escHtml(r.SoLuong)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-mahd="${escHtml(r.MaHD)}" data-madv="${escHtml(r.MaDV)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadInvoiceDetails() {
  const MaHD = detailMaHDSelect.value;
  if (!MaHD) {
    invoiceDetailsTbody.innerHTML = '<tr><td colspan="4">Chưa chọn hóa đơn</td></tr>';
    return;
  }
  invoiceDetailsTbody.innerHTML = '<tr><td colspan="4">Đang tải...</td></tr>';

  const res = await fetchJson(`${API_INV_DETAILS}?action=listByInvoice&MaHD=${encodeURIComponent(MaHD)}`);
  renderDetails(res.data);
}

detailForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = detailFormMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const MaHD = mode === 'create' ? detailMaHDSelect.value : detailKeyMaHD.value;
    const MaDV = mode === 'create' ? detailMaDVSelect.value : detailKeyMaDV.value;
    const payload = {
      MaHD,
      MaDV,
      SoLuong: detailSoLuongEl.value,
    };

    if (!payload.MaHD || !payload.MaDV || payload.SoLuong === '') {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg(detailFormMsg, 'Đang lưu...', null);
    await fetchJson(`${API_INV_DETAILS}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg(detailFormMsg, 'Lưu thành công', 'ok');
    await loadInvoiceDetails();
    resetDetailForm();
  } catch (err) {
    setMsg(detailFormMsg, err.message || String(err), 'err');
  }
});

detailResetBtn.addEventListener('click', resetDetailForm);

invoiceDetailsTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const MaHD = btn.getAttribute('data-mahd');
  const MaDV = btn.getAttribute('data-madv');
  const SoLuong = btn.getAttribute('data-sol');

  if (!action || !MaHD || !MaDV) return;

  try {
    if (action === 'edit') {
      detailKeyMaHD.value = MaHD;
      detailKeyMaDV.value = MaDV;
      detailMaHDSelect.value = MaHD;
      detailMaDVSelect.value = MaDV;
      detailSoLuongEl.value = SoLuong ?? '';
      setDetailFormMode('update');
      setMsg(detailFormMsg, '', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa chi tiết (${MaHD}, ${MaDV})?`)) return;
      await fetchJson(`${API_INV_DETAILS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaHD, MaDV }),
      });
      setMsg(detailFormMsg, 'Đã xóa', 'ok');
      await loadInvoiceDetails();
      resetDetailForm();
    }
  } catch (err) {
    setMsg(detailFormMsg, err.message || String(err), 'err');
  }
});

detailMaHDSelect.addEventListener('change', () => {
  // Nếu đang update thì vẫn cho phép tải theo invoice hiện tại
  loadInvoiceDetails();
});

// Init
(async function init() {
  try {
    setMsg(invoiceFormMsg, '', null);
    setMsg(detailFormMsg, '', null);
    setInvoiceFormMode('create');
    setDetailFormMode('create');

    await Promise.all([
      loadInvoiceDropdowns(),
      loadServicesDropdown(),
      loadInvoices(),
    ]);

    // chọn invoice đầu tiên để hiển thị chi tiết
    if (detailMaHDSelect.value) {
      await loadInvoiceDetails();
    } else {
      invoiceDetailsTbody.innerHTML = '<tr><td colspan="4">Chưa có hóa đơn</td></tr>';
    }
  } catch (err) {
    setMsg(invoiceFormMsg, err.message || String(err), 'err');
    invoiceDetailsTbody.innerHTML = '<tr><td colspan="4">Tải thất bại</td></tr>';
  }
})();

