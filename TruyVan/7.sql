--Liệt kê  nhân viên đang phục vụ 1 phòng cụ thể.

SELECT NV.MaNV, NV.TenNV, NV.ChucVu, NV.SDTNV, PC.CaLamViec, PC.NgayPhanCong
FROM PHANCONG_NV AS PC
JOIN NHANVIEN AS NV ON PC.MaNV = NV.MaNV
WHERE PC.SoPhong = 'P202';
