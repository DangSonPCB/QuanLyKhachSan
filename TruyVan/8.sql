--Liệt kê nhân viên có mức lương dưới 5.000.000.

select * from NHANVIEN as NV, LUONG as L
where NV.MaNV = L.MaNV and L.ThanhToan < 5000000
