--Tính tổng hoá đơn của các khách hàng

select 
	KH.MaKH, HD.MaHD,
	KH.TenKH, CT.SoLuong * DV.GiaDV as N'Tổng Hoá đơn'
from 
	KHACHHANG as KH,
	CHITIET_HD_DV as CT,
	DICHVU as DV,
	HOADON as HD
where 
	DV.MaDV = CT.MaDV and 
	KH.MaKH = HD.MaKH and 
	CT.MaHD = HD.MaHD;
