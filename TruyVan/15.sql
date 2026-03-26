--3 khách hàng thân thiết (đặt phòng nhiều nhất)
select top 3 
    KH.MaKH, 
    KH.TenKH, 
    KH.SDTKH,
    count(dp.MaDatPhong) as SoLanDatPhong
from KHACHHANG KH
join DATPHONG dp on KH.MaKH = dp.MaKH
group by KH.MaKH, KH.TenKH, KH.SDTKH
order by SoLanDatPhong
