const API_WAGES = 'api/wages.php';

const wagesTbody = document.getElementById('wagesTbody');
const wageForm = document.getElementById('wageForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaLuongEl = document.getElementById('MaLuong');
const MaNVSelect = document.getElementById('MaNVSelect');
const ThanhToanEl = document.getElementById('ThanhToan');
const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm lương';
    MaLuongEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật lương';
    MaLuongEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  wageForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaLuongEl.value = row.MaLuong ?? '';
  MaNVSelect.value = row.MaNV ?? '';
  ThanhToanEl.value = row.ThanhToan ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    wagesTbody.innerHTML = '<tr><td colspan="6">Chưa có dữ liệu</td></tr>';
    return;
  }
  wagesTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaLuong)}</td>
      <td>${escHtml(r.MaNV)}</td>
      <td>${escHtml(r.TenNV)}</td>
      <td>${escHtml(r.ChucVu)}</td>
      <td>${moneyFmt.format(moneyToNumber(r.ThanhToan))}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaLuong)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaLuong)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_WAGES}?action=list`);
  renderRows(res.data);
}

async function loadDropdownEmployees() {
  const res = await fetchJson(`${API_WAGES}?action=dropdownEmployees`);
  const rows = res.data || [];
  MaNVSelect.innerHTML = rows.map(r => `<option value="${escHtml(r.MaNV)}">${escHtml(r.MaNV)} - ${escHtml(r.TenNV)}</option>`).join('');
}

wageForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';
    const payload = {
      MaLuong: MaLuongEl.value.trim(),
      MaNV: MaNVSelect.value,
      ThanhToan: ThanhToanEl.value,
    };
    if (!payload.MaLuong || !payload.MaNV || payload.ThanhToan === '') {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_WAGES}?action=${action}`, {
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

wagesTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_WAGES}?action=list`);
      const row = res.data.find(x => x.MaLuong === id);
      if (!row) throw new Error('Không tìm thấy lương');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa lương ${id}?`)) return;
      await fetchJson(`${API_WAGES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaLuong: id }),
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
    await Promise.all([loadDropdownEmployees(), loadRows()]);
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    wagesTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

