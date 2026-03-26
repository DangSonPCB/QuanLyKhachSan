const API_ACCOUNTS = 'api/accounts.php';

const accountsTbody = document.getElementById('accountsTbody');
const accountForm = document.getElementById('accountForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const IDEl = document.getElementById('ID');
const TenDangNhapEl = document.getElementById('TenDangNhap');
const MatKhauEl = document.getElementById('MatKhau');
const TrangThaiEl = document.getElementById('TrangThai');
const MaNVSelect = document.getElementById('MaNVSelect');
const MaKHSelect = document.getElementById('MaKHSelect');
const resetBtn = document.getElementById('resetBtn');

const loginForm = document.getElementById('loginForm');
const loginTen = document.getElementById('loginTen');
const loginPass = document.getElementById('loginPass');
const loginMsg = document.getElementById('loginMsg');

function setMsg(el, text, kind) {
  el.textContent = text || '';
  el.classList.remove('ok', 'err');
  if (kind) el.classList.add(kind);
}

function setAccountFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm tài khoản';
    IDEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật tài khoản';
    IDEl.readOnly = true;
  }
  setMsg(formMsg, '', null);
}

function resetForm() {
  accountForm.reset();
  setAccountFormMode('create');
}

function fillForm(row) {
  IDEl.value = row.ID ?? '';
  TenDangNhapEl.value = row.TenDangNhap ?? '';
  MatKhauEl.value = row.MatKhau ?? '';
  TrangThaiEl.value = row.TrangThai ?? '';
  MaNVSelect.value = row.MaNV ?? '';
  MaKHSelect.value = row.MaKH ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    accountsTbody.innerHTML = '<tr><td colspan="6">Chưa có dữ liệu</td></tr>';
    return;
  }
  accountsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.ID)}</td>
      <td>${escHtml(r.TenDangNhap)}</td>
      <td>${escHtml(r.TrangThai)}</td>
      <td>${escHtml(r.MaNV ? r.MaNV + ' - ' + (r.TenNV || '') : '')}</td>
      <td>${escHtml(r.MaKH ? r.MaKH + ' - ' + (r.TenKH || '') : '')}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.ID)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.ID)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_ACCOUNTS}?action=list`);
  renderRows(res.data);
}

async function loadDropdowns() {
  const [nvRes, khRes] = await Promise.all([
    fetchJson(`${API_ACCOUNTS}?action=dropdownEmployees`),
    fetchJson(`${API_ACCOUNTS}?action=dropdownCustomers`),
  ]);

  const nvRows = nvRes.data || [];
  MaNVSelect.innerHTML = '<option value="">(NULL)</option>' + nvRows.map(r => `<option value="${escHtml(r.MaNV)}">${escHtml(r.MaNV)} - ${escHtml(r.TenNV)}</option>`).join('');

  const khRows = khRes.data || [];
  MaKHSelect.innerHTML = '<option value="">(NULL)</option>' + khRows.map(r => `<option value="${escHtml(r.MaKH)}">${escHtml(r.MaKH)} - ${escHtml(r.TenKH)}</option>`).join('');
}

accountForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      ID: IDEl.value.trim(),
      TenDangNhap: TenDangNhapEl.value.trim(),
      MatKhau: MatKhauEl.value.trim(),
      TrangThai: TrangThaiEl.value.trim(),
      MaNV: MaNVSelect.value,
      MaKH: MaKHSelect.value,
    };

    if (!payload.ID || !payload.TenDangNhap || !payload.MatKhau || !payload.TrangThai) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }
    if (!payload.MaNV && !payload.MaKH) {
      throw new Error('Bạn phải chọn 1 trong MaNV hoặc MaKH');
    }
    if (payload.MaNV && payload.MaKH) {
      throw new Error('Chỉ chọn tối đa 1 trong MaNV hoặc MaKH');
    }

    setMsg(formMsg, 'Đang lưu...', null);
    await fetchJson(`${API_ACCOUNTS}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg(formMsg, 'Lưu thành công', 'ok');
    await loadRows();
    resetForm();
  } catch (err) {
    setMsg(formMsg, err.message || String(err), 'err');
  }
});

resetBtn.addEventListener('click', resetForm);

accountsTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_ACCOUNTS}?action=list`);
      const row = res.data.find(x => x.ID === id);
      if (!row) throw new Error('Không tìm thấy tài khoản');
      // API list không trả MatKhau; ta chỉ giữ form và để user nhập lại MatKhau.
      IDEl.value = row.ID ?? '';
      TenDangNhapEl.value = row.TenDangNhap ?? '';
      TrangThaiEl.value = row.TrangThai ?? '';
      MaNVSelect.value = row.MaNV ?? '';
      MaKHSelect.value = row.MaKH ?? '';
      MatKhauEl.value = '';

      setAccountFormMode('update');
      setMsg(formMsg, '', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa tài khoản ${id}?`)) return;
      await fetchJson(`${API_ACCOUNTS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ID: id }),
      });
      setMsg(formMsg, 'Đã xóa', 'ok');
      await loadRows();
      resetForm();
    }
  } catch (err) {
    setMsg(formMsg, err.message || String(err), 'err');
  }
});

loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    setMsg(loginMsg, '', null);
    await fetchJson(`${API_ACCOUNTS}?action=login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        TenDangNhap: loginTen.value.trim(),
        MatKhau: loginPass.value.trim(),
      }),
    });
    setMsg(loginMsg, 'Đăng nhập thành công', 'ok');
  } catch (err) {
    setMsg(loginMsg, err.message || String(err), 'err');
  }
});

(async function init() {
  try {
    setMsg(formMsg, '', null);
    setAccountFormMode('create');
    await Promise.all([loadDropdowns(), loadRows()]);
  } catch (err) {
    setMsg(formMsg, err.message || String(err), 'err');
    accountsTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

