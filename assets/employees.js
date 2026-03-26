const API_EMPLOYEES = 'api/employees.php';

const employeesTbody = document.getElementById('employeesTbody');
const employeeForm = document.getElementById('employeeForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaNVEl = document.getElementById('MaNV');
const TenNVEl = document.getElementById('TenNV');
const NgaySinhEl = document.getElementById('NgaySinhNV');
const ChucVuEl = document.getElementById('ChucVu');
const SDTNVEl = document.getElementById('SDTNV');
const CCCDNVEl = document.getElementById('CCCDNV');

const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm nhân viên';
    MaNVEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật nhân viên';
    MaNVEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  employeeForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaNVEl.value = row.MaNV ?? '';
  TenNVEl.value = row.TenNV ?? '';
  NgaySinhEl.value = row.NgaySinhNV ? String(row.NgaySinhNV) : '';
  ChucVuEl.value = row.ChucVu ?? '';
  SDTNVEl.value = row.SDTNV ?? '';
  CCCDNVEl.value = row.CCCDNV ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    employeesTbody.innerHTML = '<tr><td colspan="6">Chưa có dữ liệu</td></tr>';
    return;
  }
  employeesTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaNV)}</td>
      <td>${escHtml(r.TenNV)}</td>
      <td>${escHtml(r.NgaySinhNV)}</td>
      <td>${escHtml(r.ChucVu)}</td>
      <td>${escHtml(r.SDTNV)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaNV)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaNV)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_EMPLOYEES}?action=list`);
  renderRows(res.data);
}

employeeForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaNV: MaNVEl.value.trim(),
      TenNV: TenNVEl.value.trim(),
      NgaySinhNV: NgaySinhEl.value,
      ChucVu: ChucVuEl.value.trim(),
      SDTNV: SDTNVEl.value.trim(),
      CCCDNV: CCCDNVEl.value.trim(),
    };

    if (!payload.MaNV || !payload.TenNV || !payload.NgaySinhNV || !payload.ChucVu || !payload.SDTNV || !payload.CCCDNV) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_EMPLOYEES}?action=${action}`, {
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

employeesTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_EMPLOYEES}?action=list`);
      const row = res.data.find(x => x.MaNV === id);
      if (!row) throw new Error('Không tìm thấy nhân viên');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa nhân viên ${id}?`)) return;
      await fetchJson(`${API_EMPLOYEES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaNV: id }),
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
    employeesTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

