--Số lượng khách hàng trong các tháng và sắp xếp từ lớn đến bé
select 
    year (NgayNhan) as Nam, 
    month (NgayNhan) as Thang, 
    sum(SoKhanhhang) AS TongLuotKhach
from DATPHONG
group by year (NgayNhan), month (NgayNhan)
order by TongLuotKhach desc;
