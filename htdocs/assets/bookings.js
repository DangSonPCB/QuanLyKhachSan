const API_BOOKINGS = 'api/bookings.php';

const bookingsTbody = document.getElementById('bookingsTbody');
const bookingForm = document.getElementById('bookingForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaDatPhongEl = document.getElementById('MaDatPhong');
const SoPhongSelect = document.getElementById('SoPhongSelect');
const MaKHSelect = document.getElementById('MaKHSelect');
const NgayNhanEl = document.getElementById('NgayNhan');
const NgayTraEl = document.getElementById('NgayTra');
const SoKhanhhangEl = document.getElementById('SoKhanhhang');

const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm đặt phòng';
    MaDatPhongEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật đặt phòng';
    MaDatPhongEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  bookingForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaDatPhongEl.value = row.MaDatPhong ?? '';
  SoPhongSelect.value = row.SoPhong ?? '';
  MaKHSelect.value = row.MaKH ?? '';
  NgayNhanEl.value = row.NgayNhan ? String(row.NgayNhan) : '';
  NgayTraEl.value = row.NgayTra ? String(row.NgayTra) : '';
  SoKhanhhangEl.value = row.SoKhanhhang ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    bookingsTbody.innerHTML = '<tr><td colspan="9">Chưa có dữ liệu</td></tr>';
    return;
  }

  bookingsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaDatPhong)}</td>
      <td>${escHtml(r.SoPhong)}</td>
      <td>${escHtml(r.Loai)}</td>
      <td>${escHtml(r.MaKH)}</td>
      <td>${escHtml(r.TenKH)}</td>
      <td>${escHtml(r.NgayNhan)}</td>
      <td>${escHtml(r.NgayTra)}</td>
      <td>${escHtml(r.SoKhanhhang)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaDatPhong)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaDatPhong)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_BOOKINGS}?action=list`);
  renderRows(res.data);
}

async function loadDropdownRooms() {
  const res = await fetchJson(`${API_BOOKINGS}?action=dropdownRooms`);
  const rows = res.data || [];
  SoPhongSelect.innerHTML = rows.map(r => `<option value="${escHtml(r.SoPhong)}">${escHtml(r.SoPhong)} - ${escHtml(r.Loai)}</option>`).join('');
}

async function loadDropdownCustomers() {
  const res = await fetchJson(`${API_BOOKINGS}?action=dropdownCustomers`);
  const rows = res.data || [];
  MaKHSelect.innerHTML = rows.map(r => `<option value="${escHtml(r.MaKH)}">${escHtml(r.MaKH)} - ${escHtml(r.TenKH)}</option>`).join('');
}

bookingForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaDatPhong: MaDatPhongEl.value.trim(),
      SoPhong: SoPhongSelect.value,
      MaKH: MaKHSelect.value,
      NgayNhan: NgayNhanEl.value,
      NgayTra: NgayTraEl.value,
      SoKhanhhang: SoKhanhhangEl.value,
    };

    if (!payload.MaDatPhong || !payload.SoPhong || !payload.MaKH || !payload.NgayNhan || !payload.NgayTra || payload.SoKhanhhang === '') {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_BOOKINGS}?action=${action}`, {
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

bookingsTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_BOOKINGS}?action=list`);
      const row = res.data.find(x => x.MaDatPhong === id);
      if (!row) throw new Error('Không tìm thấy đặt phòng');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa đặt phòng ${id}?`)) return;
      await fetchJson(`${API_BOOKINGS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaDatPhong: id }),
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
    await Promise.all([loadDropdownRooms(), loadDropdownCustomers(), loadRows()]);
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    bookingsTbody.innerHTML = '<tr><td colspan="9">Tải thất bại</td></tr>';
  }
})();

