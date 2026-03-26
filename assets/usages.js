const API_USAGES = 'api/usages.php';

const usageTbody = document.getElementById('usageTbody');
const usageForm = document.getElementById('usageForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaDVSelect = document.getElementById('MaDVSelect');
const MaKHSelect = document.getElementById('MaKHSelect');
const NgaySuDungEl = document.getElementById('NgaySuDung');
const SoLuongEl = document.getElementById('SoLuong');
const resetBtn = document.getElementById('resetBtn');

const keyMaDV = document.getElementById('keyMaDV');
const keyMaKH = document.getElementById('keyMaKH');
const keyNgaySuDung = document.getElementById('keyNgaySuDung');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm dùng dịch vụ';
    keyMaDV.value = '';
    keyMaKH.value = '';
    keyNgaySuDung.value = '';
    MaDVSelect.disabled = false;
    MaKHSelect.disabled = false;
    NgaySuDungEl.disabled = false;
  } else {
    formTitle.textContent = 'Cập nhật dùng dịch vụ';
    MaDVSelect.disabled = true;
    MaKHSelect.disabled = true;
    NgaySuDungEl.disabled = true;
  }
  setMsg('', null);
}

function resetForm() {
  usageForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaDVSelect.value = row.MaDV ?? '';
  MaKHSelect.value = row.MaKH ?? '';
  NgaySuDungEl.value = row.NgaySuDung ? String(row.NgaySuDung) : '';
  SoLuongEl.value = row.SoLuong ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    usageTbody.innerHTML = '<tr><td colspan="7">Chưa có dữ liệu</td></tr>';
    return;
  }
  usageTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaDV)}</td>
      <td>${escHtml(r.TenDV)}</td>
      <td>${escHtml(r.MaKH)}</td>
      <td>${escHtml(r.TenKH)}</td>
      <td>${escHtml(r.NgaySuDung)}</td>
      <td>${escHtml(r.SoLuong)}</td>
      <td>
        <button class="btn" data-action="edit" data-madv="${escHtml(r.MaDV)}" data-makh="${escHtml(r.MaKH)}" data-ngay="${escHtml(r.NgaySuDung)}" data-sol="${escHtml(r.SoLuong)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-madv="${escHtml(r.MaDV)}" data-makh="${escHtml(r.MaKH)}" data-ngay="${escHtml(r.NgaySuDung)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_USAGES}?action=list`);
  renderRows(res.data);
}

async function loadDropdowns() {
  const [dvRes, khRes] = await Promise.all([
    fetchJson(`${API_USAGES}?action=dropdownServices`),
    fetchJson(`${API_USAGES}?action=dropdownCustomers`),
  ]);

  const dvRows = dvRes.data || [];
  MaDVSelect.innerHTML = dvRows.map(r => `<option value="${escHtml(r.MaDV)}">${escHtml(r.MaDV)} - ${escHtml(r.TenDV)}</option>`).join('');

  const khRows = khRes.data || [];
  MaKHSelect.innerHTML = khRows.map(r => `<option value="${escHtml(r.MaKH)}">${escHtml(r.MaKH)} - ${escHtml(r.TenKH)}</option>`).join('');
}

usageForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaDV: mode === 'create' ? MaDVSelect.value : keyMaDV.value,
      MaKH: mode === 'create' ? MaKHSelect.value : keyMaKH.value,
      NgaySuDung: mode === 'create' ? NgaySuDungEl.value : keyNgaySuDung.value,
      SoLuong: SoLuongEl.value,
    };

    if (!payload.MaDV || !payload.MaKH || !payload.NgaySuDung || payload.SoLuong === '') {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_USAGES}?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    setMsg('Lưu thành công', 'ok');
    await loadRows();
    await loadDropdowns();
    resetForm();
  } catch (err) {
    setMsg(err.message || String(err), 'err');
  }
});

resetBtn.addEventListener('click', resetForm);

usageTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const MaDV = btn.getAttribute('data-madv');
  const MaKH = btn.getAttribute('data-makh');
  const NgaySuDung = btn.getAttribute('data-ngay');
  const SoLuong = btn.getAttribute('data-sol');

  if (!action || !MaDV || !MaKH || !NgaySuDung) return;

  try {
    if (action === 'edit') {
      keyMaDV.value = MaDV;
      keyMaKH.value = MaKH;
      keyNgaySuDung.value = NgaySuDung;
      MaDVSelect.value = MaDV;
      MaKHSelect.value = MaKH;
      NgaySuDungEl.value = NgaySuDung;
      SoLuongEl.value = SoLuong ?? '';
      setFormMode('update');
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa dùng dịch vụ (${MaDV}, ${MaKH}, ${NgaySuDung})?`)) return;
      await fetchJson(`${API_USAGES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaDV, MaKH, NgaySuDung }),
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
    await Promise.all([loadDropdowns(), loadRows()]);
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    usageTbody.innerHTML = '<tr><td colspan="7">Tải thất bại</td></tr>';
  }
})();

