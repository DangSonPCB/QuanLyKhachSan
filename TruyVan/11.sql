--Tính tổng tiền hóa đơn cho một mã hóa đơn cụ thể (Vd hoá đơn mã HD01)
select 
	HD.MaHD, 
	KH.TenKH,
	HD.NgayIn, 
	CT.SoLuong * DV.GiaDV as N'Tổng tiền hoá đơn'
from 
	HOADON as HD,
	CHITIET_HD_DV as CT,
	KHACHHANG as KH,
	DICHVU as DV
where 
	HD.MaHD = 'HD01' and 
	CT.MaHD = 'HD01' and 
	CT.MaDV = DV.MaDV and
	KH.MaKH = HD.MaKH
