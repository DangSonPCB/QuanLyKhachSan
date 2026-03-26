const API_SCHEDULES = 'api/schedules.php';

const schedulesTbody = document.getElementById('schedulesTbody');
const scheduleForm = document.getElementById('scheduleForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaNVSelect = document.getElementById('MaNVSelect');
const SoPhongSelect = document.getElementById('SoPhongSelect');
const CaLamViecEl = document.getElementById('CaLamViec');
const NgayPhanCongEl = document.getElementById('NgayPhanCong');
const resetBtn = document.getElementById('resetBtn');

const keyMaNV = document.getElementById('keyMaNV');
const keySoPhong = document.getElementById('keySoPhong');
const keyNgayPhanCong = document.getElementById('keyNgayPhanCong');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm phân công';
    keyMaNV.value = '';
    keySoPhong.value = '';
    keyNgayPhanCong.value = '';
    MaNVSelect.disabled = false;
    SoPhongSelect.disabled = false;
    NgayPhanCongEl.disabled = false;
  } else {
    formTitle.textContent = 'Cập nhật phân công';
    MaNVSelect.disabled = true;
    SoPhongSelect.disabled = true;
    NgayPhanCongEl.disabled = true;
  }
  setMsg('', null);
}

function resetForm() {
  scheduleForm.reset();
  setFormMode('create');
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    schedulesTbody.innerHTML = '<tr><td colspan="7">Chưa có dữ liệu</td></tr>';
    return;
  }
  schedulesTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaNV)}</td>
      <td>${escHtml(r.TenNV)}</td>
      <td>${escHtml(r.SoPhong)}</td>
      <td>${escHtml(r.Loai)}</td>
      <td>${escHtml(r.CaLamViec)}</td>
      <td>${escHtml(r.NgayPhanCong)}</td>
      <td>
        <button class="btn" data-action="edit" data-manv="${escHtml(r.MaNV)}" data-map="${escHtml(r.SoPhong)}" data-ngay="${escHtml(r.NgayPhanCong)}" data-ca="${escHtml(r.CaLamViec)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-manv="${escHtml(r.MaNV)}" data-map="${escHtml(r.SoPhong)}" data-ngay="${escHtml(r.NgayPhanCong)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_SCHEDULES}?action=list`);
  renderRows(res.data);
}

async function loadDropdowns() {
  const [nvRes, roomRes] = await Promise.all([
    fetchJson(`${API_SCHEDULES}?action=dropdownEmployees`),
    fetchJson(`${API_SCHEDULES}?action=dropdownRooms`),
  ]);

  const nvRows = nvRes.data || [];
  MaNVSelect.innerHTML = nvRows.map(r => `<option value="${escHtml(r.MaNV)}">${escHtml(r.MaNV)} - ${escHtml(r.TenNV)}</option>`).join('');

  const roomRows = roomRes.data || [];
  SoPhongSelect.innerHTML = roomRows.map(r => `<option value="${escHtml(r.SoPhong)}">${escHtml(r.SoPhong)} - ${escHtml(r.Loai)}</option>`).join('');
}

scheduleForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaNV: mode === 'create' ? MaNVSelect.value : keyMaNV.value,
      SoPhong: mode === 'create' ? SoPhongSelect.value : keySoPhong.value,
      NgayPhanCong: mode === 'create' ? NgayPhanCongEl.value : keyNgayPhanCong.value,
      CaLamViec: CaLamViecEl.value.trim(),
    };

    if (!payload.MaNV || !payload.SoPhong || !payload.NgayPhanCong || !payload.CaLamViec) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_SCHEDULES}?action=${action}`, {
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

schedulesTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const MaNV = btn.getAttribute('data-manv');
  const SoPhong = btn.getAttribute('data-map');
  const NgayPhanCong = btn.getAttribute('data-ngay');
  const CaLamViec = btn.getAttribute('data-ca');

  if (!action || !MaNV || !SoPhong || !NgayPhanCong) return;

  try {
    if (action === 'edit') {
      keyMaNV.value = MaNV;
      keySoPhong.value = SoPhong;
      keyNgayPhanCong.value = NgayPhanCong;

      MaNVSelect.value = MaNV;
      SoPhongSelect.value = SoPhong;
      NgayPhanCongEl.value = NgayPhanCong;
      CaLamViecEl.value = CaLamViec ?? '';

      setFormMode('update');
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa phân công (${MaNV}, ${SoPhong}, ${NgayPhanCong})?`)) return;
      await fetchJson(`${API_SCHEDULES}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaNV, SoPhong, NgayPhanCong }),
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
    schedulesTbody.innerHTML = '<tr><td colspan="7">Tải thất bại</td></tr>';
  }
})();

