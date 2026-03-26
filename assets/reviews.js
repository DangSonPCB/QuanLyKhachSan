const API_REVIEWS = 'api/reviews.php';

const reviewsTbody = document.getElementById('reviewsTbody');
const reviewForm = document.getElementById('reviewForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const MaDGEl = document.getElementById('MaDG');
const MaKHSelect = document.getElementById('MaKHSelect');
const DiemDGEl = document.getElementById('DiemDG');
const NoiDungDGEl = document.getElementById('NoiDungDG');
const NgayDGEl = document.getElementById('NgayDG');
const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm đánh giá';
    MaDGEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật đánh giá';
    MaDGEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  reviewForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  MaDGEl.value = row.MaDG ?? '';
  MaKHSelect.value = row.MaKH ?? '';
  DiemDGEl.value = row.DiemDG ?? '';
  NoiDungDGEl.value = row.NoiDungDG ?? '';
  NgayDGEl.value = row.NgayDG ? String(row.NgayDG) : '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    reviewsTbody.innerHTML = '<tr><td colspan="6">Chưa có dữ liệu</td></tr>';
    return;
  }
  reviewsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.MaDG)}</td>
      <td>${escHtml(r.MaKH)}</td>
      <td>${escHtml(r.TenKH)}</td>
      <td>${escHtml(r.DiemDG)}</td>
      <td>${escHtml(r.NgayDG)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.MaDG)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.MaDG)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_REVIEWS}?action=list`);
  renderRows(res.data);
}

async function loadDropdownCustomers() {
  const res = await fetchJson(`${API_REVIEWS}?action=dropdownCustomers`);
  const rows = res.data || [];
  MaKHSelect.innerHTML = rows.map(r => `<option value="${escHtml(r.MaKH)}">${escHtml(r.MaKH)} - ${escHtml(r.TenKH)}</option>`).join('');
}

reviewForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      MaDG: MaDGEl.value.trim(),
      MaKH: MaKHSelect.value,
      DiemDG: DiemDGEl.value,
      NoiDungDG: NoiDungDGEl.value.trim(),
      NgayDG: NgayDGEl.value,
    };

    if (!payload.MaDG || !payload.MaKH || payload.DiemDG === '' || !payload.NoiDungDG || !payload.NgayDG) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_REVIEWS}?action=${action}`, {
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

reviewsTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_REVIEWS}?action=list`);
      const row = res.data.find(x => x.MaDG === id);
      if (!row) throw new Error('Không tìm thấy đánh giá');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa đánh giá ${id}?`)) return;
      await fetchJson(`${API_REVIEWS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ MaDG: id }),
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
    await Promise.all([loadDropdownCustomers(), loadRows()]);
  } catch (err) {
    setMsg(err.message || String(err), 'err');
    reviewsTbody.innerHTML = '<tr><td colspan="6">Tải thất bại</td></tr>';
  }
})();

