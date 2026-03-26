--Liệt kê các tài khoản khách đang không hoạt động
select * from TAIKHOAN as acc
where not acc.MaKH = 'NULL' and acc.TrangThai = 'KhongHoatDong'
