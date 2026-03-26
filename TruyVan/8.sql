--Đổi phòng còn trống cho khách (update)
--Ví dụ: Đổi phòng cho khách KH04 từ P201 sang P202 (P202 đang trống):
UPDATE DATPHONG
SET SoPhong = 'P201'
WHERE MaKH = 'KH04'
  AND SoPhong = 'P202';

UPDATE PHONG
SET TrangThaiThue = N'DangThue'
WHERE SoPhong = 'P202';

UPDATE PHONG
SET TrangThaiThue = N'Trong'
WHERE SoPhong = 'P201';

SELECT SoPhong, Loai, GiaThue, TrangThaiThue
FROM PHONG
