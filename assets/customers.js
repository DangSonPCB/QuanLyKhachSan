const API_CUSTOMERS = 'api/customers.php';

const customersTbody = document.getElementById('customersTbody');
const customerForm = document.getElementById('customerForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaKHEl = document.getElementById('MaKH');
const TenKHEl = document.getElementById('TenKH');
const SDTKHEl = document.getElementById('SDTKH');
const NgaySinhEl = document.getElementById('NgaySinhKH');
const GioiTinhEl = document.getElementById('GioiTinh');
const CCCDKHEl = document.getElementById('CCCDKH');
const emailEl = document.getElementById('emailKH');

const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm khách hàng';
    MaKHEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật khách hàng';
    MaKHEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  customerForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaKHEl.value = row.MaKH ?? '';
  TenKHEl.value = row.TenKH ?? '';
  SDTKHEl.value = row.SDTKH ?? '';
  // input[type=date] cần yyyy-mm-dd
  NgaySinhEl.value = row.NgaySinhKH ? String(row.NgaySinhKH) : '';
  GioiTinhEl.value = row.GioiTinh ?? '';
  CCCDKHEl.value = row.CCCDKH ?? '';
  emailEl.value = row.emailKH ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    customersTbody.innerHTML = '<tr><td colspan="6">Chưa có dữ liệu</td></tr>';
    return;
  }
  customersTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaKH)}</td>
      <td>${escHtml(r.TenKH)}</td>
      <td>${escHtml(r.SDTKH)}</td>
      <td>${escHtml(r.NgaySinhKH)}</td>
      <td>${escHtml(r.GioiTinh)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaKH)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaKH)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_CUSTOMERS}?action=list`);
  renderRows(res.data);
}

customerForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaKH: MaKHEl.value.trim(),
      TenKH: TenKHEl.value.trim(),
      SDTKH: SDTKHEl.value.trim(),
      NgaySinhKH: NgaySinhEl.value,
      GioiTinh: GioiTinhEl.value.trim(),
      CCCDKH: CCCDKHEl.value.trim(),
      emailKH: emailEl.value.trim(),
    };

    if (!payload.MaKH || !payload.TenKH || !payload.SDTKH || !payload.NgaySinhKH || !payload.GioiTinh || !payload.CCCDKH || !payload.emailKH) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_CUSTOMERS}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg('Lưu thành công', 'ok');
    await loadRows();
    resetForm();
  } catch (err) {
    setMsg(err.message || String(err), 'err');
  }
});

resetBtn.addEventListener('click', resetForm);

customersTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_CUSTOMERS}?action=list`);
      const row = res.data.find(x => x.MaKH === id);
      if (!row) throw new Error('Không tìm thấy khách hàng');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa khách hàng ${id}?`)) return;
      await fetchJson(`${API_CUSTOMERS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaKH: id }),
      });
      setMsg('Đã xóa', 'ok');
      await loadRows();
      resetForm();
    }
  } catch (err) {
    setMsg(err.message || String(err), 'err');
  }
});

(async function init() {
  try {
    setMsg('', null);
    await loadRows();
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    customersTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

