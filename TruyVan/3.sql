--Đưa ra thông tin khách hàng đến nhận phòng vào ngày 26-03-2026

SELECT KH.MaKH, KH.TenKH, KH.SDTKH,
       P.SoPhong, P.Loai,
       DP.NgayNhan
FROM KHACHHANG KH
JOIN DATPHONG DP ON KH.MaKH = DP.MaKH
JOIN PHONG P ON DP.SoPhong = P.SoPhong
WHERE DP.NgayNhan = '2026-03-26';
