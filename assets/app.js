const API_SERVICES = 'api/services.php';
const API_USAGE = 'api/usage.php';

const moneyFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

const servicesTbody = document.getElementById('servicesTbody');
const serviceForm = document.getElementById('serviceForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaDVEl = document.getElementById('MaDV');
const TenDVEl = document.getElementById('TenDV');
const GiaDVEl = document.getElementById('GiaDV');
const BatDauEl = document.getElementById('BatDau');
const KetThucEl = document.getElementById('KetThuc');

const resetBtn = document.getElementById('resetBtn');
const serviceSelect = document.getElementById('serviceSelect');
const usageTbody = document.getElementById('usageTbody');
const billsTbody = document.getElementById('billsTbody');
const reloadBtn = document.getElementById('reloadBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

async function fetchJson(url, options = {}) {
  const res = await fetch(url, options);
  const data = await res.json().catch(() => ({}));
  if (!res.ok || data.ok === false) {
    const err = data.error || data.message || `HTTP ${res.status}`;
    throw new Error(err);
  }
  return data;
}

function moneyToNumber(x) {
  const n = typeof x === 'number' ? x : Number(String(x).replace(',', '.'));
  return Number.isFinite(n) ? n : 0;
}

function normalizeTime(value) {
  if (!value) return '';
  const text = String(value).trim();
  if (/^\d{2}:\d{2}$/.test(text)) return text;
  if (/^\d{2}:\d{2}:\d{2}$/.test(text)) return text.slice(0, 5);
  return text;
}

function fillForm({ MaDV, TenDV, GiaDV, BatDau, KetThuc }) {
  MaDVEl.value = MaDV ?? '';
  TenDVEl.value = TenDV ?? '';
  GiaDVEl.value = moneyToNumber(GiaDV);
  BatDauEl.value = normalizeTime(BatDau);
  KetThucEl.value = normalizeTime(KetThuc);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm dịch vụ';
    MaDVEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật dịch vụ';
    MaDVEl.readOnly = true; // MaDV là PK nên giữ cố định khi update
  }
  setMsg('', null);
}

function resetForm() {
  serviceForm.reset();
  // form.reset() sẽ xoá cả số; đảm bảo kiểu create
  setFormMode('create');
  MaDVEl.value = '';
  TenDVEl.value = '';
  GiaDVEl.value = '';
  BatDauEl.value = '';
  KetThucEl.value = '';
}

function renderServices(rows) {
  if (!rows || rows.length === 0) {
    servicesTbody.innerHTML = '<tr><td colspan="6">Chưa có dịch vụ</td></tr>';
    return;
  }

  servicesTbody.innerHTML = rows.map(r => {
    return `
      <tr>
        <td>${r.MaDV}</td>
        <td>${r.TenDV}</td>
        <td>${moneyFmt.format(moneyToNumber(r.GiaDV))}</td>
        <td>${r.BatDau ?? ''}</td>
        <td>${r.KetThuc ?? ''}</td>
        <td class="actions-cell">
          <button class="btn" data-action="edit" data-id="${r.MaDV}">Sửa</button>
          <button class="btn danger" data-action="delete" data-id="${r.MaDV}">Xóa</button>
        </td>
      </tr>
    `;
  }).join('');
}

async function loadServices() {
  const res = await fetchJson(`${API_SERVICES}?action=list`);
  renderServices(res.data);
}

async function loadDropdown() {
  const res = await fetchJson(`${API_SERVICES}?action=dropdown`);
  const rows = res.data || [];

  serviceSelect.innerHTML = '';
  if (rows.length === 0) {
    serviceSelect.innerHTML = '<option value="">(Trống)</option>';
    return;
  }

  serviceSelect.innerHTML = rows.map(r => `<option value="${r.MaDV}">${r.MaDV} - ${r.TenDV}</option>`).join('');

  // Nếu chưa có lựa chọn thì chọn đầu tiên
  if (!serviceSelect.value) {
    serviceSelect.value = rows[0].MaDV;
  }
}

function renderUsageTable(rows) {
  if (!rows || rows.length === 0) {
    usageTbody.innerHTML = '<tr><td colspan="4">Không có dữ liệu</td></tr>';
    return;
  }
  usageTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${r.MaKH}</td>
      <td>${r.TenKH}</td>
      <td>${r.NgaySuDung ? String(r.NgaySuDung) : ''}</td>
      <td>${r.SoLuong}</td>
    </tr>
  `).join('');
}

function renderBillsTable(rows) {
  if (!rows || rows.length === 0) {
    billsTbody.innerHTML = '<tr><td colspan="5">Không có dữ liệu</td></tr>';
    return;
  }
  billsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${r.MaHD}</td>
      <td>${r.NgayIn ? String(r.NgayIn) : ''}</td>
      <td>${r.TenKH ?? ''}</td>
      <td>${r.TenNV ?? ''}</td>
      <td>${r.SoLuong}</td>
    </tr>
  `).join('');
}

async function loadUsageForSelected() {
  const MaDV = serviceSelect.value;
  if (!MaDV) {
    usageTbody.innerHTML = '<tr><td colspan="4">Chưa chọn dịch vụ</td></tr>';
    billsTbody.innerHTML = '<tr><td colspan="5">Chưa chọn dịch vụ</td></tr>';
    return;
  }

  usageTbody.innerHTML = '<tr><td colspan="4">Đang tải...</td></tr>';
  billsTbody.innerHTML = '<tr><td colspan="5">Đang tải...</td></tr>';

  const [u, b] = await Promise.all([
    fetchJson(`${API_USAGE}?action=usageByService&MaDV=${encodeURIComponent(MaDV)}`),
    fetchJson(`${API_USAGE}?action=billsByService&MaDV=${encodeURIComponent(MaDV)}`),
  ]);

  renderUsageTable(u.data);
  renderBillsTable(b.data);
}

// Event: CRUD submit
serviceForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value; // create | update
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaDV: MaDVEl.value.trim(),
      TenDV: TenDVEl.value.trim(),
      GiaDV: GiaDVEl.value,
      BatDau: BatDauEl.value,
      KetThuc: KetThucEl.value,
    };

    if (!payload.MaDV || !payload.TenDV || payload.GiaDV === '' || !payload.BatDau || !payload.KetThuc) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_SERVICES}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg('Lưu thành công', 'ok');
    await loadServices();
    await loadDropdown();
    await loadUsageForSelected();
    resetForm();
  } catch (err) {
    setMsg(err.message || String(err), 'err');
  }
});

resetBtn.addEventListener('click', resetForm);

// Event: edit/delete
servicesTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');

  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_SERVICES}?action=list`);
      const row = res.data.find(x => x.MaDV === id);
      if (!row) throw new Error('Không tìm thấy dịch vụ');

      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa dịch vụ ${id}? (Có thể thất bại do ràng buộc khóa ngoại)`)) return;
      await fetchJson(`${API_SERVICES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaDV: id }),
      });
      setMsg('Đã xóa', 'ok');
      await loadServices();
      await loadDropdown();
      await loadUsageForSelected();
      resetForm();
    }
  } catch (err) {
    setMsg(err.message || String(err), 'err');
  }
});

// Event: usage view
serviceSelect.addEventListener('change', () => loadUsageForSelected());
reloadBtn.addEventListener('click', () => loadUsageForSelected());

// Init
(async function init() {
  try {
    setMsg('', null);
    await Promise.all([loadServices(), loadDropdown()]);
    await loadUsageForSelected();
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    servicesTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

