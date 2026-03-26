--Liệt kê các dịch vụ có thể được sử dụng bây giờ

select * from DICHVU as DV
where cast(getdate() as time) between DV.BatDau and DV.KetThuc
