const API_ROOMS = 'api/rooms.php';

const roomsTbody = document.getElementById('roomsTbody');
const roomForm = document.getElementById('roomForm');
const formMode = document.getElementById('formMode');
const formTitle = document.getElementById('formTitle');
const formMsg = document.getElementById('formMsg');

const SoPhongEl = document.getElementById('SoPhong');
const LoaiEl = document.getElementById('Loai');
const GiaThueEl = document.getElementById('GiaThue');
const TrangThaiThueEl = document.getElementById('TrangThaiThue');

const resetBtn = document.getElementById('resetBtn');

function setMsg(text, kind) {
  formMsg.textContent = text || '';
  formMsg.classList.remove('ok', 'err');
  if (kind) formMsg.classList.add(kind);
}

function setFormMode(mode) {
  formMode.value = mode;
  if (mode === 'create') {
    formTitle.textContent = 'Thêm phòng';
    SoPhongEl.readOnly = false;
  } else {
    formTitle.textContent = 'Cập nhật phòng';
    SoPhongEl.readOnly = true;
  }
  setMsg('', null);
}

function resetForm() {
  roomForm.reset();
  setFormMode('create');
}

function fillForm(row) {
  SoPhongEl.value = row.SoPhong ?? '';
  LoaiEl.value = row.Loai ?? '';
  GiaThueEl.value = row.GiaThue ?? '';
  TrangThaiThueEl.value = row.TrangThaiThue ?? '';
}

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    roomsTbody.innerHTML = '<tr><td colspan="5">Chưa có dữ liệu</td></tr>';
    return;
  }
  roomsTbody.innerHTML = rows.map(r => `
    <tr>
      <td>${escHtml(r.SoPhong)}</td>
      <td>${escHtml(r.Loai)}</td>
      <td>${moneyFmt.format(moneyToNumber(r.GiaThue))}</td>
      <td>${escHtml(r.TrangThaiThue)}</td>
      <td>
        <button class="btn" data-action="edit" data-id="${escHtml(r.SoPhong)}">Sửa</button>
        <button class="btn danger" data-action="delete" data-id="${escHtml(r.SoPhong)}">Xóa</button>
      </td>
    </tr>
  `).join('');
}

async function loadRows() {
  const res = await fetchJson(`${API_ROOMS}?action=list`);
  renderRows(res.data);
}

roomForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const mode = formMode.value;
    const action = mode === 'create' ? 'create' : 'update';

    const payload = {
      SoPhong: SoPhongEl.value.trim(),
      Loai: LoaiEl.value.trim(),
      GiaThue: GiaThueEl.value,
      TrangThaiThue: TrangThaiThueEl.value.trim(),
    };

    if (!payload.SoPhong || !payload.Loai || payload.GiaThue === '' || !payload.TrangThaiThue) {
      throw new Error('Vui lòng điền đầy đủ thông tin');
    }

    setMsg('Đang lưu...', null);
    await fetchJson(`${API_ROOMS}?action=${action}`, {
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

roomsTbody.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');
  if (!action || !id) return;

  try {
    if (action === 'edit') {
      const res = await fetchJson(`${API_ROOMS}?action=list`);
      const row = res.data.find(x => x.SoPhong === id);
      if (!row) throw new Error('Không tìm thấy phòng');
      setFormMode('update');
      fillForm(row);
      setMsg('', null);
    }

    if (action === 'delete') {
      if (!confirm(`Xóa phòng ${id}?`)) return;
      await fetchJson(`${API_ROOMS}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ SoPhong: id }),
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
    roomsTbody.innerHTML = '<tr><td colspan="5">Tải thất bại</td></tr>';
  }
})();

